<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");

class mytesttask extends testtask
{
    /**
     * Confirm case changed.
     *
     * @param  int    $caseID
     * @param  int    $taskID
     * @param  string $from
     * @access public
     * @return void
     */
    public function confirmChange($caseID, $taskID = 0)
    {
        $case = $this->loadModel('testcase')->getById($caseID);

        $this->dao->update(TABLE_TESTRUN)
            ->set('version')->eq($case->version)
            ->set('precondition = null')
            ->where('`case`')->eq($caseID)
            ->exec();

        $this->loadModel('action')->create('case', $caseID, 'confirmChange');
        die(js::reload('parent'));
    }
}