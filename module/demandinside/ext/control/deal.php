<?php

include '../../control.php';
class myDemandinside extends demandinside
{
    /**
     * 20220311 新增 系统部验证 验证人员 实验室验证  测试人员逻辑
     * @param $demandID
     */
    public function deal($demandID)
    {
        if($_POST)
        {
            $changes = $this->demandinside->deal($demandID);

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

        $demand = $this->loadModel('demandinside')->getByID($demandID);

        $statusList = array('' => '');
        switch($demand->status)
        {
            case 'wait':
                $statusList['feedbacked'] = $this->lang->demandinside->statusList['feedbacked'];
                break;
        }
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->title      = $this->lang->demandinside->edit;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->demand     = $demand;
        $this->view->statusList = $statusList;
        //根据项目实现和二线实现，默认获取对应所属项目list
        $this->view->plans      = array('0' => '') + $this->loadModel('projectplan')->getAliveProjects($demand->fixType == 'second');
        $this->view->apps       = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
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