<?php
include '../../control.php';
class myProblem extends problem
{

    public function ajaxGetDealUser($type = null)
    {
        $users = $this->loadModel('user')->getPairs('noletter');
        $where = '';
        $name = 'dealUser';
        /*if($type == 'other'){
            $where = "multiple";
            $name = 'dealUser[]';
        }*/
        die(html::select( $name, $users, '', "class='form-control  chosen' " ));
    }
}
