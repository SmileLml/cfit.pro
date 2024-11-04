<?php
class secondline extends control
{
    /* Process historical correlation data of production changes. */
    public function processModify()
    {
        $historyData = $this->dao->select('id,problem,demand')->from(TABLE_MODIFY)->fetchAll();
        foreach($historyData as $data)
        {
            $this->secondline->saveRelationship($data->id, 'modify', $data->problem, 'problem');
            $this->secondline->saveRelationship($data->id, 'modify', $data->demand, 'demand');
        }
        echo 'A total of ' . count($historyData) . ' were processed.';
    }

    /* Process historical associated data for data correction. */
    public function processFix()
    {
        $historyData = $this->dao->select('id,problem,demand')->from(TABLE_INFO)->where('action')->eq('fix')->fetchAll();
        foreach($historyData as $data)
        {
            $this->secondline->saveRelationship($data->id, 'fix', $data->problem, 'problem');
            $this->secondline->saveRelationship($data->id, 'fix', $data->demand, 'demand');
        }
        echo 'A total of ' . count($historyData) . ' were processed.';
    }

    /* Process historical association data obtained from data. */
    public function processGain()
    {
        $historyData = $this->dao->select('id,problem,demand')->from(TABLE_INFO)->where('action')->eq('gain')->fetchAll();
        foreach($historyData as $data)
        {
            $this->secondline->saveRelationship($data->id, 'gain', $data->problem, 'problem');
            $this->secondline->saveRelationship($data->id, 'gain', $data->demand, 'demand');
        }
        echo 'A total of ' . count($historyData) . ' were processed.';
    }

    /* Handle the relationship between products and production changes. */
    public function processProductCode()
    {
        $modifyList = $this->dao->select('id,productCode')->from(TABLE_MODIFY)->where('status')->ne('deleted')->fetchPairs();
        foreach($modifyList as $modifyID => $code)
        {
            $this->dao->delete()->from(TABLE_PRODUCTCODE)->where('modify')->eq($modifyID)->exec();
        }

        foreach($modifyList as $modifyID => $codes)
        {
            if(!empty($codes))
            {
                $codeList = json_decode($codes);
                foreach($codeList as $code)
                {
                    $recordCode = new stdClass();
                    $recordCode->product = $code->assignProduct;
                    $recordCode->modify  = $modifyID;
                    $recordCode->code    = json_encode($code);
                    $this->dao->insert(TABLE_PRODUCTCODE)->data($recordCode)->exec();
                }
            }
        }

        echo 'A total of ' . count($modifyList) . ' were processed.';
    }

    /**
     * Project: chengfangjinke
     * Method: processProject
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:49
     * Desc: This is the code comment. This method is called processProject.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function processProject()
    {
        $historyData = $this->dao->select('id,project')->from(TABLE_INFO)->where('action')->eq('gain')->fetchAll();
        foreach($historyData as $data)
        {
            $this->secondline->saveRelationship($data->id, 'projectGain', $data->project, 'project');
        }

        $historyData = $this->dao->select('id,project')->from(TABLE_INFO)->where('action')->eq('fix')->fetchAll();
        foreach($historyData as $data)
        {
            $this->secondline->saveRelationship($data->id, 'projectFix', $data->project, 'project');
        }

        $historyData = $this->dao->select('id,project,problem,demand')->from(TABLE_MODIFY)->fetchAll();
        foreach($historyData as $data)
        {
            $this->secondline->saveRelationship($data->id, 'projectModify', $data->project, 'project');

            $oldModify = $data;
            if($oldModify->problem)
            {
                $problemIdList = $oldModify->problem;
                if(!is_array($problemIdList)) $problemIdList = explode(',', $problemIdList);
                foreach($problemIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectProblem', '', 'project');
                }
            }

            if($oldModify->demand)
            {
                $demandIdList = $oldModify->demand;
                if(!is_array($demandIdList)) $demandIdList = explode(',', $demandIdList);
                foreach($demandIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectDemand', '', 'project');
                }
            }

            $modify = $data;
            if($modify->problem)
            {
                $problemIdList = $modify->problem;
                if(!is_array($problemIdList)) $problemIdList = explode(',', $problemIdList);
                foreach($problemIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectProblem', $modify->project, 'project');
                }
            }

            if($modify->demand)
            {
                $demandIdList = $modify->demand;
                if(!is_array($demandIdList)) $demandIdList = explode(',', $demandIdList);
                foreach($demandIdList as $relationID)
                {
                    if(empty($relationID)) continue;
                    $this->secondline->saveRelationship($relationID, 'projectDemand', $modify->project, 'project');
                }
            }
        }

        //$historyData = $this->dao->select('id,project')->from(TABLE_PROBLEM)->fetchAll();
        //foreach($historyData as $data)
        //{
        //    $this->secondline->saveRelationship($data->id, 'projectProblem', $data->project, 'project');
        //}

        //$historyData = $this->dao->select('id,project')->from(TABLE_DEMAND)->fetchAll();
        //foreach($historyData as $data)
        //{
        //    $this->secondline->saveRelationship($data->id, 'projectDemand', $data->project, 'project');
        //}

        echo 'exec success.';
    }

    /* 处理旧的问题数据最后处理时间字段数据。*/
    public function processProblemDeal()
    {
        $problems = $this->dao->select('id')->from(TABLE_PROBLEM)->fetchAll();
        foreach($problems as $problem)
        {
            $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('problem')->andWhere('objectID')->eq($problem->id)->orderBy('id_desc')->fetch();
            if(!empty($bestDeal))
            {
                $bestDate = substr($bestDeal->createdDate, 0, 10);
                $this->dao->update(TABLE_PROBLEM)->set('lastDealDate')->eq($bestDate)->where('id')->eq($problem->id)->exec();
            }
        }
        echo 'A total of ' . count($problems) . ' were processed.';
    }

