<?php

include '../../control.php';
class myDemand extends demand
{
    /**
     * 20220311 新增 系统部验证 验证人员 实验室验证  测试人员逻辑
     * @param $demandID
     */
    public function deal($demandID)
    {
        if($_POST)
        {
            $changes = $this->demand->deal($demandID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('demand', $demandID, 'deal', $this->post->comment);
            if($changes) $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $demand = $this->loadModel('demand')->getByID($demandID);
        $requirement = $this->loadModel('requirement')->getByID($demand->requirementID);
        $opinion = $this->loadModel('opinion')->getByID($demand->opinionID);

        //不可编辑提示语
        $cantDeal = '';
        if(!in_array($demand->status, $this->demand::$_dealStatus))
        {
            $cantDeal = $this->lang->demand->canDealMeg;
        }
        $statusList = array('' => '');
        switch($demand->status)
        {
            case 'wait':
                $statusList['feedbacked'] = $this->lang->demand->statusList['feedbacked'];
                break;
        }
        //标识变更锁定提示
        $this->view->opinion = $opinion;
        $this->view->requirement = $requirement;
        $this->view->opinionLock = isset($opinion->changeLock) ? $opinion->changeLock : '';
        $this->view->requirementLock = isset($requirement->changeLock) ? $requirement->changeLock : '';

        $this->view->cantDeal     = $cantDeal;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->demand->edit;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->demand     = $demand;
        $this->view->statusList = $statusList;
//        $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getPairs();
        //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getAliveProjects($demand->fixType == 'second');
        $this->view->apps       = array('0' => '') + $this->loadModel('application')->getPairs();
       // $this->view->products   = array('0' => '') + $this->loadModel('product')->getPairs();
        $this->view->products   = array('0' => '');
        if($demand->product){
            $this->view->productplan      = array('0' => '') + $this->loadModel('productplan')->getPairs($demand->product);
        }else{
            $this->view->productplan = array('0' => '');
        }
        /* Get executions. */
        $executions = array('' => '');
        $this->view->executions       = $executions;
        $this->display();
    }
}