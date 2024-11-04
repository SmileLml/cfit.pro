<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mytask extends task
{
    public function export($executionID, $orderBy, $type)
    {
        $execution = $this->execution->getById($executionID);
        $allExportFields = $this->config->task->exportFields;
        if($execution->type == 'ops') $allExportFields = str_replace(' story,', '', $allExportFields);

        if($_POST)
        {
            $this->loadModel('file');
            $taskLang = $this->lang->task;
            $this->task->setListValue($executionID);

            /* Create field lists. */
            $sort   = $this->loadModel('common')->appendOrder($orderBy);
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $allExportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($taskLang->$fieldName) ? $taskLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get tasks. */
            $tasks = array();
            if($this->session->taskOnlyCondition)
            {
                $tasks = $this->dao->select('*')->from(TABLE_TASK)->alias('t1')->where($this->session->taskQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('t1.id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($sort)->fetchAll('id');

                foreach($tasks as $key => $task)
                {
                    /* Compute task progress. */
                    if($task->consumed == 0 and $task->left == 0)
                    {
                        $task->progress = 0;
                    }
                    elseif($task->consumed != 0 and $task->left == 0)
                    {
                        $task->progress = 100;
                    }
                    else
                    {
                        $task->progress = round($task->consumed / ($task->consumed + $task->left), 2) * 100;
                    }
                    $task->progress .= '%';
                }
            }
            elseif($this->session->taskQueryCondition)
            {
                $stmt = $this->dbh->query($this->session->taskQueryCondition . ($this->post->exportType == 'selected' ? " AND t1.id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $tasks[$row->id] = $row;
            }

            /* Get users and executions. */
            $users      = $this->loadModel('user')->getPairs('noletter');
            $executions = $this->execution->getPairs($execution->project, 'all', 'all|nocode');

            /* Get related objects id lists. */
            $relatedStoryIdList  = array();
            foreach($tasks as $task) $relatedStoryIdList[$task->story] = $task->story;

            /* Get team for multiple task. */
            $taskTeam = $this->dao->select('*')->from(TABLE_TEAM)
                ->where('root')->in(array_keys($tasks))
                ->andWhere('type')->eq('task')
                ->fetchGroup('root');

            /* Process multiple task info. */
            if(!empty($taskTeam))
            {
                foreach($taskTeam as $taskID => $team)
                {
                    $tasks[$taskID]->team     = $team;
                    $tasks[$taskID]->estimate = '';
                    $tasks[$taskID]->left     = '';
                    $tasks[$taskID]->consumed = '';

                    foreach($team as $userInfo)
                    {
                        $tasks[$taskID]->estimate .= zget($users, $userInfo->account) . "(#$userInfo->account)" . ':' . $userInfo->estimate . "\n";
                        $tasks[$taskID]->left     .= zget($users, $userInfo->account) . "(#$userInfo->account)" . ':' . $userInfo->left . "\n";
                        $tasks[$taskID]->consumed .= zget($users, $userInfo->account) . "(#$userInfo->account)" . ':' . $userInfo->consumed . "\n";
                    }
                }
            }

            /* Get related objects title or names. */
            $relatedStories = $this->dao->select('id,title')->from(TABLE_STORY)->where('id')->in($relatedStoryIdList)->fetchPairs();
            $relatedFiles   = $this->dao->select('id, objectID, pathname, title')->from(TABLE_FILE)->where('objectType')->eq('task')->andWhere('objectID')->in(@array_keys($tasks))->andWhere('extra')->ne('editor')->fetchGroup('objectID');
            $relatedModules = $this->loadModel('tree')->getAllModulePairs('task');

            if($tasks)
            {
                $children = array();
                foreach($tasks as $task)
                {
                    if(!empty($task->parent) and isset($tasks[$task->parent]))
                    {
                        $children[$task->parent][$task->id] = $task;
                        unset($tasks[$task->id]);
                    }
                }
                if(!empty($children))
                {
                    $position = 0;
                    foreach($tasks as $task)
                    {
                        $position ++;
                        if(isset($children[$task->id]))
                        {
                            array_splice($tasks, $position, 0, $children[$task->id]);
                            $position += count($children[$task->id]);
                        }
                    }
                }
            }

            if($type == 'group')
            {
                $stories    = $this->loadModel('story')->getExecutionStories($executionID);
                $groupTasks = array();
                foreach($tasks as $task)
                {
                    $task->storyTitle = isset($stories[$task->story]) ? $stories[$task->story]->title : '';
                    if(isset($task->team))
                    {
                        if($orderBy == 'finishedBy') $task->consumed = $task->estimate = $task->left = 0;
                        foreach($task->team as $team)
                        {
                            if($orderBy == 'finishedBy' and $team->left != 0)
                            {
                                $task->estimate += $team->estimate;
                                $task->consumed += $team->consumed;
                                $task->left     += $team->left;
                                continue;
                            }

                            $cloneTask = clone $task;
                            $cloneTask->estimate = $team->estimate;
                            $cloneTask->consumed = $team->consumed;
                            $cloneTask->left     = $team->left;
                            if($team->left == 0) $cloneTask->status = 'done';

                            if($orderBy == 'assignedTo')
                            {
                                $cloneTask->assignedToRealName = zget($users, $team->account);
                                $cloneTask->assignedTo = $team->account;
                            }
                            if($orderBy == 'finishedBy')$cloneTask->finishedBy = $team->account;
                            $groupTasks[$team->account][] = $cloneTask;
                        }
                        if(!empty($task->left) and $orderBy == 'finishedBy') $groupTasks[$task->finishedBy][] = $task;
                    }
                    else
                    {
                        $groupTasks[$task->$orderBy][] = $task;
                    }
                }

                $tasks = array();
                foreach($groupTasks as $groupTask)
                {
                    foreach($groupTask as $task)$tasks[] = $task;
                }
            }

            foreach($tasks as $task)
            {
                if($this->post->fileType == 'csv')
                {
                    $task->desc = htmlspecialchars_decode($task->desc);
                    $task->desc = str_replace("<br />", "\n", $task->desc);
                    $task->desc = str_replace('"', '""', $task->desc);
                }

                /* fill some field with useful value. */
                $task->story = isset($relatedStories[$task->story]) ? $relatedStories[$task->story] . "(#$task->story)" : '';

                if(isset($executions[$task->execution]))              $task->execution    = $executions[$task->execution] . "(#$task->execution)";
                if(isset($taskLang->typeList[$task->type]))           $task->type         = $taskLang->typeList[$task->type];
                if(isset($taskLang->priList[$task->pri]))             $task->pri          = $taskLang->priList[$task->pri];
                if(isset($taskLang->statusList[$task->status]))       $task->status       = $taskLang->statusList[$task->status];
                if(isset($taskLang->reasonList[$task->closedReason])) $task->closedReason = $taskLang->reasonList[$task->closedReason];
                if(isset($relatedModules[$task->module]))             $task->module       = $relatedModules[$task->module] . "(#$task->module)";

                if(isset($users[$task->openedBy]))     $task->openedBy     = $users[$task->openedBy];
                if(isset($users[$task->assignedTo]))   $task->assignedTo   = $users[$task->assignedTo];
                if(isset($users[$task->finishedBy]))   $task->finishedBy   = $users[$task->finishedBy];
                if(isset($users[$task->canceledBy]))   $task->canceledBy   = $users[$task->canceledBy];
                if(isset($users[$task->closedBy]))     $task->closedBy     = $users[$task->closedBy];
                if(isset($users[$task->lastEditedBy])) $task->lastEditedBy = $users[$task->lastEditedBy];

                /* Convert username to real name. */
                if(!empty($task->mailto))
                {
                    $mailtoList = explode(',', $task->mailto);

                    $task->mailto = '';
                    foreach($mailtoList as $mailto)
                    {
                        if(!empty($mailto)) $task->mailto .= ',' . zget($users, $mailto);
                    }
                }

                if($task->parent > 0 && strpos($task->name, htmlentities('>')) !== 0) $task->name = '>' . $task->name;
                if(!empty($task->team))   $task->name = '[' . $taskLang->multipleAB . '] ' . $task->name;

                $task->openedDate     = substr($task->openedDate,     0, 10);
                $task->assignedDate   = substr($task->assignedDate,   0, 10);
                $task->finishedDate   = substr($task->finishedDate,   0, 10);
                $task->canceledDate   = substr($task->canceledDate,   0, 10);
                $task->closedDate     = substr($task->closedDate,     0, 10);
                $task->lastEditedDate = substr($task->lastEditedDate, 0, 10);

                /* Set related files. */
                if(isset($relatedFiles[$task->id]))
                {
                    $task->files = '';
                    foreach($relatedFiles[$task->id] as $file)
                    {
                        $fileURL = common::getSysURL() . $this->createLink('file', 'download', "fileID={$file->id}");
                        $task->files .= html::a($fileURL, $file->title, '_blank') . '<br />';
                    }
                }
            }
            if($this->post->excel == 'excel')
            {
                $trees = $this->execution->getTree($this->post->executionID);
                $trees = $this->treeToList($trees, $users);
                $this->post->set('exportFields', array('id','title','startTime','assignedTo','pri'));
                $this->post->set('fileType', 'xlsx');
                $this->post->set('exportType', 'all');
                $this->post->set('fields', $this->lang->task->field);
                $this->post->set('rows', $trees);
                $this->post->set('kind', 'tree');
                unset($_POST['moduleList']);
                unset($_POST['storyList']);
                unset($_POST['priList']);
                unset($_POST['typeList']);
                unset($_POST['listStyle']);
                unset($_POST['extraNum']);
                unset($_POST['excel']);
                $this->fetch('file', 'export2' . $this->post->fileType, $_POST);

            }
            else
            {
                if(isset($this->config->bizVersion)) list($fields, $tasks) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $tasks);

                $this->post->set('fields', $fields);
                $this->post->set('rows', $tasks);
                $this->post->set('kind', 'task');
                $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            }
        }

        $this->app->loadLang('execution');
        $fileName = $this->lang->task->common;
        $executionName = $this->dao->findById($executionID)->from(TABLE_EXECUTION)->fetch('name');
        if(isset($this->lang->execution->featureBar['task'][$type]))
        {
            $browseType = $this->lang->execution->featureBar['task'][$type];
        }
        else
        {
            $browseType = isset($this->lang->execution->statusSelects[$type]) ? $this->lang->execution->statusSelects[$type] : '';
        }

        $this->view->fileName        = $executionName . $this->lang->dash . $browseType . $fileName;
        $this->view->allExportFields = $allExportFields;
        $this->view->customExport    = true;
        $this->view->orderBy         = $orderBy;
        $this->view->type            = $type;
        $this->view->executionID     = $executionID;
        $this->display();
    }

    public function treeToList($trees, $users)
    {
        $rows = array();
        foreach($trees as $tree)
        {
            if($tree->type == 'product')
            {
                $rows[] = (object)array('title' => $this->lang->task->excelproduct . $tree->name, 'id' => $tree->id);
                if(isset($tree->children) and !empty($tree->children))
                {
                    foreach($tree->children as $module)
                    {
                        $rows[] = (object)array('title' => str_repeat(' ' , 2) . $this->lang->task->excelmodule . $module->name , 'id' => $module->id);
                        if(isset($module->children) and !empty($module->children))
                        {
                            foreach($module->children as $story)
                            {
                                $rows[] = (object)array('title' => str_repeat(' ' , 4) . $this->lang->task->excelstory . $story->title , 'id' => $story->id, 'assignedTo' => zget($users, $story->assignedTo));
                                if(isset($story->children) and !empty($story->children))
                                {
                                    foreach($story->children as $task)
                                    {
                                        $startTime = $this->dao->select('date')->from(TABLE_ACTION)->where('objectType')->eq('task')->andWhere('objectID')->eq($task->id)->andWhere('action')->eq('started')->fetch();
                                        $rows[] = (object)array('title' => str_repeat(' ' , 6) . $this->lang->task->exceltaskName . $task->title, 'id' => $task->id,'startTime' => isset($startTime->date) ? $startTime->date : '', 'pri' => $task->pri, 'assignedTo' => zget($users, $task->assignedTo));
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($tree->type == 'module')
            {

                $rows[] = (object)array('title' => $this->lang->task->module . $tree->name);
                if(isset($tree->children) and !empty($tree->children))
                {
                    foreach($tree->children as $tasks)
                    {
                        $rows[] = (object)array('title' => str_repeat(' ' , 2) . $this->lang->task->taskName . $tasks->title , 'assignedTo' => zget($users, $tasks->assignedTo),'pri' => $tasks->pri, 'id' => $tasks->id);
                        if(isset($tasks->children) and !empty($tasks->children))
                        {
                            foreach($tasks->children as $childrenTasks)
                            {
                                $startTime = $this->dao->select('date')->from(TABLE_ACTION)->where('objectType')->eq('task')->andWhere('objectID')->eq($childrenTasks->id)->andWhere('action')->eq('started')->fetch();
                                $rows[] = (object)array('title' => str_repeat(' ' , 4)  . $this->lang->task->taskName . $childrenTasks->title , 'id' => $childrenTasks->id,'startTime' => isset($startTime->date) ? $startTime->date : '', 'assignedTo' => zget($users, $childrenTasks->assignedTo), 'pri' => $childrenTasks->pri);
                            }
                        }
                    }
                }

            }

        }
        return $rows;
    }
}
