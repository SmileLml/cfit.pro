<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mymy extends my
{
    public function savepublish(){
        $userid=$this->app->user->id;
        $result=$this->my->queryPublish($userid);
        foreach ($result as $value) {
            $this->dao->insert(TABLE_PUBLISHRECORD)
                ->set('publishId')->eq($value->id)
                ->set('userId')->eq($userid)
                ->set('createTime')->eq(date('Y-m-d H:i:s'))
                ->exec();
        }
        die();
    }
}
