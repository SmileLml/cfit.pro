<?php
class effortTask extends taskModel
{
    public function addTaskEstimate($data)
    {
        $this->app->loadLang('effort');
        $oldTask = $this->getById($data->task);

        $relation = $this->loadModel('action')->getRelatedFields('task', $data->task);

        $action = $data->left == 0 ? 'finished' : 'started';
        if(($oldTask->status != 'wait' and $oldTask->status != 'pause') and $action == 'started') $action = 'edited';
        if(($oldTask->status == 'done' or $oldTask->status == 'closed' or $oldTask->status == 'cancel') and $action == 'finished') $action = 'edited';

        $effort = new stdclass();
        $effort->objectType = 'task';
        $effort->objectID   = $data->task;
        $effort->execution  = $oldTask->execution;
        $effort->product    = $relation['product'];
        $effort->project    = (int)$relation['project'];
        $effort->account    = $data->account;
        $effort->deptID     = $data->deptID;
        $effort->date       = $data->date;
        $effort->realDate   = date('Y-m-d H:i:s');
        $effort->consumed   = $data->consumed;
        $effort->left       = $data->left;
        $effort->work       = $this->lang->action->label->$action . $this->lang->effort->objectTypeList['task'] . " : " . $oldTask->name;
        $this->dao->insert(TABLE_EFFORT)->data($effort)->autoCheck()->exec();

        $effortID = $this->dao->lastInsertID();
        $this->action->create('effort', $effortID, 'created', '', '', '', false);
    }
}
