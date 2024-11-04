<?php
include '../../control.php';
class myProgramPlan extends programplan
{
    /**
     * Batch change programplan.
     *
     * @param int $projectID
     * @access public
     * @return void
     */
    public function batchchange($projectID,$flag = null)
    {
        if($_POST)
        { 
	   // 获取项目关联的所有产品ID。
            $this->loadModel('execution');
            $requiredFields = explode(',', $this->config->programplan->create->requiredFields);
            $i = 0;
            $data = fixer::input('post')->get();
            //检查数据是否为空
            foreach($data->name as $id => $name)
            {
                $i++;
                $plan               = new stdClass();
                $plan->project      = $projectID;
                $plan->name         = $name;
                $plan->begin        = $data->begin[$id];
                $plan->end          = $data->end[$id];
                $plan->realBegan    = $data->realBegan[$id];
                $plan->realEnd      = $data->realEnd[$id];
                $plan->planDuration = $data->planDuration[$id];
                $plan->milestone    = $data->milestone[$id];
                $plan->grade        = $data->parent[$id] > 0 ? 2 : 1;
                $plan->realBegan    = $plan->realBegan == '0000-00-00' ? '' : $plan->realBegan;
                $plan->realEnd      = $plan->realEnd   == '0000-00-00' ? '' : $plan->realEnd;
                $plan->order        = $i * 5;
                $alertMessage = '';
                foreach($requiredFields as $field)
                {
                    $field = trim($field);
                    if($field and empty($plan->$field))
                    {
                        $alertMessage .= sprintf($this->lang->error->notempty, $this->lang->programplan->$field) . '\n';
                    }
                }
                if($alertMessage ) die(js::alert(sprintf($this->lang->programplan->batchErrorAlert, $i) . '\n' . $alertMessage));
                $plans[$id] = $plan;

                // 计划开始时间  计划完成时间验证
                if (strtotime($plan->begin) > strtotime($plan->end)) {
                    die(js::alert(sprintf($this->lang->programplan->dateError, $i )));
                }
                //实际开始时间  实际完成时间
                if($plan->realBegan && $plan->realEnd){
                    if(strtotime($plan->realBegan) > strtotime($plan->realEnd))
                    {
                        die(js::alert(sprintf($this->lang->programplan->realdateError, $i)));
                    }
                }
            }
                // 获取项目关联的所有产品ID。
                $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();

            $parentMap = array();
            $i = 0;
            $now = helper::today();
            foreach($plans as $id => $plan)
            {
                $i++;
                $oldPlan = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($id)->fetch();
                // 判断是新增还是更新。
                if(empty($oldPlan))
                {
                    $pid                = $data->parent[$id];
                    $plan->parent       = $pid > 999999 ? $parentMap[$pid] : $pid;
                    $plan->type         = 'stage';
                    $plan->openedBy     = $this->app->user->account;
                    $plan->openedDate   = $now;
                    $plan->status       = 'wait';
                    $plan->version      = 1;
                    $plan->dataVersion  = $flag ? $flag : '1';
                    $this->dao->insert(TABLE_EXECUTION)->data($plan)->autoCheck()->exec();

                    if(dao::isError())
                    {
                        $alertMessage = '';
                        foreach(dao::getError() as $item)
                        {
                            is_array($item) ? $alertMessage .= join('\n', $item) . '\n' : $alertMessage .= $item . '\n';
                        }
                        die(js::alert(sprintf($this->lang->programplan->batchErrorAlert, $i) . '\n' . $alertMessage));
                    }

                    $executionID = $this->dao->lastInsertID();
                    if($id > 999999) $parentMap[$id] = $executionID;

                    //记录到版本库
                    $spec               = new stdclass();
                    $spec->execution    = $executionID;
                    $spec->version      = 1;
                    $spec->name         = $plan->name;
                    $spec->milestone    = $plan->milestone;
                    $spec->begin        = $plan->begin;
                    $spec->end          = $plan->end;
                    $spec->planDuration = $plan->planDuration;
                    $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

                    // 为执行关联项目的产品。
                    if(!empty($products))
                    {
                        $_POST['products'] = $products;
                        $this->execution->updateProducts($executionID);
                        unset($_POST['products']);
                    }

                    $path = ',' . $projectID . ',' . ($plan->grade == 2 ? $plan->parent  . ',' : '') . $executionID . ',';
                    $this->dao->update(TABLE_EXECUTION)->set('path')->eq($path)->where('id')->eq($executionID)->exec();
                }
                else
                {
                    $planChanged = ($oldPlan->name != $plan->name || $oldPlan->milestone != $plan->milestone || $oldPlan->begin != $plan->begin || $oldPlan->end != $plan->end ||
                                    $oldPlan->realBegan != $plan->realBegan || $oldPlan->realEnd != $plan->realEnd || $oldPlan->planDuration != $plan->planDuration);
                    if($planChanged)  $plan->version = $oldPlan->version + 1;

                    $rowCount = $this->dao->update(TABLE_EXECUTION)->data($plan)->autoCheck()->where('id')->eq($id)->exec();
                    if(dao::isError())
                    {
                        $alertMessage = '';
                        foreach(dao::getError() as $item)
                        {
                            is_array($item) ? $alertMessage .= join('\n', $item) . '\n' : $alertMessage .= $item . '\n';
                        }
                        die(js::alert(sprintf($this->lang->programplan->batchErrorAlert, $i) . '\n' . $alertMessage));
                    }

                    $changes  = common::createChanges($oldPlan, $plan);
                    if($changes || $this->post->comment)
                    {
                        $actionID = $this->loadModel('action')->create('execution', $id, 'changed', $this->post->comment);
                        $this->action->logHistory($actionID, $changes);
                    }

                    if($planChanged)
                    {
                        $spec               = new stdclass();
                        $spec->execution    = $id;
                        $spec->version      = $plan->version;
                        $spec->name         = $plan->name;
                        $spec->milestone    = $plan->milestone;
                        $spec->begin        = $plan->begin;
                        $spec->end          = $plan->end;
                        $spec->realBegan    = $plan->realBegan;
                        $spec->realEnd      = $plan->realEnd;
                        $spec->planDuration = $plan->planDuration;
                        $spec->code         = $oldPlan->code;
                        $spec->desc         = $oldPlan->desc;
                        $this->dao->insert(TABLE_EXECUTIONSPEC)->data($spec)->exec();

                        // 记录最后变更时间和用户。
                        $change             = new stdClass();
                        $change->changeBy   = $this->app->user->account;
                        $change->changeDate = helper::now();
                        $this->dao->update(TABLE_EXECUTION)->data($change)->where('id')->eq($id)->exec();
                    }
                }
            }
            die(js::locate($this->createLink('project', 'execution', "status=all&projectID=$projectID"), 'parent'));
        }

        $this->loadModel('project')->setMenu($projectID);
        $stages = $this->project->getStagesByProject($projectID,  false,  'order_asc',$flag);
        foreach($stages as $id => $stage)
        {
            $stage->realBegan = $stage->realBegan == '0000-00-00' ? '' : $stage->realBegan;
            $stage->realEnd   = $stage->realEnd   == '0000-00-00' ? '' : $stage->realEnd;
        }
        //如果空内置两行 一个一级阶段一个二级阶段
        if(empty($stages))
        {
            $stages = array();
            $id = 999999;
            for($i = 1; $i<= 2; $i++)
            {
                $stage = new stdClass();
                $stage->id = $id + $i;
                $stage->name = '';
                $stage->grade = $i;
                $stage->parent = $i == 1 ? 0 : 1000000;
                $stage->path = ($i == 1 ? ',1,' : ',1,1000000,') . $stage->id . ',';
                $stage->order = $i * 5;
                $stage->planDuration = '';
                $stage->milestone = 0;
                $stage->begin = '';
                $stage->end = '';
                $stage->realBegan = '';
                $stage->realEnd = '';
                $stages[$stage->id] = $stage;
            }
        }

        $this->view->stages = $stages;
        $this->view->title= $this->lang->programplan->batchChange;
        $this->view->flag= $flag;
        $this->display();
    }
}
