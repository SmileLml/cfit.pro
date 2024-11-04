<?php
include '../../control.php';
class myProjectRelease extends projectrelease
{

    public function ajaxGetProductVersion($productID = 0, $orderBy = 'id_desc')
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);

        $planName = 'productVersion';
        $plans    = array('' => '','1'=>'无') + $plans;
        echo html::select($planName, $plans, '', "class='form-control'");
    }

}
