<?php
include '../../control.php';
class myExecution extends execution
{
    /**
     * Delete All execution.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function deleteAll($projectID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->execution->confirmDeleteAll, $this->createLink('execution', 'deleteAll', "projectID=$projectID&confirm=yes"));
            exit;
        }
        else
        {
            // 查询全部阶段
            $stageList = $this->dao->select('id')->from(TABLE_EXECUTION)
                ->where('type')->eq('stage')
                ->andWhere('deleted')->eq(0)
                ->andWhere('project')->eq($projectID)
                ->fetchPairs();

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

            die(js::locate($this->createLink('project', 'execution', "browseType=all&projectID=$projectID"), 'parent'));
        }
    }
}
