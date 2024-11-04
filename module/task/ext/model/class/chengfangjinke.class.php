<?php
class chengfangjinkeTask extends taskModel
{
    public function create($executionID, $auto = false)
    {
        if($this->post->estimate < 0)
        {
            dao::$errors[] = $this->lang->task->error->recordMinus;
            return false;
        }

        $executionID    = (int)$executionID;
        $taskIdList     = array();
        $taskFiles      = array();
        $requiredFields = "," . $this->config->task->create->requiredFields . ",";

        if($this->post->selectTestStory)
        {
            $requiredFields = str_replace(",estimate,", ',', "$requiredFields");
            $requiredFields = str_replace(",story,", ',', "$requiredFields");
            $requiredFields = str_replace(",estStarted,", ',', "$requiredFields");
            $requiredFields = str_replace(",deadline,", ',', "$requiredFields");
        }

        $grade = 0;
        if($this->post->parent) $grade = $this->getById($this->post->parent)->grade;

        $this->loadModel('file');
        $task = fixer::input('post')
            ->setDefault('execution', $executionID)
            ->setDefault('estimate,left,story', 0)
            ->setDefault('status', 'wait')
            ->setIF($this->config->systemMode == 'new', 'project', $this->getProjectID($executionID))
            ->setIF($this->post->estimate != false, 'left', $this->post->estimate)
            ->setIF($this->post->story != false, 'storyVersion', $this->loadModel('story')->getVersion($this->post->story))
            ->setIF($this->post->parent, 'grade', $grade + 1)
            ->setDefault('estStarted', '0000-00-00')
            ->setDefault('deadline', '0000-00-00')
            /*->setIF(strpos($requiredFields, 'estStarted') !== false, 'estStarted', helper::isZeroDate($this->post->estStarted) ? '' : $this->post->estStarted)
            ->setIF(strpos($requiredFields, 'deadline') !== false, 'deadline', helper::isZeroDate($this->post->deadline) ? '' : $this->post->deadline)*/
            ->setIF(strpos($requiredFields, 'estStarted') !== false, 'estStarted', $this->post->estStarted)
            ->setIF(strpos($requiredFields, 'deadline') !== false, 'deadline',  $this->post->deadline)
            ->setIF(strpos($requiredFields, 'estimate') !== false, 'estimate', $this->post->estimate)
            ->setIF(strpos($requiredFields, 'left') !== false, 'left', $this->post->left)
            ->setIF(strpos($requiredFields, 'story') !== false, 'story', $this->post->story)
            ->setIF(is_numeric($this->post->estimate), 'estimate', (float)$this->post->estimate)
            ->setIF(is_numeric($this->post->consumed), 'consumed', (float)$this->post->consumed)
            ->setIF(is_numeric($this->post->left),     'left',     (float)$this->post->left)
            ->setDefault('openedBy',   $this->app->user->account)
            ->setDefault('openedDate', helper::now())
            ->cleanINT('execution,story,module')
            ->stripTags($this->config->task->editor->create['id'], $this->config->allowedTags)
            ->join('mailto', ',')
            ->join('resourceTo', ',')
            ->remove('after,files,labels,assignedTo,uid,storyEstimate,storyDesc,storyPri,team,teamEstimate,teamMember,multiple,teams,contactListMenu,selectTestStory,testStory,testPri,testEstStarted,testDeadline,testAssignedTo,testEstimate,sync')
            //->add('version', 1)
            ->add('version',0)//新建变更次数设为 0
            ->get();
        if($task->type != 'test') $this->post->set('selectTestStory', 0);

        foreach($this->post->assignedTo as $assignedTo)
        {
            /* When type is affair and has assigned then ignore none. */
            if($task->type == 'affair' and count($this->post->assignedTo) > 1 and empty($assignedTo)) continue;

            $task->assignedTo = $assignedTo;
            if($assignedTo) $task->assignedDate = helper::now();

            /* Check duplicate task. */
            if($task->type != 'affair' and $task->name)
            {
                $result = $this->loadModel('common')->removeDuplicate('task', $task, "execution={$executionID} and story=" . (int)$task->story);
                if($result['stop'])
                {
                    $taskIdList[$assignedTo] = array('status' => 'exists', 'id' => $result['duplicate']);
                    continue;
                }
            }

            $task = $this->file->processImgURL($task, $this->config->task->editor->create['id'], $this->post->uid);

            /* Fix Bug #1525 */
            $execution = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($executionID)->fetch();

            // 判断是否为运维类阶段。
            if($execution->type == 'ops')
            {
                $requiredFields = str_replace(",story,", ',', "$requiredFields");
                $task->story = 0;
            }

            // 判断是否为子阶段。
            if($execution->parent == '0')
            {
                dao::$errors['execution'] = $this->lang->task->error->subStageError;
                return false;
            }

            if(strpos($requiredFields, ',estimate,') !== false)
            {
                if(strlen(trim($task->estimate)) == 0) dao::$errors['estimate'] = sprintf($this->lang->error->notempty, $this->lang->task->estimate);
                $requiredFields = str_replace(',estimate,', ',', $requiredFields);
            }

            $requiredFields = trim($requiredFields, ',');

            // 判断是否存在上级。
            $task->grade = 1;
            if(isset($task->parent))
            {
                $parentGrade = $this->dao->select('grade')->from(TABLE_TASK)->where('id')->eq($task->parent)->fetch('grade');
                $task->grade = $parentGrade + 1;
            }

            // 多人任务，则阈值 * 人数。
            $teamTotal = 0;
            if($this->post->multiple and count(array_filter($this->post->team)) > 1)
            {
                foreach($this->post->team as $team)
                {
                    if(!empty($team))
                    {
                        $teamTotal ++;
                    }
                }
            }

            // 判断工期是否超出阈值。
            $diffDate = helper::diffDate2($task->estStarted, $task->deadline, false);
            $threshold = $diffDate * $this->config->task->workThreshold;
            if($teamTotal) $threshold = $threshold * $teamTotal;
            if($task->estimate > $threshold)
            {
                dao::$errors['name'] = sprintf($this->lang->task->error->thresholdError, $this->config->task->workThreshold);
                return false;
            }

            /* Fix Bug #2466 */
            if($this->post->multiple) $task->assignedTo = '';
            $this->dao->insert(TABLE_TASK)->data($task, $skip = 'gitlab,gitlabProject')
                ->autoCheck()
                ->batchCheck($requiredFields, 'notempty')
                ->checkIF($task->estimate != '', 'estimate', 'float')
                ->checkIF(!helper::isZeroDate($task->deadline), 'deadline', 'ge', $task->estStarted)
                ->exec();

            if(dao::isError()) return false;

            $taskID = $this->dao->lastInsertID();
            if($auto) $this->computeConsumed($taskID, $auto);
            // 用创建任务的时间更新作为阶段的拆分时间。
            $execution = new stdClass();
            $execution->splitBy   = $task->openedBy;
            $execution->splitDate = $task->openedDate;
            $this->dao->update(TABLE_EXECUTION)->data($execution)->where('id')->eq($task->execution)->exec();

            // 计算任务path字段。
            if($this->post->parent)
            {
                $newPath = $this->getById($this->post->parent)->path;
                $newPath = $newPath . ',' . $taskID;
                $this->dao->update(TABLE_TASK)->set('path')->eq($newPath)->where('id')->eq($taskID)->exec();
            }

            /* Mark design version.*/
            if(isset($task->design) && !empty($task->design))
            {
                $design = $this->loadModel('design')->getByID($task->design);
                $this->dao->update(TABLE_TASK)->set('designVersion')->eq($design->version)->where('id')->eq($taskID)->exec();
            }

            $taskSpec = new stdClass();
            $taskSpec->task       = $taskID;
            $taskSpec->version    = $task->version;
            $taskSpec->name       = $task->name;
            $taskSpec->estStarted = $task->estStarted;
            $taskSpec->deadline   = $task->deadline;

            $this->dao->insert(TABLE_TASKSPEC)->data($taskSpec)->autoCheck()->exec();
            if(dao::isError()) return false;

            if($this->post->story) $this->loadModel('story')->setStage($this->post->story);
            if($this->post->selectTestStory)
            {
                $testStoryIdList = array();
                $this->loadModel('action');
                if($this->post->testStory)
                {
                    foreach($this->post->testStory as $storyID)
                    {
                        if($storyID) $testStoryIdList[$storyID] = $storyID;
                    }
                    $testStories = $this->dao->select('id,title,version')->from(TABLE_STORY)->where('id')->in($testStoryIdList)->fetchAll('id');
                    foreach($this->post->testStory as $i => $storyID)
                    {
                        if(!isset($testStories[$storyID])) continue;

                        $task->parent       = $taskID;
                        $task->story        = $storyID;
                        $task->storyVersion = $testStories[$storyID]->version;
                        $task->name         = $this->lang->task->lblTestStory . " #{$storyID} " . $testStories[$storyID]->title;
                        $task->pri          = $this->post->testPri[$i];
                        $task->estStarted   = $this->post->testEstStarted[$i];
                        $task->deadline     = $this->post->testDeadline[$i];
                        $task->assignedTo   = $this->post->testAssignedTo[$i];
                        $task->estimate     = $this->post->testEstimate[$i];
                        $task->left         = $this->post->testEstimate[$i];
                        $this->dao->insert(TABLE_TASK)->data($task)->exec();

                        $childTaskID = $this->dao->lastInsertID();
                        $this->action->create('task', $childTaskID, 'Opened');
                    }
                }

                $this->computeWorkingHours($taskID);
                $this->computeBeginAndEnd($taskID);
                $this->dao->update(TABLE_TASK)->set('parent')->eq(1)->where('id')->eq($taskID)->exec();
            }
            $this->file->updateObjectID($this->post->uid, $taskID, 'task');
            if(!empty($taskFiles))
            {
                foreach($taskFiles as $taskFile)
                {
                    $taskFile->objectID = $taskID;
                    $this->dao->insert(TABLE_FILE)->data($taskFile)->exec();
                }
            }
            else
            {
                $taskFileTitle = $this->file->saveUpload('task', $taskID);
                $taskFiles     = $this->dao->select('*')->from(TABLE_FILE)->where('id')->in(array_keys($taskFileTitle))->fetchAll('id');
                foreach($taskFiles as $fileID => $taskFile) unset($taskFiles[$fileID]->id);
            }

            $teams = array();
            if($this->post->multiple and count(array_filter($this->post->team)) > 1)
            {
                foreach($this->post->team as $row => $account)
                {
                    if(empty($account) or isset($team[$account])) continue;
                    $member = new stdClass();
                    $member->root     = 0;
                    $member->account  = $account;
                    $member->role     = $assignedTo;
                    $member->join     = helper::today();
                    $member->estimate = $this->post->teamEstimate[$row] ? (float)$this->post->teamEstimate[$row] : 0;
                    $member->left     = $member->estimate;
                    $member->order    = $row;
                    $teams[$account]  = $member;
                }
            }

            if(!empty($teams))
            {
                foreach($teams as $team)
                {
                    $team->root = $taskID;
                    $team->type = 'task';
                    $this->dao->insert(TABLE_TEAM)->data($team)->autoCheck()->exec();
                }

                $task->id = $taskID;
                $this->computeHours4Multiple($task);
            }

            if(!dao::isError()) $this->loadModel('score')->create('task', 'create', $taskID);
            $taskIdList[$assignedTo] = array('status' => 'created', 'id' => $taskID);
            $taskIdList['id'] = $taskID;
        }
        return $taskIdList;
    }

    public function batchCreateEffort($taskID, $efforts)
    {
        $task     = $this->getById($taskID);
        $consumed = $this->dao->select('SUM(consumed) c')->from(TABLE_EFFORT)->where('objectType')->eq('task')
            ->andWhere('objectID')->eq($taskID)
            ->andWhere('deleted')->eq(0)
            ->fetch('c');
        $left     = $task->estimate - $consumed;
        //$progress = $task->progress;
        $depts    = $this->dao->select('account, dept')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();

        foreach($efforts as $effort)
        {
            $this->dao->insert(TABLE_EFFORT)->set('objectType')->eq('task')
                ->set('objectID')->eq($taskID)
                ->set('product')->eq(',,')
                ->set('project')->eq($task->project)
                ->set('execution')->eq($task->execution)
                ->set('account')->eq(isset($effort->account) ? $effort->account : $this->app->user->account)
                ->set('deptID')->eq(isset($effort->account) ? $depts[$effort->account] : $this->app->user->dept)
                ->set('source')->eq(isset($effort->support) ? 2 : (isset($effort->source) ? 1 : 0))
                ->set('buildID')->eq(isset($effort->buildID) ? $effort->buildID : 0)
                ->set('consumedID')->eq(isset($effort->consumedID) ? $effort->consumedID : 0)
                ->set('workID')->eq(isset($effort->workID) ? $effort->workID : 0)
                ->set('work')->eq($effort->work)
                //->set('progress')->eq($effort->progress)
               /* ->set('beginDate')->eq(isset($effort->beginDate) ? $effort->beginDate : '')
                ->set('endDate')->eq(isset($effort->endDate) ? $effort->endDate : '')*/
                ->set('date')->eq($effort->date)
                ->set('realDate')->eq(date('Y-m-d H:i:s'))
                ->set('left')->eq($effort->left)
                ->set('consumed')->eq($effort->consumed)
                ->exec();
            $effortID =  $this->dao->lastInsertID();
            $this->loadModel('action')->create('effort', $effortID, 'created', '');
            $consumed += $effort->consumed;
            $left     = $effort->left;
            //if($effort->progress > $progress) $progress = $effort->progress;

            // 判断是否为多人任务，如果是多人任务，则算一下团队参与人员工时信息。
            if(!empty($task->team))
            {
                if(isset($task->team[$this->app->user->account]))
                {
                    $teamUser         = $task->team[$this->app->user->account];
                    $teamUserLeft     = $teamUser->left + $effort->left;
                    $teamUserConsumed = $teamUser->consumed + $effort->consumed;
                    $this->dao->update(TABLE_TEAM)->set('left')->eq($teamUserLeft)->set('consumed')->eq($teamUserConsumed)
                        ->where('root')->eq((int)$taskID)
                        ->andWhere('type')->eq('task')
                        ->andWhere('account')->eq($this->app->user->account)->exec();
                }
            }
        }

        $this->dao->update(TABLE_TASK)->set('consumed')->eq($consumed)->set('left')->eq($left)->where('id')->eq($taskID)->exec();
        $this->computeConsumed($taskID);
    }

    public function createEffort($taskID, $consumed, $left, $date)
    {
        if($consumed)
        {
            $task = $this->getById($taskID);
            $this->dao->insert(TABLE_EFFORT)->set('objectType')->eq('task')
                ->set('objectID')->eq($taskID)
                ->set('product')->eq(',,')
                ->set('project')->eq($task->project)
                ->set('execution')->eq($task->execution)
                ->set('account')->eq($this->app->user->account)
                ->set('deptID')->eq($this->app->user->dept)
                ->set('work')->eq($this->lang->task->finish)
                ->set('date')->eq($date)
                ->set('realDate')->eq(date('Y-m-d H:i:s'))
                ->set('left')->eq($left)
                ->set('consumed')->eq($consumed)
                ->exec();
        }
        $this->computeConsumed($taskID);
    }

    public function computeConsumed($taskID, $auto = false)
    {
        $task = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();

        // 计算一下当前任务的实际工期。
        if($task->realStarted == '3000-00-00 00:00:00') $task->realStarted = '0000-00-00 00:00:00';
        if($task->estStarted == '3000-00-00')           $task->estStarted  = '0000-00-00';

        // 只要是进行中和未开始就清空。
        // if($task->status == 'wait' or $task->status == 'doing' or $task->status == 'pause')
        //迭代十四 20220908去掉进行中 
        if($task->status == 'wait'  or $task->status == 'pause')
        {
            $task->realDuration = 0;

            $task->finishedBy   = '';
            $task->finishedDate = $auto ? $task->finishedDate : '0000-00-00 00:00:00';
            $task->closedBy     = '';
            $task->closedDate   = '0000-00-00 00:00:00';
            $task->closedReason = '';
        }

        $task->planDuration = helper::diffDate3($task->deadline, $task->estStarted);
        if($task->status == 'done' or $task->status == 'closed')
        {
            $task->realDuration = helper::diffDate3($task->finishedDate, $task->realStarted);
        }
        //其他状态也计算实际工期
        $task->realDuration = helper::diffDate3($task->finishedDate, $task->realStarted);
        //迭代十四 20220906 计划和任务状态 未开始，实际工作量不为0，强制将状态更新为 进行中
        if($task->status == 'wait' && $task->consumed > 0){
            $task->status = 'doing';
        }

        $this->dao->update(TABLE_TASK)->data($task)->where('id')->eq($taskID)->exec();

        if($task->parent)
        {
            $this->computeTaskConsumed($taskID, $auto);
        }
        else
        {
            $this->computeParentExecutionStatus($taskID, $auto);
        }
    }

