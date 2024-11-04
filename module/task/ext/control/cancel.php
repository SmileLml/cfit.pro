<?php
include '../../control.php';
class myTask extends task
{
    /**
     * Cancel a task.
     *
     * @param  int    $taskID
     * @param  string $subStatus
     * @access public
     * @return void
     */
    public function cancel($taskID, $subStatus = '')
    {
        $this->view->subStatus = $subStatus;

        parent::cancel($taskID);
    }
}
