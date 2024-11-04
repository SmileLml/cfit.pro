<?php
include '../../control.php';
class myOutwarddelivery extends outwarddelivery
{
    public function ajaxDemandSelect($isNewModifycncc, $outwarddeliveryId = 0,$source='')
    {
        if($outwarddeliveryId > 0){
            //获取对外交付信息
            $outwarddelivery = $this->loadModel('outwarddelivery')->getByID($outwarddeliveryId);
            $list = array('' => '') + $this->loadModel('demand')->modifySelectByEdit($outwarddelivery->demandId,'outwarddelivery', $outwarddeliveryId, $isNewModifycncc,$source);

            echo html::select('demandId[]', $list, $outwarddelivery->demandId, "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple");
        }else{
            $list = array('' => '') + $this->loadModel('demand')->modifySelect('outwarddelivery', $outwarddeliveryId, $isNewModifycncc);

            echo html::select('demandId[]', $list, '', "class='form-control chosen demandIdClass' onchange='selectDemand()' multiple");
        }
    }
}