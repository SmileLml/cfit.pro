<?php
class modifycncc extends control
{
    public function setNew($modifycnccID){
        $this->modifycncc->pushmodifycncc($modifycnccID);
    }


    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2022/05/22
     * Time: 14:43
     * Desc: 驳回操作
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $infoID
     */
    public function reject($modifycnccID = 0)
    {
        if($_POST)
        {
            $this->modifycncc->reject($modifycnccID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('modifycncc', $modifycnccID, 'reject', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        //数据信息
        $modifycncc = $this->modifycncc->getByID($modifycnccID);
        //检查是否允许驳回
        $res = $this->modifycncc->checkAllowReject($modifycncc);

        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->modifycncc->reject;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modifycncc = $modifycncc;
        //是否允许审核
        $this->view->res        = $res;
        $this->display();
    }



    /**
     * Project: chengfangjinke
     * Method: browse
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called browse.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'code_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('outwarddelivery');
        $browseType = strtolower($browseType);
        
        $this->view->users = $this->loadModel('user')->getPairs('noletter');

        /* By search. */
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->modifycncc->search['params']['createdDept']['values'] = $depts;

        $apps = $this->loadModel('application')->getPairs();
        $this->view->projectList  = $this->loadModel('projectplan')->getAllProjects();

        $isPaymentList = array();
        $appAll  =  $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
        $this->view->appList = array('' => '') + array_column($appAll, 'name', 'id');
        foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
        {
            if(!$paymentID) continue;
            $isPaymentList[',' . $paymentID . ','] = $paymentValue;        
        }
        $this->config->modifycncc->search['params']['isPayment']['values'] += $isPaymentList;
        $reviewReportList = array('' => '') + $this->loadModel('review')->getPairs('','');
        $this->config->modifycncc->search['params']['reviewReport']['values'] = $reviewReportList;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('modifycncc', 'browse', "browseType=bySearch&param=myQueryID");
        $this->modifycncc->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('modifycnccList', $this->app->getURI(true));

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->modifycncc->common;
        $this->view->modifycnccs    = $this->modifycncc->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->param      = $param;
        $this->view->pager      = $pager;
        $this->view->apps       = $apps;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function create()
    {
        if($_POST)
        {
            $modifycnccID = $this->modifycncc->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modifycncc', $modifycnccID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $this->view->title          = $this->lang->modifycncc->create;
        $this->view->apps           = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),`desc`),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
        $this->view->requirement    = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->fetchPairs();
        $this->view->users          = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problems       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        $this->view->cbpproject     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
        $this->view->demands        = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
//        $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs();
        $this->view->projects       = array('' => '') + $this->loadModel('projectplan')->getCodeProjects(false);
        $this->view->products       = array('' => '') + $this->loadModel('product')->getPairs();
        //审核节点下的审核人列表
        $reviewers            = $this->modifycncc->getReviewers();
        //审核节点下的审核人列表
        $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
        //审核节点下默认设置审核节点人
        $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
        $this->view->reviewers            = $reviewers;
        $this->view->reviewerAccounts     = $reviewerAccounts;
        $this->view->defChosenReviewNodes = $defChosenReviewNodes;
        $this->display();
    }

    /**
     * Edit a modifycncc.
     * 
     * @param  int $modifycnccID 
     * @access public
     * @return void
     */
    public function edit($modifycnccID = 0)
    {
        if($_POST)
        {
            $changes = $this->modifycncc->update($modifycnccID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('modifycncc', $modifycnccID, 'edited', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('view', "modifycnccID=$modifycnccID");

            $this->send($response);
        }

        //变更信息
        $modifycncc                 = $this->modifycncc->getByID($modifycnccID);
        $this->view->title          = $this->lang->modifycncc->edit;
        $this->view->apps           = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),`desc`),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
        $this->view->requirement    = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->fetchPairs();
        $this->view->users          = $this->loadModel('user')->getPairs('noclosed');
        $this->view->problems       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
        $this->view->cbpproject     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
        $this->view->demands        = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
//        $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getCodeProjects($modifycncc->fixType == 'second');
        $this->view->products  = array('' => '') + $this->loadModel('product')->getPairs();
        $deptId = $modifycncc->createdDept;
        $this->view->reviewers = $this->modifycncc->getReviewers($deptId);
        $this->view->modifycncc    = $modifycncc;



        //审核节点以及审核节点的审核人
        $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('modifycncc', $modifycnccID, $modifycncc->version);
        $this->view->nodesReviewers = $nodesReviewers;

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifycnccID
     */
    public function view($modifycnccID = 0)
    {
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('api');
        $this->app->loadLang('modify');
        $modifycncc = $this->modifycncc->getByID($modifycnccID);
        $this->view->title              = $this->lang->modifycncc->view;
        $this->view->users              = $this->loadModel('user')->getPairs('noletter');
        $this->view->depts              = $this->loadModel('dept')->getDeptPairs();
        $this->view->problems           = array('' => '') + $this->loadModel('problem')->getPairs('noclosed');
        $this->view->secondorders           = array('' => '') +$this->loadModel('secondorder')->getNamePairsAll();;
        $this->view->demands            = array('' => '') + $this->loadModel('demand')->getPairs('noclosed');
        $this->view->requirements       = array('' => '') + $this->loadModel('requirement')->getCodePairs();
        $this->view->projects           = array('' => '') + $this->loadModel('projectplan')->getProject($modifycncc->fixType == 'second');//更新获取所属项目的方法
        $this->view->apps               = $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->objects            = $this->loadModel('secondline')->getByID($modifycnccID, 'modifycncc');
        $this->view->releaseInfoList        = $this->loadModel('outwarddelivery')->getReleaseInfoInIds($modifycncc->release);
        $this->view->releasePushLogs             = $this->loadModel('release')->getPushLog($modifycncc->release);
        $modifycncc->belongedAppsInfo   = (Object)$this->outwarddelivery->getAppInfo(explode(',',$modifycncc->belongedApp));

        if(!empty($modifycncc->returnLog)){
            $modifycncc->returnLogArray = json_decode($modifycncc->returnLog);
            $verificationReturnNum = 0;
            foreach ($modifycncc->returnLogArray as $key=>$value){
                if($value->node == '基准审核中' || $value->node == '基准实验室审核'){
                    $verificationReturnNum++;
                }
            }
            $modifycncc->verificationReturnNum = $verificationReturnNum;
        }

        unset($this->lang->modifycncc->implementModalityNewList[0]);
        $this->lang->modifycncc->implementModalityList = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;

        $this->view->actions            = $this->loadModel('action')->getList('modifycncc', $modifycnccID);
        $this->view->modifycncc         = $modifycncc;

        $this->view->testingrequestPairs   = $this->loadModel('testingrequest')->getCodePairs();
        $this->view->productenrollPairs    = $this->loadModel('productenroll')->getCodePairs();
        $this->view->modifycnccPairs       = $this->modifycncc->getCodePairs();

        $this->view->MClog                 = $this->modifycncc->getRequestLog($modifycnccID);

        $this->view->parentId              = $this->outwarddelivery->getOutwardDeliveryByTypeId('modifycncc',$modifycnccID);
        $outwarddeliveryPairs =  [];
        if ($this->view->parentId){
            $outwarddeliveryPairs = $this->outwarddelivery->getDetailPairs($this->view->parentId);
        }
        $this->view->outwarddeliveryPairs  = $outwarddeliveryPairs;
        $this->view->nodes                 = $this->loadModel('review')->getNodes('outwardDelivery', $this->view->parentId, $modifycncc->version);
        $this->view->parent                = $this->outwarddelivery->getByID($this->view->parentId);
        $this->view->allRelations          = $this->loadModel('outwarddelivery')->getTypeRelations('modifycncc',$modifycnccID);
        $reviewReportList = array('' => '') + $this->loadModel('review')->getPairs('','');
        $this->view->reviewReportList = $reviewReportList;

        if($this->view->parent->productEnrollId){
            $this->view->modifycncc->productRegistrationCode = $this->productenroll->getEmisRegisterNumberById($this->view->parent->productEnrollId)->emisRegisterNumber;
        }
        else{
            $this->view->modifycncc->productRegistrationCode = '';
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifycnccID
     * @param int $version
     * @param int $reviewStage
     */
    public function review($modifycnccID = 0, $version = 1, $reviewStage = 0)
    {

        if($_POST)
        {
            $this->modifycncc->review($modifycnccID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('modifycncc', $modifycnccID, 'review', $this->post->comment);
            //$this->modifycncc->sendmail($modifycnccID, $actionID, 'modifycncc', 'review');
            $modifycncc2 = $this->loadModel('modifycncc')->getByID($modifycnccID);
            //检查是否需要退送到清算中心接口
            if($modifycncc2->status == 'productsuccess'){
                $this->modifycncc->pushmodifycncc($modifycnccID);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $modifycncc = $this->loadModel('modifycncc')->getByID($modifycnccID);
        //检查是否允许审核
        $res = $this->loadModel('modifycncc')->checkAllowReview($modifycncc, $version, $reviewStage, $this->app->user->account);
        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->modifycncc->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modifycncc  = $modifycncc;
        $this->view->res     = $res;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifycnccID
     * @param int $version
     * @param int $reviewStage
     */
    public function link($modifycnccID = 0,  $version = 1, $reviewStage = 0)
    {
        if($_POST)
        {
            $this->modifycncc->link($modifycnccID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modifycncc', $modifycnccID, 'linkrelease', $this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $modifycncc = $this->loadModel('modifycncc')->getByID($modifycnccID);
        //检查是否允许审核
        $res = $this->loadModel('modifycncc')->checkAllowReview($modifycncc, $version,  $reviewStage, $this->app->user->account);

        $this->view->depts    = $this->loadModel('dept')->getOptionMenu();
        $this->view->title    = $this->lang->modifycncc->edit;
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modifycncc   = $modifycncc;
        $this->view->res      = $res;
        //新增查询，projectplan 表和TABLE_RELEASE 表关联关系
       /* $projectid = $this->dao->select('project')->from(TABLE_PROJECTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($this->view->modifycncc->project)
            ->orderBy('id_desc')
            ->fetchPairs();
        $projectid =  implode(',',$projectid);*/
        $this->view->releases = array('' => '') + $this->loadModel('project')->getReleases($this->view->modifycncc->project);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     */
    public function feedback($modifycnccID)
    {
        if($_POST)
        {
            $changes = $this->modifycncc->feedback($modifycnccID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes || $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('modifycncc', $modifycnccID, 'feedbacked', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->depts   = $this->loadModel('dept')->getOptionMenu();
        $this->view->title   = $this->lang->modifycncc->edit;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modifycncc = $this->loadModel('modifycncc')->getByID($modifycnccID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifycnccID
     */
    public function close($modifycnccID = 0)
    {
        if($_POST)
        {
            $this->modifycncc->close($modifycnccID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modifycncc', $modifycnccID, 'closed', $this->post->comment, $this->post->result);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: run
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called run.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $modifycnccID
     */
    public function run($modifycnccID = 0)
    {
        if($_POST)
        {
            $this->modifycncc->run($modifycnccID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modifycncc', $modifycnccID, 'run');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->modifycncc->run;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->modifycncc = $this->loadModel('modifycncc')->getByID($modifycnccID);
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: export
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called export.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $orderBy
     * @param string $browseType
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        set_time_limit(0);
        $this->app->loadLang('modify');
        /* format the fields of every modifycncc in order to export data. */
        if($_POST)
        {
            $this->loadModel('file');
            $this->app->loadLang('application');
            $modifycnccLang   = $this->lang->modifycncc;
            $applicationLang  = $this->lang->application;
            $modifycnccConfig = $this->config->modifycncc;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $modifycnccConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($modifycnccLang->$fieldName) ? $modifycnccLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get modifycnccs. */
            $modifycnccs = array();
            if($this->session->modifycnccOnlyCondition)
            {
                $modifycnccs = $this->dao->select('*')->from(TABLE_MODIFYCNCC)->where($this->session->modifycnccQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');
            }
            else
            {
                $stmt = $this->dbh->query($this->session->modifycnccQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $modifycnccs[$row->id] = $row;
            }
            $modifycnccIdList = array_keys($modifycnccs);

            // 获取待处理人数据集。
            $reviewerList = $this->loadModel('review')->getObjectIdListReviewer($modifycnccIdList, 'modifycncc');

            /* Get users, products and executions. */
            $apps  = $this->loadModel('application')->getPairs();
            $depts = $this->loadModel('dept')->getTopPairs();
            $dmap  = $this->dao->select('account,realname,dept')->from(TABLE_USER)->fetchAll('account');
            $users = $this->loadModel('user')->getPairs('noletter');
            $secondorderList     =  $this->loadModel('secondorder')->getNamePairs();
            $reviewReportList = $this->loadModel('review')->getPairs('','');

            $outwarddeliveryPairs  = $this->loadModel('outwarddelivery')->getDetailPairs();
            $testingrequestPairs   = $this->loadModel('testingrequest')->getCodePairs();
            $productenrollPairs    = $this->loadModel('productenroll')->getCodePairs();
            $modifycnccPairs       = $this->modifycncc->getCodePairs();
            unset($this->lang->modifycncc->implementModalityNewList[0]);
            $this->lang->modifycncc->implementModalityList = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;
            // $projects = array('' => '') + $this->loadModel('project')->getPairs();
            foreach($modifycnccs as $modifycncc)
            {
                // 获取关联发布。
                if($modifycncc->release) $modifycncc->release = $this->dao->select('name')->from(TABLE_RELEASE)->where('id')->eq($modifycncc->release)->fetch('name');

                // 获取业务系统相关字段。
                if($modifycncc->app)
                {
                    $modifycncc->appWithPartition = json_decode(str_replace('/',',', $modifycncc->app)); #回写系统和分区
                    foreach($modifycncc->appWithPartition as $app) #回写受影响系统
                    {
                        $modifycncc->appOnly[] = $app[0];
                    }

                    // 处理历史数据，如果app为数字就查application表
                    if(is_numeric($modifycncc->appOnly[0])){
                        $apps = $this->dao->select('id,code,name')->from(TABLE_APPLICATION)->where('id')->in($modifycncc->appOnly)->fetchAll();
                    }else{
                        $apps = $this->dao->select('distinct application as id,application as code,application as name')
                        ->from(TABLE_PARTITION)
                        ->where('application')->in($modifycncc->appOnly)
                        ->andWhere('deleted')->eq('0')
                        ->fetchAll();
                    }
                    $appsInfo=array();
                    foreach($apps as $app)
                    {
                        $appsInfo[$app->id]=$app;
                        $appsInfo[$app->id]->partition=array();
                    }
                    foreach ($modifycncc->appWithPartition as $appPartition){
                        $appsInfo[$appPartition[0]]->partition[]=$appPartition[1];
                    }

                    // 业务系统分类。
                    // $isPayments = array();
                    // $teams = array();
                    $partitionMsg='';
                    foreach($appsInfo as $appID=>$appInfo)
                    {
//                         $isPayments[] = $applicationLang->isPaymentList[$appInfo->isPayment];

//                         $teams[] = $applicationLang->teamList[$appInfo->team];

                        $partitionMsg.=$appInfo->name;
                        if (!empty($appInfo->partition[0])){
                            $partitionMsg.=' (';
                            foreach($appInfo->partition as $partition){
                                $partitionMsg.=$partition.' 分区,';
                            }
                            $partitionMsg=trim($partitionMsg,',').' )';
                        }
                        $partitionMsg.= "\r\n";
                    }
                    $modifycncc->app       = $partitionMsg;
//                     $modifycncc->isPayment = trim(implode(',',array_unique($isPayments)));
//                     $modifycncc->team      = trim(implode(',',array_unique($teams)));
                }
                $isPayments = array();
                $teams = array();
                if ($modifycncc->belongedApp){
                    $modifycncc->belongedAppsInfo   = (Object)$this->loadModel('outwarddelivery')->getAppInfo(explode(',',$modifycncc->belongedApp));
                    foreach($modifycncc->belongedAppsInfo as $appInfo)
                    {
                        if ($appInfo){
                            $belongedApps[] = zget($apps, $appInfo->id);
                            $isPayments[] = zget($this->lang->application->isPaymentList,$appInfo->isPayment, '');
                            $teams[] = zget($this->lang->application->teamList,$appInfo->team, '');
                        }
                    }
                }
                $modifycncc->isPayment = trim(implode(',',array_unique($isPayments)));
                $modifycncc->team      = trim(implode(',',array_unique($teams)));

                if ($modifycncc->riskAnalysisEmergencyHandle){
                    $modifycncc->riskAnalysisEmergencyHandle = json_decode($modifycncc->riskAnalysisEmergencyHandle);
                    $num=1;
                    $ERmsg='';
                    foreach ($modifycncc->riskAnalysisEmergencyHandle as $ER){
                        $ERmsg=$ERmsg.$num.'、【'.$modifycnccLang->riskAnalysis.'】'.$ER->riskAnalysis.';';
                        $ERmsg=$ERmsg.' 【'.$modifycnccLang->emergencyBackWay.'】'.$ER->emergencyBackWay."\r\n";
                        $num=$num+1;
                    }
                    $modifycncc->riskAnalysisEmergencyHandle=$ERmsg;
                }

                // 获取关联单。
                $objects = $this->loadModel('secondline')->getByID($modifycncc->id, 'modifycncc');

                // 获取需求单。
                $demandMsg='';
                foreach($objects['demand'] as $demandID => $demandCode){
                    $demandMsg.= $demandCode->code.', ';
                }
                $demandMsg=rtrim($demandMsg,', ')."\r\n";
                $modifycncc->demand=$demandMsg;

                $secondorderIds = explode(',', $modifycncc->secondorderId);
                $secondorderNameList = array();
                foreach ($secondorderIds as $secondorder){
                    if(!empty($secondorder)){
                        $secondorderNameList[] = zget($secondorderList, $secondorder,'');
                    }
                }
                $modifycncc->secondorderId             = implode(',',$secondorderNameList);

                // 获取需求任务。
                $requirementMsg='';
                foreach($objects['requirement'] as $requirementID => $requirementCode){
                    $requirementMsg.= $requirementCode->code.', ';
                }
                $requirementMsg=rtrim($requirementMsg,', ')."\r\n";
                $modifycncc->relatedDemandNum=$requirementMsg;

                // 获取问题单。
                $problemMsg='';
                foreach($objects['problem'] as $problemID => $problemCode){
                    $problemMsg=$problemMsg. $problemCode->code.', ';
                }
                $problemMsg=rtrim($problemMsg,', ')."\r\n";
                $modifycncc->problem=$problemMsg;

                //所属CBP项目
                if($modifycncc->CNCCprojectIdUnique)
                {
                    $CBPProjectCode = $this->dao->select('code,name')->from(TABLE_CBPPROJECT)->where('code')->in(explode(',', $modifycncc->CNCCprojectIdUnique))->fetchAll();
                    $modifycncc->CNCCprojectIdUnique='';
                    foreach($CBPProjectCode as $cbpprojectcode){
                        $modifycncc->CNCCprojectIdUnique.=$cbpprojectcode->name."\r\n";
                    }
                }

                //关联变更单
                $changeRelations='';
                $objects = $this->loadModel('secondline')->getByID($modifycncc->id, 'modifycncc');
                foreach($objects['modifycncc'] as $relation => $modifycnccObjects){
                    if(!empty($modifycnccObjects)){
                        if ($relation=='beInclude'){
                            $relationModifycnccMsg=$modifycnccLang->relateTypeIncluded.'(';
                        }
                        else{
                            $relationModifycnccMsg=zget($modifycnccLang->relateTypeList,$relation,'').'(';
                        }
                        foreach ($modifycnccObjects as $num=>$relatedModifycncc){
                            $relationModifycnccMsg=$relationModifycnccMsg. $relatedModifycncc[1].', ';
                        }
                        $changeRelations.=rtrim($relationModifycnccMsg,', ').')'."\r\n";
                    }
                }
                $modifycncc->changeRelation=$changeRelations;

                $changeNodes=explode(',',$modifycncc->node);
                $nodeName=array();
                foreach($changeNodes as $changeNode){
                    if (!empty($changeNode)){
                        $nodeName[]=$modifycnccLang->nodeList[$changeNode];
                    }
                }
                $modifycncc->node     = implode(', ',$nodeName);

                // 待处理人处理。
                $reviewers = array();
                if ($modifycncc->status == 'productsuccess' or $modifycncc->status == 'reject') {
                    $reviewers[] = zget($users, $modifycncc->createdBy, $modifycncc->createdBy);
                }
                elseif ($modifycncc->status == 'closing') {
                    $secondlineStage = array_search('产创部二线专员', $modifycnccLang->reviewerList);
                    $secondlineReviewers = $this->loadModel('review')->getLastPendingPeople('modifycncc', $modifycncc->id, $modifycncc->version, $secondlineStage + 1);
                    $secondlineReviewers = explode(',',$secondlineReviewers);
                    //所有二线审核人
                    foreach($secondlineReviewers as $secondlineReviewer){
                        $reviewers[] = zget($users, $secondlineReviewer, $secondlineReviewer);
                    }
                }
                else {
                    foreach ($reviewerList[$modifycncc->id] as $reviewer) {
                        if (!$reviewer) continue;
                        $reviewers[] = zget($users, $reviewer);
                    }
                }
                $reviewers=array_unique($reviewers);
                $modifycncc->dealUser = implode(',', $reviewers);

                // 处理所属项目。
                $projects  =  $this->loadModel('projectplan')->getProject($modifycncc->fixType=='second');//更新表
                if($modifycncc->project)
                {
                    $as = array();
                    foreach(explode(',', $modifycncc->project) as $project)
                    {
                        if(!$project) continue;
                        $as[] = zget($projects, $project);
                    }
                    $modifycncc->project = implode(',', $as);
                }

                if (!empty($modifycncc->feasibilityAnalysis)) {
                    $feasibilityAnalysisInfo = array();
                    $feasibilityAnalysises = explode(',', $modifycncc->feasibilityAnalysis);
                    foreach ($feasibilityAnalysises as $feasibilityAnalysis) {
                        $feasibilityAnalysisInfo[] = $modifycnccLang->feasibilityAnalysisList[$feasibilityAnalysis];
                    }
                    $modifycncc->feasibilityAnalysis= trim(implode(',', $feasibilityAnalysisInfo), ',');
                }

                // 支持人员处理。
                if($modifycncc->internalSupply)
                {
                    $as = array();
                    foreach(explode(',', $modifycncc->internalSupply) as $supply)
                    {
                        if(!$supply) continue;
                        $as[] = zget($users, $supply);
                    }
                    $modifycncc->internalSupply = implode(',', $as);
                }

                // Processing version number.
                $modifycncc->productCodeList = array();
               /* if($modifycncc->productCode)
                {
                    $codeList = json_decode($modifycncc->productCode);
                    foreach($codeList as $code)
                    {
                        $product = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($code->assignProduct)->fetch();
                        $line    = $this->dao->select('id,code')->from(TABLE_PRODUCTLINE)->where('id')->eq($product->line)->fetch();
                        $codeTitle =$product->code . '-' . $code->versionNumber . '-for-' . $code->supportPlatform;
                        if(trim($code->hardwarePlatform)) $codeTitle .= '-' . $code->hardwarePlatform;
                        $modifycncc->productCodeList[] = $codeTitle;
                    }
                }
                $codeList = '';
                foreach($modifycncc->productCodeList as $code)
                {
                    $codeList .= $code . "\r\n";
                }
                $modifycncc->productCode = $codeList; */

                $modifycncc->level    = $modifycnccLang->levelList[$modifycncc->level];
//                $modifycncc->status   = $modifycnccLang->statusList[$modifycncc->status];
                $modifycncc->status   = $modifycncc->closed == '1' ? $this->lang->modifycncc->labelList['closed'] :zget($this->lang->modifycncc->statusList, $modifycncc->status);
                $modifycncc->type     = $modifycnccLang->typeList[$modifycncc->type];
                $modifycncc->mode     = $modifycnccLang->modeList[$modifycncc->mode];
                $modifycncc->classify = $modifycnccLang->classifyList[$modifycncc->classify];
                $modifycncc->fixType  = $modifycnccLang->fixTypeList[$modifycncc->fixType];
                $modifycncc->property = $modifycnccLang->propertyList[$modifycncc->property];
                $modifycncc->operationType = $modifycnccLang->operationTypeList[$modifycncc->operationType];
                $modifycncc->changeSource  = $modifycnccLang->changeSourceList[$modifycncc->changeSource];
                $modifycncc->changeStage   = $modifycnccLang->changeStageList[$modifycncc->changeStage];
                $modifycncc->implementModality      = $modifycnccLang->implementModalityList[$modifycncc->implementModality];
                $modifycncc->cooperateDepNameList   = $modifycnccLang->cooperateDepNameListList[$modifycncc->cooperateDepNameList];
                $modifycncc->benchmarkVerificationType    = $modifycnccLang->benchmarkVerificationTypeList[$modifycncc->benchmarkVerificationType];
                $modifycncc->changeStatus           = $modifycnccLang->changeStatusList[$modifycncc->changeStatus];
                $modifycncc->result                 = $modifycnccLang->resultList[$modifycncc->result];

                // 是否中断业务和是否后补流程。
                $modifycncc->isInterrupt         = $modifycnccLang->interruptList[$modifycncc->isInterrupt];
                $modifycncc->isAppend            = $modifycnccLang->appendList[$modifycncc->isAppend];
                $modifycncc->isBusinessAffect    = $modifycnccLang->isBusinessAffectList[$modifycncc->isBusinessAffect];
                $modifycncc->isBusinessCooperate = $modifycnccLang->isBusinessCooperateList[$modifycncc->isBusinessCooperate];
                $modifycncc->isBusinessJudge     = $modifycnccLang->isBusinessJudgeList[$modifycncc->isBusinessJudge];

                //迭代二十六-删除部门第一个'/'
                $modifycncc->createdDept = ltrim(zget($depts, $modifycncc->createdDept, ''), '/');
                $modifycncc->judgeDep    = $modifycnccLang->judgeDepList[$modifycncc->judgeDep];
                $modifycncc->createdDate  = substr($modifycncc->createdDate,0, 10);//创建时间
                $modifycncc->editedDate  = substr($modifycncc->editedDate,0, 10);// 编辑时间
                $modifycncc->createdBy   = zget($users, $modifycncc->createdBy, $modifycncc->createdBy);
                $modifycncc->editedBy    = zget($users, $modifycncc->editedBy, $modifycncc->editedBy);
                $modifycncc->closedBy     = zget($users, $modifycncc->closedBy, $modifycncc->closedBy);

                $modifycncc->businessAffect           = strip_tags($modifycncc->businessAffect);
                $modifycncc->target                   = strip_tags($modifycncc->target);
                $modifycncc->reason                   = strip_tags($modifycncc->reason);
                $modifycncc->changeContentAndMethod   = strip_tags($modifycncc->changeContentAndMethod);
                $modifycncc->techniqueCheck           = strip_tags($modifycncc->techniqueCheck);
                $modifycncc->test                     = strip_tags($modifycncc->test);
                $modifycncc->checkList                = strip_tags($modifycncc->checkList);
                $modifycncc->businessCooperateContent = strip_tags($modifycncc->businessCooperateContent);
                $modifycncc->judgePlan                = strip_tags($modifycncc->judgePlan);
                $modifycncc->controlTableSteps        = strip_tags($modifycncc->controlTableSteps);
                $modifycncc->risk                     = strip_tags($modifycncc->risk);
                $modifycncc->step                     = strip_tags($modifycncc->step);
                $modifycncc->effect                   = strip_tags($modifycncc->effect);
                $modifycncc->businessFunctionAffect   = strip_tags($modifycncc->businessFunctionAffect);
                $modifycncc->backupDataCenterChangeSyncDesc = strip_tags($modifycncc->backupDataCenterChangeSyncDesc);
                $modifycncc->emergencyManageAffect    = strip_tags($modifycncc->emergencyManageAffect);
                $modifycncc->businessAffect           = strip_tags($modifycncc->businessAffect);
                $modifycncc->isReview                      = zget($this->lang->modifycncc->isReviewList, $modifycncc->isReview);
                $modifycncc->isReviewPass                      = zget($this->lang->modifycncc->isReviewPassList, $modifycncc->isReviewPass);
                $reviewReportArrayList = explode(",",$modifycncc->reviewReport);
                $reviewReportArray = array();
                foreach ($reviewReportArrayList as $reviewReport){
                    array_push($reviewReportArray, zget($reviewReportList, $reviewReport, ''));
                }
                $modifycncc->reviewReport = implode(",",$reviewReportArray);


                $parentId   = $this->outwarddelivery->getOutwardDeliveryByTypeId('modifycncc',$modifycncc->id);
                $parent     = $this->outwarddelivery->getByID($parentId);

                $belongedODMsg = $outwarddeliveryPairs[$parentId]->code.'（';
                if ($parent->isNewTestingRequest){
                    $belongedODMsg = $belongedODMsg .  $testingrequestPairs[$parent->testingRequestId].',';
                }
                if ($parent->isNewProductEnroll){
                    $belongedODMsg = $belongedODMsg . $productenrollPairs[$parent->productEnrollId].',';
                }
                if ($parent->isNewModifycncc){
                    $belongedODMsg = $belongedODMsg . $modifycnccPairs[$parent->modifycnccId].',';
                }
                $belongedODMsg = trim($belongedODMsg,',').'）';

                $relatedODMsg = $outwarddeliveryPairs[$parentId]->code.'（';
                if ($parent->isNewTestingRequest){
                    $relatedODMsg = $relatedODMsg .  $testingrequestPairs[$parent->testingRequestId].',';
                }
                if ($parent->isNewProductEnroll){
                    $relatedODMsg = $relatedODMsg . $productenrollPairs[$parent->productEnrollId].',';
                }
                if ($parent->isNewModifycncc){
                    $relatedODMsg = $relatedODMsg . $modifycnccPairs[$parent->modifycnccId].',';
                }
                $relatedODMsg = trim($relatedODMsg,',').'）';
                $allrelations = $this->loadModel('outwarddelivery')->getTypeRelations('modifycncc',$modifycncc->id);
                $modifycncc->relatedOutwardDelivery   = implode(',',array_column($allrelations['parents'],'code'));
                $modifycncc->belongedOutwardDelivery  = $belongedODMsg;
                $modifycncc->relatedTestingRequest    = $testingrequestPairs[$parent->testingRequestId];
                $modifycncc->relatedProductEnroll     = $productenrollPairs[$parent->productEnrollId];
                $modifycncc->urgentSource             = zget($this->lang->modifycncc->urgentSourceList,$modifycncc->urgentSource,'');
                // 手否后补流程、实际交付时间
                $modifycncc->actualDeliveryTime   = $modifycncc->isMakeAmends == 'yes' ? $modifycncc->actualDeliveryTime : '';
                $modifycncc->isMakeAmends         = zget($this->lang->modify->isMakeAmendsList,$modifycncc->isMakeAmends,'');
                $modifycncc->changeForm           = zget($this->lang->modifycncc->changeFormList,$modifycncc->changeForm,'');
                $modifycncc->automationTools      = zget($this->lang->modifycncc->automationToolsList,$modifycncc->automationTools,'');
                $modifycncc->changeImpactAnalysis = $modifycncc->changeImpactAnalysis;

                if($parent->productEnrollId){
                    $modifycncc->productRegistrationCode = $this->productenroll->getEmisRegisterNumberById($parent->productEnrollId)->emisRegisterNumber;
                }
                else{
                    $modifycncc->productRegistrationCode='';
                }
            }
            $this->post->set('fields', $fields);
            $this->post->set('rows', $modifycnccs);
            $this->post->set('kind', 'modifycncc');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->modifycncc->exportName;
        $this->view->allExportFields = $this->config->modifycncc->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: delete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called delete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     */
    public function delete($modifycnccID)
    {
        if(!empty($_POST))
        {

            $this->dao->update(TABLE_MODIFYCNCC)->set('status')->eq('deleted')->where('id')->eq($modifycnccID)->exec();
            $actionID = $this->loadModel('action')->create('modifycncc', $modifycnccID, 'deleted', $this->post->comment);

            //2022.4.21 tangfei、guchaonan 删除与问题需求的关联关系
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('relationID')->eq($modifycnccID)
                ->andWhere('relationType')->eq('modifycncc')
                ->exec();
            $this->dao->update(TABLE_SECONDLINE)
                ->set('deleted')->eq('1')
                ->where('objectID')->eq($modifycnccID)
                ->andWhere('objectType')->eq('modifycncc')
                ->exec();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::reload('parent'));
        }

        $modifycncc = $this->modifycncc->getByID($modifycnccID);
        $this->view->actions = $this->loadModel('action')->getList('modifycncc', $modifycnccID);
        $this->view->modifycncc = $modifycncc;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }

    /**
     * Project: chengfangjinke
     * Method: fix
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called fix.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function fix()
    {
        return;

        //创建部门
        $umap = array();

        $users = $this->dao->select('*')->from(TABLE_USER)->fetchAll();
        foreach($users as $user)
        {
            $umap[$user->account] = $user->dept;
        }
        $modifies = $this->dao->select('*')->from(TABLE_MODIFYCNCC)->fetchAll();
        foreach($modifies as $m)
        {
            $this->dao->update(TABLE_MODIFYCNCC)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $infos = $this->dao->select('*')->from(TABLE_INFO)->fetchAll();
        foreach($infos as $m)
        {
            $this->dao->update(TABLE_INFO)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $problems = $this->dao->select('*')->from(TABLE_PROBLEM)->fetchAll();
        foreach($problems as $m)
        {
            $this->dao->update(TABLE_PROBLEM)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }
        $demands = $this->dao->select('*')->from(TABLE_DEMAND)->fetchAll();
        foreach($demands as $m)
        {
            $this->dao->update(TABLE_DEMAND)->set('createdDept')->eq($umap[$m->createdBy])->where('id')->eq($m->id)->exec();
        }

        echo "<p>创建部门修改完成</p>";

        //添加产品经理节点
        $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)->where('objectType')->in('modifycncc,info')->orderBy('id')->fetchAll();
        $map = array();
        foreach($nodes as $node)
        {
            if(!isset($map[$node->objectType . '-' . $node->objectID . '-' . $node->version])) $map[$node->objectType . '-' . $node->objectID . '-' . $node->version] = array();
            $map[$node->objectType . '-' . $node->objectID . '-' . $node->version][] = $node;
        }

        foreach($map as $ns)
        {
            $stage = 0;
            $reviewStage = 0;
            $type = $ns[0]->objectType;
            $oid  = $ns[0]->objectID;
            foreach($ns as $key => $n)
            {
                $stage++;
                $this->dao->update(TABLE_REVIEWNODE)->set('stage')->eq($stage)->where('id')->eq($n->id)->exec();
                if($n->status == 'pending') $reviewStage = $stage;

                if($key == 2)
                {
                    $stage++;

                    $data = new stdclass();
                    $data->status = ($n->status == 'wait' or $n->status == 'pending') ? 'wait' : 'ignore';
                    $data->objectType  = $n->objectType;
                    $data->objectID    = $n->objectID;
                    $data->stage       = $stage;
                    $data->createdBy   = $n->createdBy;
                    $data->createdDate = $n->createdDate;
                    $data->version     = $n->version;
                    $this->dao->insert(TABLE_REVIEWNODE)->data($data)->exec();

                    $insertID = $this->dao->lastInsertID();
                    $r = new stdclass();
                    $r->node        = $insertID;
                    $r->reviewer    = '';
                    $r->status      = $data->status;
                    $r->createdBy   = $n->createdBy;
                    $r->createdDate = $n->createdDate;
                    $this->dao->insert(TABLE_REVIEWER)->data($r)->exec();
                }
            }
            if($reviewStage != 0)
            {
                if($type == 'modifycncc') $this->dao->update(TABLE_MODIFYCNCC)->set('reviewStage')->eq($reviewStage-1)->where('id')->eq($oid)->exec();
                if($type == 'info') $this->dao->update(TABLE_INFO)->set('reviewStage')->eq($reviewStage-1)->where('id')->eq($oid)->exec();
            }
        }
        echo '产品经理节点添加完成';
    }

    /**
     * Project: chengfangjinke
     * Method: exportWord
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:45
     * Desc: This is the code comment. This method is called exportWord.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $modifycnccID
     */
    public function exportWord($modifycnccID)
    {
        $modifycncc = $this->modifycncc->getById($modifycnccID);
        $reviewReportList = $this->loadModel('review')->getPairs('','');
        $users  = $this->loadModel('user')->getPairs('noletter');
        $this->app->loadLang('outwarddelivery');

        $this->app->loadClass('phpword', true);
        $phpWord = new PhpOffice\PhpWord\PHPWord();
        $section = $phpWord->addSection();

        $phpWord->addParagraphStyle('pStyle', array('spacing'=>100));
        $phpWord->addTitleStyle(1, array('size' => 15, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 200), 'align' => 'center'));
        $phpWord->addTitleStyle(2, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));
        $phpWord->addTitleStyle(3, array('size' => 10, 'name' => '黑体'), array('space' => array('before' => 100, 'after' => 100)));

        $phpWord->addParagraphStyle('align_right', array('lineHeight' => "1.2", 'spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'right'));
        $phpWord->addFontStyle('font_default', array('name'=>'Arial', 'size'=>11, 'color'=>'37363a'));
        $phpWord->addFontStyle('font_bold', array('name'=>'Arial', 'size'=>11, 'color'=>'000000', 'bold'=> true));

        $section->addTitle($this->lang->modifycncc->exportTitle, 1);
        $section->addText($this->lang->modifycncc->code . ' ' . $modifycncc->code, 'font_default', 'align_right');

        $tableStyle = array(
            'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT,
            'width' => 100 * 50,
            'cellMargin' => 50,
            'borderSize' => 10,
            'borderColor' => '000000',
        );
        $cellStyle1  =  array('gridSpan' => 1);
        $cellStyle2 =  array('gridSpan' => 2);
        $cellStyle3 =  array('gridSpan' => 3);
        $cellStyle4 =  array('gridSpan' => 4);
        $cellStyle6 =  array('gridSpan' => 6);
        $table = $section->addTable($tableStyle);
        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->level);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modifycncc->levelList, $modifycncc->level, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->type);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modifycncc->typeList, $modifycncc->type, ''));

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->mode);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modifycncc->modeList, $modifycncc->mode, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->status);
        $modifycncc->statusTxt   = $modifycncc->closed == '1' ? $this->lang->modifycncc->labelList['closed'] :zget($this->lang->modifycncc->statusList, $modifycncc->status);

        $table->addCell(2000, $cellStyle2)->addText($modifycncc->statusTxt);

        //$projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
        $projects  =  array('0' => '') + $this->loadModel('projectplan')->getProject($modifycncc->fixType == 'second');//更新获取所属项目的方法
        $ps = array();
        foreach(explode(',', $modifycncc->project) as $project)
        {
            $ps[] = zget($projects, $project, '');
        }
        $modifycncc->problem = trim($modifycncc->problem, ',');
        $modifycncc->demand = trim($modifycncc->demand, ',');
        if(!empty($modifycncc->problem)) $modifycncc->problem = $this->dao->select("group_concat(`code`) as code")->from(TABLE_PROBLEM)->where('id')->in($modifycncc->problem)->fetch('code');
        if(!empty($modifycncc->demand))  $modifycncc->demand  = $this->dao->select("group_concat(`code`) as code")->from(TABLE_DEMAND)->where('id')->in($modifycncc->demand)->fetch('code');

        $changeNodes = [];
        foreach (explode(',',$modifycncc->node) as $node)
        {
            if(empty($node)) continue;
            $changeNodes [] = zget($this->lang->modifycncc->nodeList, $node, '') ;
        }

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->node);
        $table->addCell(2000, $cellStyle2)->addText(implode(',', $changeNodes));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->project);
        $table->addCell(2000, $cellStyle2)->addText(implode(',', $ps));

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->planBegin);
        $table->addCell(2000, $cellStyle2)->addText($modifycncc->planBegin);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->planEnd);
        $table->addCell(2000, $cellStyle2)->addText($modifycncc->planEnd);

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->actualBegin);
        $table->addCell(2000, $cellStyle2)->addText($modifycncc->actualBegin);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->actualEnd);
        $table->addCell(2000, $cellStyle2)->addText($modifycncc->actualEnd);

        $parentId   = $this->loadModel('outwarddelivery')->getOutwardDeliveryByTypeId('modifycncc',$modifycncc->id);
        $parent     = $this->outwarddelivery->getByID($parentId);
        if($parent->testingRequestId){
            $trMsg     = $this->loadModel('testingrequest')->getCodePairs()[$parent->testingRequestId];
            $giteeId   = $this->testingrequest->getOutercode($parent->testingRequestId)->giteeId;
            $trMsg     = $giteeId ? $trMsg . '（' . $giteeId . '）' : $trMsg;
        }
        if($parent->productEnrollId){
            $peMsg    = $this->loadModel('productenroll')->getCodePairs()[$parent->productEnrollId];
            $giteeId  = $this->productenroll->getOutercode($parent->productEnrollId)->giteeId;
            $peMsg = $giteeId ? $peMsg . '（' . $giteeId . '）' : $peMsg;
        }

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->relatedTestingRequest);
        $table->addCell(2000, $cellStyle2)->addText(isset($trMsg) ? $trMsg : '');
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->relatedProductEnroll);
        $table->addCell(2000, $cellStyle2)->addText(isset($peMsg) ? $peMsg : '');

        /*
        $ps = array();
        if($modifycncc->problem)
        {
            $problemList = explode(',', $modifycncc->problem);
            foreach($problemList as $p) $ps[] = $problems[$p];
        }
         */

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->problem);
        //$table->addCell(1000, $cellStyle1)->addText(implode(',', $ps));
        $table->addCell(2000, $cellStyle2)->addText($modifycncc->problem);

        $demands = $this->loadModel('demand')->getPairs('noclosed');
        $modifycncc->demand = trim($modifycncc->demand, ',');
        /*
        $ds = array();
        if($modifycncc->demand)
        {
            $demandList = explode(',', $modifycncc->demand);
            foreach($demandList as $d) $ds[] = $demands[$d];
        }
         */

        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->demand);
        //$table->addCell(1000, $cellStyle1)->addText(implode(',', $ds));
        $table->addCell(2000, $cellStyle2)->addText($modifycncc->demand);

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->createdBy);
        $table->addCell(2000, $cellStyle2)->addText(zget($users, $modifycncc->createdBy, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->createdDate);
        $table->addCell(2000, $cellStyle2)->addText($modifycncc->createdDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->isReview);
        $table->addCell(2000, $cellStyle2)->addText(zget($this->lang->modifycncc->isReviewList, $modifycncc->isReview, ''));
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewReport);
        $reviewReportArrayList = explode(",",$modifycncc->reviewReport);
        $reviewReportArray = array();
        foreach ($reviewReportArrayList as $reviewReport){
            array_push($reviewReportArray, zget($reviewReportList, $reviewReport, ''));
        }
        $table->addCell(2000, $cellStyle2)->addText(implode(",",$reviewReportArray));


        $partitionMsg='';
        foreach($modifycncc->appsInfo as $appID=>$appInfo)
        {
            $partitionMsg.=$appInfo->name;
            if (!empty($appInfo->partition[0])){
                $partitionMsg.=' (';
                foreach($appInfo->partition as $partition){
                    $partitionMsg.=$partition.' 分区,';
                }
                $partitionMsg=trim($partitionMsg,', ').' )';
            }
            $partitionMsg.='<w:br />';
        }

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->app);
        $table->addCell(4000, $cellStyle4)->addText($partitionMsg);

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->productCode);
        $productCodeList = '';
        if(!empty($modifycncc->productCodeList)){
            foreach($modifycncc->productCodeList as $code)
            {
                $productCodeList .= $code . '<w:br />';
            }
        }
        $table->addCell(4000, $cellStyle4)->addText($modifycncc->productCode);

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->desc);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modifycncc->desc));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->reason);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modifycncc->reason));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->target);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modifycncc->target));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->feasibilityAnalysis);
        if (!empty($modifycncc->feasibilityAnalysis)) {
            $feasibilityAnalysisInfo = array();
            $feasibilityAnalysises = explode(',', $modifycncc->feasibilityAnalysis);
            foreach ($feasibilityAnalysises as $feasibilityAnalysis) {
                $feasibilityAnalysisInfo[] = zget($this->lang->modifycncc->feasibilityAnalysisList, $feasibilityAnalysis, '');
            }
            $table->addCell(4000, $cellStyle4)->addText(trim(implode(',', $feasibilityAnalysisInfo),','));
        }
        else{
            $table->addCell(4000, $cellStyle4)->addText('');
        }

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->risk);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modifycncc->risk));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->step);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modifycncc->step));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->effect);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modifycncc->effect));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->checkList);
        $table->addCell(4000, $cellStyle4)->addText(strip_tags($modifycncc->checkList));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->result);
        $table->addCell(4000, $cellStyle4)->addText(zget($this->lang->modifycncc->resultList, $modifycncc->result, ''));

        /* guchaonan 添加风险分析与应急处置字段 */
        $table->addRow();
        $table->addCell(6000, $cellStyle6)->addText($this->lang->modifycncc->riskAnalysisEmergencyHandle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(3000,  $cellStyle3)->addText($this->lang->modifycncc->riskAnalysis);
        $table->addCell(3000,  $cellStyle3)->addText($this->lang->modifycncc->emergencyBackWay);

        if ($modifycncc->riskAnalysisEmergencyHandle){
            foreach ($modifycncc->riskAnalysisEmergencyHandle as $ER){
                $table->addRow();
                $table->addCell(3000,  $cellStyle3)->addText($ER->riskAnalysis);
                $table->addCell(3000,  $cellStyle3)->addText($ER->emergencyBackWay);
            }
        }

        /* Review. */
        $table->addRow();
        $table->addCell(6000, $cellStyle6)->addText($this->lang->modifycncc->reviewComment, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewNode);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewer);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewResult);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewComment);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->reviewTime);

        $nodes = $this->loadModel('review')->getNodes('outwardDelivery', $parentId, $modifycncc->version);
        foreach($nodes as $key => $node)
        {
            # if($key == 0) continue;
            if($node->status == 'ignore') continue;
            $reviewers = [];
            if(is_array($node->reviewers) && !empty($node->reviewers)){
                $reviewers = array_column($node->reviewers, 'reviewer');
            }
            //所有审核人
            $reviewerUsers    = getArrayValuesByKeys($users, $reviewers);

            $reviewerUsersStr = implode(',', $reviewerUsers);

            $realReviewerInfo = $this->loadModel('review')->getRealReviewerInfo($node->status, $node->reviewers);

            $realReviewerInfo->reviewerUserName = '';
            if(isset($realReviewerInfo->reviewer)){
                $realReviewerInfo->reviewerUserName = '（'.zget($users, $realReviewerInfo->reviewer).'）';
            }
            //跳过系统部节点
            if ($modifycncc->createdDate > "2024-04-02 23:59:59" && $key == 3){
                continue;
            }
            if ($key==4 and (! in_array($realReviewerInfo->status,['pass','reject']))) { continue; }
            $table->addRow();
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modifycncc->reviewNodeList, $key));
            $table->addCell(1000, $cellStyle1)->addText($reviewerUsersStr);
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modifycncc->confirmResultList, $realReviewerInfo->status, '') . $realReviewerInfo->reviewerUserName);
            $table->addCell(1000, $cellStyle1)->addText(strip_tags($realReviewerInfo->comment));
            $table->addCell(1000, $cellStyle1)->addText($realReviewerInfo->reviewTime);
        }

        //外部审批信息
        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->outwarddelivery->outerReviewNodeList['4']);
        $table->addCell(1000, $cellStyle1)->addText(zget($users,'guestjk',','));

        if(in_array($modifycncc->status,array('waitqingzong','qingzongsynfailed'))){
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modifycncc->statusList, $modifycncc->status, ''));
        }
        elseif(in_array($modifycncc->status,array('withexternalapproval','modifyfail','modifysuccesspart','modifysuccess','modifyreject'))){
            $table->addCell(1000, $cellStyle1)->addText($this->lang->outwarddelivery->synSuccess);
        }
        else{
            $table->addCell(1000, $cellStyle1)->addText('');
        }

        $MClog      = $this->modifycncc->getRequestLog($modifycnccID);
        if($modifycncc->pushStatus and $MClog->response->message){
            $table->addCell(2000, $cellStyle1)->addText($MClog->response->message);
        }
        elseif($modifycncc->status=='qingzongsynfailed'){
            $table->addCell(2000, $cellStyle1)->addText($this->lang->outwarddelivery->synFail);
        }
        else{
            $MClog = new stdClass();
            $MClog->requestDate='';
            $table->addCell(2000, $cellStyle1)->addText('');
        }
        $table->addCell(1000, $cellStyle1)->addText($MClog->requestDate);

        $table->addRow();
        $table->addCell(1000, $cellStyle1)->addText($this->lang->outwarddelivery->outerReviewNodeList['5']);
        $table->addCell(1000, $cellStyle1)->addText(zget($users,'guestcn',','));
        if(in_array($modifycncc->status,array('withexternalapproval','modifyfail','modifysuccesspart','modifysuccess','modifyreject'))){
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modifycncc->statusList,$modifycncc->status));
        }
        else{
            $table->addCell(1000, $cellStyle1)->addText('');
        }

        if($modifycncc->reasonCNCC){
        $table->addCell(2000, $cellStyle1)->addText($modifycncc->reasonCNCC);
        }
        else{
            $table->addCell(2000, $cellStyle1)->addText('');
        }

        if(strtotime($modifycncc->feedbackDate)>0){
        $table->addCell(1000, $cellStyle1)->addText($modifycncc->feedbackDate);
        }
        else {
            $table->addCell(1000, $cellStyle1)->addText('');
        }

        /* Consumed. */
        $table->addRow();
        $table->addCell(6000, $cellStyle6)->addText($this->lang->consumedTitle, 'font_bold', array('align' => 'center'));

        $table->addRow();
        $table->addCell(2000, $cellStyle2)->addText($this->lang->modifycncc->nodeUser);
        //$table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->consumed);
        $table->addCell(1000, $cellStyle1)->addText($this->lang->modifycncc->before);
        $table->addCell(2000, array('gridSpan' => 2))->addText($this->lang->modifycncc->after);

        $modifycncc->consumed = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('outwarddelivery') //状态流转 工作量
        ->andWhere('objectID')->eq($parentId)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();

        foreach($modifycncc->consumed as $c)
        {
            $table->addRow();
            $table->addCell(2000, $cellStyle2)->addText(zget($users, $c->createdBy, ''));
            //$table->addCell(1000, $cellStyle1)->addText($c->consumed . '' . $this->lang->hour);
            $table->addCell(1000, $cellStyle1)->addText(zget($this->lang->modifycncc->statusList, $c->before, '-'));
            $table->addCell(2000, array('gridSpan' => 2))->addText(zget($this->lang->modifycncc->statusList, $c->after, '-'));
        }

        $this->loadModel('file')->export2Word($this->lang->modifycncc->exportTitle . $modifycncc->code, $phpWord);
    }

    /**
     * copy a modifycncc.
     * 
     * @param  int $modifycnccID 
     * @access public
     * @return void
     */
     public function copy($modifycnccID = 0)
     {
        if($_POST)
        {
            $modifycnccID = $this->modifycncc->create();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->loadModel('action')->create('modifycncc', $modifycnccID, 'created', $this->post->comment);
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }
         //变更信息
         $modifycncc                 = $this->modifycncc->getByID($modifycnccID);
         $this->view->title          = $this->lang->modifycncc->copy;
         $this->view->apps           = $this->loadModel('application')->getapplicationNameCodePairs();
         $this->view->modifycnccList = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),`desc`),"）")')->from(TABLE_MODIFYCNCC)->fetchPairs();
         $this->view->requirement    = $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->fetchPairs();
         $this->view->users          = $this->loadModel('user')->getPairs('noclosed');
         $this->view->problems       = array('' => '') + $this->loadModel('problem')->getPairsAbstract('noclosed');
         $this->view->cbpproject     = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
         $this->view->demands        = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
