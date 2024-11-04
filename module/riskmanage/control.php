<?php

class riskmanage extends control
{
    
    public function browse($browseType = 'all', $param = 0, $orderBy = 't3.bearDept,t2.id_desc,priorder_desc,t1.id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('risk');

        $queryID   = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('riskmanage', 'browse', "browseType=bysearch&queryID=myQueryID");

        $this->riskmanage->buildSearchForm($queryID, $actionURL);


        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->pager         = $pager;
        $this->view->title         = $this->lang->riskmanage->common;

        $this->view->depts         = $this->loadModel('dept')->getOptionMenu();
        $this->view->users         = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->view->riskList = $this->riskmanage->getList($browseType, $param, $orderBy, $pager);


        $this->view->browseType    = $browseType;
        $this->view->orderBy       = $orderBy;
        $this->view->param         = $param;


        $this->display();
    }


    public function export($orderBy = 't2.id_desc,t1.id_desc', $browseType = 'all'){

        if($_POST)
        {
            $this->loadModel('file');
            $riskmanageLang   = $this->lang->riskmanage;
            $this->loadModel('risk');
            $riskLang   = $this->lang->risk;
            $riskmanageConfig = $this->config->riskmanage;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $riskmanageConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($riskmanageLang->$fieldName) ? $riskmanageLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get changes. */
            $riskList = array();
            $selectField = "t1.*,CASE 
         WHEN t1.`pri`='high' THEN '3'
         WHEN t1.`pri`='middle' THEN '2'
         WHEN t1.`pri`='low' THEN '1'
        else '0'
         END AS 'priorder',t2.code,t3.bearDept";
            if($this->session->riskmanageOnlyCondition)
            {

                $riskList = $this->dao->select($selectField)->from(TABLE_RISK)->alias('t1')->innerJoin(TABLE_PROJECT)->alias('t2')->ON('t1.project = t2.id')
                    ->innerJoin(TABLE_PROJECTPLAN)->alias('t3')->ON('t2.id = t3.project')
                    ->where($this->session->riskmanageQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('t1.id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {

                $stmt = $this->dbh->query($this->session->riskmanageQueryCondition . ($this->post->exportType == 'selected' ? " AND ti.id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $riskList[$row->id] = $row;
            }
          
            $riskIdList = array_keys($riskList);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $apps  = $this->loadModel('application')->getPairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');

            foreach($riskList as $risk)
            {
                //rate,pri,assignedTo,category
                $risk->strategy = zget($riskLang->strategyList,$risk->strategy);
                $risk->status    = zget($riskLang->statusList,$risk->status);
                $risk->identifiedDate = $risk->identifiedDate == '0000-00-00' ? '' : $risk->identifiedDate;
                $risk->pri      = zget($riskLang->priList,$risk->pri);
                $risk->assignedTo  = zget($users,$risk->assignedTo);

                $risk->category = zget($riskLang->categoryList, $risk->category);
                $risk->bearDept = zmget($depts, $risk->bearDept);


            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $riskList);
            $this->post->set('kind', 'riskmanage');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }


        $this->view->fileName        = $this->lang->riskmanage->exportName;
        $this->view->allExportFields = $this->config->riskmanage->list->exportFields;
        $this->view->customExport    = false;
        $this->display();

    }


}