<?php
include '../../control.php';
class myReview extends review
{
    /**
     *  获取所有部门负责人
     * @param $type
     *
     */
    public function ajaxGetExpert($type)
    {
        //$users      = $this->loadModel('user')->getPairs('noclosed');
        $users    = array('' => '') + $this->loadModel('user')->getUsersNameByType('inside');
        $manager = "";
        if($type == 'manage'){
            $manager = $this->review->getAllManager1();
        }

        echo html::select('expert[]', $users, $manager , 'class="form-control chosen" multiple');
    }

}