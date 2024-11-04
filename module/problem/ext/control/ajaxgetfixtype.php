<?php
include '../../control.php';
class myProblem extends problem
{

    /**
     * 获取实现方式
     */
   public function ajaxGetFixType()
   {
           $type = $this->lang->problem->fixTypeList;
           echo html::select('fixType', $type, '', "class='form-control chosen' onchange='selectfix()' ");
   }
}
