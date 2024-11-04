<?php
class chengfangjinkeProgramplan extends programplanModel
{
    /**
     * Project: chengfangjinke
     * Method: createFromImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:13
     * Desc: This is the code comment. This method is called createFromImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @return bool|void
     */
    public function createFromImport($projectID)
    {
        // 获取项目关联的所有产品ID。
        $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
        $this->loadModel('execution');

        $data = fixer::input('post')->get();

        $names     = $data->name;
        $wbss      = $data->wbs;
        $levels    = $data->level;
        $types     = $data->type;
        $begins    = $data->begin;
        $ends      = $data->end;
        $durations = $data->duration;
        $resources = $data->resource;
        $milestone = $data->milestone;

        $dict = array();
        $rows = array();
        foreach($names as $key => $name)
        {
            $row            = new stdClass();
            $row->wbs       = $wbss[$key];
            $row->level     = $levels[$key];
            $row->name      = $name;
            $row->type      = $types[$key];
            $row->duration  = $durations[$key];
            $row->begin     = $begins[$key];
            $row->end       = $ends[$key];
            $row->grade     = $row->level-1;
            $row->resource  = $resources[$key];
            $row->milestone = $milestone[$key];

            if($row->duration < 0)
            {
                $durationError = $this->lang->programplan->durationError;
                $durationError = sprintf($durationError, $key + 1);
                die(js::alert($durationError));
            }

            if($row->level === 1 and $row->type === 'task')
            {
                die(js::alert($this->lang->programplan->taskNotAllowed));
            }

            if($row->level === 2 and $row->type === 'task')
            {
                die(js::alert($this->lang->programplan->taskNotAllowed2));
            }

            // 判断是否为任务，如果是则grade再减少数值。
            if($row->type == 'task')
            {
                $row->grade = $row->grade-1;
            }

            $dict[$row->wbs] = $key;
            $rows[$key] = $row;
        }

        foreach($rows as $key => $row)
        {
            $wbs = explode('.', $row->wbs);
            if(count($wbs) != $row->level)
            {
                die(js::alert(sprintf($this->lang->programplan->wbsError, $row->level, $row->wbs)));
            }

            if(strtotime($row->begin) > strtotime($row->end))
            {
                die(js::alert(sprintf($this->lang->programplan->dateError, $key + 1)));
            }
        }

        foreach($rows as $key => $row)
        {
            $parent = 0;
            $wbs = explode('.', $row->wbs);
            array_pop($wbs);
            //if($row->level != 1)
            //{
            //    if(count($wbs) == 0)
            //    {
            //        die(js::alert(sprintf($this->lang->programplan->wbsError, $row->level, $row->wbs)));
            //    }
            //}

            if(count($wbs) == 0)
            {
                $parent = 0;
            }
            else
            {
                $parentKey = $dict[implode('.', $wbs)];

                $parentObject = $rows[$parentKey];
                if($parentObject->type == $row->type)
                {
                    if(!isset($rows[$parentKey]))
                    {
                        die(js::alert($this->lang->programplan->needWBS . ' ' . $parentKey));
                    }
                    $parent = $rows[$parentKey]->id;
                }
            }

            if($row->type == 'task')
            {
                $task = new stdClass();
                $task->project      = $projectID;
                $task->parent       = $parent;
                $task->execution    = $parent ? $rows[$parentKey]->execution : $rows[$parentKey]->id;
                $task->name         = $row->name;
                $task->grade        = $row->grade;
                $task->type         = 'devel';
                $task->resource     = $row->resource;
                $task->estStarted   = $row->begin;
                $task->deadline     = $row->end;
                $task->estimate     = $row->duration * 8;
                $task->planDuration = $row->duration;
                $task->left         = $task->estimate;
                $task->openedBy     = $this->app->user->account;
                $task->openedDate   = helper::today();
                $this->dao->insert(TABLE_TASK)->data($task)->exec();

                $taskID = $this->dao->lastInsertID();
                $rows[$key]->id = $taskID;
                $rows[$key]->execution = $task->execution;

                if($parentKey and $rows[$parentKey]->type == 'task')
                {
                    $rows[$key]->path = $rows[$parentKey]->path . ',' . $taskID;
                }
                else
                {
                    $rows[$key]->path = $taskID;
                }
                $this->dao->update(TABLE_TASK)->set('path')->eq($rows[$key]->path)->where('id')->eq($taskID)->exec();
            }
            else
            {
                $execution = new stdClass();
                $execution->project      = $projectID;
                $execution->parent       = $parent;
                $execution->name         = $row->name;
                $execution->type         = 'stage';
                $execution->resource     = $row->resource;
                $execution->begin        = $row->begin;
                $execution->end          = $row->end;
                $execution->planDuration = $row->duration;
                $execution->grade        = $row->level;
                $execution->openedBy     = $this->app->user->account;
                $execution->openedDate   = helper::today();
                $execution->status       = 'wait';
                $execution->milestone    = $row->milestone;
                $execution->version      = 1;
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

                // 为执行关联项目的产品。
                if(!empty($products))
                {
                    $_POST['products'] = $products;
                    $this->execution->updateProducts($executionID);
                    unset($_POST['products']);
                }

                $path = ($row->level == 1 ? ',' . $projectID . ',' : $parentObject->path) . $executionID . ',';
                $order = $executionID * 5;
                $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();

                $rows[$key]->id        = $executionID;
                $rows[$key]->execution = $executionID;
                $rows[$key]->path      = $path;
                $rows[$key]->grade     = $execution->grade;
            }
        }

        // $this->computeEstimate($projectID);

        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: computeEstimate
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:13
     * Desc: This is the code comment. This method is called computeEstimate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     */
    public function computeEstimate($projectID)
    {
        $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('project')->eq($projectID)
            ->andWhere('deleted')->eq(0)
            ->orderBy('grade DESC, id ASC')
            ->fetchAll();
        $stats = array();
        $taskEst = array(); // 所有的最小颗粒度任务
        $execEst = array(); // 所有的最小颗粒度任务
        foreach($tasks as $task)
        {
            if(!isset($stats[$task->execution]))
            {
                $stats[$task->execution] = array();
                $execEst[$task->execution] = array();
            }

            if(!isset($stats[$task->execution][$task->parent]))
            {
                $stats[$task->execution][$task->parent] = array();
                $taskEst[$task->parent] = array();
            }

            if(isset($stats[$task->execution][$task->id]))
            {
                $task->children = $stats[$task->execution][$task->id];
            }
            else
            {
                $task->children = array();
            }

            $stats[$task->execution][$task->parent][] = $task;

            // 将工时反应到父任务或者阶段
            if($task->parent == 0)
            {
                $execEst[$task->execution] = array_merge($execEst[$task->execution], isset($taskEst[$task->id]) ? $taskEst[$task->id] : [$task->estimate]);
            }
            else
            {
                $taskEst[$task->parent] = array_merge($taskEst[$task->parent], isset($taskEst[$task->id]) ? $taskEst[$task->id] : [$task->estimate]);
            }
        }

        foreach($exeEst as $id => $est)
        {
            $hour = 0;
            foreach($est as $e) $hour += $e;
            $this->dao->update(TABLE_EXECUTION)->set('estimate')->eq($hour)->set('left')->eq($hour)->where('id')->eq($id)->exec();
        }

        foreach($taskEst as $id => $est)
        {
            $hour = 0;
            foreach($est as $e) $hour += $e;
            $this->dao->update(TABLE_TASK)->set('estimate')->eq($hour)->set('left')->eq($hour)->where('id')->eq($id)->exec();
        }
    }

    /**
     * Get gantt data.
     *
     * @param  int     $executionID
     * @param  int     $productID
     * @param  int     $baselineID
     * @param  string  $selectCustom
     * @param  bool    $returnJson
     * @access public
     * @return string
     */
    public function getDataForGantt($executionID, $productID, $baselineID = 0, $selectCustom = '', $returnJson = true)
    {
        $this->loadModel('stage');

        $plans = $this->getStage($executionID, $productID, 'all');
        if($baselineID)
        {
            $baseline = $this->loadModel('cm')->getByID($baselineID);
            $oldData  = json_decode($baseline->data);
            $oldPlans = $oldData->stage;
            foreach($oldPlans as $id => $oldPlan)
            {
                if(!isset($plans[$id])) continue;
                $plans[$id]->version   = $oldPlan->version;
                $plans[$id]->name      = $oldPlan->name;
                $plans[$id]->milestone = $oldPlan->milestone;
                $plans[$id]->begin     = $oldPlan->begin;
                $plans[$id]->end       = $oldPlan->end;
            }
        }

        $datas       = array();
        $planIDList  = array();
        $isMilestone = "<icon class='icon icon-flag icon-sm red'></icon> ";
        $stageIndex  = array();
        foreach($plans as $plan)
        {
            $planIDList[$plan->id] = $plan->id;

            $start = $plan->begin == '0000-00-00' ? '' : date('d-m-Y', strtotime($plan->begin));
            $end   = $plan->end   == '0000-00-00' ? '' : $plan->end;

            $data = new stdclass();
            $data->id         = $plan->id;
            $data->type       = 'plan';
            $data->text       = empty($plan->milestone) ? $plan->name : $plan->name . $isMilestone ;
            $data->percent    = $plan->percent;
            $data->attribute  = zget($this->lang->stage->typeList, $plan->attribute);
            $data->milestone  = zget($this->lang->programplan->milestoneList, $plan->milestone);
            $data->start_date = $start;
            $data->deadline   = $end;
            $data->realBegan  = $plan->realBegan == '0000-00-00' ? '' : $plan->realBegan;
            $data->realEnd    = $plan->realEnd == '0000-00-00' ? '' : $plan->realEnd;
            $data->duration   = helper::diffDate($plan->end, $plan->begin) + 1;;
            $data->parent     = $plan->grade == 1 ? 0 :$plan->parent;
            $data->open       = true;

            if($data->start_date == '' or $data->deadline == '') $data->duration = 0;

            $datas['data'][] = $data;
            $stageIndex[]    = array('planID' => $plan->id, 'progress' => array('totalConsumed' => 0, 'totalReal' => 0));
        }

        $taskSign = "<span>[ T ] </span>";
        $taskPri  = "<span class='label-pri label-pri-%s' title='%s'>%s</span> ";

        /* Judge whether to display tasks under the stage. */
        $owner   = $this->app->user->account;
        $module  = 'programplan';
        $section = 'browse';
        $object  = 'stageCustom';
        if(empty($selectCustom)) $selectCustom = $this->loadModel('setting')->getItem("owner={$owner}&module={$module}&section={$section}&key={$object}");

        $tasks = array();
        if(strpos($selectCustom, 'task') !== false)
        {
            $tasks = $this->dao->select('*')->from(TABLE_TASK)->where('deleted')->eq(0)->andWhere('execution')->in($planIDList)->fetchAll('id');
        }

        if($baselineID)
        {
            $oldTasks = $oldData->task;
            foreach($oldTasks as $id => $oldTask)
            {
                if(!isset($tasks->$id)) continue;
                $tasks->$id->version    = $oldTask->version;
                $tasks->$id->name       = $oldTask->name;
                $tasks->$id->estStarted = $oldTask->estStarted;
                $tasks->$id->deadline   = $oldTask->deadline;
            }
        }

        foreach($tasks as $task)
        {
            $start = $task->estStarted == '0000-00-00' ? '' : date('d-m-Y', strtotime($task->estStarted));
            $end   = $task->deadline   == '0000-00-00' ? '' : $task->deadline;

            $realBegan = $task->realStarted == '0000-00-00' ? '' : $task->realStarted;
            $realEnd   = $task->finishedDate == '0000-00-00 00:00:00' ? '' : substr($task->finishedDate, 5, 11);
            $priIcon   = sprintf($taskPri, $task->pri, $task->pri, $task->pri);

            $data = new stdclass();
            $data->id           = $task->execution . '-' . $task->id;
            $data->type         = 'task';
            $data->text         = $taskSign . $priIcon . $task->name;
            $data->percent      = '';
            $data->attribute    = '';
            $data->milestone    = '';
            $data->start_date   = $start;
            $data->deadline     = $end;
            $data->realBegan    = $realBegan;
            $data->realEnd      = $realEnd;
            $data->duration     = helper::diffDate($task->deadline, $task->estStarted) + 1;
            $data->parent       = $task->execution;
            $data->open         = true;
            $progress           = $task->consumed ? round($task->consumed / ($task->left + $task->consumed), 3) * 100 : 0;
            $data->taskProgress = $progress . '%';

            if($data->start_date == '' or $data->deadline == '') $data->duration = 0;

            $datas['data'][] = $data;
            foreach($stageIndex as $index => $stage)
            {
                if($stage['planID'] == $task->execution)
                {
                    $stageIndex[$index]['progress']['totalConsumed'] += $task->consumed;
                    $stageIndex[$index]['progress']['totalReal']     += ($task->left + $task->consumed);
                }
            }
        }

        /* Calculate the progress of the phase. */
        foreach($stageIndex as $index => $stage)
        {
            $progress  = empty($stage['progress']['totalConsumed']) ? 0 : round($stage['progress']['totalConsumed'] / $stage['progress']['totalReal'], 3) * 100;
            $progress .= '%';
            $datas['data'][$index]->taskProgress = $progress;
        }

        return $returnJson ? json_encode($datas) : $datas;
    }

    /**
     * Get plans list.
     *
     * @param  int     $executionID
     * @param  int     $productID
     * @param  string  $browseType all|parent
     * @param  string  $orderBy
     * @access public
     * @return array
     */
    public function getStage($executionID = 0, $productID = 0, $browseType = 'all', $orderBy = 'id_asc')
    {
        if(empty($executionID)) return array();

        $plans = $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('type')->eq('stage')
            ->beginIF($browseType == 'all')->andWhere('project')->eq($executionID)->fi()
            ->beginIF($browseType == 'parent')->andWhere('parent')->eq($executionID)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->sprints)->fi()
            ->andWhere('deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll('id');

        return $this->processPlans($plans);
    }

    /**
     * Project: chengfangjinke
     * Method: getHoliday
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:13
     * Desc: This is the code comment. This method is called getHoliday.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $start
     * @param $end
     * @return mixed
     */
    public function getHoliday($start, $end)
    {
        $hs = $this->dao->select('*')->from(TABLE_HOLIDAY)
            ->where('begin')->ge($start)
            ->andWhere('end')->le($end)
            ->fetchAll();
        return $hs;
    }

    /**
     * Project: chengfangjinke
     * Method: days
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:13
     * Desc: This is the code comment. This method is called days.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $start
     * @param $end
     * @param array $hs
     * @return int
     */
    public function days($start, $end, $hs = array())
    {
        if($end < $start) return 0;

        $count = 0;
        for ($date = $start; $date <= $end; $date = date('Y-m-d', strtotime($date . ' +1day'))) {
            $count++;
        }
        return $count;
    }

    /**
     * Project: chengfangjinke
     * Method: workDays
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:13
     * Desc: This is the code comment. This method is called workDays.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $start
     * @param $end
     * @param array $hs
     * @return int
     */
    public function workDays($start, $end, $hs = array())
    {
        if($end < $start) return 0;

        $count = 0;
        for ($date = $start; $date <= $end; $date = date('Y-m-d', strtotime($date . ' +1day'))) {
            $weekday = date('w', strtotime($date));
            if($weekday != 0 and $weekday != 6) $count++;
        }

        // 识别假日和补班
        $holiday = array();
        $working = array();
        foreach($hs as $h)
        {
            if($h->type == 'holiday')
            {
                for($date = $h->begin; $date <= $h->end; $date = date('Y-m-d', strtotime($date . ' +1day')))
                {
                    if($date < $start or $date > $end) continue;

                    $weekday = date('w', strtotime($date));
                    if($weekday != 0 and $weekday != 6) $holiday[$date] = $date;
                }
            }
            else
            {
                for($date = $h->begin; $date <= $h->end; $date = date('Y-m-d', strtotime($date . ' +1day')))
                {
                    if($date < $start or $date > $end) continue;

                    $weekday = date('w', strtotime($date));
                    if($weekday == 0 and $weekday == 6) $working[$date] = $date;
                }
            }
        }

        $count = $count + count($working) - count($holiday);
        return $count;

        /*
        {
            $start = date('Y-m-d', strtotime($start));
            $end   = date('Y-m-d', strtotime($end));

            $holiday = array();
            $working = array();
            foreach($hs as $h)
            {
                if($h->type == 'holiday')
                {
                    $i = 1;
                    for($date = $h->begin; $date <= $h->end; $date = date('Y-m-d', strtotime($date . ' +1day')))
                    {
                        $weekday = date('w', strtotime($date));
                        if($weekday != 0 and $weekday != 6) $holiday[$date] = $date;
                    }
                }
                else
                {
                    for($date = $h->begin; $date <= $h->end; $date = date('Y-m-d', strtotime($date . ' +1day')))
                    {
                        $weekday = date('w', strtotime($date));
                        if($weekday == 0 and $weekday == 6) $working[$date] = $date;
                    }
                }
            }

            return array('holiday' => $holiday, 'working' => $working);
        }
         */
    }

    /**
     * Project: chengfangjinke
     * Method: createSubStage
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/03/24
     * Time: 17:13
     * Desc: This is the code comment. This method is called workDays.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     * @param $parent
     * @return boolean
     */

    public function createSubStage($projectID, $parent,$flag = null)
    {
        // 获取项目关联的所有产品ID。
        $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
        $this->loadModel('execution');

        $data          = fixer::input('post')->get();
        $names         = $data->names;
        $begins        = $data->begin;
        $ends          = $data->end;
        $planDurations = $data->planDuration;
        $resources     = $data->resource;
        $milestones    = $data->milestone;
        $attributes    = $data->attribute;
        $codes         = $data->code;

        $executions     = array();
        $now            = helper::now();
        $requiredFields = explode(',', $this->config->programplan->create->requiredFields);

        foreach($names as $k => $name)
        {
            if(empty($name)) continue;

            $execution               = new stdclass();
            $execution->type         = 'stage';
            $execution->project      = $projectID;
            $execution->parent       = $parent;
            $execution->name         = $name;
            $execution->milestone    = empty($milestones[$k]) ? 0 :$milestones[$k];
            $execution->begin        = $begins[$k];
            $execution->end          = $ends[$k];
            $execution->planDuration = $planDurations[$k];
            $execution->resource     = $resources[$k];
            $execution->grade        = 2;
            $execution->openedBy     = $this->app->user->account;
            $execution->openedDate   = $now;
            $execution->status       = 'wait';
            $execution->version      = 1;
            $execution->attribute    = $attributes[$k];
            $execution->code         = $codes[$k];
            $execution->dataVersion  = $flag ? 2 : 1;

            foreach($requiredFields as $field)
            {
                $field = trim($field);
                if($field and empty($execution->$field))
                {
                    dao::$errors['message'][] = sprintf($this->lang->error->notempty, $this->lang->programplan->$field);
                    return false;
                }
            }

            $executions[] = $execution;
        }

        $this->loadModel('execution');
        $this->loadModel('action');
        foreach($executions as $execution)
        {
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

            // 为执行关联项目的产品。
            if(!empty($products))
            {
                $_POST['products'] = $products;
                $this->execution->updateProducts($executionID);
                unset($_POST['products']);
            }

            $path  = ',' . $projectID . ',' . $parent . ',' . $executionID . ',';
            $order = $executionID * 5;
            $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->set('order')->eq($order)->where('id')->eq($executionID)->exec();

            $this->action->create('execution', $executionID, 'opened');
        }
        return true;
    }
}
