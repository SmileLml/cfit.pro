<?php

include '../../control.php';
class myProblem extends problem
{
    public function assignByUser($problemId)
    {
        $problem = $this->problem->getById($problemId);

        if(!empty($_POST))
        {
            $errorMsg = $this->problem->isAssigned($problem);
            if(!empty($errorMsg)){
                $response['result']  = 'fail';
                $response['message'] = $errorMsg;
                $this->send($response);
            }

            $this->loadModel('action');
            $dealUser = $this->post->dealUser;
            if(empty($dealUser)){
                $response['result']  = 'fail';
                $response['message'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->assignTo);
                $this->send($response);
            }
            if($dealUser == $problem->dealUser){
                $response['result']  = 'fail';
                $response['message'] = $this->lang->problem->assignToFail;
                $this->send($response);
            }

            $acceptUser = $this->loadModel('user')->getByAccount($dealUser);
            $data = new stdClass();
            $data->dealUser = $dealUser;
            $data->acceptUser = $dealUser;
            $data->acceptDept = $acceptUser->dept;
            if($problem->IssueId && $problem->ReviewStatus == 'tofeedback'){
                $data->feedbackToHandle = $dealUser;
            }
            $this->dao->update(TABLE_PROBLEM)->data($data)
                ->where('id')->eq($problemId)
                ->exec();

            $actionId = $this->action->create('problem', $problemId, 'assigned', $this->post->comment, $dealUser);
            $changes = common::createChanges($problem, $data);
            if($changes) $this->action->logHistory($actionId, $changes);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->title     = $this->lang->problem->assignedByUser;
        $this->view->users     = $this->loadModel('user')->getPairs('nodeleted|nofeedback', $problem->dealUser);
        $this->view->problem   = $problem;
        $this->view->problemId = $problemId;

        $this->display();
    }
}
