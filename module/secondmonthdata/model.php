<?php
class secondmonthdataModel extends model
{
    public function getProblemList($browseType, $queryID, $orderBy, $pager,$modelName = 'secondmonthdata'){
        $secondmonthdataQuery = '';
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

            $secondmonthdataQuery = $this->session->$modelNameQuery;
        }
        $dataList = $this->dao->select("t1.id,t1.sourceyear,t1.sourcetype,t1.objectid,t1.createTime,t1.updateTime,t1.deleted,t2.code,t2.status,t2.app,t2.abstract,t2.dealAssigned,t2.solvedTime,t2.acceptDept,t2.acceptUser,t2.source,t2.type")->from(TABLE_SECONDMONTHHISTORYDATA)->alias('t1')
            ->leftJoin(TABLE_PROBLEM)->alias("t2")->on('t1.objectid = t2.id')
            ->where("t1.sourcetype")->eq('problem')
            ->andWhere('t2.status')->ne('deleted')
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($browseType == 'bysearch')->andWhere($secondmonthdataQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), $modelName, $browseType != 'bysearch');
        return $dataList;
    }

    public function getDemandList($browseType, $queryID, $orderBy, $pager,$modelName = 'secondmonthdata'){
        $secondmonthdataQuery = '';
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

            $secondmonthdataQuery = $this->session->$modelNameQuery;
        }
        $dataList = $this->dao->select("t1.id,t1.sourceyear,t1.sourcetype,t1.objectid,t1.createTime,t1.updateTime,t1.deleted,t2.code,t2.status,t2.app,t2.title,t3.newPublishedTime,t2.solvedTime,t2.acceptDept,t2.acceptUser,t2.createdBy,t2.createdDate,t2.fixType,t3.actualMethod")->from(TABLE_SECONDMONTHHISTORYDATA)->alias('t1')
            ->leftJoin(TABLE_DEMAND)->alias("t2")->on('t1.objectid = t2.id')
            ->leftJoin(TABLE_REQUIREMENT)->alias("t3")->on('t2.requirementID = t3.id')
            ->where("t1.sourcetype")->eq('demand')
            ->andWhere('t2.status')->ne('deleted')
            ->andWhere('t2.sourceDemand')->eq(1) //查询外部的数据
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($browseType == 'bysearch')->andWhere($secondmonthdataQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), $modelName, $browseType != 'bysearch');
        return $dataList;
    }
    public function getSecondorderList($browseType, $queryID, $orderBy, $pager,$modelName = 'secondmonthdata'){
        $secondmonthdataQuery = '';
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

            $secondmonthdataQuery = $this->session->$modelNameQuery;
        }
        $dataList = $this->dao->select("t1.id,t1.sourceyear,t1.sourcetype,t1.objectid,t1.createTime,t1.updateTime,t1.deleted,t2.code,t2.status,t2.app,t2.summary,t2.type,t2.acceptDept,t2.acceptUser")->from(TABLE_SECONDMONTHHISTORYDATA)->alias('t1')
            ->leftJoin(TABLE_SECONDORDER)->alias("t2")->on('t1.objectid = t2.id')
            ->where("t1.sourcetype")->eq('secondorder')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($browseType == 'bysearch')->andWhere($secondmonthdataQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), $modelName, $browseType != 'bysearch');
        return $dataList;
    }

    public function buildSearchForm($queryID, $actionURL)
    {


//        $this->loadModel('search')->setSearchParams($this->config->secondmonthdata->search);
    }

    public function create(){
        $sourcetype = trim($_POST['sourcetype']);
        $sourceyear = trim($_POST['sourceyear']);
        $objectid = trim($_POST['objectid']);
        if(!$sourcetype){
            dao::$errors['sourcetype'] = $this->lang->secondmonthdata->sourcetypeEmpty;
            return false;
        }
        if(!$sourceyear){
            dao::$errors['sourceyear'] = $this->lang->secondmonthdata->sourceyearEmpty;
            return false;
        }
        if(!$objectid){
            dao::$errors['objectid'] = $this->lang->secondmonthdata->objectidEmpty;
            return false;
        }

        $isexist = $this->dao->select('id')->from(TABLE_SECONDMONTHHISTORYDATA)->where("sourcetype")->eq($sourcetype)->andWhere("sourceyear")->eq($sourceyear)
            ->andWhere("objectid")->eq($objectid)
            ->andWhere("deleted")->eq(0)
            ->fetch();
        if($isexist){
            dao::$errors[] = $this->lang->secondmonthdata->dataisexist;
            return false;
        }
        $indata = [
            'sourceyear'=>$sourceyear,
            'objectid'=>$objectid,
            'sourcetype'=>$sourcetype,
        ];

        return $this->dao->insert(TABLE_SECONDMONTHHISTORYDATA)->data($indata)->exec();

    }
}