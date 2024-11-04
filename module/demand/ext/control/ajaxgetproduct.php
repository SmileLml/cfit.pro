<?php
include '../../control.php';
class myDemand extends demand
{
    /**
     * 获取产品
     */
    public function ajaxGetProduct($app)
    {
        $products = $app ? array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($app) :array('0' => '','99999'=>'无');
        $productName = 'product';
        echo html::select($productName, $products, '', "class='form-control chosen' onchange='selectproduct(this.value)'");
    }
}
