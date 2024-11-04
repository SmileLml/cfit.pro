<?php
include '../../control.php';
class myTask extends task
{
    /**
     * Activate a task.
     *
     * @param  int    $taskID
     * @param  string $subStatus
     * @access public
     * @return void
     */
    public function activate($taskID, $subStatus = '')
    {
        $this->view->subStatus = $subStatus;

        parent::activate($taskID);
    }
}
