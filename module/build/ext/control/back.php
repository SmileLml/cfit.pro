<?php

include '../../control.php';
class myBuild extends build
{
    /**
     *
     * @param $buildID
     */
    public function back($buildID)
    {
        if(!empty($_POST))
        {
            $changes = $this->build->back($buildID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('build', $buildID, 'back');
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }

            if(isonlybody()) die(js::closeModal('parent.parent', 'this'));
            die(js::reload('parent'));
        }

        $build = $this->build->getById((int)$buildID);
        $this->view->actions = $this->loadModel('action')->getList('build', $buildID);
        $this->view->build = $build;
        $this->view->users   = $this->loadModel('user')->getPairs();
        $this->display();
    }
}