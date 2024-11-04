<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mymy extends my
{
    public function publish(){
       
        $result=$this->my->queryPublish($this->app->user->id);
        $typeValue= $this->dao->select('field,options')->from(TABLE_WORKFLOWFIELD)->where('module')->eq("publish")->andWhere('field')->eq('type')->fetchPairs();

        $this->view->typeValue=$typeValue;
        $this->view->result=$result;
        $this->display();
    }
}
