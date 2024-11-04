<?php
include '../../control.php';
class myProgramPlan extends programplan
{
    /**
     * Ajax delete programplan.
     *
     * @param  int  $stageID
     * @access public
     * @return void
     */
    public function ajaxdelete($executionID)
    {
        $this->loadModel('execution');
        $stage = $this->execution->getByID($executionID);

        if(empty($stage)) $this->send(array('result' => 'success', 'message' => '删除成功'));

        if($stage->deleted == 1) $this->send(array('result' => 'fail', 'message' => '该阶段已经删除!'));

        if($stage->grade == 1)
        {
            /* Get the numbers of sub stage.  */
            $hasChild = $this->dao->select ('COUNT(*) as count')->from(TABLE_EXECUTION)
                ->where('parent')->eq($executionID)
                ->andWhere('type')->eq('stage')
                ->andWhere('deleted')->eq(0)
                ->fetch('count');
            if($hasChild > 0) $this->send(array('result' => 'fail', 'message' => $this->lang->programplan->hasSubStageCannotDelete));
        }

        // 查询子阶段。
        $stageList = $this->dao->select('id')->from(TABLE_EXECUTION)
            ->where('type')->eq('stage')
            ->andWhere('deleted')->eq(0)
            ->andWhere('parent')->eq($executionID)
            ->fetchPairs();
        $stageList[$executionID] = $executionID;

        /* Delete execution. */
        $this->dao->update(TABLE_EXECUTION)->set('deleted')->eq('1')->where('id')->in($stageList)->exec();

        $this->loadModel('action');
        $this->session->set('execution', '');
        foreach($stageList as $execution)
        {
            $this->action->create('execution', $execution, 'deleted', '', ACTIONMODEL::CAN_UNDELETED);
            $this->execution->updateUserView($execution);
        }

        $this->dao->delete()->from(TABLE_PROJECTPRODUCT)->where('project')->in($stageList)->exec();
        $this->dao->delete()->from(TABLE_EXECUTIONSPEC)->where('execution')->in($stageList)->exec();

        // 删除阶段及其子阶段下所有的任务。
        $taskList = $this->dao->select('id,execution')->from(TABLE_TASK)
            ->where('execution')->in($stageList)
            ->andWhere('deleted')->eq(0)
            ->fetchAll();
        $taskListID = array();
        foreach($taskList as $task)
        {
            $taskListID[] = $task->id;
            $this->execution->deleteTasks($task->execution, $task->id);
        }
        $this->dao->delete()->from(TABLE_TASKSPEC)->where('task')->in($taskListID)->exec();

        $this->send(array('result' => 'success', 'message' => '删除成功'));
    }
}