    // 计算任务的父任务相关字段数据。
    public function computeTaskConsumed($taskID, $auto = false)
    {
        $task = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();

        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('parent')->eq($task->parent)->andWhere('deleted')->eq(0)->fetchAll();

        $total = new stdclass();
        $total->estimate     = 0;
        $total->consumed     = 0;
        $total->left         = 0;
        $total->progress     = 0;
        $total->status       = 'doing';
        $total->estStarted   = '3000-00-00';
        $total->deadline     = '0000-00-00';
        $total->realStarted  = '3000-00-00 00:00:00';
        $total->finishedDate = '0000-00-00 00:00:00';

        # task中realStarted、finishedDate都是datetime

        $isDoing  = false;
        $isDone   = true;
        $isClosed = true;
        $isWait   = true;
        $isCancel = true;
        $isPause  = true;

        // 假如删除当前的这个任务，该任务父任务下没有子任务了。
        if(empty($tasks))
        {
            $parent = $this->getById($task->parent);
            $total->estStarted   = $parent->estStarted;
            $total->deadline     = $parent->deadline;
            $total->realStarted  = $parent->realStarted;
            $total->finishedDate = $parent->finishedDate;

            if($parent->status == 'doing')  $isDoing  = true;
            if($parent->status == 'done')   $isDone   = true;
            if($parent->status == 'closed') $isClosed = true;
            if($parent->status == 'wait')   $isWait   = true;
            if($parent->status == 'cancel') $isCancel = true;
            if($parent->status == 'pause')  $isPause  = true;
        }

        // 特殊情况：完成后，任务可关闭，可关闭状态也是完成状态。
        $isSpecial = true;
        foreach($tasks as $t)
        {
            $total->estimate += $t->estimate;
            $total->consumed += $t->consumed;
            $total->left     += $t->left;
            $total->progress += $t->estimate * $t->progress;

            if($t->status == 'doing')  $isDoing  = true;
            if($t->status != 'done')   $isDone   = false;
            if($t->status != 'closed') $isClosed = false;
            if($t->status != 'wait')   $isWait   = false;
            if($t->status != 'cancel') $isCancel = false;
            if($t->status != 'pause')  $isPause  = false;

            if($t->status != 'done' and $t->status != 'closed') $isSpecial = false;

            if($t->estStarted < $total->estStarted and $t->estStarted != '0000-00-00') $total->estStarted = $t->estStarted;
            if($t->deadline   > $total->deadline)   $total->deadline   = $t->deadline;

            if($t->realStarted < $total->realStarted and $t->realStarted != '0000-00-00 00:00:00') $total->realStarted = $t->realStarted;
            if($t->finishedDate > $total->finishedDate) $total->finishedDate = $t->finishedDate;
        }
        if($total->estimate) $total->progress = $total->progress / $total->estimate;

        // 存在特殊情况处理。
        if($isSpecial and !$isClosed)
        {
            $total->finishedBy = $this->app->user->account;
            $total->status     = 'done';
            $total->progress   = '100';
        }
        //迭代十四 20220908 进行中去掉
        /*  else if($isDoing)
        {
            $total->status       = 'doing';
            $total->finishedBy   = '';
            $total->finishedDate = '0000-00-00 00:00:00';
            $total->realDuration = 0;
        }*/
        else if($isDone)
        {
            $total->status   = 'done';
            $total->progress = '100';
            $total->finishedBy = $this->app->user->account;
        }
        else if($isClosed)
        {
            $total->status       = 'closed';
            $total->assignedTo   = 'closed';
            $total->assignedDate = helper::now();
            $total->closedBy     = $this->app->user->account;
            $total->closedDate   = helper::now();
            $total->progress     = '100';
        }
        else if($isWait)
        {
            $total->status       = 'wait';
            $total->canceledBy   = '';
            $total->canceledDate = '0000-00-00 00:00:00';
        }
        else if($isCancel)
        {
            $total->status       = 'cancel';
            $total->finishedBy   = '';
            $total->finishedDate =  '0000-00-00 00:00:00';
            $total->canceledBy   = $this->app->user->account;
            $total->canceledDate = helper::now();

            $total->realDuration = 0;
        }
        else if($isPause)
        {
            $total->status = 'pause';
        }

        $this->dao->update(TABLE_TASK)->data($total)->where('id')->eq($task->parent)->exec();
        $this->computeConsumed($task->parent, $auto);
    }

    public function computeParentExecutionStatus($taskID, $auto = false)
    {
        $task = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();

        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('parent')->eq(0)->andWhere('execution')->eq($task->execution)->andWhere('deleted')->eq(0)->fetchAll();

        //如果没有任务了,重置阶段日期
        if(empty($tasks))
        {
            $initDate = '0000-00-00';
            $initData = array('begin' => $initDate, 'end' => $initDate, 'realBegan' => $initDate, 'realEnd' => $initDate, 'planDuration' => 0, 'status' => 'wait', 'realDuration' => 0, 'planHour' => 0, 'realHour' => 0,'progress' => 0);
            $this->dao->update(TABLE_EXECUTION)->data($initData)->where('id')->eq($task->execution)->exec();
            // 对阶段的父级阶段数据进行汇总更新。
            $this->computeParentExecutionParam($task->execution, $auto);
            return ;
        }

        $total = new stdclass();
        $total->estimate     = 0;
        $total->consumed     = 0;
        $total->left         = 0;
        $total->progress     = 0;
        $total->status       = 'doing';
        $total->estStarted   = '3000-00-00';
        $total->deadline     = '0000-00-00';
        $total->realStarted  = '3000-00-00 00:00:00';
        $total->finishedDate = '0000-00-00 00:00:00';
        # task中realStarted、finishedDate都是datetime

        $isDoing  = false;
        $isDone   = true;
        $isClosed = true;
        $isWait   = true;
        $isPause  = true;
        $isSpecial = true;

        foreach($tasks as $t)
        {
            $total->estimate += $t->estimate;
            $total->consumed += $t->consumed;
            $total->left     += $t->left;
            $total->progress += $t->estimate * $t->progress;

            if($t->status == 'doing')  $isDoing  = true;
            if($t->status != 'done')   $isDone   = false;
            if($t->status != 'closed') $isClosed = false;
            if($t->status != 'wait')   $isWait   = false;
            if($t->status != 'pause')  $isPause  = false;

            if($t->status != 'done' and $t->status != 'closed') $isSpecial = false;
            //and $t->estStarted != '0000-00-00'  为了自动任务新增判断
            if($t->estStarted < $total->estStarted and $t->estStarted != '0000-00-00') $total->estStarted = $t->estStarted;
            if($t->deadline   > $total->deadline)   $total->deadline   = $t->deadline;

            if($t->realStarted < $total->realStarted and $t->realStarted != '0000-00-00 00:00:00')  $total->realStarted = $t->realStarted;
            if($t->finishedDate > $total->finishedDate) $total->finishedDate = $t->finishedDate;
        }
        if($total->estimate) $total->progress = $total->progress / $total->estimate;
        //计划工期
        $total->planDuration = helper::diffDate3($total->deadline,$total->estStarted) ;//(strtotime(substr($total->deadline, 0, 10)) - strtotime(substr($total->estStarted, 0, 10))) / 86400 + 1;
        if($total->realStarted == '3000-00-00 00:00:00') $total->realStarted = '0000-00-00 00:00:00';
        if($total->estStarted == '3000-00-00')           $total->estStarted  = '0000-00-00';

        // 存在特殊情况处理。
        if($isSpecial and !$isClosed)
        {
            $total->status   = 'done';
            $total->progress = '100';
        }
        //迭代十四 20220908 进行中去掉
        /* else if($isDoing)
        {
            $total->status       = 'doing';
            $total->finishedDate = '0000-00-00 00:00:00';
            $total->realDuration = 0;
        }*/
        else if($isDone)
        {
            $total->status = 'done';
            $total->progress = '100';
        }
        else if($isClosed)
        {
            $total->status = 'closed';
        }
        else if($isWait)
        {
            $total->realDuration = 0;
            $total->status       = 'wait';
        }
        else if($isPause)
        {
            $total->realDuration = 0;
            $total->status       = 'suspended';
        }

        // 更新任务直属阶段的[实际开始、实际完成、实际工期、工期偏差]字段。
        $execution = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($task->execution)->andWhere('`type`')->eq('stage')->fetch();

        $total->begin     = $total->estStarted;
        $total->end       = $total->deadline;
        $total->realBegan = substr($total->realStarted, 0, 10);
        $total->realEnd   = substr($total->finishedDate, 0, 10);
        //实际工期
        $total->realDuration = helper::diffDate3($total->realEnd,$total->realBegan) ;
        //迭代十四 20220908去掉以下逻辑
        /*if($execution->begin < $total->begin and $execution->begin != '0000-00-00') $total->begin = $execution->begin;
        if($execution->end   > $total->end)   $total->end   = $execution->end;
        if($execution->realBegan < $total->realBegan and $execution->realBegan != '0000-00-00') $total->realBegan = $execution->realBegan;
        if($execution->realEnd   > $total->realEnd)   $total->realEnd   = $execution->realEnd;*/
        //迭代十四 20220907 子任务时间联动父阶段更新
        //and$total->begin != '0000-00-00'  为了自动任务新增判断
        if($total->begin and $execution->begin != '0000-00-00' and $total->begin != '0000-00-00') $total->begin = $total->begin;
        if($total->end)   $total->end   = $total->end;
        if($total->realBegan and $execution->realBegan != '0000-00-00') $total->realBegan = $total->realBegan;
        if($total->realEnd)   $total->realEnd   = $total->realEnd;
        unset($total->estimate);
        unset($total->consumed);
        unset($total->left);
        unset($total->estStarted);
        unset($total->deadline);
        unset($total->realStarted);
        unset($total->finishedDate);

        // 判断是否要更新阶段的完成者和完成时间。
        if($total->progress >= 100)
        {
            $total->finishBy   = $this->app->user->account;
            $total->finishDate = helper::now();
        }

        // 只要是进行中和未开始就清空。
        //if($total->status == 'doing' or $total->status == 'wait' or $total->status == 'suspended')
        //迭代十四 20220908 进行中doing去掉
        if($total->status == 'wait' or $total->status == 'suspended')
        {
            $total->realDuration = 0;
            $total->finishBy   = '';
            $total->finishDate = '0000-00-00 00:00:00';
            $total->realEnd    = $auto ? $total->realEnd : '0000-00-00';
        }

        if($total->status == 'done' or $total->status == 'closed')
        {
            $total->realDuration = helper::diffDate3($total->realEnd, $total->realBegan);
        }
        $this->dao->update(TABLE_EXECUTION)->data($total)->where('id')->eq($task->execution)->exec();

        // 对阶段的父级阶段数据进行汇总更新。
        $this->computeParentExecutionParam($task->execution, $auto);
    }

    public function computeParentExecutionParam($executionID, $auto = false)
    {
        $execution = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->andWhere('`type`')->eq('stage')->fetch();
        if(empty($execution)) return false;

        // 判断阶段是否为子阶段，如果是，则为父阶段统计数据。
        if($execution->parent)
        {
            // 获取父阶段下面的所有子阶段数据，对父阶段数据进行更新。
            $total               = new stdClass();
            $total->progress     = 0;
            $total->status       = 'doing';
            $total->planDuration = 0;
            $total->realDuration = 0;
            $total->begin        = '3000-00-00';
            $total->end          = '0000-00-00';
            $total->realBegan    = '3000-00-00';
            $total->realEnd      = '0000-00-00';

            // 获取父阶段下面的所有子阶段。
            $childrenExecution = $this->dao->select('*')->from(TABLE_EXECUTION)->where('parent')->eq($execution->parent)->andWhere('`type`')->eq('stage')->andWhere('deleted')->eq('0')->fetchAll();

            $isDoing  = false;
            $isDone   = true;
            $isClosed = true;
            $isWait   = true;
            $isSuspended   = true;
            //特殊情况处理，可关闭也是完成状态
            $isSpecial = true;
            $totalProgress = count($childrenExecution) * 100;

            foreach($childrenExecution as $children)
            {
               /* $total->planDuration += $children->planDuration;
                $total->realDuration += $children->realDuration;*/
                $total->progress     += $children->progress;
                if($children->status == 'doing')  $isDoing  = true;
                if($children->status != 'done')   $isDone   = false;
                if($children->status != 'closed') $isClosed = false;
                if($children->status != 'wait')   $isWait   = false;
                if($children->status != 'suspended')   $isSuspended   = false;
                if($children->status != 'done' and $children->status != 'closed') $isSpecial = false;

                if($children->begin < $total->begin and $children->begin != '0000-00-00') $total->begin = $children->begin;
                if($children->end   > $total->end)   $total->end   = $children->end;
                if($children->realBegan < $total->realBegan and $children->realBegan != '0000-00-00') $total->realBegan = $children->realBegan;
                if($children->realEnd   > $total->realEnd)   $total->realEnd   = $children->realEnd;
            }

            if($total->begin     == '3000-00-00') $total->begin     = '0000-00-00';
            if($total->realBegan == '3000-00-00') $total->realBegan = '0000-00-00';
            if($total->progress) $total->progress = ($total->progress / $totalProgress) * 100;
            //一级计划工期和实际工期不再累加，更新为开始时间、结束时间差

            $total->planDuration =  helper::diffDate3($total->end,$total->begin);
            $total->realDuration =  helper::diffDate3($total->realEnd ,  $total->realBegan);

            //迭代十四 20220908 进行中doing去掉
            /*if($isDoing)
            {
                $total->status       = 'doing';
                $total->realEnd      = '0000-00-00';
                $total->realDuration = 0;
                $total->finishBy     = '';
                $total->finishDate   = '0000-00-00 00:00:00';
            }
            else */
            // 特殊情况处理
            if($isSpecial and !$isClosed)
            {
                $total->status   = 'done';
               // $total->progress = '100';
            }
            else if($isDone)
            {
                $total->status = 'done';
            }
            else if($isClosed)
            {
                $total->status = 'closed';
            }
            else if($isWait)
            {
                $total->finishBy     = '';
                $total->realBegan    = $auto ? $total->realBegan : '0000-00-00';
                $total->realEnd      = $auto ? $total->realEnd : '0000-00-00';
                $total->realDuration = 0;
                $total->status       = 'wait';
            }
            else if($isSuspended)
            {
                $total->status = 'suspended';
            }

            // 判断是否要更新阶段的完成者和完成时间。
            if($total->progress >= 100)
            {
                $total->finishBy   = $this->app->user->account;
                $total->finishDate = helper::now();
            }

            // 只要是进行中和未开始就清空。
            //if($total->status == 'doing' or $total->status == 'wait' or $total->status == 'suspended')
            //迭代十四 20220909 进行中doing去掉
            if($total->status == 'wait' or $total->status == 'suspended')
            {
                $total->realEnd      = $auto ? $total->realEnd : '0000-00-00';
                $total->realDuration = 0;
                $total->finishBy     = '';
            }
            $this->dao->update(TABLE_EXECUTION)->data($total)->where('id')->eq($execution->parent)->exec();
        }
    }

    public function start($taskID)
    {
        $_POST['left'] = $this->post->estimate;
        if(!$this->post->estimate)
        {
            dao::$errors['estimate'] = $this->lang->task->estimateEmpty;
            return false;
        }

        $oldTask = $this->getById($taskID);
        if($oldTask->status == 'doing') dao::$errors[] = $this->lang->task->error->alreadyStarted;
        if(!empty($oldTask->team))
        {
            if($this->post->consumed < $oldTask->team[$this->app->user->account]->consumed) dao::$errors['consumed'] = $this->lang->task->error->consumedSmall;
        }
        else
        {
            if($this->post->consumed < $oldTask->consumed) dao::$errors['consumed'] = $this->lang->task->error->consumedSmall;
        }
        if(dao::isError()) return false;

        $now  = helper::now();
        $task = fixer::input('post')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', $now)
            ->setDefault('status', 'doing')
            ->setDefault('realDuration', '0')
            ->setIF($oldTask->assignedTo != $this->app->user->account, 'assignedDate', $now)
            ->removeIF(!empty($oldTask->team), 'consumed,left')
            ->remove('comment')->get();

        if($this->post->left == 0)
        {
            if(isset($task->consumed) and $task->consumed == 0)
            {
                dao::$errors[] = sprintf($this->lang->error->notempty, $this->lang->task->consumed);
                return false;
            }
            $task->status       = 'done';
            $task->finishedBy   = $this->app->user->account;
            $task->finishedDate = helper::now();
            $task->assignedTo   = $oldTask->openedBy; // Fix bug#1341
        }

        /* Record consumed and left. */
        $estimate = new stdclass();
        $estimate->date     = zget($task, 'realStarted', $now);
        $estimate->task     = $taskID;
        $estimate->consumed = zget($_POST, 'consumed', 0);
        $estimate->left     = zget($_POST, 'left', 0);
        $estimate->work     = zget($task, 'work', '');
        $estimate->account  = $this->app->user->account;
        $estimate->consumed = $estimate->consumed - $oldTask->consumed;
        if($this->post->comment) $estimate->work = $this->post->comment;
        $this->addTaskEstimate($estimate);

        if(!empty($oldTask->team))
        {
            $teams      = array_keys($oldTask->team);
            $assignedTo = empty($oldTask->assignedTo) ? $teams[0] : $oldTask->assignedTo;

            $data = new stdclass();
            $data->consumed = $this->post->consumed;
            $data->left     = $this->post->left;

            $this->dao->update(TABLE_TEAM)->data($data)
                ->where('root')->eq($taskID)
                ->andWhere('type')->eq('task')
                ->andWhere('account')->eq($assignedTo)
                ->exec();

            $task = $this->computeHours4Multiple($oldTask, $task);
        }

        $this->dao->update(TABLE_TASK)->data($task)
            ->autoCheck()
            ->check('consumed,left', 'float')
            ->where('id')->eq((int)$taskID)->exec();

        // 判断某个阶段下第一个开始的任务作为阶段的开始时间。
        $startBy = $this->dao->select('startBy')->from(TABLE_EXECUTION)->where('id')->eq($oldTask->execution)->fetch('startBy');
        if(empty($startBy))
        {
            $execution = new stdClass();
            $execution->startBy   = $task->lastEditedBy;
            $execution->startDate = $task->lastEditedDate;
            $this->dao->update(TABLE_EXECUTION)->data($execution)->where('id')->eq($oldTask->execution)->exec();
        }

        //成方金科逻辑不一样，注释掉
        /*
        if($oldTask->parent > 0)
        {
            $this->updateParentStatus($taskID);
            $this->computeBeginAndEnd($oldTask->parent);
        }
         */

        $this->computeConsumed($taskID);

        if($oldTask->story) $this->loadModel('story')->setStage($oldTask->story);
        if(!dao::isError()) return common::createChanges($oldTask, $task);
    }

