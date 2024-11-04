<?php
public function getDeployResults($deployID, $caseID)
{
    return $this->loadExtension('ops')->getDeployResults($deployID, $caseID);
}

/**
 * Confirm case change.
 *
 * @param  int    $taskID
 * @param  int    $caseID
 * @access public
 * @return void
 */
public function confirmCaseChange($taskID = 0, $caseID = 0)
{
    $case = $this->loadModel('testcase')->getById($caseID);
    $this->dao->update(TABLE_TESTRUN)
        ->set('version')->eq($case->version)
        ->set('precondition = null')
        ->where('`case`')->eq($caseID)
        ->exec();
}
