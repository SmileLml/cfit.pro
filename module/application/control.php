<?php
class application extends control
{
    /**
     * Browse application list.
     * 
     * @param  string $browseType 
     * @param  int    $param 
     * @param  string $orderBy 
     * @param  int    $recTotal 
     * @param  int    $recPerPage 
     * @param  int    $pageID 
     * @access public
     * @return void
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'code_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->config->application->search['params']['belongDeptIds']['values'] = $depts;

        $this->loadModel('requirement');
        $browseType = strtolower($browseType);

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0; 
        $actionURL = $this->createLink('application', 'browse', "browseType=bySearch&param=myQueryID");
        $this->application->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title        = $this->lang->application->browse;
        $this->view->applications = $this->application->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->view->browseType   = $browseType;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts        = $depts;
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->display();
    }

    /**
     * Create a application.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $appID = $this->application->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('application', $appID, 'created', $this->post->comment);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadApps($appID)"));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title = $this->lang->application->create;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->view->depts = array('' => '') + $this->loadModel('dept')->getTopPairs();
        $this->view->baseapplicationList = array('' => '') + $this->loadModel('baseapplication')->getPairs();
        $this->display();
    }

    /**
     * Edit a application.
     * 
     * @param  int $appID 
     * @access public
     * @return void
     */
    public function edit($appID = 0)
    {
        if($_POST)
        {
            $changes = $this->application->update($appID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('application', $appID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "appID=$appID");

            $this->send($response);
        }

        $app = $this->application->getByID($appID);
        $this->loadModel('program')->setMenu($app->program);

        $this->view->programs    = array('') + $this->program->getTopPairs('', 'noclosed');
        $this->view->title       = $this->lang->application->edit;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->view->application = $app;
        $this->view->depts = array('' => '') + $this->loadModel('dept')->getTopPairs();
        $this->view->baseapplicationList = array('' => '') + $this->loadModel('baseapplication')->getPairs();
        $this->display();
    }

    /**
     * View application.
     * 
     * @param  int    $appID 
     * @access public
     * @return void
     */
    public function view($appID)
    {
        $this->view->title       = $this->lang->application->view;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->view->actions     = $this->loadModel('action')->getList('application', $appID);
        $application = $this->loadModel('application')->getByID($appID);
        $this->view->application = $application;
        $this->view->depts       = $this->loadModel('dept')->getTopPairs();
        $this->view->baseapplicationList = $this->loadModel('baseapplication')->getPairs();
        if(!empty($application->systemManager)){
            $systemManagerList = explode(',',trim($application->systemManager,','));
            $phoneList = array();
            foreach ($systemManagerList as $userId){
                $user = $this->loadModel('user')->getById($userId,'account');
                array_push($phoneList, $user->mobile);
            }
            $this->view->phone       = trim(implode(',', $phoneList),',');
        }

        $this->display();
    }

    /**
     * Delete application.
     * 
     * @param  int    $appID 
     * @param  string $confirm    yes|no
     * @access public
     * @return void
     */
    public function delete($appID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->application->confirmDelete, $this->createLink('application', 'delete', "appID=$appID&confirm=yes")));
        }
        else
        {
            $app = $this->application->getByID($appID);
            $this->application->delete(TABLE_APPLICATION, $appID);
            $this->session->set('application', '');
            die(js::locate($this->createLink('application', 'browse', "programID=$app->program"), 'parent'));
        }
    }

    /**
     * Project: chengfangjinke
     * Method: ajaxGetOwner
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:18
     * Desc: This is the code comment. This method is called ajaxGetOwner.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $groupID
     */
    public function ajaxGetOwner($groupID)
    {
        die(zget($this->lang->application->ownerList, $groupID, ''));
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:19
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'code_asc', $browseType = 'all')
    {
        /* format the fields of every application in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $applicationLang   = $this->lang->application;
            $applicationConfig = $this->config->application;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $applicationConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($applicationLang->$fieldName) ? $applicationLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get applications. */
            $applications = array();
            if($this->session->applicationOnlyCondition)
            {
                $applications = $this->dao->select('*')->from(TABLE_APPLICATION)->where($this->session->applicationQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->applicationQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $applications[$row->id] = $row;
            }
            $applicationIdList = array_keys($applications);

            /* Get users, products and executions. */
            $users = $this->loadModel('user')->getPairs('noletter');
            $depts = $this->loadModel('dept')->getTopPairs();
            $baseapplicationList = array('' => '') + $this->loadModel('baseapplication')->getPairs();
            foreach($applications as $application)
            {
                if(isset($applicationLang->teamList[$application->team]))           $application->team      = $applicationLang->teamList[$application->team];
                if(isset($applicationLang->boolList[$application->isBasicLine]))           $application->isBasicLine      = $applicationLang->boolList[$application->isBasicLine];
                if(isset($applicationLang->boolList[$application->isSyncJinx]))           $application->isSyncJinx      = $applicationLang->boolList[$application->isSyncJinx];
                if(isset($applicationLang->boolList[$application->isSyncQz]))           $application->isSyncQz      = $applicationLang->boolList[$application->isSyncQz];
                if(isset($applicationLang->isPaymentList[$application->isPayment]))      $application->isPayment = $applicationLang->isPaymentList[$application->isPayment];
                if(isset($applicationLang->attributeList[$application->attribute])) $application->attribute = $applicationLang->attributeList[$application->attribute];
                if(isset($applicationLang->networkList[$application->network]))     $application->network   = $applicationLang->networkList[$application->network];
                if(isset($applicationLang->fromUnitList[$application->fromUnit]))   $application->fromUnit  = $applicationLang->fromUnitList[$application->fromUnit];
                $application->belongDeptIds = zmget($depts, $application->belongDeptIds);
                if(isset($applicationLang->runStatusList[$application->runStatus])) $application->runStatus = $applicationLang->runStatusList[$application->runStatus];
                if(isset($users[$application->createdBy]))                          $application->createdBy = $users[$application->createdBy];
                if(isset($applicationLang->continueLevelList[$application->continueLevel])) $application->continueLevel = $applicationLang->continueLevelList[$application->continueLevel];
                if(isset($applicationLang->belongOrganizationList[$application->belongOrganization])) $application->belongOrganization = zget($applicationLang->belongOrganizationList, $application->belongOrganization, '');
                $application->baselineSystem = zget($baseapplicationList, $application->baselineSystem, '');
                $application->systemManager = zmget($users, $application->systemManager, '');
                $application->systemDept = zget($depts, $application->systemDept, '');
                $application->architecture = zmget($applicationLang->architectureList, $application->architecture, '');
                $application->userScope = zmget($applicationLang->userScopeList, $application->userScope, '');
                $application->userScope = zmget($applicationLang->userScopeList, $application->userScope, '');
                $application->facilitiesStatus = zget($applicationLang->facilitiesStatusList, $application->facilitiesStatus, '');
                $application->resourceLocat = zget($applicationLang->resourceLocatList, $application->resourceLocat, '');
                $application->createDate = substr($application->createDate, 0, 10);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $applications);
            $this->post->set('kind', 'application');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->application->exportName;
        $this->view->allExportFields = $this->config->application->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: exportTemplate
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/1/5
     * Time: 17:21
     * Desc: This is the code comment. This method is called exportTemplate.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: qijingwang
     */
     public function exportTemplate()
     {
         if($_POST)
         {
             $this->application->setListValue();

             foreach($this->config->application->export->templateFields as $field) $fields[$field] = $this->lang->application->$field;

             $this->post->set('fields', $fields);
             $this->post->set('kind', 'application');
             $this->post->set('rows', array());
             $this->post->set('extraNum',   $this->post->num);
             $this->post->set('fileName', 'applicationTemplate');
             $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
         }
         $this->display();
     }

    /**
     * Project: chengfangjinke
     * Method: import
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:19
     * Desc: This is the code comment. This method is called import.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function import()
    {
        if($_FILES)
        {   
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007(); 
            if(!$phpReader->canRead($fileName))
            {   
                $phpReader = new PHPExcel_Reader_Excel5(); 
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->excel->canNotRead));
            }   
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport'), 'parent.parent'));
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: showImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:21
     * Desc: This is the code comment. This method is called showImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $pagerID
     * @param int $maxImport
     * @param string $insert
     */
    public function showImport($pagerID = 1, $maxImport = 0, $insert = '')
    {
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        if($_POST)
        {
            $res = $this->application->createFromImport();
            if (!$res['result']){
                $res['result']  = 'fail';
                $this->send($res);
            }else{
                unlink($tmpFile);
                die(js::locate($this->createLink('application', 'browse'), 'parent'));
            }

        }

        $rows = $this->file->getRowsFromExcel($file);
        $applicationData = array();

        $boolList        = $this->lang->application->boolList;
        foreach($rows as $key => $row)
        {
            if($key == 1) continue;
            if(!$row[0]) continue;

            $team = '';
            foreach($this->lang->application->teamList as $key => $t)
            {
                if($row[2] == $t)
                {
                    $team = $key;
                    break;
                }
            }

            $isPayment = '';
            foreach($this->lang->application->isPaymentList as $key => $s)
            {
                if($row[3] == $s)
                {
                    $isPayment = $key;
                    break;
                }
            }

            $attr = '';
            foreach($this->lang->application->attributeList as $key => $a)
            {
                if($row[4] == $a)
                {
                    $attr = $key;
                    break;
                }
            }

            $network = '';
            foreach($this->lang->application->networkList as $key => $n)
            {
                if($row[5] == $n)
                {
                    $network = $key;
                    break;
                }
            }

            $fromUnit = '';
            foreach($this->lang->application->fromUnitList as $key => $n)
            {
                if($row[6] == $n)
                {
                    $fromUnit = $key;
                    break;
                }
            }

            $application = new stdclass();
            $application->name        = $row[0];
            $application->code        = $row[1];
            $application->team        = $team;
            $application->isPayment   = $isPayment;
            $application->attribute   = $attr;
            $application->network     = $network;
            $application->fromUnit     = $fromUnit;
            $application->feature     = $row[7];
            $application->range     = $row[8];
            $application->useDept     = $row[9];
            $application->projectMonth     = $row[10];
            $application->productDate     = $row[11];
            $application->desc     = $row[12];
            $application->isBasicLine     = array_search($row[13],$boolList);
            $application->isSyncJinx     = array_search($row[14],$boolList);
            $application->isSyncQz     = array_search($row[15],$boolList);
            $applicationData[] = $application;
        }

        if(empty($applicationData)) die(js::locate($this->createLink('application','browse')));


        $this->view->title      = $this->lang->application->common . $this->lang->colon . $this->lang->application->showImport;
        $this->view->position[] = $this->lang->application->showImport;

        $this->view->appData     = $applicationData;
        $this->view->maxImport   = $maxImport;
        $this->view->dataInsert  = $insert;
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }
}
