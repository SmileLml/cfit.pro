<?php
include '../../control.php';
class myBug extends bug
{
    /**
     * Activate a bug.
     *
     * @param  int    $bug
     * @param  string $subStatus
     * @access public
     * @return void
     */
    public function activate($bugID, $subStatus = '')
    {
        $this->view->subStatus = $subStatus;

        parent::activate($bugID);
    }
}
