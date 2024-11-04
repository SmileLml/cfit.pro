<?php

include '../../control.php';
class myProblem extends problem
{
    public function delay($id)
    {
        $problem = $this->loadModel('problem')->getByID($id, true);
        $message = $this->loadModel('problem')->delayCheck($problem);
        if(!empty($message)){
          /*  $response['result']  = 'fail';
            $response['message'] = $message;
            $response['locate']  = inlink('view', "problemID=$id");
            $this->send($response);*/
            echo js::alert($message);
            die(js::reload('parent'));
        }
        if ($_POST) {
            $changes = $this->problem->delay($id);

            if (dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('problem', $id, 'problemchange', $this->post->comment, $this->post->dealUser);
            if($changes) $this->action->logHistory($actionID, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $month                           = $this->lang->problem->problemOutTime['problemOutTime']     ?? 2; //解决时间超时月份
        $problem->originalResolutionDate = !empty($problem->dealAssigned) ? $this->problem->getOverDate($problem->dealAssigned, $month) : $this->problem->getOverDate($problem->createdDate, $month);

        $this->view->problem = $problem;
        $this->display();
    }
}
