<?php
include '../../control.php';
class myReview extends review
{
    /**
     *  获取所有部门负责人(多人)
     *
     */
    public function ajaxgetmanagers($deptId)
    {
        $users      = $this->loadModel('user')->getPairs('noclosed');
        $manager = $this->review->getAllManager($deptId);
        echo html::select('mailto[]', $users, $manager , 'class="form-control chosen" multiple');
    }


}