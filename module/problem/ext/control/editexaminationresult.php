<?php
include '../../control.php';
class myProblem extends problem
{

    /**
     * 编辑考核结果
     */
    public function editExaminationResult($problemID)
    {
        if($_POST)
        {
            $changes = $this->problem->editExaminationResult($problemID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('problemeditexaminationresulted', $problemID, 'editexaminationresulted', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);
        }

        $this->view->title   = $this->lang->problem->edit;
        $this->view->problem = $this->problem->getByID($problemID);

        $this->display();
    }
}
