<?php
include '../../control.php';
class myProduct extends product
{
    /**
     * Project: chengfangjinke
     * Method: ajaxGetProductCode
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:55
     * Desc: This is the code comment. This method is called ajaxGetProductCode.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $productID
     */
    public function ajaxGetProductCode($productID)
    {
        $product = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($productID)->fetch();
        #$line    = $this->dao->select('id,code')->from(TABLE_PRODUCTLINE)->where('id')->eq($product->line)->fetch();
        #echo $line->code . '-' . $product->code;
        echo $product->code;
    }
}