    public function finish($taskID)
    {
        $oldTask = $this->getById($taskID);
        $now     = helper::now();
        $today   = helper::today();

        if(strpos($this->config->task->finish->requiredFields, 'comment') !== false and !$this->post->comment)
        {
            dao::$errors[] = sprintf($this->lang->error->notempty, $this->lang->comment);
            return false;
        }

        $task = fixer::input('post')
            ->setIF(is_numeric($this->post->consumed), 'consumed', (float)$this->post->consumed)
            ->setDefault('realStarted', $now)
            ->setDefault('left', 0)
            ->setDefault('progress', 100)
            ->setDefault('assignedTo',   'closed') //按照报工改造，完成即终态。不再将任务指给创建人
            ->setDefault('assignedDate', $now)
            ->setDefault('status', 'done')
            ->setDefault('finishedBy, lastEditedBy', $this->app->user->account)
            ->setDefault('finishedDate, lastEditedDate', $now)
            ->removeIF(!empty($oldTask->team), 'finishedBy,finishedDate,status,left')
            ->remove('comment,files,labels,currentConsumed')
            ->get();

        $currentConsumed = trim($this->post->currentConsumed);
        if(!is_numeric($currentConsumed))
        {
            dao::$errors[] = $this->lang->task->error->consumedNumber;
            return false;
        }

        if(empty($currentConsumed))
        {
            dao::$errors[] = $this->lang->task->error->consumedEmpty;
            return false;
        }

        if(!$this->post->realStarted)
        {
            dao::$errors[] = $this->lang->task->error->realStartedEmpty;
            return false;
        }

        if(!$this->post->finishedDate)
        {
            dao::$errors[] = $this->lang->task->error->finishedDateEmpty;
            return false;
        }

        // 判断日期合理性。
        if(strtotime(substr($_POST['finishedDate'], 0, 10)) < strtotime(substr($task->realStarted, 0, 10)))
        {
            dao::$errors[] = $this->lang->task->error->finishedDateReasonable;
            return false;
        }

        // 判断工期是否超出阈值。  20220615注释对阈值的原判断逻辑
        /*$diffDate = helper::diffDate2($task->realStarted, $_POST['finishedDate'], false);
        $threshold = $diffDate * $this->config->task->workThreshold;

        // 如果是团队任务，阈值乘以人数。
        if(!empty($oldTask->team)) $threshold = $threshold * count($oldTask->team);

        if($oldTask->consumed + $currentConsumed > $threshold)
        {
            dao::$errors[] = sprintf($this->lang->task->error->threshold2Error, $this->config->task->workThreshold);
            return false;
        }*/

        //判断工期是否超出阈值 20220615 更新判断逻辑  例：A点击完成按钮，取实际工期M天，取A在M天已经上报所有项目的累计工作量+当前新报工作量，为Q，Q/M > 阈值，弹提示框
        //以下计算当前操作人在一定时间段内总工作量
        $consumed = $this->dao->select('sum(consumed) as consumed')->from(TABLE_EFFORT)
            ->where('account')->eq($this->app->user->account)
            ->andWhere('objectType')->eq('task')
            ->andWhere('date')->between($task->realStarted,$_POST['finishedDate'])
            ->andWhere('deleted')->eq('0')
            ->fetch('consumed');
        if(empty($consumed)) $consumed = 0;
        //实际工期M天
        $diffDate = helper::diffDate2($task->realStarted, $_POST['finishedDate'], false);
        //阈值
        $threshold = $this->config->task->workThreshold;
        if((($consumed + $currentConsumed)/$diffDate) > $threshold){
            dao::$errors[] = sprintf($this->lang->task->error->threshold2Error, $this->config->task->workThreshold);
            return false;
        }

        /* Record consumed and left. */
        if(empty($oldTask->team))
        {
            $consumed = $task->consumed - $oldTask->consumed;
            if($consumed < 0)
            {
                dao::$errors[] = $this->lang->task->error->consumedSmall;
                return false;
            }
        }
        else
        {
            $consumed = $task->consumed - $oldTask->team[$this->app->user->account]->consumed;
            if($consumed < 0)
            {
                dao::$errors[] = $this->lang->task->error->consumedSmall;
                return false;
            }
        }

        $estimate = new stdclass();
        $estimate->date     = zget($_POST, 'finishedDate', date(DT_DATE1));
        $estimate->task     = $taskID;
        $estimate->left     = 0;
        $estimate->work     = zget($task, 'work', '');
        $estimate->account  = $this->app->user->account;
        $estimate->consumed = $consumed;
        if($this->post->comment) $estimate->work = $this->post->comment;
        if(!empty($oldTask->team))
        {
            foreach($oldTask->team as $teamAccount => $team)
            {
                if($teamAccount == $this->app->user->account) continue;
                $estimate->left += $team->left;
            }
        }
        if($estimate->consumed) $this->addTaskEstimate($estimate);

        if(!empty($oldTask->team))
        {
            $this->dao->update(TABLE_TEAM)->set('left')->eq(0)->set('consumed')->eq($task->consumed)
                ->where('root')->eq((int)$taskID)
                ->andWhere('type')->eq('task')
                ->andWhere('account')->eq($oldTask->assignedTo)->exec();

            $skipMembers = $this->loadModel('execution')->getTeamSkip($oldTask->team, $oldTask->assignedTo, $task->assignedTo);
            foreach($skipMembers as $account => $team) $this->dao->update(TABLE_TEAM)->set('left')->eq(0)->where('root')->eq($taskID)->andWhere('type')->eq('task')->andWhere('account')->eq($account)->exec();

            $task = $this->computeHours4Multiple($oldTask, $task);
        }

        if($_POST['finishedDate'] == substr($now, 0, 10)) $task->finishedDate = $now;

        $this->dao->update(TABLE_TASK)->data($task)
            ->autoCheck()
            ->where('id')->eq((int)$taskID)
            ->exec();

        //成方金科逻辑不一样，注释掉
        //if($oldTask->parent > 0) $this->updateParentStatus($taskID);
        $this->computeConsumed($taskID);

        if($oldTask->story) $this->loadModel('story')->setStage($oldTask->story);
        if($task->status == 'done' && !dao::isError()) $this->loadModel('score')->create('task', 'finish', $taskID);
        if(!dao::isError()) return common::createChanges($oldTask, $task);
    }

    public function update($taskID, $auto = false)
    {
        $oldTask = $this->getByID($taskID);
        if($this->post->estimate < 0 or $this->post->left < 0)
        {
            dao::$errors[] = $this->lang->task->error->recordMinus;
            return false;
        }

        if(!empty($_POST['lastEditedDate']) and $oldTask->lastEditedDate != $this->post->lastEditedDate)
        {
            dao::$errors[] = $this->lang->error->editedByOther;
            return false;
        }

        $now  = helper::now();
        $task = fixer::input('post')
            ->setDefault('story, estimate', 0)
            ->setDefault('estStarted', '0000-00-00')
            ->setDefault('deadline', '0000-00-00')
            ->setIF(is_numeric($this->post->estimate), 'estimate', (float)$this->post->estimate)
            ->setIF(is_numeric($this->post->consumed), 'consumed', (float)$this->post->consumed)
            ->setIF(is_numeric($this->post->left),     'left',     (float)$this->post->left)
            ->setIF($oldTask->parent == 0 && $this->post->parent == '', 'parent', 0)
            ->setIF(strpos($this->config->task->edit->requiredFields, 'estStarted') !== false, 'estStarted', $this->post->estStarted)
            ->setIF(strpos($this->config->task->edit->requiredFields, 'deadline') !== false, 'deadline', $this->post->deadline)
            ->setIF(strpos($this->config->task->edit->requiredFields, 'estimate') !== false, 'estimate', $this->post->estimate)
            ->setIF(strpos($this->config->task->edit->requiredFields, 'left') !== false,     'left',     $this->post->left)
            ->setIF(strpos($this->config->task->edit->requiredFields, 'consumed') !== false, 'consumed', $this->post->consumed)
            ->setIF(strpos($this->config->task->edit->requiredFields, 'story') !== false,    'story',    $this->post->story)
            ->setIF($this->post->story != false and $this->post->story != $oldTask->story, 'storyVersion', $this->loadModel('story')->getVersion($this->post->story))

            ->setIF($this->post->status == 'done', 'left', 0)
            ->setIF($this->post->status == 'done'   and !$this->post->finishedBy,   'finishedBy',   $this->app->user->account)
            ->setIF($this->post->status == 'done'   and !$this->post->finishedDate and !$auto, 'finishedDate', $now)

            ->setIF($this->post->status == 'cancel' and !$this->post->canceledBy,   'canceledBy',   $this->app->user->account)
            ->setIF($this->post->status == 'cancel' and !$this->post->canceledDate, 'canceledDate', $now)
            ->setIF($this->post->status == 'cancel', 'assignedTo',   $oldTask->openedBy)
            ->setIF($this->post->status == 'cancel', 'assignedDate', $now)

            ->setIF($this->post->status == 'closed' and !$this->post->closedBy,     'closedBy',     $this->app->user->account)
            ->setIF($this->post->status == 'closed' and !$this->post->closedDate,   'closedDate',   $now)
            ->setIF($this->post->consumed > 0 and $this->post->left > 0 and $this->post->status == 'wait', 'status', 'doing')

            ->setIF($this->post->assignedTo != $oldTask->assignedTo, 'assignedDate', $now)

            ->setIF($this->post->status == 'wait' and $this->post->left == $oldTask->left and $this->post->consumed == 0 and $this->post->estimate, 'left', $this->post->estimate)
            //->setIF($oldTask->parent > 0 and !$this->post->parent, 'parent', 0)
            //->setIF($oldTask->parent < 0, 'estimate', $oldTask->estimate)
            //->setIF($oldTask->parent < 0, 'left', $oldTask->left)
            ->setDefault('lastEditedBy',   $this->app->user->account)
            ->add('lastEditedDate', $now)
            ->stripTags($this->config->task->editor->edit['id'], $this->config->allowedTags)
            ->cleanINT('execution,story,module')
            ->join('mailto', ',')
            ->join('resourceTo', ',')
            ->remove('comment,files,labels,uid,multiple,team,teamEstimate,teamConsumed,teamLeft,contactListMenu')
            ->get();

        if($oldTask->name != $task->name || $oldTask->estStarted != $task->estStarted || $oldTask->deadline != $task->deadline)
        {
            $task->version = $oldTask->version + 1;
        }
        if($task->consumed < $oldTask->consumed and !$auto) die(js::error($this->lang->task->error->consumedSmall));

        $task = $this->loadModel('file')->processImgURL($task, $this->config->task->editor->edit['id'], $this->post->uid);

        // 多人任务，则阈值 * 人数。
        $teamTotal = 0;
        if($this->post->multiple and count(array_filter($this->post->team)) > 1)
        {
            foreach($this->post->team as $team)
            {
                if(!empty($team))
                {
                    $teamTotal ++;
                }
            }
        }

        // 判断工期是否超出阈值。 20230509暂时将以下逻辑注释
       /* $diffDate = helper::diffDate2($task->estStarted, $task->deadline, false);
        $threshold = $diffDate * $this->config->task->workThreshold;
        if($teamTotal) $threshold = $threshold * $teamTotal;
        if($task->estimate > $threshold)
        {
            dao::$errors['estimate'] = sprintf($this->lang->task->error->thresholdError, $this->config->task->workThreshold);
            return false;
        }*/

        $teams = array();
        if($this->post->multiple)
        {
            //20221028 根据要求将此验证去掉
            /*if(strpos(',done,closed,cancel,', ",{$task->status},") === false && $this->post->assignedTo && !in_array($this->post->assignedTo, $this->post->team))
            {
                dao::$errors[] = $this->lang->task->error->assignedTo;
                return false;
            }*/

            foreach($this->post->team as $row => $account)
            {
                if(empty($account) or isset($team[$account])) continue;

                $member = new stdClass();
                $member->account  = $account;
                $member->role     = $task->assignedTo;
                $member->join     = helper::today();
                $member->root     = $taskID;
                $member->type     = 'task';
                $member->estimate = $this->post->teamEstimate[$row] ? $this->post->teamEstimate[$row] : 0;
                $member->consumed = $this->post->teamConsumed[$row] ? $this->post->teamConsumed[$row] : 0;
                $member->left     = $this->post->teamLeft[$row] === '' ? 0 : $this->post->teamLeft[$row];
                $member->order    = $row;
                $teams[$account]  = $member;
                if($task->status == 'done') $member->left = 0;
            }
        }

        /* Save team. */
        $this->dao->delete()->from(TABLE_TEAM)->where('root')->eq($taskID)->andWhere('type')->eq('task')->exec();
        if(!empty($teams))
        {
            foreach($teams as $member) $this->dao->insert(TABLE_TEAM)->data($member)->autoCheck()->exec();

            /* Assign the left hours to zero who will be skipped. */
            $skipMembers = $this->loadModel('execution')->getTeamSkip($oldTask->team, $oldTask->assignedTo, $task->assignedTo);
            foreach($skipMembers as $account => $team) $this->dao->update(TABLE_TEAM)->set('left')->eq(0)->where('root')->eq($taskID)->andWhere('type')->eq('task')->andWhere('account')->eq($account)->exec();

            $task = $this->computeHours4Multiple($oldTask, $task, array(), $autoStatus = false);
            if($task->status == 'wait')
            {
                reset($teams);
                $task->assignedTo = key($teams);
            }
        }

        $execution  = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($task->execution)->fetch();
        $requiredFields = "," . $this->config->task->edit->requiredFields . ",";

        // 判断是否为运维类阶段。
        if($execution->type == 'ops')
        {
            $requiredFields = str_replace(",story,", ',', "$requiredFields");
            $task->story = 0;
        }

        // 判断是否为子阶段。
        if($execution->parent == '0')
        {
            dao::$errors['execution'] = $this->lang->task->error->subStageError;
            return false;
        }

        if($task->status != 'cancel' and strpos($requiredFields, ',estimate,') !== false)
        {
            if(strlen(trim($task->estimate)) == 0) dao::$errors['estimate'] = sprintf($this->lang->error->notempty, $this->lang->task->estimate);
            $requiredFields = str_replace(',estimate,', ',', $requiredFields);
        }

        $requiredFields = trim($requiredFields, ',');

        // 获取任务的实际消耗工时。
        $task->consumed = round($this->loadModel('effort')->getRealConsumedByTaskID($taskID),1);

        $this->dao->update(TABLE_TASK)->data($task)
            ->autoCheck()
            ->batchCheckIF($task->status != 'cancel', $requiredFields, 'notempty')
            ->checkIF(!helper::isZeroDate($task->deadline), 'deadline', 'ge', $task->estStarted)

            ->checkIF($task->estimate != false, 'estimate', 'float')
            ->checkIF($task->left     != false, 'left',     'float')
            ->checkIF($task->consumed != false, 'consumed', 'float')
            //->checkIF($task->status   != 'wait' and empty($teams) and $task->left == 0 and $task->status != 'cancel' and $task->status != 'closed', 'status', 'equal', 'done')

            ->batchCheckIF(!$auto and ($task->status == 'wait' or $task->status == 'doing'), 'finishedBy, finishedDate,canceledBy, canceledDate, closedBy, closedDate, closedReason', 'empty')

            //->checkIF($task->status == 'done', 'consumed', 'notempty')
            ->checkIF($task->status == 'done' and $task->closedReason, 'closedReason', 'equal', 'done')
            ->batchCheckIF($task->status == 'done', 'canceledBy, canceledDate', 'empty')

            ->batchCheckIF($task->closedReason == 'cancel', 'finishedBy, finishedDate', 'empty')
            ->where('id')->eq((int)$taskID)->exec();

        if(!dao::isError())
        {
        //20221116 判断阶段是否更新,如果更新同步更新工时阶段
            if($oldTask->execution != $task->execution){
                $effortid = $this->dao->select('id')->from(TABLE_EFFORT)->where('objectType')->eq('task')
                      ->andWhere('objectID')->eq($taskID)
                      ->andWhere('project')->eq($oldTask->project)
                      ->andWhere('execution')->eq($oldTask->execution)
                      ->andWhere('deleted')->eq('0')
                      ->fetchAll();
                      if($effortid){
                           $id = array_column($effortid,'id');
                            //更新工时阶段
                            $this->dao->update(TABLE_EFFORT)->set('execution')->eq($task->execution)->where('id')->in($id)->exec();
                         //更新原来剩余阶段的联动
                          $taskid = $this->dao->select('max(id) id')->from(TABLE_TASK)->where('project')->eq($oldTask->project)
                              ->andWhere('execution')->eq($oldTask->execution)
                              ->andWhere('deleted')->eq('0')
                              ->fetch();
                          $this->computeConsumed($taskid->id, $auto);
                     }
            }
            // 判断是否为多人任务，如果是多人任务，则算一下团队参与人员工时信息。
            $teams = $this->dao->select('*')->from(TABLE_TEAM)->where('root')->eq($taskID)->andWhere('type')->eq('task')->orderBy('order')->fetchAll('account');
            if(!empty($teams))
            {
                $this->loadModel('effort');
                foreach($teams as $account => $teamUser)
                {
                    $realData = $this->effort->getRealConsumedAndLeftByTaskID($taskID, $account);
                    $teamUserLeft     = $realData->left;
                    $teamUserConsumed = $realData->consumed;
                    $this->dao->update(TABLE_TEAM)->set('left')->eq($teamUserLeft)->set('consumed')->eq($teamUserConsumed)
                         ->where('root')->eq($taskID)
                         ->andWhere('type')->eq('task')
                         ->andWhere('account')->eq($account)->exec();
                }
            }

            $this->computeConsumed($taskID, $auto);

            /* Mark design version.*/
            if(isset($task->design) && !empty($task->design))
            {
                $design = $this->loadModel('design')->getByID($task->design);
                $this->dao->update(TABLE_TASK)->set('designVersion')->eq($design->version)->where('id')->eq($taskID)->exec();
            }

            /* Record task version. */
            if($task->version > $oldTask->version)
            {
                $taskSpec = new stdClass();
                $taskSpec->task       = $taskID;
                $taskSpec->version    = $task->version;
                $taskSpec->name       = $task->name;
                $taskSpec->estStarted = $task->estStarted;
                $taskSpec->deadline   = $task->deadline;
                $this->dao->insert(TABLE_TASKSPEC)->data($taskSpec)->autoCheck()->exec();
            }

            if($this->post->story != false) $this->loadModel('story')->setStage($this->post->story);
            if($task->status == 'done')   $this->loadModel('score')->create('task', 'finish', $taskID);
            if($task->status == 'closed') $this->loadModel('score')->create('task', 'close', $taskID);

            $this->file->updateObjectID($this->post->uid, $taskID, 'task');

            unset($oldTask->parent);
            unset($task->parent);
            return common::createChanges($oldTask, $task);
        }
    }

    public function close($taskID)
    {
        $oldTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();

        $now  = helper::now();
        $task = fixer::input('post')
            ->setDefault('status', 'closed')
            ->setDefault('assignedTo', 'closed')
            ->setDefault('assignedDate', $now)
            ->setDefault('closedBy, lastEditedBy', $this->app->user->account)
            ->setDefault('closedDate, lastEditedDate', $now)
            ->setIF($oldTask->status == 'done',   'closedReason', 'done')
            ->setIF($oldTask->status == 'cancel', 'closedReason', 'cancel')
            ->remove('_recPerPage')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_TASK)->data($task)->autoCheck()->where('id')->eq((int)$taskID)->exec();

