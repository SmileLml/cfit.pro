<?php
include '../../control.php';
class myProject extends project
{
    public function exportList($orderBy = 'id_desc', $browseType = 'all')
    {

        $this->app->loadLang('projectplan');
        /* format the fields of every problem in order to export data. */
        if ($_POST) {
            $this->loadModel('file');
            $projectLang = $this->lang->project;
            $projectConfig = $this->config->project;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $projectConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($projectLang->$fieldName) ? $projectLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get problems. */
            $project = array();
            $projects = $this->dao->select('*')->from(TABLE_PROJECT)
                ->where('deleted')->eq('0')
                ->beginIF($this->session->projectQueryCondition)->andWhere($this->session->projectQueryCondition)->fi()
                ->orderBy($orderBy)
                ->fetchAll('id');

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');

            $projectCode = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in(array_keys($projects))->fetchPairs();

            $projectKeys = array_keys($projects);
            $stats       = array();
            $hours       = array();
            $emptyHour   = array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalLeft' => 0, 'progress' => 0);
            $leftTasks   = array();
            $teamMembers = array();

            $taskMap = array();
            $tasks = $this->dao->select('*')->from(TABLE_TASK)
                ->where('deleted')->eq(0)
                ->andWhere('project')->in($projectKeys)
                ->andWhere('parent' )->eq(0)
                //->andWhere('(parent != 0  or name like "%任务")' ) //20240528去掉此计算工时逻辑
                // ->andWhere('name')->notLike('%已%')
                ->fetchAll('id');
            foreach($tasks as $task)
            {
                if(!isset($taskMap[$task->project])) $taskMap[$task->project] = array('estimate' => 0, 'consumed' => 0, 'left' => 0, 'progress' => 0,'progresstFinsh' => 0, 'progresstTotal' => 0,'dataVersion' => '0');
                $taskMap[$task->project]['consumed'] = 0;
                $taskMap[$task->project]['left'] += $task->left;
                //完成百分比  已完成任务/所有任务
                if($task->status == 'done' || ($task->status == 'closed')){
                    $taskMap[$task->project]['progresstFinsh'] += 1;
                }
                $taskMap[$task->project]['progresstTotal'] += 1;
                $taskMap[$task->project]['dataVersion'] = $task->dataVersion;
            }

            //获取年度计划立项计划工作量
            $projectcreation = $this->dao->select('plan,workload')->from(TABLE_PROJECTCREATION)->where('deleted')->eq(0)->fetchPairs('plan','workload');
            $projectplan = $this->dao->select('project,id')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->andWhere('status')->eq('projected')->fetchPairs('project','id');
            $projectplaninsideStatus = $this->dao->select('project,insideStatus')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchPairs('project','insideStatus');
            $projectplanworkload = $this->dao->select('project,workload')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchPairs('project','workload');
            //20240528 更新计算工时逻辑，从基准工时表获取
            $projectConsumed = $this->loadModel('project')->getEffortConsumed();
            /* Process projects. */

            foreach($projects as $key => $project)
            {
                $project->code = isset($projectCode[$project->id]) ? $projectCode[$project->id] : '';
                if($project->begin == '0000-00-00') $project->begin = '';
                if($project->end == '0000-00-00') $project->end = '';

                /* Judge whether the project is delayed. */
                if($project->status != 'done' and $project->status != 'closed' and $project->status != 'suspended')
                {
                    $delay = helper::diffDate(helper::today(), $project->end);
                    if($delay > 0) $project->delay = $delay;
                }
                $project->planDuration = !empty(helper::diffDate($project->end, $project->begin) ) ? helper::diffDate($project->end, $project->begin) + 1 : '';
                $project->realDuration = ($project->realEnd != '0000-00-00' and $project->realBegan != '0000-00-00') ? helper::diffDate($project->realEnd, $project->realBegan)+1 : 0;

                /* Process the hours. */
                $project->estimate = isset($projectplan[$project->id])&&isset($projectcreation[$projectplan[$project->id]]) ? intval($projectcreation[$projectplan[$project->id]]) * $project->workHours * 8 : 0;//获取年度计划立项计划工作量 //isset($taskMap[$project->id]) ? $taskMap[$project->id]['estimate'] : 0;
                //迭代28-如果修改了计划工时，以修改的计划工时为准
                if(!empty($project->planWorkload)){
                    $project->estimate = intval($project->planWorkload) * $project->workHours * 8;
                }else{
                    /*$projectPlan = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('project')->eq($project->id)->andWhere('deleted')->eq(0)->fetch();
                    if(isset($projectPlan->id)){
                        $creation = $this->loadModel('projectplan')->getCreationByID($projectPlan->id);
                        $project->estimate = isset($creation->workload) ? intval($creation->workload) * $project->workHours * 8 : 0;
                    }else{*/
                        $project->estimate =  0;
                   // }

                }
                //$project->consumed = isset($taskMap[$project->id]) ? $taskMap[$project->id]['consumed'] : 0; //20240528去掉此计算工时逻辑
                //20240528 更新计算工时逻辑，从基准工时表获取
                $project->consumed  = isset($projectConsumed[$project->id]) ? $projectConsumed[$project->id]->consumed : 0;
                $project->left     = isset($taskMap[$project->id]) ? $taskMap[$project->id]['left'] : 0;
                if($project->status == 'closed'){
                    $project->progress = (isset($taskMap[$project->id]) and ($taskMap[$project->id]['progresstTotal'] != 0)) ? round($taskMap[$project->id]['progresstFinsh']/$taskMap[$project->id]['progresstTotal'],2)*100 : 0;//(isset($taskMap[$project->id]) and ($taskMap[$project->id]['estimate'] != 0)) ? round($taskMap[$project->id]['progress']/$taskMap[$project->id]['estimate']) : 0;
                }else{
                    if($taskMap[$task->project]['dataVersion'] == '2'){
                        $project->progress = (isset($taskMap[$project->id]) and ($taskMap[$project->id]['progresstTotal'] != 0)) ? round($taskMap[$project->id]['progresstFinsh']/$taskMap[$project->id]['progresstTotal'],2)*100 : 0;//(isset($taskMap[$project->id]) and ($taskMap[$project->id]['estimate'] != 0)) ? round($taskMap[$project->id]['progress']/$taskMap[$project->id]['estimate']) : 0;
                    }else{
                        $project->progress = 0;
                    }
                }

                $project->teamCount   = isset($teams[$project->id]) ? $teams[$project->id]->teams : 0;
                $project->leftTasks   = isset($leftTasks[$project->id]) ? $leftTasks[$project->id]->tasks : '—';
                $project->teamMembers = isset($teamMembers[$project->id]) ? array_keys($teamMembers[$project->id]) : array();
                $project->insideStatus = isset($projectplaninsideStatus[$project->id]) ? $projectplaninsideStatus[$project->id] : '';
                $project->workload = isset($projectplanworkload[$project->id]) ? $projectplanworkload[$project->id] : '';
                $stats[$key] = $project;
            }
            foreach ($projects as $key => $project) {
                if($project->type !='project') {
                    unset($projects[$key]);
                    continue;
                }

                $project->id =  $project->id;
                $project->name = $project->name;
                $project->code = $project->code;
                $project->PM = zget($users, $project->PM);
                $project->begin = $project->begin;
                $project->end = $project->end;
                $project->planDuration = $project->planDuration;
                $project->workload = $project->workload;
                $project->realBegan = $project->realBegan == '0000-00-00' ? '' : $project->realBegan;
                $project->realEnd = $project->realEnd == '0000-00-00' ? '' : $project->realEnd;
                $project->realDuration = ($project->realEnd == '0000-00-00' || $project->realBegan =='0000-00-00') ? '' : $project->realDuration;
                $project->diffDuration =($project->realEnd == '0000-00-00' || $project->realBegan =='0000-00-00' || !$project->planDuration) ? '' : $project->realDuration - $project->planDuration;
                $project->planHour = number_format($project->estimate/(8*$project->workHours), 1);
                $project->realHour =  number_format($project->consumed/(8*$project->workHours), 1);
                $project->diffHour = number_format($project->consumed - $project->estimate,1);
                $project->complete = empty($project->progress) ? '0%' : $project->progress . '%';
                $project->status =  zget($this->lang->project->featureBar, $project->status, '');
                $project->insideStatus = zget($this->lang->projectplan->insideStatusList, $project->insideStatus);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $projects);
            $this->post->set('kind', 'project');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName = $this->lang->project->exportName;
        $this->view->allExportFields = $this->config->project->list->exportFields;
        $this->view->customExport = true;
        $this->display();
    }
}