<?php

class projectplanmsrelationModel extends model
{

    public function getList($browseType, $queryID, $orderBy, $pager, $secondLine = 0, $modelName = 'projectplanmsrelation')
    {

        $projectplanQuery = '';
        if ($browseType == 'bysearch') {

            /** @var searchModel $searchModel */
            $searchModel = $this->loadModel('search');
            $query       = $queryID ? $searchModel->getQuery($queryID) : '';

            if ($query) {
                $this->session->set($modelName . 'Query', $query->sql);
                $this->session->set($modelName . 'Form', $query->form);
            }
            $modelNameQuery = $modelName . 'Query';
            if ($this->session->$modelNameQuery == false) $this->session->set($modelName . 'Query', ' 1 = 1');

            $projectplanQuery = $this->session->$modelNameQuery;
        }

        //待处理人搜索
        $projectplans = $this->dao->select("*")->from(TABLE_PROJECTPLANMSRELATION)
            ->where(1)
            ->andWhere('deleted')->eq(0)
            ->beginIF($browseType == 'bysearch')->andWhere($projectplanQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
//        $this->dao->printSQL();
//        exit();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'projectplanmsrelation', $browseType != 'bysearch');

        return $projectplans;
    }


    public function maintenanceRelation(){

        $projectplanrelation = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->get();
        if(!$projectplanrelation->mainPlanID){
            dao::$errors['mainPlanID'] = $this->lang->projectplanmsrelation->mainPlanIDEmpty;
            return false;
        }
        if(!$projectplanrelation->slavePlanID[0]){
            dao::$errors['slavePlanID'] = $this->lang->projectplanmsrelation->slavePlanIDEmpty;
            return false;
        }
        if(in_array($projectplanrelation->mainPlanID,$projectplanrelation->slavePlanID)){
            dao::$errors[] = $this->lang->projectplanmsrelation->mainInslaveError;
            return false;
        }

        $relationInfo = $this->getByMainPlanID($projectplanrelation->mainPlanID);

        //如果存在则更新
        if($relationInfo){

            unset($projectplanrelation->createdBy);
            unset($projectplanrelation->createdDate);

            $projectplanrelation->editedBy = $this->app->user->account;
            $projectplanrelation->editedDate = helper::now();
            $projectplanrelation->slavePlanID = trim(implode(',',$projectplanrelation->slavePlanID),',');

            $this->dao->update(TABLE_PROJECTPLANMSRELATION)->data($projectplanrelation)->where("id")->eq($relationInfo->id)->exec();
            $newrelationInfo = $this->getByMainPlanID($projectplanrelation->mainPlanID);
            return ['type'=>'update','dataID'=>$relationInfo->id,'changes'=>common::createNewChanges($relationInfo,$newrelationInfo)];
        }else{

            //新增
            $projectplanrelation->slavePlanID = trim(implode(',',$projectplanrelation->slavePlanID),',');
            $projectplanrelation->editedBy = $this->app->user->account;
            $projectplanrelation->editedDate = helper::now();
            $this->dao->insert(TABLE_PROJECTPLANMSRELATION)->data($projectplanrelation)->exec();
            return ['type'=>'create','dataID'=>$this->dao->lastInsertID(),'changes'=>[]];

        }

    }

    public function getByMainPlanID($mainPlanID){

        return $this->dao->select("*")->from(TABLE_PROJECTPLANMSRELATION)->where("mainPlanID")->eq($mainPlanID)->andWhere('deleted')->eq(0)->fetch();
    }

    public function getBySlavePlanID($slavePlanID){

        return $this->dao->select("*")->from(TABLE_PROJECTPLANMSRELATION)->where(" find_in_set('{$slavePlanID}',slavePlanID) ")->andWhere('deleted')->eq(0)->fetchAll();
    }


    public function getByID($ID){
        return $this->dao->select("*")->from(TABLE_PROJECTPLANMSRELATION)->where("id")->eq($ID)->andWhere('deleted')->eq(0)->fetch();
    }

    public function update($relationID){
        $params = fixer::input('post')
            ->get();
        $relationInfo = $this->getByID($relationID);
        if(!$relationInfo){
            dao::$errors[] = $this->lang->projectplanmsrelation->relationInfoEmpty;
            return false;
        }
        if(!isset($params->slavePlanID) || (isset($params->slavePlanID) && !$params->slavePlanID[0])){
            dao::$errors['slavePlanID'] = $this->lang->projectplanmsrelation->slavePlanIDEmpty;
            return false;
        }

        if(in_array($relationInfo->mainPlanID,$params->slavePlanID)){
            dao::$errors[] = $this->lang->projectplanmsrelation->mainInslaveError;
            return false;
        }


        $params->editedBy    = $this->app->user->account;
        $params->editedDate  = helper::now();
        $params->slavePlanID = implode(',', $params->slavePlanID);

        $this->dao->update(TABLE_PROJECTPLANMSRELATION)->data($params)->where("id")->eq($relationID)->exec();
        $newrelationInfo = $this->getByID($relationID);
        return common::createNewChanges($relationInfo, $newrelationInfo);


    }

    public function ajaxGetSlaveProjectplan(){
        $params = fixer::input('post')
            ->get();
        if(!isset($params->mainPlanID)){
            return [];
        }

        if(!$params->mainPlanID){
            return [];
        }
        return $this->dao->select("*")->from(TABLE_PROJECTPLANMSRELATION)->where("mainPlanID")->eq($params->mainPlanID)->andWhere('deleted')->eq(0)->fetch();

    }

    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->projectplanmsrelation->search['actionURL'] = $actionURL;
        $this->config->projectplanmsrelation->search['queryID']   = $queryID;
        $projectplanList = $this->loadModel("projectplan")->getAllList();
        $projectplanList = array_column($projectplanList,'name','id');
        $this->config->projectplanmsrelation->search['params']['mainPlanID']['values'] = [''=>''] +$projectplanList;

        $this->loadModel('search')->setSearchParams($this->config->projectplanmsrelation->search);
    }
}