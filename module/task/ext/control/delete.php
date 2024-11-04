<?php
include '../../control.php';
class myTask extends task
{
    public function delete($executionID, $taskID, $confirm = 'no')
    {
        $task = $this->task->getById($taskID);
        if($task->parent < 0) return print(js::alert($this->lang->task->cannotDeleteParent));

        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->task->confirmDeleteTask, inlink('delete', "executionID=$executionID&taskID=$taskID&confirm=yes")));
        }
        else
        {
            $this->task->delete(TABLE_TASK, $taskID);
            if($task->parent > 0)
            {
                $this->loadModel('action')->create('task', $task->parent, 'deleteChildrenTask', '', $taskID);
            }
            if($task->fromBug != 0) $this->dao->update(TABLE_BUG)->set('toTask')->eq(0)->where('id')->eq($task->fromBug)->exec();
            if($task->story) $this->loadModel('story')->setStage($task->story);

            // 删除任务相应的工时消耗记录表。
            $this->loadModel('effort')->deleteRecord('task', $taskID);

            // 删除所有底层子任务和任务工时消耗表记录。
            $this->task->deleteChildTask($taskID);

            $this->executeHooks($taskID);

            $this->task->computeConsumed($taskID);

            $locateLink = $this->session->taskList ? $this->session->taskList : $this->createLink('execution', 'task', "executionID={$task->execution}");
            return print(js::locate($locateLink, 'parent'));
        }
    }
}
