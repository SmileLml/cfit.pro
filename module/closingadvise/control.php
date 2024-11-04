<?php
class closingadvise extends control{
    /**
     * 项目结项列表
     */
    public function browse($projectID, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1){

        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('datatable');
        $this->loadModel('closingitem');
        $users = $this->loadModel('user')->getPairs('noletter');
        $this->session->set('closingAdviseHistory', $this->app->getURI(true));

        // 查询反馈结果状态
        $feedbackResults     = $this->lang->closingitem->feedbackResult;

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title              = $this->lang->closingadvise->browse ;
        $this->view->position[]         = $this->lang->closingadvise->browse;
        $this->view->orderBy            = $orderBy;
        $this->view->pager              = $pager;
        $this->view->closingadvise      = $this->closingadvise->getByProjectID($projectID, $orderBy, $pager);
        $this->view->itemStatus         = $this->closingitem->getItemStatusPairsByProjectID($projectID); // 查询结项状态
        $this->view->projectID          = $projectID;
        $this->view->feedbackResults    = $feedbackResults;
        $this->view->users              = $users;
        $this->display();
    }

    /**
     *项目结项详情
     *
     * @param $reviewId
     */
    public function view($projectId, $adviseId){
        $this->loadModel('closingitem');
        $this->loadModel('project')->setMenu($projectId);
        $objectType = $this->lang->closingadvise->objectType;

        $actions = $this->loadModel('action')->getList($objectType, $adviseId);
        $users   = $this->loadModel('user')->getPairs('noletter');
        $this->view->title = $this->lang->closingadvise->view;
        $this->view->position[] = $this->lang->closingadvise->view;

        $this->view->actions          = $actions; //日志信息
        $this->view->users            = $users;
        $this->view->objectType       = $objectType;
        $this->view->projectId        = $projectId;
        $this->view->feedbackResults  = $this->lang->closingitem->feedbackResult;
        $this->view->closingadvise    = $this->closingadvise->getByID($adviseId);
        $this->display();
    }

    // 审批
    public function review($itemID){
        if($_POST)
        {
            $logChanges = $this->closingadvise->review($itemID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('closingadvise', $itemID, 'reviewed', $this->post->suggest);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->loadModel('closingitem');
        $closingadvise       = $this->closingadvise->getByID($itemID);

        // 查询反馈结果状态
        $feedbackResults     = $this->lang->closingitem->feedbackResult;

        $this->view->title            = $this->lang->review->submit;
        $this->view->position[]       = $this->lang->review->submit;
        $this->view->closingadvise    = $closingadvise;
        $this->view->feedbackResults  = $feedbackResults;
        $this->display();

    }

    // 指派待处理人
    public function assignUser($id){
        if($_POST){
            $logChanges = $this->closingadvise->assignUser($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('closingadvise', $id, 'assigned', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $dealuserStr = '';
        $closingadvise = $this->closingadvise->getByID($id);
        $users       = $this->loadModel('user')->getPairs('noletter');

        // 查询当前待处理人
        $dealuserArr = explode(',',$closingadvise->dealuser);
        if(!empty($dealuserArr)){
            $names = array_flip(array_flip($users));
            foreach($dealuserArr as $name){
                if($names[$name]){
                    $dealuserStr .= $names[$name].',';
                }
            }
            $dealuserStr = substr($dealuserStr,0,-1);
        }

        $this->view->reviewers    = $closingadvise->dealuser;
        $this->view->dealuserStr  = $dealuserStr;
        $this->view->users        = $users;
        $this->display();
    }

}
