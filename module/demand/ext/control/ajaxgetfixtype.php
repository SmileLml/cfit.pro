<?php
include '../../control.php';
class myDemand extends demand
{

    /**
     * 获取实现方式
     */
   public function ajaxGetFixType()
   {
           $type = $this->lang->demand->fixTypeList;
           echo html::select('fixType', $type, '', "class='form-control chosen fixTypeClass' onchange='selectfix(this.id, this.value)' ");
   }
}
