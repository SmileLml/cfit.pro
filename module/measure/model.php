<?php
/**
 * The model file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     kanban
 * @version     $Id: model.php 5118 2021-10-22 10:18:41Z $
 * @link        https://www.zentao.net
 */
?>
<?php
class measureModel extends model{
    public function getEffortBySpace($spaceID, $begin = 0, $end = 0, $account = [], $particDepts = [],$order = "project_desc")
    {
        /* 根据项目和时间范围分组查询用户工作量。 */
        $workloadPairs = $this->dao->select('account,deptID, cast(sum(consumed) as decimal(11,2)) as workload,project')->from(TABLE_EFFORT)
            ->where('objectType')->eq('kanban')
            ->beginIF($spaceID)->andWhere('project')->in($spaceID)
            ->beginIF($account)->andWhere('account')->in($account)->fi()
            ->beginIF($particDepts)->andWhere('deptID')->in($particDepts)->fi()
            ->beginIF($begin)->andWhere('`date`')->ge($begin)->fi()
            ->beginIF($end)->andWhere('`date`')->le($end)->fi()
            ->andWhere('deleted')->eq('0')
            ->groupBy('project,account, deptID')
            ->orderBy($order)
            ->fetchAll();
        return $workloadPairs;
    }
    public function getMembersBySpace($spaceID)
    {
        $users = $this->dao->select('t2.id, t2.account, t2.realname, t2.dept')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
            ->where('t1.root')->in($spaceID)
            ->andWhere('t1.type')->eq('kanban')
            ->andWhere('t2.deleted')->eq(0)
            ->orderBy('t2.dept_asc')
            ->fetchAll();
        return $users;
    }
    //空间-看板
    public function getEffortBySpaceKanban($spaceID = [],$kanban = [], $begin = 0, $end = 0, $account = '', $particDepts = [])
    {
        /* 根据项目和时间范围分组查询用户工作量。 */
        $workloadPairs = $this->dao->select('account,deptID, cast(sum(consumed) as decimal(11,2)) as workload,project,execution')->from(TABLE_EFFORT)
            ->where('objectType')->eq('kanban')
            ->beginIF($spaceID)->andWhere('project')->in($spaceID)
            ->beginIF($kanban)->andWhere('execution')->in($kanban)->fi()
            ->beginIF($account)->andWhere('account')->in($account)->fi()
            ->beginIF($particDepts)->andWhere('deptID')->in($particDepts)->fi()
            ->beginIF($begin)->andWhere('`date`')->ge($begin)->fi()
            ->beginIF($end)->andWhere('`date`')->le($end)->fi()
            ->andWhere('deleted')->eq('0')
            ->groupBy('project,Execution,account, deptID')
            ->fetchAll();
        return $workloadPairs;
    }
    //人员工作量明细
    public function getEffortDetail($spaceID = [],$kanban = [], $begin = 0, $end = 0, $account = '',$particDepts = [])
    {
        /* 根据项目和时间范围分组查询用户工作量。 */
        $workloadPairs = $this->dao->select('account,deptID, cast(consumed as decimal(11,2)) as workload,project,execution,objectID,date,realDate,work')->from(TABLE_EFFORT)
            ->where('objectType')->eq('kanban')
            ->beginIF($spaceID)->andWhere('project')->in($spaceID)
            ->beginIF($account)->andWhere('account')->in($account)->fi()
            ->beginIF($kanban)->andWhere('execution')->in($kanban)->fi()
            ->beginIF($particDepts)->andWhere('deptID')->in($particDepts)->fi()
            ->beginIF($begin)->andWhere('`date`')->ge($begin)->fi()
            ->beginIF($end)->andWhere('`date`')->le($end)->fi()
            ->andWhere('deleted')->eq('0')
//            ->groupBy('project,Execution,account, deptID')
            ->orderBy('date_desc')
            ->fetchAll();
        return $workloadPairs;
    }
}
