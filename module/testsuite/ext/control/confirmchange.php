<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");

class mytestsuite extends testsuite
{
    /**
     * Confirm case changed.
     *
     * @param  int    $suiteID
     * @param  int    $caseID
     * @param  string $from
     * @access public
     * @return void
     */
    public function confirmChange($suiteID = 0, $caseID = 0)
    {
        $case = $this->loadModel('testcase')->getById($caseID);

        $this->dao->update(TABLE_SUITECASE)
            ->set('version')->eq($case->version)
            ->where('`suite`')->eq($suiteID)
            ->andWhere('`case`')->eq($caseID)
            ->exec();

        $this->loadModel('action')->create('case', $caseID, 'confirmChange');
        die(js::reload('parent'));
    }
}