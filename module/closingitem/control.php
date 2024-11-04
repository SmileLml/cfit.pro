<?php
class closingitem extends control{
    /**
     * 项目结项列表
     */
    public function browse($projectID){
        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('datatable');
        $this->loadModel('projectplan');
        $users = $this->loadModel('user')->getPairs('noletter');
        $this->session->set('closingitemHistory', $this->app->getURI(true));

        $this->view->title        = $this->lang->closingitem->browse ;
        $this->view->position[]   = $this->lang->closingitem->browse;
        $this->view->closingitem  = $this->closingitem->getByProjectID($projectID);
        $this->view->projectID    = $projectID;
        $this->view->users        = $users;
        $this->view->typeList     = $this->lang->projectplan->typeList;
        $this->display();
    }

    /**
     *项目结项详情
     *
     * @param $reviewId
     */
    public function view($projectId, $itemId = ''){
        $this->loadModel('project')->setMenu($projectId);
        $objectType = $this->lang->closingitem->objectType;
        $this->loadModel('projectplan');
        $this->loadModel('closingadvise');
        $this->loadModel('component');

        // 结项信息
        $closingitem = $this->closingitem->getByID($itemId);

        $actions = $this->loadModel('action')->getList($objectType, $itemId);
        $users   = $this->loadModel('user')->getPairs('noletter');
        $closingitem->assembly = json_decode(str_replace('&quot;', '"', $closingitem->assemblyInfo),true);
        $closingitem->tools = json_decode(str_replace('&quot;', '"', $closingitem->toolsInfo), true);
        $closingitem->knowledge = json_decode(str_replace('&quot;', '"', $closingitem->knowledgeInfo), true);

        $this->view->title = $this->lang->closingitem->view;
        $this->view->position[] = $this->lang->closingitem->view;
        $this->view->closingitem = $closingitem;
        $this->view->components  = $this->component->getComponentPairs();
        $this->view->statusList   = $this->lang->component->statusList;
        $this->view->levelList    = $this->lang->component->levelList;
        $this->view->adviseStatus = $this->lang->closingitem->feedbackResult + $this->lang->closingadvise->browseStatus;
        $this->view->actions = $actions; //日志信息
        $this->view->users = $users;
        $this->view->objectType = $objectType;
        $this->view->project = $projectId;
        $this->view->typeList     = $this->lang->projectplan->typeList;
        $this->display();
    }

    /**
     * 创建结项
     * @param int $projectID
     * @param string $object
     * @param int $productID
     * @param string $reviewRange
     * @param string $checkedItem
     */
    public function create($projectID = 0)
    {
        if($_POST)
        {
            $closingitemID = $this->closingitem->create($projectID);

            if(!dao::isError())
            {
                $this->loadModel('action')->create('closingitem', $closingitemID, 'created', $this->post->comment, $this->app->user->account);
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = inlink('browse', "project=$projectID");
                $response['callback'] = "addDisabled()";
                $this->send($response);
            }

            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $response['callback'] = "addDisabled()";
            $this->send($response);
        }
        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('projectplan');
        $this->loadModel('component');

        // 查询项目类型
        $projectPlan = $this->dao->select('type')->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->fetch();
        $components  = $this->component->getComponentPairs();

        $this->view->title        = $this->lang->closingitem->create;
        $this->view->position[]   = $this->lang->closingitem->create;
        $this->view->projectPlan  = $projectPlan;
        $this->view->projectID    = $projectID;
        $this->view->components   = array('' => '') + $components;
        $this->view->typeList     = $this->lang->projectplan->typeList;
        $this->view->statusList   = $this->lang->component->statusList;
        $this->view->levelList    = array('' => '/') + $this->lang->component->levelList;
        $this->view->users        = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }

