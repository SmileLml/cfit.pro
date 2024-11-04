<?php
class ganttExecution extends executionModel
{
    public function createRelationOfTasks($executionID)
    {
        $relations = fixer::input('post')->get();
        $data->execution = $executionID;
        foreach($relations->id as $id)
        {
            if($relations->pretask[$id] != '' and $relations->condition[$id] != '' and $relations->task[$id] != '' and $relations->action[$id] != '')
            {
                $data->pretask   = $relations->pretask[$id];
                $data->condition = $relations->condition[$id];
                $data->task      = $relations->task[$id];
                $data->action    = $relations->action[$id];

                $this->dao->insert(TABLE_RELATIONOFTASKS)->data($data)->exec();
            }
        }
    }

    public function editRelationOfTasks($executionID)
    {
        $relations = fixer::input('post')->get();
        $data->execution = $executionID;

        /*Whether there is conflict between the judgment task relations.*/
        foreach($relations->pretask as $id => $pretask)
        {
            if(empty($pretask)) continue;
            if($pretask == $relations->task[$id])die(js::alert(sprintf($this->lang->execution->gantt->warning->noEditSame, $id)));
            foreach($relations->pretask as $newid => $newpretask)
            {
                if($newid != $id and $pretask == $newpretask and $relations->task[$id] == $relations->task[$newid]) die(js::alert(sprintf($this->lang->execution->gantt->warning->noEditRepeat, $id, $newid)));
                if($newid != $id and $relations->task[$id] == $newpretask and $pretask == $relations->task[$newid]) die(js::alert(sprintf($this->lang->execution->gantt->warning->noEditContrary, $id, $newid)));
            }
            foreach($relations->newpretask as $newid => $newpretask)
            {
                if(empty($newpretask)) continue;
                if($newpretask == $pretask and $relations->task[$id] == $relations->newtask[$newid]) die(js::alert(sprintf($this->lang->execution->gantt->warning->noRepeat, $id, $newid)));
                if($relations->task[$id] == $newpretask and $pretask == $relations->newtask[$newid]) die(js::alert(sprintf($this->lang->execution->gantt->warning->noContrary, $id, $newid)));
            }
        }
        foreach($relations->newpretask as $id => $pretask)
        {
            if(empty($pretask)) continue;
            if($pretask == $relations->newtask[$id])die(js::alert(sprintf($this->lang->execution->gantt->warning->noNewSame, $id)));
            foreach($relations->newpretask as $newid => $newpretask)
            {
                if(empty($pretask)) continue;
                if($newid != $id and $pretask == $newpretask and $relations->newtask[$id] == $relations->newtask[$newid]) die(js::alert(sprintf($this->lang->execution->gantt->warning->noNewRepeat, $id, $newid)));
                if($newid != $id and $relations->newtask[$id] == $newpretask and $pretask == $relations->newtask[$newid]) die(js::alert(sprintf($this->lang->execution->gantt->warning->noNewContrary, $id, $newid)));
            }
        }

        /* update relations.*/
        foreach($relations->id as $id)
        {
            if($relations->pretask[$id] != '' and $relations->condition[$id] != '' and $relations->task[$id] != '' and $relations->action[$id] != '')
            {
                $data->pretask   = $relations->pretask[$id];
                $data->condition = $relations->condition[$id];
                $data->task      = $relations->task[$id];
                $data->action    = $relations->action[$id];

                $this->dao->update(TABLE_RELATIONOFTASKS)->data($data)->where('id')->eq($id)->exec();
            }
        }

        /* create new relations.*/
        foreach($relations->newid as $id)
        {
            if($relations->newpretask[$id] != '' and $relations->newcondition[$id] != '' and $relations->newtask[$id] != '' and $relations->newaction[$id] != '')
            {
                $data->pretask   = $relations->newpretask[$id];
                $data->condition = $relations->newcondition[$id];
                $data->task      = $relations->newtask[$id];
                $data->action    = $relations->newaction[$id];

                $this->dao->insert(TABLE_RELATIONOFTASKS)->data($data)->exec();
            }
        }
    }

    public function getRelationsOfTasks($executionID)
    {
        $relations = $this->dao->select('*')->from(TABLE_RELATIONOFTASKS)->where('execution')->eq($executionID)->fetchAll('id');
        return $relations;
    }

