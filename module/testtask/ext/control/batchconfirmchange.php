<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");

class mytesttask extends testtask
{
    /**
     * Batch confirm change.
     *
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function batchConfirmChange($taskID = 0)
    {
        if(isset($_POST['caseIDList']))
        {
            $caseIDList = $this->post->caseIDList ? $this->post->caseIDList : die(js::locate($this->session->caseList));
            $caseIDList = array_unique($caseIDList);

            $this->loadModel('action');
            foreach($caseIDList as $caseID)
            {
                $this->testtask->confirmCaseChange($taskID, $caseID);
                $this->action->create('case', $caseID, 'confirmChange');
            }
            die(js::locate($this->session->caseList));
        }
    }
}