<?php
class oaAttend extends attendModel
{
    /**
     * Compute attend's status.
     *
     * @param  object $attend
     * @access public
     * @return string
     */
    public function computeStatus($attend)
    {
        $status = parent::computeStatus($attend);
        if($status != 'lieu' and $this->loadModel('lieu')->isLieu($attend->date, $attend->account)) $status = 'lieu';
        if($status != 'leave' and $this->loadModel('leave')->isLeave($attend->date, $attend->account)) $status = 'leave';
        return $status;
    }

    /**
     * Save stat.
     *
     * @param  string    $date
     * @access public
     * @return bool
     */
    public function saveStat($date)
    {
        foreach($this->post->normal as $account => $normal)
        {
            $data = new stdclass();
            $data->account         = $account;
            $data->normal          = $normal;
            $data->late            = $this->post->late[$account];
            $data->early           = $this->post->early[$account];
            $data->absent          = $this->post->absent[$account];
            $data->trip            = $this->post->trip[$account];
            $data->egress          = $this->post->egress[$account];
            $data->paidLeave       = $this->post->paidLeave[$account];
            $data->unpaidLeave     = $this->post->unpaidLeave[$account];
            $data->timeOvertime    = $this->post->timeOvertime[$account];
            $data->restOvertime    = $this->post->restOvertime[$account];
            $data->holidayOvertime = $this->post->holidayOvertime[$account];
            $data->lieu            = $this->post->lieu[$account];
            $data->deserve         = $this->post->deserve[$account];
            $data->actual          = $this->post->actual[$account];
            $data->month           = $date;
            $data->status          = 'wait';

            $this->dao->replace(TABLE_ATTENDSTAT)->data($data)->autoCheck()->exec();
        }

        return !dao::isError();
    }

    /**
     * Set reviewer for attend.
     *
     * @access public
     * @return bool
     */
    public function setManager()
    {
        $deptList = $this->post->dept;
        foreach($deptList as $id => $dept)
        {
            if(!empty($dept)) $dept = ",{$dept}," ;
            $this->dao->update(TABLE_DEPT)->set('manager')->eq($dept)->where('id')->eq($id)->exec();
        }

        return !dao::isError();
    }
}
