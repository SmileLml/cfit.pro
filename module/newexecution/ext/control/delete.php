<?php
include '../../control.php';
class myNewExecution extends newexecution
{
    /**
     * Delete a execution.
     *
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function delete($executionID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->execution->confirmDeleteStage, $this->createLink('execution', 'delete', "executionID=$executionID&confirm=yes"));
            exit;
        }
        else
        {
            $stage = $this->execution->getByID($executionID);
            if($stage->deleted == 1) $this->send(array('result' => 'fail', 'message' => array('error' => array('已经删除'))));

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

            die(js::locate($this->createLink('project', 'execution', "browseType=all&projectID=$stage->project"), 'parent'));
        }
    }
}
