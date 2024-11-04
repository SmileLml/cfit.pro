<?php
/**
 * The control file of osspchange module of ZenTaoPMS.
 */
class osspchange extends control{
    /**
     * 列表
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = "id_desc", $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $this->loadModel('datatable');

        /* By search. */
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $params = "&browseType=bySearch&param=myQueryID&orderBy=$orderBy";
        $actionURL = $this->createLink('osspchange', 'browse', $params);
        $this->osspchange->buildSearchForm($queryID, $actionURL);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $osspchanges = $this->osspchange->getList($browseType, $queryID, $orderBy, $pager);
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->title = $this->lang->osspchange->common;

        $this->view->pager              = $pager;
        $this->view->users              = $users;
        $this->view->orderBy            = $orderBy;
        $this->view->browseType         = $browseType;
        $this->view->status             = $browseType;
        $this->view->osspchanges        = $osspchanges;
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('osspchangeList', $this->app->getURI(true), 'backlog');

        $this->display();
    }

    /**
     * 新建
     */
    public function create($orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        $this->loadModel('application');
        $this->app->loadLang('opinion');
        $params = "&browseType=all&param=0&orderBy=$orderBy" . "&recTotal=$recTotal" . "&recPerPage=$recPerPage" . "&pageID=$pageID";
        if ($_POST) {
            $osspchangeID = $this->osspchange->create();
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('osspchange', $osspchangeID, 'created', $this->post->comment);
            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('osspchange', 'browse', $params)));
        }

        // 查询所有用户
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');

        // 返回数据
        $this->view->title          = $this->lang->osspchange->create;
        $this->view->users          = $users;
        $this->display();
    }

    /**
     * 提交
     */
    public function submit($osspchangeID = 0, $confirm = 'no')
    {
        if (!empty($osspchangeID)) {
            if($confirm == 'no')
            {
                echo js::confirm($this->lang->osspchange->confirmSubsmit, $this->createLink('osspchange', 'submit', "osspchangeID=$osspchangeID&confirm=yes"), '');
                exit;
            }
            else
            {
                $osspchangeInfo = $this->osspchange->getByID($osspchangeID);
                $status = $this->lang->osspchange->statusList['waitConfirm'];
                $version = $osspchangeInfo->version;

                // 修改移交单状态和待处理人
                $this->dao->update(TABLE_OSSPCHANGE)
                    ->set('status')->eq($status)
                    ->set('dealuser')->eq($this->config->osspchange->interfacePerson)
                    ->set('version')->eq($version)
                    ->set('submitBy')->eq($this->app->user->account)
                    ->set('submitDate')->eq(helper::now())
                    ->where('id')->eq($osspchangeID)
                    ->exec();

                // 生成待接口人确认节点
                $reviewStatus = 'pending';
                $stage        = '0';
                $extParams    = [
                    'nodeCode' => $status,
                ];
                $this->loadModel('review')->addNode('osspchange', $osspchangeID, $version, explode(',',$this->config->osspchange->interfacePerson), true, $reviewStatus, $stage, $extParams);

                // 记录操作历史和状态流程
                $this->loadModel('action')->create('osspchange', $osspchangeID, 'submited');
                $this->loadModel('consumed')->record('osspchange', $osspchangeID, '0', $this->app->user->account, $osspchangeInfo->status, $status, array());

                if (isonlybody()) {
                    die(js::closeModal('parent.parent', $this->session->common_back_url));
                }
                die(js::reload('parent'));
            }
        }
    }

    // 体系接口人确认
    public function confirm($id, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1){
        // 查询当前数据
        $oldData = $this->osspchange->getByID($id);

        if ($_POST) {
            $osspchangeID = $this->osspchange->confirm($oldData);
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($_POST['type'] == 'save'){
                $actionID = $this->loadModel('action')->create('osspchange', $id, 'edited');
                $this->action->logHistory($actionID, $osspchangeID);
            }else{
                $this->loadModel('action')->create('osspchange', $osspchangeID, 'confirm');
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        // 查询所有部门与用户
        $depts          = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();
        $users          = $this->loadModel('user')->getPairs('noclosed|noletter');
        $QMDmanager     = $this->dao->select('manager')->from(TABLE_DEPT)->where('id')->eq(3)->fetch('manager');

        // 返回数据
        $this->view->title          = $this->lang->osspchange->confirm;
        $this->view->depts          = $depts;
        $this->view->users          = $users;
        $this->view->osspchange     = $oldData;
        $this->view->QMDmanager     = $QMDmanager;
        $this->display();
    }

    // 评审
    public function review($id, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1){
        // 查询当前数据
        $oldData = $this->osspchange->getByID($id);
        $resultList = $oldData->status == $this->lang->osspchange->statusList['waitDeptApprove'] ? $this->lang->osspchange->systemManagerList : $this->lang->osspchange->QMDmanagerList;

        if ($_POST) {
            $logChange = $this->osspchange->review($oldData);
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('osspchange', $id, 'reviewed', "审批结果：".$resultList[$this->post->result]."。审批意见：".$this->post->comment."。");
            $this->action->logHistory($actionID, $logChange);

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        // 查询对应处理结果
        if($oldData->status == $this->lang->osspchange->statusList['waitDeptApprove']){
            $result = $this->lang->osspchange->systemManagerList;
        }elseif($oldData->status == $this->lang->osspchange->statusList['waitQMDApprove']){
            $result = $this->lang->osspchange->QMDmanagerList;
        }else{
            $result = $this->lang->osspchange->maxLeaderList;
        }

        // 返回数据
        $this->view->title          = $this->lang->osspchange->review;
        $this->view->osspchange     = $oldData;
        $this->view->result         = $result;
        $this->display();

    }

    // 关闭
    public function close($id, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1){
        // 查询当前数据
        $oldData = $this->osspchange->getByID($id);

        if ($_POST) {
            $osspchangeID = $this->osspchange->close($oldData);
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('osspchange', $osspchangeID, 'closed');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        // 查询所有用户
        $users = $this->loadModel('user')->getPairs('noclosed|noletter');

        // 返回数据
        $this->view->title          = $this->lang->osspchange->close;
        $this->view->users          = $users;
        $this->view->QMDmanager     = $oldData->QMDmanager;
        $this->display();
    }

    // 编辑
    public function edit($id, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1){
        // 查询当前数据
        $oldData = $this->osspchange->getByID($id);

        if ($_POST) {
            $changeData = $this->osspchange->update($oldData);
            if(!empty($changeData))
            {
                $actionID = $this->loadModel('action')->create('osspchange', $id, 'edited');
                $this->action->logHistory($actionID, $changeData);
            }
            if (dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('osspchange', 'browse')));
        }

        // 查询用户和部门
        $users      = $this->loadModel('user')->getPairs('noletter');
        $depts      = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();

        // 返回数据
        $this->view->title      = $this->lang->osspchange->edit;
        $this->view->users      = $users;
        $this->view->depts      = $depts;
        $this->view->osspchange = $oldData;
        $this->display();
    }

    // 详情页
    public function view($id = 0){
        $data = $this->osspchange->getByID($id);

        // 返回数据
        $this->view->title       = $this->lang->osspchange->view;
        $this->view->osspchange  = $data;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->view->depts       = array('0'=>'') + $this->loadModel('dept')->getOptionMenu();
        $this->view->actions     = $this->loadmodel('action')->getList('osspchange', $id);
        $this->view->consumed    = $data->consumed;
        $this->view->nodes       = $this->loadModel('review')->getNodes('osspchange', $id, $data->version);
        $this->view->result      = $this->osspchange->getOsspResult($data);
        $this->view->resultList         = $this->lang->osspchange->resultList;
        $this->view->systemManagerList  = $this->lang->osspchange->systemManagerList;
        $this->view->QMDmanagerList     = $this->lang->osspchange->QMDmanagerList;
        $this->view->maxLeaderList      = $this->lang->osspchange->maxLeaderList;
        $this->view->closedList         = $this->lang->osspchange->interfaceClosedList;
        $this->display();
    }

    //删除
    public function delete($id = 0, $confirm = 'no')
    {
        if (!empty($id)) {
            if($confirm == 'no')
            {
                echo js::confirm($this->lang->osspchange->confirmDelete, $this->createLink('osspchange', 'delete', "ID=$id&confirm=yes"), '');
                exit;
            }
            else
            {
                $this->dao->update(TABLE_OSSPCHANGE)
                    ->set('deleted')->eq('1')
                    ->where('id')->eq($id)->exec();
                $this->loadModel('action')->create('osspchange', $id, 'deleted');

                if (isonlybody()) {
                    die(js::closeModal('parent.parent', $this->session->common_back_url));
                }
                die(js::reload('parent'));
            }
        }
    }

    // 下拉框联动
    public function ajaxGetSystemManager($dept = ''){
        $manager     = $this->dao->select('manager1')->from(TABLE_DEPT)->where('id')->eq($dept)->fetch('manager1');
        $users       = $this->loadModel('user')->getPairs('noclosed|noletter');
        die(html::select('systemManager', $users, $manager, 'class=form-control chosen'));
    }

    /**
     * 查询历史数据
     * @param $id
     * @return void
     */
    public function showHistoryNodes($id){
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('osspchange')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('osspchange', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
        }

        $this->view->nodes              = $nodes;
        $this->view->resultList         = $this->lang->osspchange->resultList;
        $this->view->systemManagerList  = $this->lang->osspchange->systemManagerList;
        $this->view->QMDmanagerList     = $this->lang->osspchange->QMDmanagerList;
        $this->view->maxLeaderList      = $this->lang->osspchange->maxLeaderList;
        $this->view->closedList         = $this->lang->osspchange->interfaceClosedList;
        $this->view->users              = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

}
