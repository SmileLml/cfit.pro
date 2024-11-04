<?php
include '../../control.php';
class myProject extends project
{
    /**
     * 维护人员
     * @param $user
     */
   public function ajaxGetMaintenance($user,$bearDept){
       $deptObj = $this->loadModel('dept')->getByID($bearDept);
       $qa = $deptObj->qa;
       $users   = $this->loadModel('user')->getPairs('noclosed|nodeleted');
       $user = array_filter(explode(',',$user));
       $user =  $user ? in_array('admin',$user) ? $user : array_push($user,'admin') : 'admin'.','.$qa;
       echo html::select('maintenanceStaff[]', $users, $user, "class='form-control chosen w-200px' multiple ");
   }
}
