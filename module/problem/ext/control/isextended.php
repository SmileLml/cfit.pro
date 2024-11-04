<?php
include '../../control.php';
class myProblem extends problem
{
    public function isExtended($problemID)
    {
        $problem       = $this->problem->getByID($problemID);

        if($_POST)
        {
            $isExtended = $_POST['isExtended'] ?? '';
            if(empty($_POST['isExtended'])){
                dao::$errors['isExtended'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->isExtended);

                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->dao->update(TABLE_PROBLEM)->set('isExtended')->eq($isExtended)->where('id')->eq($problemID)->exec();

            $newProblem = $this->problem->getByID($problemID);

            $actionID = $this->loadModel('action')->create('problem', $problemID, 'updateIsExtended', $this->post->suggest);
            $this->action->logHistory($actionID, common::createChanges($problem, $newProblem));

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $this->view->problem       = $problem;
        $this->display();
    }
}