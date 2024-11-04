<?php
class effortReport extends reportModel
{
    /**
     * Get user year efforts.
     *
     * @param  string $accounts
     * @param  int    $year
     * @access public
     * @return object
     */
    public function getUserYearEfforts($accounts, $year)
    {
        $effort    = parent::getUserYearEfforts($accounts, $year);
        $proEffort = $this->dao->select('count(*) as count, sum(consumed) as consumed')->from(TABLE_EFFORT)
            ->where('deleted')->eq(0)
            ->andWhere('LEFT(date, 4)')->eq($year)
            ->beginIF($accounts)->andWhere('account')->in($accounts)->fi()
            ->fetch();

        $effort->count    += $proEffort->count;
        $effort->consumed += round($proEffort->consumed, 2);
        return $effort;
    }

    /**
     * Get effort for month
     *
     * @param  string $account
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getEffort4Month($account, $year)
    {
        $months  = parent::getEffort4Month($account, $year);
        $efforts = $this->dao->select('*')->from(TABLE_EFFORT)
            ->where('account')->eq($account)
            ->andWhere('deleted')->eq(0)
            ->andWhere('LEFT(date, 4)')->eq($year)
            ->orderBy('date')
            ->fetchAll();

        foreach($efforts as $effort)
        {
            $month = (int)substr($effort->date, 5, 2) - 1;
            $months[$month] += round($effort->consumed, 2);
        }

        return $months;
    }
}
