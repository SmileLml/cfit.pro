<?php
include '../../control.php';
class myProblem extends problem
{

    /**
     * 获取产品
     */
    public function ajaxGetProduct($app,$data_id = 1)
    {
        $products = $app ? array('0' => '','99999'=>'无') + $this->loadModel('product')->getCodeNamePairsByApp($app) :array('0' => '','99999'=>'无');
        $productName = 'product[]';
        $id = empty($data_id) ? '1'  : $data_id;
        echo html::select($productName, $products, '', "class='form-control chosen' data_id='$id'  id='product$data_id' onchange='productChange(this)'");
    }
}
