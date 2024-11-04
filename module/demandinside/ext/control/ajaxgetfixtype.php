<?php
include '../../control.php';
class myDemandinside extends demandinside
{

    /**
     * 获取实现方式
     */
   public function ajaxGetFixType()
   {
           $type = $this->lang->demandinside->fixTypeList;
           echo html::select('fixType', $type, '', "class='form-control chosen fixTypeClass' onchange='selectfix(this.id, this.value)' ");
   }
}
