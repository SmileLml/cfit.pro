<?php
include '../../control.php';
class myBuild extends build
{
    /**
     * 根据项目获取应用系统下的产品
     */
    public function ajaxGetAppProduct($productID = 0, $application)
    {
        $products = $this->loadModel('application')->getAppProducts($productID,$application);
        $products    = array('' => '','99999'=>'无') + $products;
        echo html::select('product', $products, '', "class='form-control chosen' onchange='getproductversion()'");
    }
}