    // 编辑
    public function edit($itemId = 0, $projectID)
    {
        if($_POST)
        {
            $changes = $this->closingitem->update($itemId, $projectID);

            if(!dao::isError())
            {
                $actionID = $this->loadModel('action')->create('closingitem', $itemId, 'edited');
                $this->action->logHistory($actionID, $changes);
                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['callback'] = "addDisabled()";
                $response['locate']  = inlink('browse', "project=$projectID");
                $this->send($response);
            }

            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $response['callback'] = "addDisabled()";
            $this->send($response);
        }
        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('component');
        $this->loadModel('projectplan');

        // 查询结项信息
        $itemInfo = $this->closingitem->getByID($itemId);
        $components  = $this->component->getComponentPairs();

        $this->view->title        = $this->lang->closingitem->edit;
        $this->view->position[]   = $this->lang->closingitem->edit;
        $this->view->closingitem  = $itemInfo;
        $this->view->projectID    = $projectID;
        $this->view->components   = array('' => '') + $components;
        $this->view->typeList     = $this->lang->projectplan->typeList;
        $this->view->statusList   = $this->lang->component->statusList;
        $this->view->levelList    = array('' => '/') + $this->lang->component->levelList;
        $this->view->users        = $this->loadModel('user')->getPairs('noclosed');
        $this->view->assemblyInfo   = isset($itemInfo->assemblyInfo) ? json_decode(str_replace('&quot;', '"', $itemInfo->assemblyInfo),true) : [['codes1' =>'','assemblyIndex' =>'','assemblyDesc' =>'','assemblyLevel' =>'']];
        $this->view->toolsInfo      = isset($itemInfo->toolsInfo) ? json_decode(str_replace('&quot;', '"', $itemInfo->toolsInfo),true) : [['codes3' =>'','toolsName' =>'','toolsVersion' =>'','isTesting' =>'','toolsType' =>'','toolsDesc' =>'']];
        $this->view->knowledgeInfo  = isset($itemInfo->knowledgeInfo) ? json_decode(str_replace('&quot;', '"', $itemInfo->knowledgeInfo),true) : [['codes7' =>'','submitFileName' =>'','submitReason' =>'','versionCodeOSSP' =>'','advise' =>'']];
        $this->display();
    }

    // 提交
    public function submit($itemID, $projectID, $confirm = 'no')
    {
        if (!empty($itemID)) {
            if($confirm == 'no')
            {
                echo js::confirm($this->lang->closingitem->confirmSubmit, $this->createLink('closingitem', 'submit', "itemID=$itemID&projectID=$projectID&confirm=yes"), '');
                exit;
            }
            else
            {
                // 改主表数据
                $itemInfo = $this->closingitem->getByID($itemID);
                $dept = $this->dao->select('bearDept')->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->fetch();
                $deptQa = $this->dao->select('qa')->from(TABLE_DEPT)->where('id')->eq($dept->bearDept)->fetch();

                // 数据存主表
                $this->dao->update(TABLE_CLOSINGITEM)->set('status')->eq($this->lang->closingitem->statusList['waitPreReview'])->set('dealuser')->eq($deptQa->qa)->where('id')->eq($itemID)->exec();

                // 保存历史记录和流程状态
                $this->loadModel('action')->create('closingitem', $itemID, 'submitexamine');
                $this->loadModel('consumed')->record('closingitem', $itemID, '0', $this->app->user->account, $itemInfo->status, $this->lang->closingitem->statusList['waitPreReview']);

                if (isonlybody()) {
                    die(js::closeModal('parent.parent', $this->session->common_back_url));
                }
                die(js::reload('parent'));
            }
        }
    }

    // 审批
    public function review($itemID){
        if($_POST)
        {
            $logChanges = $this->closingitem->review($itemID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('closingitem', $itemID, 'reviewed', $this->post->suggest);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title          = $this->lang->review->submit;
        $this->view->position[]     = $this->lang->review->submit;
        $this->view->closingitem    = $this->closingitem->getByID($itemID);
        $this->display();

    }

    //删除
    public function delete($itemID = 0, $confirm = 'no')
    {
        if (!empty($itemID)) {
            if($confirm == 'no')
            {
                echo js::confirm($this->lang->closingitem->confirmDelete, $this->createLink('closingitem', 'delete', "itemID=$itemID&confirm=yes"), '');
                exit;
            }
            else
            {
                // 删除主表记录
                $this->dao->update(TABLE_CLOSINGITEM)
                    ->set('deleted')->eq('1')
                    ->where('id')->eq($itemID)->exec();
                $this->loadModel('action')->create('closingitem', $itemID, 'deleted');

                // 删除意见表记录
                $this->dao->update(TABLE_CLOSINGADVISE)
                    ->set('deleted')->eq('1')
                    ->where('itemId')->eq($itemID)->exec();

                if (isonlybody()) {
                    die(js::closeModal('parent.parent', $this->session->common_back_url));
                }
                die(js::reload('parent'));
            }
        }
    }


    // 获取组件相关信息
    public function ajaxGetComponent($component, $val){
        $this->loadModel('component');
        $componentInfo = $this->loadModel('component')->getByID($component);
        $statusList   = $this->lang->component->statusList;
        $tlevelList    = $this->lang->component->levelList;
        switch($val) {
            case 'functionDesc':
                $str  = empty($componentInfo->$val) ? $componentInfo->$val: html_entity_decode(strip_tags($componentInfo->$val));
                die($str);
            case 'level';
                die(html::select('assemblyLevel[]', $tlevelList, $componentInfo->$val, "class='form-control chosen'"));
//            case 'status';
//                die(html::select('assemblyStatus[]', $statusList, $componentInfo->$val, "class='form-control chosen'"));
        }
    }

}
