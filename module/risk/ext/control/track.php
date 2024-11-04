<?php
include '../../control.php';
class myRisk extends risk
{
    public function track($riskID)
    {
        $risk = $this->risk->getById($riskID);
        $this->loadModel('project')->setMenu($risk->project);

        if($_POST)
        {
            $changes = array();
            if($this->post->isChange) $changes = $this->risk->track($riskID);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                return $this->send($response);
            }

            $this->loadModel('action');
            $actionID = $this->action->create('risk', $riskID, 'Tracked', $_POST['comment']);
            if(!empty($changes) or $_POST['comment'])
            {
                $this->action->logHistory($actionID, $changes);
            }

            if(isonlybody()) return $this->send(array('locate' => 'parent', 'message' => $this->lang->saveSuccess, 'result' => 'success'));
            return $this->send(array('locate' => inlink('browse', "projectID=$risk->project")));
        }

        $this->view->title      = $this->lang->risk->common . $this->lang->colon . $this->lang->risk->track;
        $this->view->position[] = $this->lang->risk->track;

        $this->view->risk  = $risk;
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');
        $this->display();
    }
}
