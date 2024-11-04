<?php
include '../../control.php';
class myBuild extends build
{

    public function ajaxGetProductVersion($productID = 0, $orderBy = 'id_desc')
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);

        $planName = 'version';
        // $plans    = empty($plans) ? array('' => '') : $plans;
        $plans    = array('' => '','1'=>'无') + $plans;
        echo html::select($planName, $plans, '', "class='form-control'  onchange='getversion()'");
    }

}
