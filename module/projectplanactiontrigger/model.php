<?php

class projectplanactiontriggerModel extends model
{

    public function getList($browseType, $queryID, $orderBy, $pager, $secondLine = 0, $modelName = 'projectplanactiontrigger')
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
        $projectplans = $this->dao->select("*")->from(TABLE_PROJECTPLANACTION)
            ->where(1)
            ->andWhere('deleted')->eq(0)
            ->beginIF($browseType == 'bysearch')->andWhere($projectplanQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
//        $this->dao->printSQL();
//        exit();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'projectplanactiontrigger', $browseType != 'bysearch');

        return $projectplans;
    }


    public function acttagging($ID,$fileUrl=''){

        $acttagging = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->get();
        if(!$acttagging->snapshotVersion){
            dao::$errors['snapshotVersion'] = $this->lang->projectplanactiontrigger->snapshotVersionEmpty;
            return false;
        }
        $acttagInfo = $this->getByID($ID);

        $upData = [
            'snapshotVersion'=>$acttagging->snapshotVersion,
            'fileUrl'=>$fileUrl
        ];

        $this->dao->update(TABLE_PROJECTPLANACTION)->data($upData)->where("id")->eq($ID)->exec();

        if($acttagInfo->snapshotVersion){
            $acttagflag = false;
        }else{
            $acttagflag = true;
        }


        return ['snapshotVersion'=>$acttagging->snapshotVersion,'acttagflag'=>$acttagflag];

    }




    public function getByID($ID){
        return $this->dao->select("*")->from(TABLE_PROJECTPLANACTION)->where("id")->eq($ID)->andWhere('deleted')->eq(0)->fetch();
    }





    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->projectplanactiontrigger->search['actionURL'] = $actionURL;
        $this->config->projectplanactiontrigger->search['queryID']   = $queryID;
        $projectplanList = $this->loadModel("projectplan")->getAllList();
        $projectplanList = array_column($projectplanList,'name','id');
        $this->config->projectplanactiontrigger->search['params']['planID']['values'] = [''=>''] +$projectplanList;

        $this->loadModel('search')->setSearchParams($this->config->projectplanactiontrigger->search);
    }
}