        if(!dao::isError())
        {
            $this->computeConsumed($taskID);

            // if($oldTask->parent > 0) $this->updateParentStatus($taskID);
            if($oldTask->story)  $this->loadModel('story')->setStage($oldTask->story);
            $this->loadModel('score')->create('task', 'close', $taskID);
            return common::createChanges($oldTask, $task);
        }
    }

    public function computeTask($taskID)
    {
        $task = $this->getByID($taskID);
        $left = $task->estimate;

        // 成方金科剩余工时=estimate-consumed
        $efforts = $this->dao->select('*')->from(TABLE_EFFORT)
            ->where('objectType')->eq('task')
            ->andWhere('objectID')->eq($taskID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('date')
            ->fetchAll();

        $consumed = 0;
        foreach($efforts as $effort)
        {
            $left     -= $effort->consumed;
            $consumed += $effort->consumed;
            $this->dao->update(TABLE_EFFORT)->set('left')->eq($left)->where('id')->eq($effort->id)->exec();
        }

        $this->dao->update(TABLE_TASK)->set('consumed')->eq($consumed)->set('left')->eq($left)->where('id')->eq($taskID)->exec();
    }

    public function getById($taskID, $setImgSize = false)
    {
        $task = $this->dao->select('t1.*, t2.id AS storyID, t2.title AS storyTitle, t2.version AS latestStoryVersion, t2.status AS storyStatus, t3.realname AS assignedToRealName')
            ->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')
            ->on('t1.story = t2.id')
            ->leftJoin(TABLE_USER)->alias('t3')
            ->on('t1.assignedTo = t3.account')
            ->where('t1.id')->eq((int)$taskID)
            ->fetch();
        if(!$task) return false;

        $children = $this->dao->select('*')->from(TABLE_TASK)->where('parent')->eq($taskID)->andWhere('deleted')->eq(0)->fetchAll('id');
        $task->children = $children;

        /* Check parent Task. */
        if($task->parent > 0) $task->parentName = $this->getParentTitle($task->parent);

        $task->team = $this->dao->select('*')->from(TABLE_TEAM)->where('root')->eq($taskID)->andWhere('type')->eq('task')->orderBy('order')->fetchAll('account');
        foreach($children as $child) $child->team = array();

        $task = $this->loadModel('file')->replaceImgURL($task, 'desc');
        if($setImgSize) $task->desc = $this->file->setImgSize($task->desc);

        if($task->assignedTo == 'closed') $task->assignedToRealName = 'Closed';
        foreach($task as $key => $value)
        {
            if(strpos($key, 'Date') !== false and !(int)substr($value, 0, 4)) $task->$key = '';
        }
        $task->files = $this->loadModel('file')->getByObject('task', $taskID);

        /* Get related test cases. */
        if($task->story) $task->cases = $this->dao->select('id, title')->from(TABLE_CASE)->where('story')->eq($task->story)->andWhere('storyVersion')->eq($task->storyVersion)->andWhere('deleted')->eq('0')->fetchPairs();

        return $this->processTask($task);
    }

    public function processTask($task)
    {
        $today = helper::today();

        /* Delayed or not?. */
        if($task->status !== 'done' and $task->status !== 'cancel' and $task->status != 'closed')
        {
            if(!helper::isZeroDate($task->deadline))
            {
                $delay = helper::diffDate($today, $task->deadline);
                if($delay > 0) $task->delay = $delay;
            }
        }

        /* Story changed or not. */
        $task->needConfirm = false;
        if(!empty($task->storyStatus) and $task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion) $task->needConfirm = true;

        /* Set product type for task. */
        if(!empty($task->product))
        {
            $product = $this->loadModel('product')->getById($task->product);
            if($product) $task->productType = $product->type;
        }

        /* Set closed realname. */
        if($task->assignedTo == 'closed') $task->assignedToRealName = 'Closed';

        return $task;
    }

    public function getParentTitle($parentID, $parentList = array())
    {
        if($parentID)
        {
            $parentTask = $this->dao->findById($parentID)->from(TABLE_TASK)->fetch();
            $parentList[$parentTask->id] = $parentTask->name;
            if($parentTask->parent)
            {
                return $this->getParentTitle($parentTask->parent, $parentList);
            }
            else
            {
                ksort($parentList);
                return $parentList;
            }
        }
        else
        {
            ksort($parentList);
            return $parentList;
        }
    }

    /**二线工单管理 、二线研发管理 阶段任务自动创建程序入口
     *
     * 检查阶段和任务（二线工单管理 、二线研发管理）
     * @param $projectID 项目id
     * @param $app       应用系统
     * @param $source    来源（问题池 需求池 二线工单）
     * @param $data      数据
     * @param $sourecType 数据类型 只区分问题和其他
     */
    public function checkStageAndTask($projectID, $app,$source,$data,$sourecType = 0, $unbindGd = false)
    {
        if($source == 'project'  and $data->fixType == 'second'){
            //二线实现
            $name = $this->lang->task->stageList['sendyf']; //一级阶段 ：二线研发
            $this->createAndCheckStage($projectID, $app,$source,$data,$name,$sourecType);
        }else if($source =='project'  and $data->fixType == 'project'){
            //项目实现
            $stage = $data->execution; //所属阶段
            $this->createProjectTask($projectID,$stage,$data,$sourecType);
        }elseif($source =='secondorder'  and $data->implementationForm == 'project'){
            $this->createProjectTaskByOrder($projectID, $data->execution, $source, $data, $sourecType);
        }else{
            if($source == 'secondorder'){
                //二线工单
                $name = $this->lang->task->stageList['sendgd']; //一级阶段 ：二线工单
            }elseif($source == 'deptorder'){
                //部门工单
                $name = $this->lang->task->deptgd; //一级阶段 ：部门工单
            }
            //二线工单需根据 项目承担部门 年度 是否二线项目 在年度计划中查出项目id
            $projectID = isset($projectID) ? $projectID : 0 ;
            $this->createAndCheckStage($projectID, $app,$source,$data,$name,$sourecType, $unbindGd = false);
        }
    }

    /**
     * 创建阶段和任务（二线工单管理 、二线研发管理）
     * @param $projectID
     * @param $app
     * @param $data
     * @param $name
     */
    public function createAndCheckStage($projectID, $app,$source,$data,$name,$sourecType, $unbindGd = false){
        //查询一级阶段
        $stageres = $this->dao->select('id,path,parent')->from(TABLE_EXECUTION)->where('project')->eq($projectID)
            ->andWhere('grade')->eq(1)
            ->andWhere('name')->eq($name)
            ->andWhere('type')->eq('stage')
            ->andWhere('deleted')->eq(0)
            ->fetch();
        $stage = $data->execution;//所属阶段
        if(!$stageres){
            //一级阶段不存在 ，创建
            $this->autoCreateStage($projectID,$name,1,0,$app,$source,$data,$sourecType);
        }else{
            //一级阶段存在
            //查询二级阶段
            $apps =  $this->loadModel('application')->getapplicationNameCodePairs();
            $appname = zget($apps,$app,'');
            $stagetwo = $this->dao->select('id,path')->from(TABLE_EXECUTION)->where('project')->eq($projectID)
                ->andWhere('grade')->eq(2)
                ->andWhere('name')->eq($appname)
                ->andWhere('type')->eq('stage')
                ->andWhere('path')->like("$stageres->path%")
                ->andWhere('deleted')->eq(0)
                ->fetch();
            if($stagetwo){
                //查询三级任务
                $products = array('0' => '','99999'=>'无') + $this->loadModel('product')->getPairs();
                $code = $this->dao->select('code')->from(TABLE_PRODUCT)->where('id')->eq($data->product)->fetch('code');
                $productPlan = $source == 'secondorder' || $source == 'deptorder' ? zget($this->lang->$source->typeList,$data->type) : ($data->productPlan == '1' ? $this->lang->task->jobList['scriptkind'] : $code.'_'.zget($products,$data->product,'')); //脚本或产品
                
                $taskthree = $this->dao->select('id,path,execution')->from(TABLE_TASK)->where('project')->eq($projectID)
                    ->andWhere('grade')->eq(1)
                    ->andWhere('name')->eq($productPlan)
                    ->andWhere('execution')->eq($stagetwo->id)
                    ->andWhere('type')->eq('devel')
                    ->andWhere('parent')->eq(0)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                if($source == 'secondorder' || $source == 'deptorder'){ //二线工单或问题工单
                    $code = $data->code;//工单单号
                    $type = $source == 'secondorder'? 'gd' : 'dtgd';
                    if($taskthree){
                        //查询四级
                        $this->updateFourTask($code,$projectID,$data,$taskthree,$taskthree->execution,$type, $unbindGd);//更新四级任务
                    }else{
                        //创建三级
                        $threeName = zget($this->lang->$source->typeList,$data->type);// 任务名称
                        $nextName = $code;//四级任务
                        $executionid = $stagetwo->id;//所属阶段
                        $this->newTaskThreeObject($threeName,$nextName,$executionid,$projectID,$stage,$data,$type,$sourecType);
                    }
                }else if($data->productPlan != '1' and $productPlan){ //问题池、需求池
                        $productversion = $data->productPlan;//产品版本
                        $productplanlist      = array('0' => '') + $this->loadModel('productplan')->getPairs($data->product);
                        $productVersionName = zget($productplanlist,$productversion,'');//产品版本
                    if($taskthree){
                        //查询四级
                        if($sourecType){
                            $this->addFourTask($productVersionName,$projectID,$data,$taskthree,$taskthree->execution,'yf');//更新四级任务
                        }else{
                            $this->updateFourTask($productVersionName,$projectID,$data,$taskthree,$taskthree->execution,'yf');//更新四级任务
                        }
                    }else{
                        //创建三级
                        $products = array('0' => '','99999'=>'无') + $this->loadModel('product')->getPairs();
                        $code = $this->dao->select('code')->from(TABLE_PRODUCT)->where('id')->eq($data->product)->fetch('code');
                        $producename = $code.'_'.zget($products,$data->product,'');//产品编号
                        $executionid = $stagetwo->id;//所属阶段
                        $this->newTaskThreeObject($producename,$productVersionName,$executionid,$projectID,$stage,$data,'yf',$sourecType);
                    }
                }else{
                    if($taskthree){
                        //查询四级
                        if($sourecType){
                           $this->addFourTask($data->code,$projectID,$data,$taskthree,$taskthree->execution,'jb');//更新四级任务
                        }else{
                            $this->updateFourTask($data->code,$projectID,$data,$taskthree,$taskthree->execution,'jb');//更新四级任务
                        }
                    }else {
                        //创建三级
                        $executionid = $stagetwo->id;//所属阶段
                        $this->newTaskThreeObject($productPlan, $data->code, $executionid, $projectID, $stage, $data, 'jb',$sourecType);
                    }
                }
            }else{
                //二级阶段不存在 ，创建
                $this->autoCreateStage($projectID,$appname,2,$stageres,$app,$source,$data,$sourecType);
            }
        }
    }

    /**
     * 创建三四级任务（二线工单-项目实现）
     * @param $projectID
     * @param $stage
     * @param $source
     * @param $data
     * @param $sourecType
     * @return void
     */
    public function createProjectTaskByOrder($projectID,$stage, $source, $data,$sourecType){
        //查询三级任务
        $productPlan = zget($this->lang->$source->typeList,$data->type);
        $taskthree = $this->dao->select('id,path,execution')->from(TABLE_TASK)->where('project')->eq($projectID)
            ->andWhere('grade')->eq(1)
            ->andWhere('name')->eq($productPlan)
            ->andWhere('execution')->eq($stage)
            ->andWhere('type')->eq('devel')
            ->andWhere('parent')->eq(0)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        $code = $data->code;//工单单号
        $type = $source == 'secondorder'? 'gd' : 'dtgd';
        if($taskthree){
            //查询四级
            $this->updateFourTask($code,$projectID,$data,$taskthree,$taskthree->execution,$type);//更新四级任务
        }else{
            //创建三级
            $threeName = zget($this->lang->$source->typeList,$data->type);// 任务名称
            $nextName = $code;//四级任务
            $this->newTaskThreeObject($threeName,$nextName,$stage,$projectID,$stage,$data,$type,$sourecType);
        }
    }
    /**
     * 创建三四级任务
     * @param $projectID
     * @param $stage
     */
    public function createProjectTask($projectID,$stage,$data,$sourecType){
        //查询三级任务
        $products = array('0' => '','99999'=>'无') + $this->loadModel('product')->getPairs();
        $code = $this->dao->select('code')->from(TABLE_PRODUCT)->where('id')->eq($data->product)->fetch('code');
        $product = $code.'_'.zget($products,$data->product,'');//产品编号
        $productName = $data->productPlan == '1' ? $this->lang->task->jobList['scriptkind'] : $product; //脚本或产品
        $taskthree = $this->dao->select('id,path,execution')->from(TABLE_TASK)->where('project')->eq($projectID)
            ->andWhere('grade')->eq(1)
            ->andWhere('name')->eq($productName)
            ->andWhere('execution')->eq($stage)
            ->andWhere('type')->eq('devel')
            ->andWhere('parent')->eq(0)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        if($data->productPlan != '1' and $productName){ //问题池、需求池
            $productversion = $data->productPlan;//产品版本
            $productplanlist      = array('0' => '') + $this->loadModel('productplan')->getPairs($data->product);
            $productVersionName = zget($productplanlist,$productversion,'');//产品版本
            if($taskthree){
                if($sourecType){

                    $this->addFourTask($productVersionName,$projectID,$data,$taskthree,$stage,'yf');//更新四级任务
                }else{
                    $this->updateFourTask($productVersionName,$projectID,$data,$taskthree,$stage,'yf');//更新四级任务
                }
            }else{
                //创建三级
                $this->newTaskThreeObject($product,$productVersionName,$stage,$projectID,$stage,$data,'yf',$sourecType);
            }
        }else{
            if($taskthree){
                //查询四级
                if($sourecType){
                    $this->addFourTask($data->code,$projectID,$data,$taskthree,$taskthree->execution,'jb');//更新四级任务
                }else{
                    $this->updateFourTask($data->code,$projectID,$data,$taskthree,$taskthree->execution,'jb');//更新四级任务
                }
            }else {
                //创建三级
                $this->newTaskThreeObject($productName, $data->code, $stage, $projectID, $stage, $data, 'jb',$sourecType);
            }
        }

    }

    /**
     * 新增三级任务
     * @param $taskname
     * @param $stagename
     * @param $projectID
     * @param $stage
     * @param $data
     */
    public function newTaskThreeObject($threeName,$nextName,$execution,$projectID,$stage,$data,$type,$sourecType){
        unset($_POST);
        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type']      = 'devel';// 任务类型
        $_POST['module']    =  0;//所属模块
        $_POST['assignedTo']     = array('0'=>'');// array(current(explode(',',$data->dealUser)));//array_filter(explode(',',$data->dealUser));//指派给
        $_POST['mailto']    = array('0'=>'');//$data->mailto;
        $_POST['name']      =  $threeName;// 任务名称
        $_POST['estStarted'] = '0000-00-00';//$this->lang->task->begintime;//预计开始
        $_POST['deadline']   = '0000-00-00';//$this->lang->task->endtime; //预计结束
        $_POST['openedBy']   = $this->app->user->account; //由谁创建
        $_POST['openedDate'] = date('Y-m-d'); //创建时间
        $_POST['pri']    = 1;//优先级
        $_POST['status'] = 'wait';//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['design'] = 0;
        $_POST['color'] = '';
        $_POST['dropType']  = '0';

        $taskID = $this->newTask($projectID,$stage,$storyID=0,$moduleID=0, $taskID = 0, $todoID = 0, $type == 'gd');
        $this->dao->update(TABLE_TASK)->set('path')->eq($taskID)->where('id')->eq($taskID)->exec();
        $three = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();
        if($sourecType){
            $this->addFourTask($nextName,$projectID,$data,$three,$execution,$type);//更新四级任务
        }else{
            $this->updateFourTask($nextName,$projectID,$data,$three,$execution,$type);//更新四级任务
        }
    }

    /**
     * 新建四级
     * @param $execution
     * @param $assignedto
     * @param $name
     * @param $data
     * @param $projectID
     * @param $stage
     * @param $flag
     */
    public function newTaskFourObject($execution,$assignedto,$name,$data,$projectID,$taskthree,$stage,$flag, $type,$codes){
        unset($_POST);
        if($flag){
            $_POST['multiple'] = '1'; //多人
            $_POST['assignedTo']      =  array(0=>"");//array(0=>"");//指派给
            $_POST['team']            =  array_unique($assignedto);//指派给
        }else{
            $_POST['assignedTo']      =  array_unique($assignedto);//指派给
        }
        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type']      = 'devel';// 任务类型
        $_POST['module']    =  0;//所属模块
        $_POST['mailto']  = isset($data->mailto) ? array_filter($data->mailto) : '';
        $_POST['name']       = $name;// 任务名称
        $_POST['estStarted'] = '0000-00-00'; //$this->lang->task->begintime;//预计开始
        $_POST['deadline']   = '0000-00-00'; //$this->lang->task->endtime; //预计结束
        $_POST['openedBy']   = $this->app->user->account; //由谁创建
        $_POST['openedDate'] = isset($data->lastDealDate) ? $data->lastDealDate : helper::today(); //创建时间
        $_POST['pri']    = 1;//优先级
        $_POST['status'] = 'wait';//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['productVersion'] = $data->productPlan ?? 0;
        $_POST['parent'] = $taskthree->id;
        $_POST['dropType']  = '0';
        if($type == 'gd' || $type == 'dtgd')
        {
            $_POST['estStarted'] = $data->planstartDate;//预计开始
            $_POST['deadline']   = $data->planoverDate; //预计结束
            $_POST['realStarted'] = $data->startDate ?? '';//实际开始
            $_POST['finishedDate']   = $data->overDate ?? ''; //实际结束
            $_POST['type']      = 'affair';// 任务类型
            if(empty($data->startDate) && empty($data->overDate)) {
                $_POST['status'] = 'wait';//状态
            }
            if(!empty($data->startDate)){
                $_POST['status'] = 'doing';//状态
            }
            if(!empty($data->overDate)){
                $_POST['status'] = 'doing';//状态
            }
        }
        $taskID = $this->newTask($projectID,$stage,$storyID=0,$moduleID=0, $taskID = 0, $todoID = 0, $type == 'gd');
        if(($type == 'jb' || $type == 'gd' || $type == 'yf' || $type == 'dtgd') && $taskID){
           $id = array_filter(explode(',',$codes->id));
           $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('`execution`')->eq($execution)->where('id')->in($id)->exec();
           $flag = array_filter(explode(',',$codes->type));
           if(current($flag) == 'problem'){
            $this->dao->update(TABLE_PROBLEM)->set('`execution`')->eq($execution)->where('id')->in($data->id)->exec();
           }
           if(current($flag) == 'demand'){
            $this->dao->update(TABLE_DEMAND)->set('`execution`')->eq($execution)->where('id')->in($data->id)->exec();
           }
        }
        return $taskID;
    }
    /**
     * 编辑任务
     * @param $assignto
     * @param $name
     * @param $stage
     * @param $taskfour
     * @param $id
     * @param $flag
     */
    public function editTaskObject($assignto,$name,$stage,$taskfour,$id,$flag, $type, $data, $edit = false){
        unset($_POST);
        $oldTask = $this->getByID($id);
        if($flag){
            $_POST['multiple'] = '1'; //多人
            $_POST['assignedTo']      =  '';//$data->dealUser;//指派给
            $_POST['team']            = array_unique( $assignto);//指派给
        }else{
            $_POST['assignedTo']      =  $data->dealUser;//指派给
        }
        $_POST['name'] = $name;// 任务名称
        $_POST['source'] = '1'; //数据来源
        $_POST['estStarted'] = $taskfour->estStarted;//$this->lang->task->begintime;//预计开始
        $_POST['deadline']   = $taskfour->deadline;//$this->lang->task->endtime; //预计结束
        $_POST['execution']   = $stage;
        $_POST['status']   = $taskfour->status;
        $_POST['left']     =  round($taskfour->left,1);
        $_POST['closedReason'] = '';
        $_POST['version'] = $taskfour->version;
        $_POST['lastEditedBy'] = $this->app->user->account;
        $_POST['mailto']  = isset($data->mailto) ? array_filter($data->mailto) : '';
        $_POST['lastEditedDate'] =  $taskfour->lastEditedDate == '0000-00-00 00:00:00' ? '' : $taskfour->lastEditedDate;
        $_POST['dropType']   = $taskfour->dropType;
        $_POST['estimate'] = round($taskfour->estimate,1) ;
        if($type == 'gd' || $type == 'dtgd')
        {
            $_POST['estStarted'] = $edit || strtotime($oldTask->estStarted) < strtotime($data->planstartDate) ? $oldTask->estStarted : $data->planstartDate;//预计开始
            $_POST['deadline']   = $edit  || strtotime($oldTask->deadline) > strtotime($data->planoverDate)? $oldTask->deadline : $data->planoverDate; //预计结束
//            $_POST['realStarted'] = $edit ? $oldTask->realStarted : $data->startDate;//实际开始
//            $_POST['finishedDate']   = $edit ? $oldTask->finishedDate : $data->overDate; //实际结束
            $_POST['realStarted'] =  !empty($data->startDate)  ?  $oldTask->realStarted != '0000-00-00 00:00:00' && (strtotime($oldTask->realStarted) < strtotime($data->startDate))   ? $oldTask->realStarted : $data->startDate : $oldTask->realStarted;//实际开始
            $_POST['finishedDate']   = !empty($data->overDate)  ? strtotime($oldTask->finishedDate) > strtotime($data->overDate)  ? $oldTask->finishedDate : $data->overDate : $oldTask->finishedDate; //实际结束
            if(($_POST['realStarted'] == '0000-00-00 00:00:00' && $_POST['finishedDate']  == '0000-00-00 00:00:00') || (empty($_POST['realStarted'])  &&  empty($_POST['finishedDate'])) ) {
                $_POST['status'] = 'wait';//状态
            }
            if($_POST['realStarted'] != '0000-00-00 00:00:00' && !empty($_POST['realStarted'])){
                $_POST['status'] = 'doing';//状态
            }
            if($_POST['finishedDate'] != '0000-00-00 00:00:00' && !empty($_POST['finishedDate']) && $taskfour->status != 'done'){
                $_POST['status'] = 'doing';//状态
            }
            $_POST['consumed'] = 0;
        }
         if(($type == 'gd' && $id) || ($type == 'dtgd' && $id)){
            $taskproblemid = array_filter(explode(',',$taskfour->tid));
            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('`execution`')->eq($stage)->where('id')->in($taskproblemid)->exec();
            unset($taskfour->tid);
         }
         if(isset($taskfour->tid)) unset($taskfour->tid);
        $this->editTask($id);
    }
    /**
     * 解绑任务
     * @param $taskname
     * @param $data
     */
    public function unBindTask($projectID,$stage,$taskname,$data,$type,$version,$taskthree, $unbindGd = false){
        //查询四级任务是否已经存在
        //所有条件都符合
         $update = false;
         $taskone = $this->dao->select('id')->from(TABLE_TASK)
             ->where('grade')->eq(2)
             ->beginIF($type != 'gd' && $type != 'dtgd')->andWhere('name')->like("%$data->code%")->fi()
             ->beginIF($type == 'gd' || $type == 'dtgd')->andWhere('name')->eq('['.$data->code.']')->fi()
             /*->beginIF($type == 'gd')->andWhere('type')->eq('affair')->fi()
             ->beginIF($type != 'gd')->andWhere('type')->eq('devel')->fi()*/
             ->beginIF($type != 'gd' && $type != 'dtgd')->andWhere("((execution = '$stage' and path like '$taskthree->path%' and project = '$projectID'))")->fi()
             ->beginIF($type == 'gd' || $type == 'dtgd')->andWhere("(execution = $stage and parent = $taskthree->id and project = '$projectID')")->fi()
             ->andWhere('deleted')->eq(0)
             ->andWhere('dropType')->eq(0)
             ->fetch();
       //  $taskoneid = array_column($taskone,'id');
         //任意符合一个条件的，不包含所有符合的
         $task = $this->dao->select('id,name,productVersion,status,version,lastEditedDate,assignedTo,execution,status,version,lastEditedDate,dropType,estStarted,deadline,`left`,estimate')->from(TABLE_TASK)
             ->where('grade')->eq(2)
             ->beginIF($type != 'gd' && $type != 'dtgd')->andWhere('name')->like("%$data->code%")->fi()
             ->beginIF($type == 'gd' || $type == 'dtgd')->andWhere('name')->eq('['.$data->code.']')->fi()
             /*->beginIF($type == 'gd')->andWhere('type')->eq('affair')->fi()
             ->beginIF($type != 'gd')->andWhere('type')->eq('devel')->fi()*/
             ->beginIF($type != 'gd' && $type != 'dtgd')->andWhere("((execution = '$stage' or path like '$taskthree->path%' or project = '$projectID') or (execution != '$stage' and path not like '$taskthree->path%' and project != '$projectID'))")->fi()
             ->beginIF($type == 'gd' || $type == 'dtgd')->andWhere("(execution != $stage or parent != $taskthree->id or project != '$projectID')")->fi()
             //->beginIF($type != 'gd')->andWhere("((execution = '$stage' or path like '$taskthree->path%' or project = '$projectID') )")->fi()
             ->andWhere('deleted')->eq(0)
             ->andWhere('dropType')->eq(0)
            // ->beginIF($taskoneid && $type != 'gd') ->andWhere('id')->notin($taskoneid)->fi()
             ->fetchAll();
             if(count($task) == 1 && isset($taskone->id) && $taskone->id == $task[0]->id && ($type != 'gd' or $type != 'dtgd' or $unbindGd)){
                 $update = $unbindGd ? false : true;
                 $task = $task;
             }else if(count($task) > 1 && $taskone){
                 foreach ($task as $key => $it) {
                     if($it->id == $taskone->id){
                         unset($task[$key]);
                     }
                }
             }
        $taskfour = new stdClass();
        if($task){
            foreach ($task as $item) {
                $taskfour->status  = $item->status;
                $taskfour->version = $item->version;
                $taskfour->lastEditedDate =  $item->lastEditedDate;
                $taskfour->estStarted =  $item->estStarted;
                $taskfour->deadline =  $item->deadline;
                $taskfour->left =  $item->left;
                $taskfour->estimate = $item->estimate;
                if(strpos($item->name,'V') !== false || strpos($item->name,'.') !== false){
                    if($update && substr_count($item->name,'CFIT') == '1' && strpos($item->name,$taskname) !== false){
                          $name =  $item->name;
                          $taskfour->dropType = '0';
                    }else{
                       $nowname =  trim(str_replace(',,',',',str_replace('[,','[',str_replace(',]',']',str_replace($data->code,'',$item->name)))),',');
                       $name =  $nowname == $taskname ? $taskname.'[废弃]' : $nowname;
                       $assigned = $this->dao->select('assignTo')->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->eq($item->id)->andWhere('deleted')->eq('0')->andWhere('code')->ne($data->code)->fetchAll();
                       $item->assignedTo = array_column($assigned,'assignTo');
                       if(strpos($name,'[]') !== false ){
                          $name = str_replace('[]','[废弃]',$name);
                          $taskfour->dropType = '1';
                       }else{
                          $taskfour->dropType = '0';
                       }
                   }
                }else{
                    if($update){
                       $name =  $item->name;
                       $taskfour->dropType = '0';
                       $taskproblemid = $this->dao->select('id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->eq($item->id)->andWhere('deleted')->eq('0')->andWhere('code')->eq($data->code)->fetch();
                       $taskfour->tid = $taskproblemid->id;
                    }else{
                       $name =  $item->name.'[废弃]';
                       $taskfour->dropType = '1';
                    }

                }
               // $nowname = trim(str_replace(',]',']',str_replace($data->code,'',$item->name)),',');
               // $name =  $type == 'gd' ? $item->name : $nowname == $taskname ? $taskname.'[废弃]' : $nowname;
                //查询是否团队
                $team = $this->dao->select("*")->from(TABLE_TEAM)->where('root')->eq($item->id)->andWhere('type')->eq('task')->fetchAll();
                $assignedTo = is_array($item->assignedTo) ? $item->assignedTo : array($item->assignedTo);
                if(count($team) > 0){
                  $this->editTaskObject($assignedTo,$name,$item->execution,$taskfour,$item->id,1, $type, $data);
                 }else{
                   $this->editTaskObject($assignedTo,$name,$item->execution,$taskfour,$item->id,0, $type, $data, true);
                 }
              //  $this->editTaskObject($item->assignedTo,$name,$item->execution,$taskfour,$item->id,0, $type, $data);
            }
            if($type == 'gd' || $type == 'dtgd')
            {
                $code = $this->dao->select('code,assignto,id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                    ->andWhere('application')->eq($data->app)
                    ->andWhere('deleted')->eq(0)
                    ->orderBy('id_desc')
                    ->fetch();
            }else {
                $code = $this->dao->select('group_concat(code) code,group_concat(assignTo) assignto,group_concat(id) id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                    ->beginIf($type != 'gd')->andWhere('product')->eq($data->product)->fi()
                    ->andWhere('application')->eq($data->app)
                    ->beginIf($type != 'gd' && $type != 'jb')->andWhere('version')->eq($version)->fi()
                    ->beginIf($data->execution != '')->andWhere('execution')->eq($stage)->fi()
                    ->beginIf( $type == 'jb')->andWhere('code')->eq($data->code)->fi()
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
            }
            $nowid = explode(',',$code->id);
            //查询关联表
            $codes = $this->dao->select('code,assignto,id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('code')->eq($data->code)
                ->andWhere('deleted')->eq(0)
                ->orderBy('id_desc')
                ->fetchAll();
            if($codes){
                $id = array_column($codes,'id');
                $id = array_diff($id,$nowid);
                if($id){
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq(1)->where('id')->in($id)->andWhere('taskid')->ne('')->exec();
                }
            }
        }
    }

    /**
     * 创建更新四级任务
     * @param $taskname
     * @param $projectID
     * @param $data
     * @param $taskthree
     * @param $stage
     * @param $type
     */
    public function updateFourTask($taskname,$projectID,$data,$taskthree,$stage,$type, $unbindGd = false){
        $version =  $type == 'yf' ? $data->productPlan : '';//产品版本
        //20221108 任务只能在一个项目阶段下,如已存在需解绑
        $this->unBindTask($projectID,$stage,$taskname,$data,$type,$version,$taskthree, $unbindGd);
        if($unbindGd) return;
        //查询四级
        $taskfour = $this->dao->select('id,name,productVersion,status,version,lastEditedDate,dropType,estStarted,deadline,`left`,estimate')->from(TABLE_TASK)->where('project')->eq($projectID)
            ->andWhere('grade')->eq(2)
            ->beginIf($type == 'jb')->andWhere('name')->eq("[$data->code]")->fi()
            ->beginIf($type == 'gd')->andWhere('name')->eq("[$data->code]")->fi()
            ->beginIf($type == 'dtgd')->andWhere('name')->eq("[$data->code]")->fi()
            ->beginIf($type == 'yf')->andWhere('name')->like("$taskname%")->fi()
            ->andWhere('execution')->eq($stage)
           /* ->beginIF($type == 'gd')->andWhere('type')->eq('affair')->fi()
            ->beginIF($type != 'gd')->andWhere('type')->eq('devel')->fi()*/
            ->andWhere('path')->like("$taskthree->path%")
            ->andWhere('deleted')->eq(0)
            ->andWhere('dropType')->eq(0)
            ->fetch();
        //查询任务-问题-需求 关联表
        $codes = $this->dao->select('group_concat(code  order by id asc) code,group_concat(assignTo  order by id asc) assignto,group_concat(id  order by id asc) id,group_concat(type  order by id asc) type')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
            ->beginIf($type != 'gd' && $type != 'dtgd')->andWhere('product')->eq($data->product)->fi()
            ->beginIf(count(array_filter(explode(',',$data->app))) >1)->andWhere('application')->in($data->app)->fi()
            ->beginIf(count(array_filter(explode(',',$data->app))) == 1)->andWhere('application')->eq($data->app)->fi()
            ->beginIf($type != 'gd' && $type != 'jb' && $type != 'dtgd')->andWhere('version')->eq($version)->fi()
            ->beginIf(!empty($data->execution))->andWhere('execution')->eq($stage)->fi()
            ->beginIf( $type == 'jb' || $type == 'gd' || $type == 'dtgd')->andWhere('code')->eq($data->code)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetch();
        unset($_POST);
        $productplanlist      = isset($data->product) ? array('0' => '') + $this->loadModel('productplan')->getPairs($data->product) : array();
        //是否多人任务
        $assignto = isset($codes->assignto) ? array_filter(explode(',',$codes->assignto)) : array();
        if($taskfour){
            //查询库中已存在的数据名字是否包含本次版本
            if(strpos($taskfour->name,$taskname) !== false && strpos($taskfour->name, $codes->code) === false){
                //存在，是否需要更新
                $taskname = $taskfour->name;
                //更新
                //查询任务-问题-需求 关联表
                $historycode = $this->dao->select('group_concat(code  order by id asc) code,group_concat(assignTo  order by id asc) assignto,group_concat(id  order by id asc) id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                    ->andWhere('product')->eq($data->product)
                    ->beginIf($type != 'yf')->andWhere('application')->eq($data->app)->fi()
                    ->andWhere('version')->eq($taskfour->productVersion)
                    ->andWhere('execution')->eq($stage)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                $taskID = $taskfour->id;
                $nowcode = trim($historycode->code ,',');
                if($assignto && count($assignto) > 1) {
                    //多人任务
                    $name = $data->productPlan == '1' ? $codes->code : zget($productplanlist,$data->productPlan,'') . '[' . $nowcode . ']';// 任务名称
                    $this->editTaskObject($assignto,$name,$stage,$taskfour,$taskID,1, $type, $data);
                }else{
                    //单人任务
                    $name  = $data->productPlan == '1' ? $codes->code : zget($productplanlist,$data->productPlan,'')  . '[' . $nowcode . ']';// 任务名称
                    $this->editTaskObject($assignto,$name,$stage,$taskfour,$taskID,0, $type, $data);
                }
                //更新 任务-问题-需求 关联表的taskid
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('taskid')->eq($taskID)->where('id')->in($codes->id)->exec();
            }else{
                //不包含 删除关联关系
                //更新 任务-问题-需求 关联表的taskid
                //查询任务-问题-需求 关联表
                if($type == 'gd' || $type == 'dtgd')
                {
                    $code = $this->dao->select('code,assignto,id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                        ->andWhere('application')->eq($data->app)
                        ->andWhere('deleted')->eq(0)
                        ->orderBy('id_desc')
                        ->fetch();
                }else {
                    $code = $this->dao->select('group_concat(code  order by id asc) code,group_concat(assignTo  order by id asc) assignto,group_concat(id  order by id asc) id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                        ->andWhere('product')->eq($data->product)
                        ->andWhere('application')->eq($data->app)
                        ->andWhere('version')->eq($taskfour->productVersion)
                        ->andWhere('execution')->eq($stage)
                        ->andWhere('deleted')->eq(0)
                        ->groupBy('taskid')
                        ->fetch();
                }
                $verName = zget($productplanlist,$taskfour->productVersion,'');//产品版本
                if($code->code){
                    $updaname = $type == 'gd' || $type == 'dtgd' ? '['.$code->code.']' : ($verName ? $verName.'['.$code->code.']' : '['.$code->code.']' );
                    $taskID = $taskfour->id;
                    $taskfour->tid = $codes->id;
                    $this->editTaskObject($assignto,$updaname,$stage,$taskfour,$taskID,0, $type, $data);

                }else{
                    //没数据
                    $noname = $verName.'[废弃]';
                    $this->dao->update(TABLE_TASK)->set('name')->eq($noname)->where('id')->eq($taskfour->id)->exec();
                    $this->deleteTaskEstimate($taskfour->id); //删除工时
                    //创建四级
                    $name = $type == 'gd' || $type == 'dtgd' ? '['.$taskname.']' : ($taskname.'['.$codes->code.']');//产品编号
                    if($assignto && count($assignto) > 1){
                        //多人任务
                        $fourname = $data->productPlan == '1' ?  '['.$codes->code.']' : $name;// 任务名称
                        $taskID =  $this->newTaskFourObject($stage,$assignto,$fourname,$data,$projectID,$taskthree,$stage,1, $type,$codes);
                    }else{
                        //单人任务
                        $fourname = $data->productPlan == '1' ?  '['.$codes->code.']' : $name;// 任务名称
                        $taskID = $this->newTaskFourObject($stage,$assignto,$fourname,$data,$projectID,$taskthree,$stage,0, $type,$codes);
                    }
                }
            }
        }else{
            //创建四级
            $name = $type == 'gd' || $type == 'dtgd' ? '['.$taskname.']' : ($taskname.'['.trim($codes->code,',').']');//产品编号
            if($assignto && count($assignto) > 1){
                //多人任务
                $fourname = $data->productPlan == '1' ?  '['.$codes->code.']' : $name;// 任务名称
                $taskID = $this->newTaskFourObject($stage,$assignto,$fourname,$data,$projectID,$taskthree,$stage,1, $type,$codes);
            }else{
                //单人任务
                $fourname = isset($data->productPlan) && $data->productPlan == '1' ?  '['.$codes->code.']' : $name;// 任务名称
                $taskID = $this->newTaskFourObject($stage,$assignto,$fourname,$data,$projectID,$taskthree,$stage,0, $type,$codes);
            }
        }
        //更新 任务-问题-需求 关联表的taskid
        $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('taskid')->eq($taskID)->where('id')->in($codes->id)->exec();
    }

    /**
     * 创建问题单四级任务
     * @param $taskname
     * @param $projectID
     * @param $data
     * @param $taskthree
     * @param $stage
     * @param $type
     */
    public function addFourTask($taskname,$projectID,$data,$taskthree,$stage,$type){
        $version =  $type == 'yf' ? $data->productPlan : '';//产品版本
        //查询四级

        $taskfour = $this->dao->select('id,name,productVersion,status,version,lastEditedDate,dropType,estStarted,deadline,`left`,estimate')->from(TABLE_TASK)->where('project')->eq($projectID)
            ->andWhere('grade')->eq(2)
            ->beginIf($type == 'jb')->andWhere('name')->eq("[$data->code]")->fi()
            ->beginIf($type == 'gd')->andWhere('name')->eq("[$data->code]")->fi()
            ->beginIf($type == 'dtgd')->andWhere('name')->eq("[$data->code]")->fi()
            ->beginIf($type == 'yf')->andWhere('name')->like("$taskname%")->fi()
            ->andWhere('execution')->eq($stage)
            ->andWhere('path')->like("$taskthree->path%")
            ->andWhere('deleted')->eq(0)
            ->andWhere('dropType')->eq(0)
            ->fetch();

        //查询任务-问题-需求 关联表
        $codes = $this->dao->select('group_concat(code  order by id asc) code,group_concat(assignTo  order by id asc) assignto,group_concat(id  order by id asc) id,group_concat(type  order by id asc) type')
            ->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
            ->beginIf($type != 'gd' && $type != 'dtgd')->andWhere('product')->eq($data->product)->fi()
            ->beginIf(count(array_filter(explode(',',$data->app))) >1)->andWhere('application')->in($data->app)->fi()
            ->beginIf(count(array_filter(explode(',',$data->app))) == 1)->andWhere('application')->eq($data->app)->fi()
            ->beginIf($type != 'gd' && $type != 'jb' && $type != 'dtgd')->andWhere('version')->eq($version)->fi()
            ->beginIf($data->execution != '')->andWhere('execution')->eq($stage)->fi()
            ->beginIf( $type == 'jb' || $type == 'gd' || $type == 'dtgd')->andWhere('code')->eq($data->code)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetch();

        unset($_POST);
        $productplanlist      = isset($data->product) ? array('0' => '') + $this->loadModel('productplan')->getPairs($data->product) : array();
        //是否多人任务
        $assignto = isset($codes->assignto) ? array_filter(explode(',',$codes->assignto)) : array();

        if($taskfour){
            //查询库中已存在的数据名字是否包含本次版本
            if(strpos($taskfour->name,$taskname) !== false && strpos($taskfour->name, $codes->code) === false){
                //存在，是否需要更新
                $taskname = $taskfour->name;
                //更新
                //查询任务-问题-需求 关联表
                $historycode = $this->dao->select('group_concat(code  order by id asc) code,group_concat(assignTo  order by id asc) assignto,group_concat(id  order by id asc) id')->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                    ->andWhere('product')->eq($data->product)
                    ->beginIf($type != 'yf')->andWhere('application')->eq($data->app)->fi()
                    ->andWhere('version')->eq($taskfour->productVersion)
                    ->andWhere('execution')->eq($stage)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                $taskID = $taskfour->id;
                $nowcode = trim($historycode->code ,',');
                if($assignto && count($assignto) > 1) {
                    //多人任务
                    $name = $data->productPlan == '1' ? $codes->code : zget($productplanlist,$data->productPlan,'') . '[' . $nowcode . ']';// 任务名称
                    $this->editTaskObject($assignto,$name,$stage,$taskfour,$taskID,1, $type, $data);
                }else{
                    //单人任务
                    $name  = $data->productPlan == '1' ? $codes->code : zget($productplanlist,$data->productPlan,'')  . '[' . $nowcode . ']';// 任务名称
                    $this->editTaskObject($assignto,$name,$stage,$taskfour,$taskID,0, $type, $data);
                }
                //更新 任务-问题-需求 关联表的taskid
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('taskid')->eq($taskID)->where('id')->in($codes->id)->exec();
            }
        }else{
            //创建四级
            $name =  $taskname.'['.trim($codes->code,',').']';//产品编号
            if($assignto && count($assignto) > 1){
                //多人任务
                $fourname = $data->productPlan == '1' ?  '['.$codes->code.']' : $name;// 任务名称
                $taskID = $this->newTaskFourObject($stage,$assignto,$fourname,$data,$projectID,$taskthree,$stage,1, $type,$codes);
            }else{
                //单人任务
                $fourname = isset($data->productPlan) && $data->productPlan == '1' ?  '['.$codes->code.']' : $name;// 任务名称
                $taskID = $this->newTaskFourObject($stage,$assignto,$fourname,$data,$projectID,$taskthree,$stage,0, $type,$codes);
            }
        }
        //更新 任务-问题-需求 关联表的taskid
        $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('taskid')->eq($taskID)->where('id')->in($codes->id)->exec();
    }
    /**
     * 创建一二级阶段（二线工单管理 、二线研发管理）
     * @param $projectID
     * @param $name
     * @param $grade
     * @param $stageres
     */
    public function autoCreateStage($projectID,$name,$grade,$stageres,$app,$source,$data,$sourecType){
        $execution = new stdClass();
        $execution->project      = $projectID;
        $execution->parent       = $stageres != '0' ? $stageres->id : $stageres;
        $execution->name         = $name;
        $execution->type         = 'stage';
        $execution->resource     = '';
        $execution->begin        = $this->lang->task->begintime ;
        $execution->end          = $this->lang->task->endtime ;
        $execution->planDuration = helper::diffDate3($execution->end , $execution->begin);
        $execution->grade        = $grade;
        $execution->openedBy     = 'admin';
        $execution->openedDate   = helper::today();
        $execution->status       = 'wait';
        $execution->milestone    = 0;
        $execution->version      = 1;
        $execution->source       = 1;
        if($source == 'secondorder' || $source == 'deptorder')
        {
            $execution->realBegan = $_POST['startDate'] ;
            $execution->realEnd = $_POST['overDate'] ;
        }
        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
        $executionID = $this->dao->lastInsertID();

        //记录到版本库
        $spec               = new stdclass();
        $spec->execution    = $executionID;
        $spec->version      = 1;
        $spec->name         = $execution->name;
        $spec->milestone    = $execution->milestone;
        $spec->begin        = $execution->begin;
        $spec->end          = $execution->end;
        $spec->planDuration = $execution->planDuration;
        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

        $path = ($grade == 1 ? ',' . $projectID . ',' : ($stageres != '0' ? $stageres->path : '')) . $executionID . ',';
        $order = $executionID * 5;
        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();
        $this->checkStageAndTask($projectID, $app,$source,$data,$sourecType);

    }

    /**
     * 创建三四级任务（二线工单管理 、二线研发管理）
     * @param $projectID
     * @param $name
     * @param $grade
     * @param $data
     */
    public function createTask($projectID,$name,$grade,$data,$type,$execution){
        $task = new stdClass();
        $task->project      = $projectID;
        $task->parent       = is_array($data) ? $data->parent : $data;
        $task->execution    = $execution ;
        $task->name         = $name;
        $task->grade        = $grade;
        $task->type         = $type;
        $task->resource     = '';
        $task->estStarted   = $this->lang->task->begintime ;;
        $task->deadline     = $this->lang->task->endtime ;
        $task->estimate     = 0;
        $task->planDuration =  helper::diffDate3($task->deadline  ,$task->estStarted);
        $task->left         = 0;
        $task->openedBy     = $this->app->user->account;
        $task->openedDate   = helper::today();
        $this->dao->insert(TABLE_TASK)->data($task)->exec();

        $taskID = $this->dao->lastInsertID();
        $path =  $task->parent == '0' ?  $taskID.',' : $task->parent .','.$taskID;

        $this->dao->update(TABLE_TASK)->set('path')->eq($path)->where('id')->eq($taskID)->exec();
        return $taskID;
    }

    /**
     * 获取关联表 关联项
     * @param $taskid
     */
    public function getTaskDemandProblem($taskid){
        //查询任务-问题-需求 关联表
        $codes = $this->dao->select('GROUP_CONCAT(typeid) typeid ,`type`')->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->eq($taskid)
          //  ->andWhere('deleted')->eq(0)
            ->groupBy('`type`')
            ->fetchAll('type');
        $res = array('demand' => 0,'problem' => 0,'second' => 0,'deptorder' => 0,'demandinside' => 0,);
        if(isset($codes['demand'])){
            $demand =  $this->dao->select('id,(case when status ="deleted"  then concat(code,"(已刪除)") else code end)code,reason,solution')->from(TABLE_DEMAND)->where('id')->in($codes['demand']->typeid)
                ->fetchAll();
            $res['demand'] = $demand;
        }
        if(isset($codes['problem'])){
            $problem =  $this->dao->select('id,(case when status ="deleted"  then concat(code,"(已刪除)") else code end)code,`desc`,solution')->from(TABLE_PROBLEM)->where('id')->in($codes['problem']->typeid)
                ->fetchAll();
            $res['problem'] = $problem;
        }
        if(isset($codes['secondorder'])){
            $second =  $this->dao->select('id,(case when deleted ="1"  then concat(code,"(已刪除)") else code end)code,summary,`type`')->from(TABLE_SECONDORDER)->where('id')->in($codes['secondorder']->typeid)
                ->fetchAll();
            $res['second'] = $second;
        }
        if(isset($codes['deptorder'])){
            $dept =  $this->dao->select('id,(case when deleted ="1"  then concat(code,"(已刪除)") else code end)code,summary,`type`')->from(TABLE_DEPTORDER)->where('id')->in($codes['deptorder']->typeid)
                ->fetchAll();
            $res['deptorder'] = $dept;
        }
        if(isset($codes['demandinside'])){
            $dept =  $this->dao->select('id,(case when status ="deleted"  then concat(code,"(已刪除)") else code end)code,reason,solution')->from(TABLE_DEMAND)->where('id')->in($codes['demandinside']->typeid)
                ->fetchAll();
            $res['demandinside'] = $dept;
        }
        return $res;
    }

    /**
     * 新建任务
     * @param $project
     * @param int $executionID
     * @param int $storyID
     * @param int $moduleID
     * @param int $taskID
     * @param int $todoID
     * @return int
     */
    public function newTask($project,$executionID = 0, $storyID = 0, $moduleID = 0, $taskID = 0, $todoID = 0, $auto = false){

            $this->loadModel('task');
            if(!empty($_POST)) {
                if ($this->post->execution) $executionID = (int)$this->post->execution;
                $tasksID = $this->task->create2($executionID, $auto);
                if(!$auto) $this->task->computeConsumed($tasksID['id']);
                /* Create actions. */
                $this->loadModel('action');
                foreach ($tasksID as $taskID) {
                    /* if status is exists then this task has exists not new create. */
                    if(isset($taskID['id'])){
                        $taskID = $taskID['id'] ;
                        //$this->action->create('task', $taskID, 'Opened', '');
                        $this->action->create('task', $taskID, 'Opened', '','',isset($_POST['openedBy']) ? $_POST['openedBy'] : $this->app->user->account);
                    }
                }
                if ($todoID > 0) {
                    $this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq($todoID)->exec();
                    $this->action->create('todo', $todoID, 'finished', '', "TASK:$taskID");
                }
                $this->executeHooks($taskID);
                return isset($tasksID) ? $tasksID['id'] : 0;
            }
    }


    /**
     * @Notes: 迭代三十需求条目去掉下一节点处理人后任务的创建人、指派给
     * 创建人取成方金科、指派给取需求条目的研发责任人 历史记录同步修改 主要用于区分历史记录
     * @Date: 2023/9/8
     * @Time: 14:35
     * @Interface newTaskByDemand
     * @param $project
     * @param int $executionID
     * @param int $storyID
     * @param int $moduleID
     * @param int $taskID
     * @param int $todoID
     * @param false $auto
     * @return int
     */
    public function newTaskByDemand($project,$executionID = 0, $storyID = 0, $moduleID = 0, $taskID = 0, $todoID = 0, $auto = false){

        $this->loadModel('task');
        if(!empty($_POST)) {
            if ($this->post->execution) $executionID = (int)$this->post->execution;
            $tasksID = $this->task->create2($executionID, $auto);
            if(!$auto) $this->task->computeConsumed($tasksID['id']);
            /* Create actions. */
            $this->loadModel('action');
            foreach ($tasksID as $taskID) {
                /* if status is exists then this task has exists not new create. */
                if(isset($taskID['id'])){
                    $taskID = $taskID['id'] ;
                    $this->action->create('task', $taskID, 'Opened', '','','guestjk');
                }
            }
            if ($todoID > 0) {
                $this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq($todoID)->exec();
                $this->action->create('todo', $todoID, 'finished', '', "TASK:$taskID");
            }
            $this->executeHooks($taskID);
            return isset($tasksID) ? $tasksID['id'] : 0;
        }
    }

    /**
     * 编辑任务
     * @param $taskID
     * @param bool $comment
     */
    public function editTask($taskID,$comment = false){
        $this->loadModel('task');
        if(!empty($_POST))
        {
            $this->loadModel('action');
            $changes = array();
            $files   = array();
            if($comment == false)
            {
                $changes = $this->task->update2($taskID, true);;
                if(dao::isError()) die(js::error(dao::getError()));
                $files = $this->loadModel('file')->saveUpload('task', $taskID);
            }
            //$task = $this->task->getById($taskID);
            if($this->post->comment != '' or !empty($changes) or !empty($files))
            {
                $action = (!empty($changes) or !empty($files)) ? 'Edited' : 'Commented';
                $fileAction = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
                $actionID = $this->action->create('task', $taskID, $action, $fileAction . $this->post->comment);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }
            $this->executeHooks($taskID);
        }
    }

    /**
     * 制版删除同步删除工时
     * @param $execution
     * @param $taskid
     */
    public function deleteTaskEstimate($taskid){
        $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('objectType')->eq('task')
            ->andWhere('objectID')->eq("$taskid")
            ->andWhere('source')->eq(1)
            ->exec();
        $this->loadModel('action')->create('task', $taskid, 'deleteestimate', '任务无关联单,同步删除工时');
        $this->loadModel('task')->computeConsumed($taskid);
    }

    /**
     * 问题池、需求池、二线工单删除联动任务名称更新
     * @param $data
     * @param $code
     */
    public function  deleteCodeUpdateTask($taskall,$code){

        //查询关联关系
       /* $build_task = $this->dao->select('*')->from(TABLE_TASK_DEMAND_PROBLEM)
            ->where('deleted')->eq('0')
            ->beginIF($flag)->andWhere('project')->eq((int)$projectID)->fi()
            ->beginIF($version != '1')->andWhere('application')->eq((int)$app)->fi()
            ->beginIF($flag)->andWhere('product')->eq((int)$productID)->fi()
            ->beginIF($flag)->andWhere('version')->eq((int)$version)->fi()
            ->andWhere('code')->eq($code)
            ->andWhere('typeid')->eq((int)$id)
            ->fetchAll();
        if($build_task){*/
       $users      = $this->loadModel('user')->getPairs('noletter|noclosed');
            $taskid = $taskall->id;
            $tasks = $this->dao->select('name,id,lastEditedDate')->from(TABLE_TASK)->where('id')->in($taskid)->fetchAll();
            foreach ( $tasks as $key=>$task) {
                $data = new stdClass();
                $newname = str_replace(',,',',',str_replace(',]',']',str_replace('[,','[',str_replace($code,'',$task->name))));
                $lastEditedBy = $this->app->user->account;
                $lastEditedDate=  $task->lastEditedDate == '0000-00-00 00:00:00' ? '' : $task->lastEditedDate;
                $data->name = $newname;
                $data->lastEditedBy = $lastEditedBy;
                $data->lastEditedDate = $lastEditedDate;
                if(strpos($newname,'[]') !== false ){
                    $newname = str_replace('[]','[废弃]',$newname);
                    $data->name = $newname;
                    $data->dropType = 1;
                }
                $this->dao->update(TABLE_TASK)->data($data)->where('id')->in($task->id)->exec();
                $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq(1)->where('id')->in($taskall->typeid)->exec();
                $this->loadModel('action')->create('task', $task->id, 'Edited', '由'.zget($users,$this->app->user->account,'').'删除 '.'['.$code.']');
            }
        }
   // }

    /**
     * 问题、需求、工单分析 对应项目自动生成任务
     * @param $projectID
     * @param $sourceType
     * @param $app
     */
    public function assignedAutoCreateStageTask($projectID,$sourceType,$app,$code,$data){

        $this->app->loadLang('deptorder');
        $this->app->loadLang('secondorder');
        $apps =  $this->loadModel('application')->getapplicationNameCodePairs();
        if($this->lang->task->sourceType[$sourceType] == 'dept'){
            //部门工单
            if(strpos($code,'CFIT-Q') !== false){
                //问题 或需求
                $onestage =  $this->lang->task->stageList['deptDevelopmentProblem'] ;
            }else if((strpos($code,'CFIT-D') !== false ||  strpos($code,'CFIT-WD') !== false) && strpos($code,'CFIT-DT') === false){
                //外部需求、内部需求 不能三部门工单
                $onestage =  $this->lang->task->stageList['deptDevelopmentDemand'];
            }else{
                //部门
                $onestage =   $this->lang->task->stageList['deptDevelopmentDept'] ;
            }
           // $onestage =  $this->lang->task->stageList['deptDevelopment'];//部门研发管理
            $code = strpos($code,$this->lang->task->deptname[$sourceType]) !== false  ? $code : $this->lang->task->deptname[$sourceType] ."_".$code ."_".zget($this->lang->deptorder->typeList,$data->type);
            $app = zget($apps,$app);
            $this->assignedcheckCreateStage($projectID,$onestage,$app,$code,$sourceType,$data);
        }/*elseif($this->lang->task->sourceType[$sourceType] == 'second'){

            //二线工单
            if(strpos($code,'CFIT-Q') !== false){
                //问题 或需求
                $onestage =  $this->lang->task->stageList['secondDevelopmentProblem'] ;
            }else if(strpos($code,'CFIT-D') !== false){
                $onestage =   $this->lang->task->stageList['secondDevelopmentDemand'];
            }else{
                //二线
                $onestage =  $this->lang->task->stageList['secondWorkOrderSecond'];
            }

           //$onestage =  $data->fixType == 'project' ? $this->lang->task->stageList['secondActivity'] :$this->lang->task->stageList['secondWorkOrder'] ;//二线研发活动 或二线工单管理
            $code = strpos($code,$this->lang->task->deptname[$sourceType]) !== false  ? $code : $this->lang->task->deptname[$sourceType] ."_".$code."_".zget($this->lang->seondorder->typeList,$data->type) ;
            $app = zget($apps,$app);
            $this->assignedcheckCreateStage($projectID,$onestage,$app,$code,$sourceType,$data);
        }*/elseif($this->lang->task->sourceType[$sourceType] == 'demand' ||$this->lang->task->sourceType[$sourceType] == 'problem' ||$this->lang->task->sourceType[$sourceType] == 'demandinside' || $this->lang->task->sourceType[$sourceType] == 'second'){
            if($data->fixType == 'project'){
                if($this->lang->task->sourceType[$sourceType] != 'second'){
                    //问题 或需求
                    $onestage = $this->lang->task->sourceType[$sourceType] == 'problem' ? $this->lang->task->stageList['projectDevelopmentProblem'] : $this->lang->task->stageList['projectDevelopmentDemand'];
                }else{
                    $onestage =  $this->lang->task->stageList['projectDevelopmentSecond'];
                }
            }else {
                //二线
                $onestage = $this->lang->task->sourceType[$sourceType] == 'problem' ? $this->lang->task->stageList['secondDevelopmentProblem'] : ($this->lang->task->sourceType[$sourceType] == 'second' ? $this->lang->task->stageList['secondWorkOrderSecond'] : $this->lang->task->stageList['secondDevelopmentDemand']);
                //$onestage =  $this->lang->task->stageList['projectDevelopmentSecond'];
            }
           // $onestage =  $data->fixType == 'project' ? $this->lang->task->stageList['projectDevelopment'] :$this->lang->task->stageList['secondDevelopment'] ;//部门研发活动 或二线研发管理
            if($this->lang->task->sourceType[$sourceType] != 'second'){
                $code = strpos($code,$this->lang->task->deptname[$sourceType]) !== false  ? $code : $this->lang->task->deptname[$sourceType] ."_".$code ;
            }else{
                $code = strpos($code,$this->lang->task->deptname[$sourceType]) !== false  ? $code : $this->lang->task->deptname[$sourceType] ."_".$code."_".zget($this->lang->secondorder->typeList,$data->type) ;
            }
            $app = zget($apps,$app);
            $this->assignedcheckCreateStage($projectID,$onestage,$app,$code,$sourceType,$data);

        }else if($this->lang->task->sourceType[$sourceType] == 'localesupport' ){
            //现场支持
            $onestage = $this->lang->task->stageList['secondLocaleSupport'];
            //现场支持的app 应用系统 默认取单子的开始时间的年份;
            $depts = $this->loadModel('dept')->getDeptPairs();
            $code =  $this->lang->task->deptname[$sourceType] ."_".$code ."_".zget($depts,$data->deptId);
            $this->assignedcheckCreateStage($projectID,$onestage,$app,$code,$sourceType,$data);
        }
    }

    /**
     * 查询二线是否存在一级阶段
     * @param $projectID
     * @param $name
     */
    public function assignedcheckCreateStage($projectID,$name,$app,$code,$sourceType,$data){
        $flag = false;
        $projectplan = $this->dao->select("mark,name,secondLine,begin,end,code")->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->andWhere('deleted')->eq(0)->fetch();
        //查询一级阶段
        $field = "id,path,parent";
        $stageres = $this->getSelectTable(TABLE_EXECUTION,$field,$projectID,1,$name);
        //查询二级阶段
        $stagetwo = $this->getSelectTable(TABLE_EXECUTION,$field,$projectID,2,$app,null,$stageres->path);

        if($stagetwo){
            //二级阶段存在 查询三级任务
            $fieldTask = 'id,path,execution';
            //三级任务命名规则：1、产品编号_产品名称_版本号（有产品有版本）  2、产品编号_产品名称（有产品无版本） 3、系统英文_系统中文（无产品无版本）
            if($this->lang->task->sourceType[$sourceType] == 'dept'){
                //部门工单
                $taskName = $code;
            }else if($this->lang->task->sourceType[$sourceType] == 'localesupport'){
                //现场支持 三级任务 取 支持属性
                $this->app->loadLang('localesupport');
                $taskName = zget($this->lang->localesupport->stypeList,$data->stype);
            }else{
                //三级任务名按照不同规则生成
                $productList = $this->loadModel('product')->getCodeNamePairs();//产品
                $product = strip_tags(zget($productList,$data->product,''));
                $productversion = $data->productPlan;//产品版本
                $productplanlist      = array('0' => '') + $this->loadModel('productplan')->getPairs($data->product);
                $productVersionName = zget($productplanlist,$productversion,'');//产品版本
                if($data->product != '99999' && $productversion != '1'){
                    $taskName  = $product.'_'.$productVersionName;
                }elseif($data->product != '99999' && $productversion == '1'){
                    $taskName  = $product;
                }elseif($data->product == '99999' && $productversion == '1'){
                    $taskName  = $app;
                }
            }
            $taskthree = $this->getSelectTable(TABLE_TASK,$fieldTask,$projectID,1,htmlspecialchars($taskName),$stagetwo->id,null);
            if($taskthree){
                //针对部门工单 三级检测是否已存在
                if($this->lang->task->sourceType[$sourceType] == 'dept'){
                    $this->checkCodeExist($projectID,$app,$sourceType,$code,$data);
                }else{
                    //存在 查询四级任务
                    if(!in_array($this->lang->task->sourceType[$sourceType],array('problem','localesupport'))  ){
                        //检测本单号任务是否存在，并根据规则重命名
                      $flag =  $this->checkCodeExist($projectID,$app,$sourceType,$code,$data);
                    }
                    if(!$flag){
                        $taskfour = $this->getSelectTable(TABLE_TASK,$fieldTask,$projectID,2,$code,$taskthree->id,$taskthree->path);
                        if(!$taskfour){
                            //四级不存在 创建
                            $this->assignedNewTaskFourObject($taskthree->execution,$data->dealUser,$code,$data,$projectID,$taskthree,$sourceType);
                        }
                    }
                }
            }else{
                //针对部门工单 三级检测是否已存在
                if($this->lang->task->sourceType[$sourceType] == 'dept'){
                    $flag =  $this->checkCodeExist($projectID,$app,$sourceType,$code,$data);
                }
                //三级不存在 创建
                if(!$flag) {
                    $this->assignedAutoCreateTaskThreeObject($taskName, $stagetwo->id, $projectID, $projectplan, $sourceType, $data);
                }
            }
        }else{
            // 二级阶段不存在
            $this->assignedAutoCreateSecondStage($projectID,$app,2,$stageres,$projectplan->name,$projectplan->mark,$projectplan,$sourceType,$app,$code,$data);
        }
    }

    /**
     * 检测任务状态，重新对任务命名
     * @param $projectID
     * @param $app
     * @param $sourceType
     * @param $code
     * @param $data
     * @param null $del
     * @return bool
     */
    public function checkCodeExist($projectID, $app, $sourceType, $code, $data, $del = null){
        $flag = false;//区分是编辑还是 编辑后再新增
        //查询包含单号的任务，不包含任务名中有 已删除、已不纳入本项目 已不属于本系统的
        $taskones = $this->dao->select('t1.*')->from(TABLE_TASK)->alias('t1')
            ->beginIF($sourceType == 'deptorder')->where('t1.grade')->eq(1)->fi()
            ->beginIF($sourceType != 'deptorder')->where('t1.grade')->eq(2)->fi()
            ->andWhere('t1.name')->like("%$data->code%")
            ->andWhere('t1.name')->notLike('%已%')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.dropType')->eq(0)
            ->andWhere('t1.dataVersion')->eq(2)
            ->fetchAll();
        $apps =  array_flip($this->loadModel('application')->getapplicationNameCodePairs());
        if($taskones){
            foreach ($taskones as $taskone) {
                $execution = $this->dao->select("*")->from(TABLE_EXECUTION)->where('id')->eq($taskone->execution)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                $oldapp = zget($apps,$execution->name,'');//旧应用系统
                $taskProduct = $this->dao->select("name")->from(TABLE_TASK)->where('id')->eq($taskone->parent)
                    ->andWhere('deleted')->eq(0)
                    ->fetch();
                $code = strpos($code,$this->lang->task->deptname[$sourceType] ) !== false ? $code : $this->lang->task->deptname[$sourceType] ."_".$code ;
                $appName = zget(array_flip($apps),$app);

                //项目 或二线
                $productList = $this->loadModel('product')->getCodeNamePairs();//产品
                $product = strip_tags(zget($productList,$data->product,''));
                $productversion = $data->productPlan;//产品版本
                $productplanlist      = array('0' => '') + $this->loadModel('productplan')->getPairs($data->product);
                $productVersionName = zget($productplanlist,$productversion,'');//产品版本
                if($data->product != '99999' && $productversion != '1'){
                    $taskName  = $product.'_'.$productVersionName;
                }elseif($data->product != '99999' && $productversion == '1'){
                    $taskName  = $product;
                }elseif($data->product == '99999' && $productversion == '1'){
                    $taskName  = $appName;
                }
                if($del){
                    $name = $taskone->name."(".$this->lang->task->taskdelete.")";
                    $this->assignededitTaskObject($taskone->assignedTo, $name, $taskone->execution, $taskone, $taskone->id, 0, $this->lang->task->sourceType[$sourceType] , $data);
                    $id = $this->dao->select("id")->from(TABLE_TASK_DEMAND_PROBLEM)->where('taskid')->eq($taskone->id)->fetch();
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('deleted')->eq('1')->where('id')->eq($id->id)->exec();
                }else{
                    //单号一致 项目不一致 更新任务名，新增 不纳入本项目后缀
                    if($taskone->project != $projectID){
                        $name = $code."(".$this->lang->task->taskNoProject.")";
                        $this->assignededitTaskObject($taskone->assignedTo, $name, $taskone->execution, $taskone, $taskone->id, 0, $this->lang->task->sourceType[$sourceType] , $data);
                    }elseif($oldapp != $data->app){
                        //单号一致 系统不一致 更新任务名，新增 已不属于本系统
                        $name = $code."(".$this->lang->task->taskNoApp .")";
                        $this->assignededitTaskObject($taskone->assignedTo, $name, $taskone->execution, $taskone, $taskone->id, 0, $this->lang->task->sourceType[$sourceType] , $data);
                    }else {
                        if($this->lang->task->sourceType[$sourceType] == 'dept'){
                            $this->assignededitTaskObject($taskone->assignedTo, $code, $taskone->execution, $taskone, $taskone->id, 0, $this->lang->task->sourceType[$sourceType] , $data);
                        }else if($taskName != $taskProduct->name){
                            $name = $code."(".$this->lang->task->taskNoProduct .")";
                            $this->assignededitTaskObject($taskone->assignedTo, $name, $taskone->execution, $taskone, $taskone->id, 0, $this->lang->task->sourceType[$sourceType] , $data);
                        }else{
                            $flag = true;
                            $this->assignededitTaskObject($taskone->assignedTo, $code, $taskone->execution, $taskone, $taskone->id, 0, $this->lang->task->sourceType[$sourceType] , $data);
                        }
                    }
                }
            }
        }
        return $flag;
    }

    /**
     * 编辑任务
     * @param $assignto
     * @param $name
     * @param $stage
     * @param $taskfour
     * @param $id
     * @param $flag
     */
    public function assignededitTaskObject($assignto,$name,$stage,$taskfour,$id,$flag, $type, $data){
        unset($_POST);
        $oldTask = $this->getByID($id);
        if($flag){
            $_POST['multiple'] = '1'; //多人
            $_POST['assignedTo']      =  '';//$data->dealUser;//指派给
            $_POST['team']            = array_unique( $assignto);//指派给
        }else{
            $_POST['assignedTo']      = strpos($name,$this->lang->task->taskDrop) !== false ? '' : $data->dealUser;//指派给 已删除、已不是本系统、不是本项目处理人置空
        }
        $_POST['name'] = $name;// 任务名称
        $_POST['source'] = '1'; //数据来源
        $_POST['estStarted'] = $taskfour->estStarted;//预计开始
        $_POST['deadline']   = $taskfour->deadline; //预计结束
        $_POST['execution']   = $stage;
        $_POST['status']   = strpos($name,$this->lang->task->taskDrop) !== false ? 'closed' : $taskfour->status; //已删除、已不是本系统、不是本项目状态关闭
        $_POST['left']     =  round($taskfour->left,1);
        $_POST['closedReason'] = '';
        $_POST['version'] = $taskfour->version;
        $_POST['lastEditedBy'] = $this->app->user->account;
        $_POST['mailto']  = isset($data->mailto) ? array_filter($data->mailto) : '';
        $_POST['lastEditedDate'] =  $taskfour->lastEditedDate == '0000-00-00 00:00:00' ? '' : $taskfour->lastEditedDate;
        $_POST['dropType']   = $taskfour->dropType;
        $_POST['estimate'] = round($taskfour->estimate,1) ;
        $_POST['consumed'] = $taskfour->consumed;

        if($type == 'dept' || $type == 'second')
        {
            $_POST['estStarted'] = $data->planstartDate;//预计开始
            $_POST['deadline']   = $data->planoverDate; //预计结束
            $_POST['realStarted'] =  !empty($data->startDate)  ?  $data->startDate : '';//实际开始
            $_POST['finishedDate']   = !empty($data->overDate)  ?  $data->overDate : ''; //实际结束
            $_POST['type']      = 'affair';// 任务类型
        }else if($type == 'problem'){
            $_POST['deadline']    = ($data->createdBy == 'guestjx' || $data->createdBy == 'guestcn') ? $data->PlannedDateOfChange : $data->PlannedTimeOfChange; //预计结束
        }else if($type == 'demandinside' || $type == 'demand' ){
            $_POST['deadline']   = $data->end; //预计结束
        }
        $_POST['deadline'] = empty( $_POST['deadline']) ? '0000-00-00' : $_POST['deadline'];
        if($taskfour->deadline != $_POST['deadline']){
            $_POST['estStarted'] = '0000-00-00';
        }
        //查询任务-问题-需求 关联表
        $codes = $this->dao->select(' code, assignto, id, type')
            ->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($taskfour->project)
            ->andWhere('application')->eq($data->app)
            ->andWhere('code')->eq($data->code)
            ->andWhere('deleted')->eq(0)
            ->beginIF($type != 'dept')->andWhere('product')->eq($data->product)->andWhere('version')->eq($data->productPlan)->fi()
            ->fetch();
        $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('`taskid`')->eq($id)->set('`execution`')->eq($stage)->where('id')->in($codes->id)->exec();
        $this->editTask($id);
    }

    /**
     * 创建一级阶段（二线管理 、部门管理）
     * @param $projectID
     * @param $name
     */
    public function assignedAutoCreateSecondStage($projectID,$name,$grade,$stageres,$projectPlanName,$mark,$projectplan,$sourceType,$app,$code,$data){
        $execution = new stdClass();
        $execution->project      = $projectID;
        $execution->parent       = $stageres != '0' ? $stageres->id : $stageres;  ;
        $execution->name         = $name;
        $execution->type         = 'stage';
        $execution->resource     = '';
        $execution->begin        = $projectplan->begin ;
        $execution->end          = $projectplan->end ;
        $execution->planDuration = helper::diffDate3($execution->end , $execution->begin);
        $execution->grade        = $grade;
        $execution->openedBy     = 'admin';
        $execution->openedDate   = helper::today();
        $execution->status       = 'wait';
        $execution->milestone    = 0;
        $execution->version      = 1;
        $execution->source       = 1;
        $execution->dataVersion  = 2;//为了将历史数据隔离
        $execution->isLocaleSupport = $sourceType == 'localesupport' ? 2 : 1;
        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
        $executionID = $this->dao->lastInsertID();

        //记录到版本库
        $spec               = new stdclass();
        $spec->execution    = $executionID;
        $spec->version      = 1;
        $spec->name         = $execution->name;
        $spec->milestone    = $execution->milestone;
        $spec->begin        = $execution->begin;
        $spec->end          = $execution->end;
        $spec->planDuration = $execution->planDuration;
        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

        $path =   ($grade == 1 ? ',' . $projectID . ',' : ($stageres != '0' ? $stageres->path : '')) . $executionID . ',';
        $order = $executionID * 5;
        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();
        $this->loadModel('action')->create('newexecution', $executionID, 'created',sprintf($this->lang->task->projectPlanName,$projectPlanName,$mark ),sprintf($this->lang->task->projectPlanName,$projectPlanName,$mark ));

        $this->assignedAutoCreateStageTask($projectID,$sourceType,$app,$code,$data);
    }

    /**
     * 新增三级任务
     * @param $taskname
     * @param $stagename
     * @param $projectID
     * @param $stage
     * @param $data
     */
    public function assignedAutoCreateTaskThreeObject($threeName,$execution,$projectID,$projectplan,$sourceType,$data)
    {
        unset($_POST);

        $type = $this->lang->task->sourceType[$sourceType];
        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type'] = 'devel';// 任务类型
        $_POST['module'] = 0;//所属模块
        $_POST['assignedTo'] = $type == 'dept' ? array($data->dealUser) : array('0' => '');//指派给
        $_POST['mailto'] = array('0' => '');
        $_POST['name'] = $threeName;// 任务名称
        $_POST['estStarted'] =  $projectplan->begin; //预计开始
        $_POST['deadline'] = $projectplan->end; //预计结束
        $_POST['openedBy'] = $type == 'localesupport' ? $data->createdBy :$this->app->user->account; //由谁创建
        $_POST['openedDate'] = date('Y-m-d'); //创建时间
        $_POST['pri'] = 1;//优先级
        $_POST['status'] = $type == 'localesupport' ? 'closed' : ($type == 'dept' || $type == 'second' ? 'doing' : 'wait');//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['design'] = 0;
        $_POST['color'] = '';
        $_POST['dropType'] = '0';
        $_POST['dataVersion'] = 2;//为了将历史数据隔离
        $_POST['taskType'] =  $sourceType == 'localesupport' ? 2 : 0;

        if($type == 'dept')
        {
            $_POST['estStarted'] = $data->planstartDate;//预计开始
            $_POST['deadline']   = $data->planoverDate; //预计结束
            $_POST['realStarted'] =  !empty($data->startDate)  ?  $data->startDate : '';//实际开始
            $_POST['finishedDate']   = !empty($data->overDate)  ?  $data->overDate : ''; //实际结束

        }
        if($type == 'localesupport')
        {
            $_POST['estStarted'] = $data->startDate;//预计开始
            $_POST['deadline']   = $data->endDate; //预计结束
            $_POST['realStarted'] =  !empty($data->startDate)  ?  $data->startDate : '';//实际开始
            $_POST['finishedDate']   = !empty($data->endDate)  ?  $data->endDate : ''; //实际结束

        }

        $taskID = $this->newTask($projectID, $execution, $storyID = 0, $moduleID = 0, $taskID = 0, $todoID = 0);
        $this->dao->update(TABLE_TASK)->set('path')->eq($taskID)->where('id')->eq($taskID)->exec();
        $this->computeConsumed($taskID);
        //部门管理项目特殊，三级任务 就是具体的单号
        if($type == 'dept'){
            //查询任务-问题-需求 关联表
            $codes = $this->dao->select(' code, assignto, id, type')
                ->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                ->andWhere('application')->eq($data->app)
                ->andWhere('code')->eq($data->code)
                ->andWhere('deleted')->eq(0)
                ->fetch();

            //更新 任务-问题-需求 关联表的taskid
            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('taskid')->eq($taskID)->set('`execution`')->eq($execution)->where('id')->eq($codes->id)->exec();
           // $this->dao->update(TABLE_DEPTORDER)->set('`execution`')->eq($execution)->where("id =(select id from (select id from zt_deptorder where code = '$codes->id')t1 )")->exec();

        }else {
           // $three = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();
           // $this->assignedNewTaskFourObject($execution, $data->dealUser, $data->code, $data, $projectID, $three, $this->lang->task->sourceType[$sourceType]);
            $this->assignedAutoCreateStageTask($projectID,$sourceType,$data->app,$data->code,$data);
        }

    }

    /**
     * 新建四级
     * @param $execution
     * @param $assignedto
     * @param $name
     * @param $data
     * @param $projectID
     * @param $stage
     * @param $flag
     */
    public function assignedNewTaskFourObject($execution,$assignedto,$name,$data,$projectID,$taskthree,$sourceType){
        unset($_POST);
        $type = $this->lang->task->sourceType[$sourceType];
        $_POST['assignedTo']      =  is_array($assignedto) ? array_unique($assignedto) : array_unique(array($assignedto));//指派给

        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type']      = 'devel';// 任务类型
        $_POST['module']    =  0;//所属模块
        $_POST['mailto']  = isset($data->mailto) ? array_filter($data->mailto) : '';
        $_POST['name']       = $name;// 任务名称
        $_POST['estStarted'] = '0000-00-00'; //$this->lang->task->begintime;//预计开始
        $_POST['deadline']   = '0000-00-00'; //$this->lang->task->endtime; //预计结束
        $_POST['openedBy']   = $type == 'localesupport' ? $data->createdBy : $this->app->user->account; //由谁创建
        $_POST['openedDate'] = isset($data->lastDealDate) ? $data->lastDealDate : helper::today(); //创建时间
        $_POST['pri']    = 1;//优先级
        $_POST['status'] = $type == 'localesupport' ? 'closed' : ($type == 'dept' || $type == 'second' ? 'doing' : 'wait');//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['productVersion'] = $data->productPlan ?? 0;
        $_POST['parent'] = $taskthree->id;
        $_POST['dropType']  = '0';
        $_POST['dataVersion'] = 2;//为了将历史数据隔离
        $_POST['taskType'] =  $sourceType == 'localesupport' ? 2 : 0;
        if($type == 'dept' || $type == 'second')
        {
            $_POST['estStarted'] = $data->planstartDate;//预计开始
            $_POST['deadline']   = $data->planoverDate; //预计结束
            $_POST['realStarted'] =  !empty($data->startDate)  ?  $data->startDate : '';//实际开始
            $_POST['finishedDate']   = !empty($data->overDate)  ?  $data->overDate : ''; //实际结束
            $_POST['type']      = 'affair';// 任务类型
        }else if($type == 'problem'){
            $_POST['deadline']    = ($data->createdBy == 'guestjx' || $data->createdBy == 'guestcn') ? $data->PlannedDateOfChange : $data->PlannedTimeOfChange; //预计结束
        }else if($type == 'demandinside'){
            $_POST['deadline']   = $data->end; //预计结束
        }else if($type == 'demand' ){
            $_POST['deadline']   = $data->end; //预计结束
            //迭代三十 创建人取成方金科、指派给取需求条目的研发责任人 只加外部
            $_POST['openedBy'] = 'guestjk';
        }else if($type == 'localesupport'){
            $_POST['estStarted'] = $data->startDate;//预计开始
            $_POST['deadline']   = $data->endDate; //预计结束
            $_POST['realStarted'] =  !empty($data->startDate)  ?  $data->startDate : '';//实际开始
            $_POST['finishedDate']   = !empty($data->endDate)  ?  $data->endDate : ''; //实际结束
        }
        $_POST['deadline'] = empty( $_POST['deadline']) ? '0000-00-00' : $_POST['deadline'];
        /*
         * 迭代三十：需求条目去掉下一节点处理人后：任务的创建人、指派给创建人取成方金科、指派给取需求条目的研发责任人 且历史记录需要同步为成方金科创建
         * 注：由于newTask被多种情况调用,故单区分需求条目生成任务时新增相同方法newTaskByDemand 唯一不同的地方就是增加action时不同
         */
        if($type == 'demand')
        {
            $taskID = $this->newTaskByDemand($projectID,$execution,$storyID=0,$moduleID=0, $taskID = 0, $todoID = 0);
        }else{
            $taskID = $this->newTask($projectID,$execution,$storyID=0,$moduleID=0, $taskID = 0, $todoID = 0);
        }
        if( $taskID){
            //查询任务-问题-需求 关联表
            $codes = $this->dao->select(' code, assignto, id, type,project,application')
                ->from(TABLE_TASK_DEMAND_PROBLEM)->where('project')->eq($projectID)
                ->andWhere('application')->eq($data->app)
                ->andWhere('code')->eq($data->code)
                ->andWhere('deleted')->eq(0)
                ->beginIF( !in_array($sourceType,array('dept','localesupport')) )->andWhere('product')->eq($data->product)->andWhere('version')->eq($data->productPlan)->fi()
                ->fetch();
            switch ($sourceType) {
                case 'dept':
                    $table = TABLE_DEPTORDER;
                    break;
                case 'secondorder':
                    $table = TABLE_SECONDORDER;
                    break;
                case 'demand':
                    $table = TABLE_DEMAND;
                    break;
                case 'demandinside':
                    $table = TABLE_DEMANDINSIDE;
                    break;
                case 'problem':
                    $table = TABLE_PROBLEM;
                    break;
                case 'localesupport';
                    $table = "";
                    break;
            }
            $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('`taskid`')->eq($taskID)->set('execution')->eq($execution)->where('id')->in($codes->id)->exec();
            if($table){
                $this->dao->update($table)->set('`execution`')->eq($execution)->where("id =(select id from (select id from $table where code = '$data->code')t1 )")->exec();
            }
            //生成任务自动更新现场支持报工表中的信息
            if($sourceType == 'localesupport'){
                $this->loadModel('localesupport')->updateLocalSupportTaskID($codes,$taskID,$execution,$data->supportId,$data->deptId);
            }
        }
        return $taskID;
    }


    /**
     * 项目管理、二线管理、部门管理 年度计划立项自动生成一二级阶段 、三级任务
     * @param $projectID 项目id
     * @return mixed
     */
    public function approvalAutoCreateStageAndTask($projectID)
    {
        $projectplan = $this->dao->select("mark,name,secondLine,begin,end,code")->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->andWhere('deleted')->eq(0)->fetch();
         //二线项目（分为部门管理和二线管理）
        if($projectplan->secondLine){
            //二线管理
            if(strpos($projectplan->code,'EX') !== false) {
                $stage = array(
                   // $this->lang->task->stageList['secondDevelopment'],//二线研发管理
                    //$this->lang->task->stageList['secondWorkOrder'] //二线工单管理
                    $this->lang->task->stageList['secondDevelopmentDemand'],   // '二线实现_需求池';//一级阶段 二线研发管理
                    $this->lang->task->stageList['secondDevelopmentProblem'],  // '二线实现_问题池';//一级阶段 二线研发管理
                    $this->lang->task->stageList['secondWorkOrderSecond'],     // '二线实现_工单池';//一级阶段 二线工单管理
                    $this->lang->task->stageList['secondLocaleSupport'], // 现场支持
                );
                foreach ($stage as $item) {
                    $this->checkCreateSecondStage($projectID, $item,$projectplan->name,$projectplan->mark,$projectplan->secondLine,$projectplan); //生成一级阶段
                }
            } else{
                //部门管理
                $deptTwoStage = array(
                    $this->lang->task->stageSecondList['deptForeignThing'] ,//二级阶段 部门其他管理  外来事物
                    $this->lang->task->stageSecondList['deptInternalAffairs']  //二级阶段 部门其他管理 内部事物
                );

                $deptThreeTask = array(
                    $this->lang->task->stageSecondList['deptInternalAffairs'] => array(
                        $this->lang->task->threeTaskList['deptMeeting'] ,//三级阶段 部门其他管理 会议
                        $this->lang->task->threeTaskList['deptTrain'] ,//三级阶段 部门其他管理 培训
                        $this->lang->task->threeTaskList['deptBeAway'] ,//三级阶段 部门其他管理 公出
                        $this->lang->task->threeTaskList['deptOffcial'] ,//三级阶段 部门其他管理 出差
                        $this->lang->task->threeTaskList['deptThreeOther'] ,//三级阶段 部门其他管理 其他
                    )
                );
                $stage = array(
                    array(
                        'stageone' => $this->lang->task->stageList['deptDevelopmentDemand'],//'部门实现_需求池', //部门研发管理 一级阶段
                    ) ,
                    array(
                        'stageone' => $this->lang->task->stageList['deptDevelopmentProblem'],//'部门实现_问题池'//部门研发管理 一级阶段
                    ) ,
                    array(
                        'stageone' => $this->lang->task->stageList['deptDevelopmentDept'],  // '部门实现_工单池' //部门研发管理 一级阶段

                    ) ,
                    array(
                        'stageone' => $this->lang->task->stageList['deptOther'], //部门其他管理
                        'stagetwo' => $deptTwoStage, //二级阶段
                        'taskthree' => $deptThreeTask //三级任务
                    )
                );
                foreach ($stage as $item) {
                    $this->checkCreateSecondStage($projectID, $item,$projectplan->name,$projectplan->mark,$projectplan->secondLine,$projectplan);//生成一级阶段
                }
            }
        }else{
         //项目实现
            /**
             * 1、项目管理活动 ->(计划阶段、采购阶段 工程实施阶段 结项阶段 其他阶段)->(计划阶段任务 采购阶段任务 工程实施阶段任务 结项阶段任务 其他阶段任务)
             * 2、项目研发活动
             */
            $mangerTwoStage = array(
                $this->lang->task->stageSecondList['projectPlan'] ,    //二级阶段 项目管理活动  计划阶段
                $this->lang->task->stageSecondList['projectProcure'],  //二级阶段 项目管理活动  采购阶段
                $this->lang->task->stageSecondList['projectImplement']  , //二级阶段 项目管理活动  工程实施阶段
                $this->lang->task->stageSecondList['projectTechnology'] ,//二级阶段 项目管理活动  技术支持阶段
                $this->lang->task->stageSecondList['projectDirect']  ,
                $this->lang->task->stageSecondList['projectClose']   ,   //二级阶段 项目管理活动  结项阶段
                $this->lang->task->stageSecondList['projectOther'] //二级阶段 项目管理活动  其他阶段
            );
            $mangerThreeTask = array(
                $this->lang->task->stageSecondList['projectPlan'] => $this->lang->task->threeTaskList['projectPlanTask'],    //三级任务 项目管理活动 计划阶段任务
                $this->lang->task->stageSecondList['projectProcure'] => $this->lang->task->threeTaskList['projectProcureTask'], //三级任务 项目管理活动 采购阶段任务
                $this->lang->task->stageSecondList['projectImplement'] => $this->lang->task->threeTaskList['projectImplementTask'] , //三级任务 项目管理活动 工程实施阶段任务
                $this->lang->task->stageSecondList['projectTechnology'] => $this->lang->task->threeTaskList['projectTechnologyTask'] ,//三级任务 项目管理活动 技术支持阶段任务
                $this->lang->task->stageSecondList['projectDirect']  => $this->lang->task->threeTaskList['projectDirectTask']   ,
                $this->lang->task->stageSecondList['projectClose'] => $this->lang->task->threeTaskList['projectCloseTask']  ,   //三级任务 项目管理活动 结项阶段任务
                $this->lang->task->stageSecondList['projectOther'] => $this->lang->task->threeTaskList['projectOtherTask']  //三级任务 项目管理活动 其他阶段任务
            );
            $stage = array(
                array(
                    'stageone' => $this->lang->task->stageList['projectManger'], //一级阶段
                    'stagetwo' => $mangerTwoStage, //二级阶段
                    'taskthree' => $mangerThreeTask //三级任务
                ) ,// 项目管理活动
                array(
                   // 'stageone' => $this->lang->task->stageList['projectDevelopment']
                    'stageone' => $this->lang->task->stageList['projectDevelopmentDemand']  //'项目实现_需求池';//一级阶段 项目研发活动

                ),//项目研发活动
                array(
                    'stageone' => $this->lang->task->stageList['projectDevelopmentProblem'] //'项目实现_问题池';//一级阶段 项目研发活动
                ),
                array(
                    'stageone' => $this->lang->task->stageList['projectDevelopmentSecond']     //'项目实现_工单池';//一级阶段 二线研发活动
                )
            );
            foreach ($stage as $item) {
                $this->checkCreateSecondStage($projectID, $item,$projectplan->name,$projectplan->mark,$projectplan->secondLine,$projectplan);//生成一级阶段
            }
        }
    }


    /**
     * 查询二线是否存在一级阶段
     * @param $projectID
     * @param $name
     */
    public function checkCreateSecondStage($projectID,$name,$projectPlanName,$mark,$isSecond,$projectplan){
        if(!is_array($name)){
            //二线（包含二线管理、部门管理）
            //查询一级阶段
            $field = "id,path,parent";
            $stageres = $this->getSelectTable(TABLE_EXECUTION,$field,$projectID,1,$name);
            if(!$stageres){
                //一级阶段不存在 ，创建
                $this->autoCreateSecondStage($projectID,$name,1,0,$projectPlanName,$mark,$isSecond,$projectplan);
            }
        }else{
            //项目
            //查询一级阶段
            $field = "id,path,parent";
            $stageres = $this->getSelectTable(TABLE_EXECUTION,$field,$projectID,1,$name['stageone']);
            if(!$stageres){
                //一级阶段不存在 ，创建
                $this->autoCreateSecondStage($projectID,$name['stageone'],1,0,$projectPlanName,$mark,$isSecond,$projectplan);
            }else{
                //一级阶段存在
                //查询二级阶段
                if(isset($name['stagetwo'])) {
                    $two = $name['stagetwo'];
                    foreach ($two as $item) {
                        $field = "id,path";
                        $stagetwo = $this->getSelectTable(TABLE_EXECUTION,$field,$projectID,2,$item,null,$stageres->path);
                        if($stagetwo){
                            //二级阶段存在
                            //查询三级任务
                            $threes = $name['taskthree'];
                            if(strpos($projectplan->code,'DEP') !== false ){
                                if(isset($threes[$item]) && is_array($threes[$item])){
                                    $field = 'id,path,execution';
                                    foreach($threes[$item] as $three){
                                        $taskthree = $this->getSelectTable(TABLE_TASK,$field,$projectID,1,$three,$stagetwo->id,null);
                                        if(!$taskthree){
                                            //三级任务不存在 创建
                                            $this->autoCreateTaskThreeObject($three, $stagetwo->id, $projectID,$projectplan);
                                        }
                                    }
                                }
                            }else{
                                $taskthree = $this->getSelectTable(TABLE_TASK,$field,$projectID,1,$threes[$item],$stagetwo->id,null);
                                if(!$taskthree){
                                    //三级任务不存在 创建
                                    $this->autoCreateTaskThreeObject($threes[$item], $stagetwo->id, $projectID,$projectplan);
                                }
                            }
                        }else{
                            //二级阶段不存在 ，创建
                            $this->autoCreateSecondStage($projectID,$item,2,$stageres,$projectPlanName,$mark,$isSecond,$projectplan);
                        }
                    }
                }
            }
        }
    }
    /**
     * 创建一级阶段（二线管理 、部门管理）
     * @param $projectID
     * @param $name
     */
    public function autoCreateSecondStage($projectID,$name,$grade,$stageres,$projectPlanName,$mark,$isSecond,$projectplan){
        $execution = new stdClass();
        $execution->project      = $projectID;
        $execution->parent       = $stageres != '0' ? $stageres->id : $stageres;  ;
        $execution->name         = $name;
        $execution->type         = 'stage';
        $execution->resource     = '';
        $execution->begin        = $projectplan->begin ;
        $execution->end          = $projectplan->end ;
        $execution->planDuration = helper::diffDate3($execution->end , $execution->begin);
        $execution->grade        = $grade;
        $execution->openedBy     = 'admin';
        $execution->openedDate   = helper::today();
        $execution->status       = 'wait';
        $execution->milestone    = 0;
        $execution->version      = 1;
        $execution->source       = 1;
        $execution->dataVersion  = 2;//为了将历史数据隔离

        $this->dao->insert(TABLE_EXECUTION)->data($execution)->exec();
        $executionID = $this->dao->lastInsertID();

        //记录到版本库
        $spec               = new stdclass();
        $spec->execution    = $executionID;
        $spec->version      = 1;
        $spec->name         = $execution->name;
        $spec->milestone    = $execution->milestone;
        $spec->begin        = $execution->begin;
        $spec->end          = $execution->end;
        $spec->planDuration = $execution->planDuration;
        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

        $path =   ($grade == 1 ? ',' . $projectID . ',' : ($stageres != '0' ? $stageres->path : '')) . $executionID . ',';
        $order = $executionID * 5;
        $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();
        $this->loadModel('action')->create('newexecution', $executionID, 'created',sprintf($this->lang->task->projectPlanName,$projectPlanName,$mark ),sprintf($this->lang->task->projectPlanName,$projectPlanName,$mark ));
        //如果是项目 则需要生成多个阶段
        if(!$isSecond || strpos($projectplan->code,'DEP') !== false){
            $this->approvalAutoCreateStageAndTask($projectID);
        }
    }
    /**
     * 新增三级任务
     * @param $taskname
     * @param $stagename
     * @param $projectID
     * @param $stage
     * @param $data
     */
    public function autoCreateTaskThreeObject($threeName,$execution,$projectID,$projectplan)
    {
        unset($_POST);
        $_POST['execution'] = $execution;// 所属阶段
        $_POST['type'] = 'devel';// 任务类型
        $_POST['module'] = 0;//所属模块
        $_POST['assignedTo'] = array('0' => '');//指派给
        $_POST['mailto'] = array('0' => '');
        $_POST['name'] = $threeName;// 任务名称
        $_POST['estStarted'] = $projectplan->begin; //预计开始
        $_POST['deadline'] = $projectplan->end; //预计结束
        $_POST['openedBy'] = $this->app->user->account; //由谁创建
        $_POST['openedDate'] = date('Y-m-d'); //创建时间
        $_POST['pri'] = 1;//优先级
        $_POST['status'] = 'wait';//状态
        $_POST['source'] = '1'; //数据来源
        $_POST['design'] = 0;
        $_POST['color'] = '';
        $_POST['dropType'] = '0';
        $_POST['dataVersion'] = 2;//为了将历史数据隔离

        $taskID = $this->newTask($projectID, $execution, $storyID = 0, $moduleID = 0, $taskID = 0, $todoID = 0);
        $this->dao->update(TABLE_TASK)->set('path')->eq($taskID)->where('id')->eq($taskID)->exec();
    }

    /**
     * 生成任务时查询的表和字段
     * @param $tableName
     * @param $field
     * @param $projectID
     * @param $grade
     * @param $name
     * @param $execution
     * @param $path
     * @return mixed
     */
    public function getSelectTable($tableName,$field,$projectID,$grade,$name,$execution = null,$path = null){
        $res =  $this->dao->select("$field")->from($tableName)->where('project')->eq($projectID)
            ->andWhere('grade')->eq($grade)
            ->andWhere('name')->eq($name)
            ->andWhere('type')->eq($tableName == TABLE_EXECUTION ? 'stage' : 'devel')
            ->beginIF($grade == '2' && $tableName == TABLE_EXECUTION)->andWhere('path')->like("$path%")->fi()
            ->beginIF($grade == '1' && $tableName == TABLE_TASK)->andWhere('execution')->eq($execution)->fi()
            ->beginIF($grade == '1' && $tableName == TABLE_TASK)->andWhere('parent')->eq(0)->fi()
            ->beginIF($grade == '2' && $tableName == TABLE_TASK)->andWhere('path')->like("$path%")->fi()
            ->andWhere('deleted')->eq(0)
            ->andWhere('dataVersion')->eq(2)
            ->fetch();

        return $res;
    }

    /**
     * 编辑任务计划开始 、计划结束
     * @param $taskID
     * @return array|bool
     */
    public function editTaskDate($taskID)
    {
        $oldTask = $this->getById($taskID);
        $task = fixer::input('post')
            ->get();

        $this->dao->update(TABLE_TASK)->data($task)
            ->autoCheck()
            ->checkIF($task->deadline != '', 'deadline', 'gt', $task->estStarted)
            ->where('id')->eq((int)$taskID)
            ->exec();

        $this->computeConsumed($taskID);
        if(!dao::isError()) return common::createChanges($oldTask, $task);
    }

}