//        $this->view->projects  = array('' => '') + $this->loadModel('project')->getPairs('noclosed');
         $this->view->projects  =  array('' => '') + $this->loadModel('projectplan')->getCodeProjects($modifycncc->fixType == 'second');
         $this->view->products  = array('' => '') + $this->loadModel('product')->getPairs();
         $this->view->modifycncc    = $modifycncc;

         //审核节点以及审核节点的审核人
         $nodesReviewers = $this->loadModel('review')->getAllNodeReviewers('modifycncc', $modifycnccID, $modifycncc->version);
         $this->view->nodesReviewers = $nodesReviewers;

         //审核节点下的审核人列表
         $reviewers            = $this->modifycncc->getReviewers();
         //审核节点下的审核人列表
         $reviewerAccounts     = $this->loadModel('review')->getReviewerAccounts($reviewers);
         //审核节点下默认设置审核节点人
         $defChosenReviewNodes = $this->config->modifycncc->create->setDefChosenReviewNodes;
         $this->view->reviewers            = $reviewers;
         $this->view->reviewerAccounts     = $reviewerAccounts;
         $this->view->defChosenReviewNodes = $defChosenReviewNodes;

         $this->display();
     }


    /**
     * Desc:实现方式与所属项目联动
     * Date: 2022/3/21
     * Time: 17:00
     *
     * @param string $fixType
     *
     */
    public function ajaxGetSecondLine($fixType)
    {
        $secondLineType = $fixType == 'second';
        $projects = array('' => '') +  $this->loadModel('projectplan')->getCodeProjects($secondLineType);
        echo html::select('project[]', $projects, '',"class='form-control chosen' multiple");
    }

    public function ajaxGetApp($isCPCC=false)
    {
        $apps = array('' => '') + $this->loadModel('application')->getapplicationNameCodePairsWithPartition2($isCPCC);
        echo html::select('appmodify[]', $apps, '', "class = 'form-control chosen' onchange='selectApp(this.value,this.id)'");
    }

    /**
     * Desc: 根据系统code获取分区信息
     * User: chendongcheng
     * Date: 2022/5/26
     * Time: 16:00
     *
     * @param $applicationcode
     *
     */
    public function ajaxGetPartitionByCode($applicationcode)
    {
        $applicationcode = implode('-',explode('^',$applicationcode));   
        $applicationcode = urldecode($applicationcode);     
        if($applicationcode)
        {
            $partitionList = $this->dao->select('name,name')->from(TABLE_PARTITION)
            ->where('application')->eq($applicationcode)
            ->andWhere('deleted')->eq('0')
            ->fetchPairs();
            echo html::select('partition[]', $partitionList, '', "class='form-control chosen' multiple");
        }
        else
        {
            echo html::select('partition[]', array(), '', "class='form-control chosen' multiple");
        }
    }

    /**
     * 编辑退回次数
     * @param $testingrequestID
     * @return void
     */
    public function editreturntimes($outwardDeliveryId = 0){
        if($_POST)
        {
            $this->modifycncc->editreturntimes($outwardDeliveryId);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }


            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }
        $this->view->title             = $this->lang->modifycncc->editreturntimes;
        $this->display();
    }

    /**
     * 同步发布信息到发布表
     */
    public function syncReleaseInfo($type=1)
    {
//        $sql = "select id, `release`, createdBy, createdDate,createdDept from zt_modifycncc zm where 1 and status in ('modifysuccess', 'modifysuccesspart') and releaseSyncStatus = 1 and `release` != ''  and createdDate >= '2021-01-01 00:00:00.00'";
//        $data = $this->dao->query($sql)->fetchAll();
        $data = $this->dao->select("id, `code`, `release`, createdBy, createdDate, createdDept, `status`")
            ->from(TABLE_MODIFYCNCC)
            ->where("status")->in($this->lang->modifycncc->syncReleaseStatus)
            ->andwhere('releaseSyncStatus')->eq(1)
            ->andwhere('`release`')->ne('')
            ->andwhere('createdDate')->ge('2021-01-01 00:00:00')
            ->fetchAll();
        if (!$data) {
            echo '没有数据需要同步';
            exit();
        }
        $releaseIds = [];
        foreach ($data as $key => $val) {
            $currentReleaseIds = explode(',', $val->release);
            $val->releaseIds = $currentReleaseIds;
            $data[$key] = $val;
            $releaseIds = array_merge($releaseIds, $currentReleaseIds);
        }

        $select = 'id, status, dealUser, version, syncObjectCreateTime,syncStateTimes,createdBy';
        $releaseList = $this->loadModel('projectrelease')->getValidListByIds($releaseIds, $select,true);
        if (!$releaseList) {
            echo '没有数据需要同步';
            exit();
        }
        $releaseList = array_column($releaseList, null, 'id');
        $data = array_column($data, null, 'id');

        //要操作的发布信息
        $tempReleaseList = [];
        foreach ($data as $val) {
            $currentReleaseIds = $val->releaseIds;
            foreach ($currentReleaseIds as $releaseId) {
                $releaseInfo = zget($releaseList, $releaseId);
                if (!$releaseInfo) {
                    continue;
                }
                $releaseInfo->syncModifyId = $val->id;
                $tempReleaseList[] = $releaseInfo;
            }
        }
        if (!$tempReleaseList) {
            echo '没有数据需要同步';
            exit();
        }

        $i = 0;
        $releaseSyncStatus = 2;
        $updateParams = new stdClass();
        $updateParams->releaseSyncStatus = $releaseSyncStatus;
        foreach ($tempReleaseList as $val) {
            $syncModifyId = $val->syncModifyId;
            $modifyInfo = zget($data, $syncModifyId);
            if (($modifyInfo->createdDate > $val->syncObjectCreateTime) && ($modifyInfo->createdBy)) {
                //2023-01-01~2023-04-30之后的表单刷成CM
                $dealUser = $modifyInfo->createdBy.',';
                //需求收集3670 将变更单创建人所属部门 cm 拼接
                //if($type != 1){
                    $deptObj = $this->loadModel('dept')->getByID($modifyInfo->createdDept);
                    $dealUser .= $deptObj->cm;
               // }
                $modifyInfo->status = zget($this->lang->modifycncc->statusList, $modifyInfo->status);
                $res = $this->loadModel('projectrelease')->syncObjectInfo($val, 'modifycncc', $syncModifyId,  trim($dealUser,','), $modifyInfo->createdDate, $modifyInfo);
                $i++;
            }
            $this->dao->update(TABLE_MODIFYCNCC)->data($updateParams)->where('id')->eq($syncModifyId)->exec();
        }
        echo '处理了' . $i . '条数据';
        exit();
    }
    public function importpartition()
    {
        if($_FILES)
        {
            /* 如果文件存在，则判断文件类型是否符合要求。*/
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];
            if($file['extension'] != 'xlsx') die(js::alert($this->lang->file->onlySupportXLSX));

            /* 将导入的文件存放于临时目录。*/
            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            /* 加载phpexcel库，解析excel文件内容，解析完调用showImport方法进行数据确认。*/
            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if(!$phpReader->canRead($fileName))
            {
                $phpReader = new PHPExcel_Reader_Excel5();
                if(!$phpReader->canRead($fileName))die(js::alert($this->lang->excel->canNotRead));
            }
            $importPartitionParms['name'] = $_POST['name'];
            $importPartitionParms['application'] = $_POST['application'];
            $importPartitionParms['applicationName'] = $_POST['applicationName'];
            $importPartitionParms['ip'] = $_POST['ip'];
            $importPartitionParms['dataOrigin'] = $_POST['dataOrigin'];
            foreach($importPartitionParms as $key => $item){
                if(array_search($item,$importPartitionParms)!=$key){
                    dao::$errors[''] = $item.'匹配了多个字段';
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                    $this->send($response);
                }
            }
            $this->session->set('importPartitionParms', json_encode($importPartitionParms));
            $this->session->set('fileImport', $fileName);
            die(js::locate(inlink('showImport'), 'parent.parent'));
        }

        $this->display();
    }

    public function showImport()
    {
        /* 获取import方法导入的临时文件。*/
        $file    = $this->session->fileImport;
        $importPartitionParms    = json_decode($this->session->importPartitionParms);

        $partitionByIp = $this->dao->select('name,id,application,applicationName,ip,dataOrigin')->from(TABLE_PARTITION)->where('deleted')->ne('1')->fetchall('ip');
        
        $fields = (array)$importPartitionParms;
        $data = array();
        $rows = $this->loadModel('file')->getRowsFromExcel($file);
        $number = 0;
        $columnKey = array();
        $origin = array('1'=>'NPC','2'=>'CCPC','3'=>'央行云','99'=>'');
        foreach($rows[1] as $currentColumn => $cellValue){
            $field = array_search($cellValue, $fields);
            if($field){
                $number = $number + 1;
                $columnKey[$currentColumn] = $field;
            }else{
                $columnKey[$currentColumn] = '';
            }
        }
        if($number!=5){
            dao::$errors[''] = '部分字段表中不存在,请检查后重试';
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
        foreach($rows as $currentRow => $row)
        {
            $item = array();
            foreach($row as $currentColumn => $cellValue)
            {
                if($currentRow == 1)
                {
                    continue;
                }

                /* 判断该列是否存在于导入的列中。*/
                if(empty($columnKey[$currentColumn]))
                {
                    continue;
                }
                if($columnKey[$currentColumn]=='dataOrigin'){
                    $item[$columnKey[$currentColumn]] = array_search($cellValue, $origin);
                }else{
                    $item[$columnKey[$currentColumn]] = $cellValue;
                }
            }
            if(empty($item['name'])) continue;
            $data[$currentRow] = $item;
            unset($item);
        }
        $delete = 0;
        $insert = 0;
        $update = 0;
        foreach($data as $item){
            if($item['name']=='***'or$item['name']==''){
                if($partitionByIp[$item['ip']]){
                    $date = helper::today();
                    $delete = $delete + 1;
                    // var_dump($this->dao->update(TABLE_PARTITION)->set('deleted')->eq('1')->set('deletedDate')->eq($date)->where('ip')->eq($item['ip'])->get());
                    $this->dao->update(TABLE_PARTITION)->set('deleted')->eq('1')->set('deletedDate')->eq($date)->where('ip')->eq($item['ip'])->exec();
                }
                continue;
            }
            $itemInDatabase = $partitionByIp[$item['ip']];
            $itemInDatabase = (array)$itemInDatabase;
            if(empty($itemInDatabase)){
                $insert = $insert + 1;
                // var_dump($this->dao->insert(TABLE_PARTITION)->data($item)->get());
                $this->dao->insert(TABLE_PARTITION)->data($item)->exec();
                continue;
            }
            $diff = array_diff($itemInDatabase,$item);
            if(count($diff)>1){
                $update = $update + 1;
                // var_dump($this->dao->update(TABLE_PARTITION)->data($item)->where('id')->eq($itemInDatabase['id'])->get());
                $this->dao->update(TABLE_PARTITION)->data($item)->where('id')->eq($itemInDatabase['id'])->andwhere('deleted')->ne('1')->exec();
            }

        }
        var_dump('新增分区：'.$insert.'个；更新分区：'.$update.'个；删除分区：'.$delete.'个');
        die();
    }
    public function showHistoryNodes($id){
        $modify = $this->loadModel('outwarddelivery')->getByID($id);
        $reviewFailReason = json_decode($modify->reviewFailReason,true);
        $this->app->loadLang('outwarddelivery');
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('outwarddelivery')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('outwarddelivery', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
                if ($v->stage == 4 && isset($modify->createdDate) && $modify->createdDate > "2024-04-02 23:59:59"){
                    unset($data[$k]);
                }
                if (in_array($v->stage-1, $this->lang->outwarddelivery->skipNodes) and (!in_array($v->status,['pass','reject']))) {
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if (isset($reviewFailReason[$key][4]) && !empty($reviewFailReason[$key][4])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][5]) && !empty($reviewFailReason[$key][5])){
                $nodes[$key]['countNodes']++;
            }
        }
        $this->view->nodes      = $nodes;
        $this->view->outwarddelivery     = $modify;
        $this->view->modifycncc = $modify->isNewModifycncc ? $this->modifycncc->getByID($modify->modifycnccId) : new stdClass();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->reviewFailReason      = $reviewFailReason;
        $this->display();
    }
    /**
     * Desc: 根据系统code获取分区信息 返回html标签 多选框
     * User: songdi
     * Date: 2023/6/21
     * Time: 09:36
     * @param $applicationcode
     *
     */
    public function ajaxGetPartitionByCodeNew()
    {
        $applicationcode = implode('-',explode('^',$_POST['applicationcode']));
        $applicationcode = urldecode($applicationcode);
        if($applicationcode)
        {
            $partitionList = $this->dao->select('name,name')->from(TABLE_PARTITION)
                ->where('application')->eq($applicationcode)
                ->andWhere('deleted')->eq('0')
                ->fetchPairs();
            foreach ($partitionList as $key=>$item) {
                $partitionList[$key] = strtolower($item);
                $oldpartitionList[$key] = strtolower($item);
            }
            sort($partitionList);
            foreach ($partitionList as $k1=>$v1){
                $newpartitionList[array_search($v1,$oldpartitionList)] = array_search($v1,$oldpartitionList);
            }
            echo html::checkbox("partition",$newpartitionList);
//            echo html::select('partition[]', $partitionList, '', "class='form-control chosen' multiple");
        }
        else
        {
            echo "";
        }
    }
}
