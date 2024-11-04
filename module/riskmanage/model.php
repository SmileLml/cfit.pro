<?php

class riskmanageModel extends model
{
    /*public function getList($browseType, $pager, $orderBy = 'id_desc'){
        $riskList = $this->dao->select('*')->from(TABLE_RISK)
            ->where('deleted')->eq('0')
//            ->beginIF($browseType != 'all')->andWhere('outprojectStatus')->eq($browseType)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'riskmanage', $browseType != 'bysearch');

        return $riskList;
    }*/

    public function getList( $browseType = '', $param = '', $orderBy = 'id_desc', $pager = null)
    {
                $selectField = "t1.*,CASE 
         WHEN t1.`pri`='high' THEN '3'
         WHEN t1.`pri`='middle' THEN '2'
         WHEN t1.`pri`='low' THEN '1'
        else '0'
         END AS 'priorder',t2.code,t3.bearDept";
        if($browseType == 'bysearch') return $this->getBySearch( $param, $orderBy, $pager,$selectField);


        $risklist = $this->dao->select($selectField)->from(TABLE_RISK)->alias('t1')->innerJoin(TABLE_PROJECT)->alias('t2')->ON('t1.project = t2.id')->innerJoin(TABLE_PROJECTPLAN)->alias('t3')->ON('t2.id = t3.project')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.status')->ne('closed')
            ->andWhere('t2.deleted')->eq(0)
            ->beginIF($browseType != 'all' and $browseType != 'assignTo')->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType == 'assignTo')->andWhere('t1.assignedTo')->eq($this->app->user->account)->fi()
//            ->andWhere('project')->eq($projectID)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'riskmanage', false);


        return $risklist;
    }

    /**
     * Get risks by search
     *
     * @param  int    $projectID
     * @param  string $queryID
     * @param  string $orderBy
     * @param  int    $pager
     * @access public
     * @return object
     */
    public function getBySearch( $queryID = '', $orderBy = 'id_desc', $pager = null,$selectField = "t1.*,t2.code,t3.bearDept")
    {
        if($queryID && $queryID != 'myQueryID')
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('riskmanageQuery', $query->sql);
                $this->session->set('riskmanageForm', $query->form);
            }
            else
            {
                $this->session->set('riskmanageQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->riskmanageQuery == false) $this->session->set('riskmanageQuery', ' 1 = 1');
        }

        $riskmanageQuery = $this->session->riskmanageQuery;
        $riskmanageQuery = str_replace('AND `', ' AND `t1.', $riskmanageQuery);
        $riskmanageQuery = str_replace('AND (`', ' AND (`t1.', $riskmanageQuery);

        $riskmanageQuery = str_replace('OR `', ' OR `t1.', $riskmanageQuery);
        $riskmanageQuery = str_replace('OR (`', ' OR (`t1.', $riskmanageQuery);

        $riskmanageQuery = str_replace('`', '', $riskmanageQuery);

        if(strpos($riskmanageQuery, 'code') !== false)
        {
            $riskmanageQuery = str_replace('t1.code', "t2.code", $riskmanageQuery);
        }
        if(strpos($riskmanageQuery, 'bearDept') !== false)
        {
            $riskmanageQuery = str_replace('t1.bearDept', "t3.bearDept", $riskmanageQuery);
        }
        $riskList =  $this->dao->select($selectField)->from(TABLE_RISK)->alias('t1')->innerJoin(TABLE_PROJECT)->alias('t2')->ON('t1.project = t2.id')
            ->innerJoin(TABLE_PROJECTPLAN)->alias('t3')->ON('t2.id = t3.project')
            ->where($riskmanageQuery)
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.status')->ne('closed')
            ->andWhere('t2.deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'riskmanage', false);

        return $riskList;
    }


    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->riskmanage->search['actionURL'] = $actionURL;
        $this->config->riskmanage->search['queryID']   = $queryID;

        $this->config->riskmanage->search['params']['bearDept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();

        $this->loadModel('search')->setSearchParams($this->config->riskmanage->search);
    }
}