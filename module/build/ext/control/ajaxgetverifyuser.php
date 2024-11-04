<?php
include '../../control.php';
class myBuild extends build
{

    public function ajaxGetVerifyUser($type)
    {
        $users      = $this->loadModel('user')->getPairs('noclosed');
        if($type){
            echo html::select('verifyUser', $users, '', "class='form-control' ");
        }else{
            echo html::input('verifyUser', '', "class='form-control' readonly = 'readonly'" );
        }
    }

}