    public function getDataForGantt($executionID, $type)
    {
        $this->app->loadLang('task');
        $relations  = $this->dao->select('*')->from(TABLE_RELATIONOFTASKS)->where('execution')->eq($executionID)->fetchGroup('task', 'pretask');
        $taskGroups = $this->dao->select('t1.*, t2.realname,t3.branch')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.assignedTo = t2.account')
            ->leftJoin(TABLE_STORY)->alias('t3')->on('t1.story = t3.id')
            ->where('t1.execution')->eq($executionID)
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy("{$type}_asc,id_asc")
            ->fetchGroup($type ,'id');

        $products     = $this->getProducts($executionID, $withBranch = false);
        $branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products));
        $branches     = array();
        foreach($branchGroups as $product => $productBranch)
        {
            foreach($productBranch as $branchID => $branchName) $branches[$branchID] = $branchName;
        }

        $execution  = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->fetch();
        if($type == 'story') $stories = $this->dao->select('*')->from(TABLE_STORYSPEC)->where('story')->in(array_keys($taskGroups))->fetchGroup('story', 'version');
        if($type == 'module')
        {
            $showAllModule = isset($this->config->execution->task->allModule) ? $this->config->execution->task->allModule : '';
            $modules       = $this->loadModel('tree')->getTaskOptionMenu($executionID, 0, 0, $showAllModule ? 'allModule' : '');
            $orderedGroup  = array();
            foreach($modules as $moduleID => $moduleName)
            {
                if(isset($taskGroups[$moduleID])) $orderedGroup[$moduleID] = $taskGroups[$moduleID];
            }
            $taskGroups = $orderedGroup;
        }
        if($type == 'assignedTo') $users = $this->loadModel('user')->getPairs('noletter');

        $groupID    = 0;
        $ganttGroup = array();

        foreach($taskGroups as $group => $tasks)
        {
            $groupID --;
            $groupName = $group;
            if($type == 'type')   $groupName = zget($this->lang->task->typeList, $group);
            if($type == 'module') $groupName = zget($modules, $group);
            if($type == 'assignedTo') $groupName = zget($users, $group);
            if($type == 'story')
            {
                $task = current($tasks);
                if(isset($stories[$group][$task->storyVersion]))
                {
                    $story = $stories[$group][$task->storyVersion];
                    $groupName = $story->title;
                    unset($taskGroups[$group]);
                    $group = $groupName;
                }
                if((string)$groupName === '0') $groupName = $this->lang->task->noStory;
            }

            $data             = new stdclass();
            $data->id         = $groupID;
            $data->text       = $groupName;
            $data->start_date = '';
            $data->deadline   = '';
            $data->priority   = '';
            $data->owner_id   = '';
            $data->progress   = '';
            $data->parent     = 0;
            $data->open       = true;

            $groupKey = $type == 'story' ? $groupID : $groupID . $group;
            $ganttGroup[$groupKey]['common'] = $data;

            $totalConsumed = 0;
            $totalHours    = 0;
            $minStartDate  = '';
            $maxDeadline   = '';
            foreach($tasks as $id => $task)
            {
                $ganttItem = $this->buildGanttItem(($task->parent > 0 and isset($tasks[$task->parent])) ? $task->parent : $groupID, $task, $execution, $branches);
                $ganttGroup[$groupKey][$id] = $ganttItem;

                $totalConsumed += $task->consumed;
                $totalHours    += $task->left + $task->consumed;

                if(empty($minStartDate)) $minStartDate = $ganttItem->start_date;
                if(strtotime($ganttItem->start_date) < strtotime($minStartDate)) $minStartDate = $ganttItem->start_date;

                if(empty($maxDeadline)) $maxDeadline = $ganttItem->deadline;
                if(strtotime($ganttItem->deadline) > strtotime($maxDeadline)) $maxDeadline = $ganttItem->deadline;
            }

            $ganttGroup[$groupKey]['common']->progress   = $totalHours == 0 ? 0 : round($totalConsumed / $totalHours, 4);
            $ganttGroup[$groupKey]['common']->start_date = $minStartDate;
            $ganttGroup[$groupKey]['common']->deadline   = $maxDeadline;
        }
        if($type == 'story') krsort($ganttGroup);

        $execution = array();
        foreach($ganttGroup as $groupID => $tasks)
        {
            foreach($tasks as $task) $execution['data'][] = $task;
        }

        foreach($relations as $taskID => $preTasks)
        {
            foreach($preTasks as $preTask => $relation)
            {
                $link['source'] = $preTask;
                $link['target'] = $taskID;
                $link['type']   = $this->config->execution->gantt->linkType[$relation->condition][$relation->action];
                $execution['links'][] = $link;
            }
        }

        return json_encode($execution);
    }

    public function buildGanttItem($groupID, $task, $execution, $branches)
    {
        $start = '';
        if(helper::isZeroDate($task->realStarted) and helper::isZeroDate($task->estStarted))
        {
            $start = date('d-m-Y', strtotime($execution->begin));
        }
        else
        {
            $start = helper::isZeroDate($task->realStarted) ? $task->estStarted : $task->realStarted;
            $start = date('d-m-Y', strtotime($start));
        }

        $end = '';
        $end = helper::isZeroDate($task->deadline) ? $execution->end : $task->deadline;
        $end = (in_array($task->status, array('done', 'closed')) and !helper::isZeroDate($task->finishedDate)) ? $task->finishedDate : $end;
        $end = date('Y-m-d', strtotime($end));

        $name = '#' . $task->id . ' ';
        if($task->branch and isset($branches[$task->branch])) $name .= "<span class='label label-info'>{$branches[$task->branch]}</span> ";
        $name .= $task->name;

        $data             = new stdclass();
        $data->id         = $task->id;
        $data->text       = $name;
        $data->start_date = $start;
        $data->deadline   = $end;
        $data->pri        = $task->pri;
        $data->duration   = helper::diffDate($end, $start) + 1;
        $data->owner_id   = $task->assignedTo;
        $data->progress   = ($task->consumed + $task->left) == 0 ? 0 : round($task->consumed / ($task->consumed + $task->left), 4);
        $data->parent     = $groupID;
        $data->open       = true;

        return $data;
    }

    public function deleteRelation($id)
    {
        $this->dao->delete()->from(TABLE_RELATIONOFTASKS)->where('id')->eq($id)->exec();
    }
}
