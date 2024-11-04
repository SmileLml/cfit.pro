<?php
include '../../control.php';
class myProblem extends problem
{
    public function isBackExtended($problemID)
    {
        $problem = $this->problem->getByID($problemID);

        if($_POST)
        {
            $isBackExtended = $_POST['isBackExtended'] ?? '';
            if(empty($_POST['isBackExtended'])){
                dao::$errors['isBackExtended'] = sprintf($this->lang->problem->emptyObject, $this->lang->problem->isBackExtended);

                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $this->dao->update(TABLE_PROBLEM)->set('isBackExtended')->eq($isBackExtended)->where('id')->eq($problemID)->exec();

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