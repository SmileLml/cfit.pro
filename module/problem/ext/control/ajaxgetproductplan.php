<?php
include '../../control.php';
class myProblem extends problem
{

    /**
     * 获取产品版本
     */
    public function ajaxGetProductPlan($productID = 0, $data_id = 1)
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, 0);
        $plans = empty(array_filter($plans)) ? array('0' => '','1' => '无') : array('0' => '','1' => '无') + $plans;
        echo html::select('productPlan[]', $plans, '', "class='form-control chosen productPlanSelect w-100px' id='p-{$data_id}'");
    }
}