    /* 处理旧的需求数据最后处理时间字段数据。*/
    public function processDemandDeal()
    {
        $demands = $this->dao->select('id')->from(TABLE_DEMAND)->fetchAll();
        foreach($demands as $demand)
        {
            $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('demand')->andWhere('objectID')->eq($demand->id)->orderBy('id_desc')->fetch();
            if(!empty($bestDeal))
            {
                $bestDate = substr($bestDeal->createdDate, 0, 10);
                $this->dao->update(TABLE_DEMAND)->set('lastDealDate')->eq($bestDate)->where('id')->eq($demand->id)->exec();
            }
        }
        echo 'A total of ' . count($demands) . ' were processed.';
    }

    /* 处理旧的生产变更数据最后处理时间字段数据。*/
    public function processModifyDeal()
    {
        $modifys = $this->dao->select('id')->from(TABLE_MODIFY)->fetchAll();
        foreach($modifys as $modify)
        {
            $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('modify')->andWhere('objectID')->eq($modify->id)->orderBy('id_desc')->fetch();
            if(!empty($bestDeal))
            {
                $bestDate = substr($bestDeal->createdDate, 0, 10);
                $this->dao->update(TABLE_MODIFY)->set('lastDealDate')->eq($bestDate)->where('id')->eq($modify->id)->exec();
            }
        }
        echo 'A total of ' . count($modifys) . ' were processed.';
    }

    /* 处理旧的数据修正和数据获取数据最后处理时间字段数据。*/
    public function processInfoDeal()
    {
        $infos = $this->dao->select('id')->from(TABLE_INFO)->fetchAll();
        foreach($infos as $info)
        {
            $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('info')->andWhere('objectID')->eq($info->id)->orderBy('id_desc')->fetch();
            if(!empty($bestDeal))
            {
                $bestDate = substr($bestDeal->createdDate, 0, 10);
                $this->dao->update(TABLE_INFO)->set('lastDealDate')->eq($bestDate)->where('id')->eq($info->id)->exec();
            }
        }
        echo 'A total of ' . count($infos) . ' were processed.';
    }

    /* 处理旧的项目计划最后处理时间字段数据。*/
    public function processProjectPlanDeal()
    {
        $plans = $this->dao->select('id')->from(TABLE_PROJECTPLAN)->fetchAll();
        foreach($plans as $plan)
        {
            $bestDeal = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('projectplan')->andWhere('objectID')->eq($plan->id)->orderBy('id_desc')->fetch();
            if(!empty($bestDeal))
            {
                $bestDate = substr($bestDeal->createdDate, 0, 10);
                $this->dao->update(TABLE_PROJECTPLAN)->set('lastDealDate')->eq($bestDate)->where('id')->eq($plan->id)->exec();
            }
        }
        echo 'A total of ' . count($plans) . ' were processed.';
    }

    /* 处理旧的需求条目状态为评审中。*/
    public function processRequirementStatus()
    {
        $this->loadModel('review');
        $requirements = $this->dao->select('id,changeVersion')->from(TABLE_REQUIREMENT)->fetchAll();
        foreach($requirements as $requirement)
        {
            $reviewer = $this->review->getReviewer('requirement', $requirement->id, $requirement->changeVersion);
            if(!empty($reviewer))
            {
                $this->dao->update(TABLE_REQUIREMENT)->set('status')->eq('reviewing')->where('id')->eq($requirement->id)->exec();
            }

            $bestDeal = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->eq('requirement')->andWhere('objectID')->eq($requirement->id)->orderBy('id_desc')->fetch();
            if(!empty($bestDeal))
            {
                $bestDate = substr($bestDeal->createdDate, 0, 10);
                $this->dao->update(TABLE_REQUIREMENT)->set('changedDate')->eq($bestDate)->where('id')->eq($requirement->id)->exec();
                $this->dao->update(TABLE_REQUIREMENTSPEC)->set('changedDate')->eq($bestDate)->where('requirement')->eq($requirement->id)->exec();
            }
        }
        echo 'A total of ' . count($requirements) . ' were processed.';
    }
}
