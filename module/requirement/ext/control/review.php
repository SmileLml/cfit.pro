<?php
include '../../control.php';
class myRequirement  extends requirement
{
    /**
     * Review a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
     public function review($requirementID)
     {
         $requirement = $this->loadModel('requirement')->getByID($requirementID, 'latest');
         /* 当请求方式为post时，调用review方法处理需求条目评审逻辑，评审成功则记录操作动作和变动字段，返回成功信息。*/
         if($_POST)
         {
             $changes = $this->requirement->reviewfeedback($requirementID);
             if(dao::isError())
             {
                 $response['result']  = 'fail';
                 $response['message'] = dao::getError();
                 $this->send($response);
             }

             //$this->action->logHistory($actionID, $changes);

             $response['result']  = 'success';
             $response['message'] = $this->lang->submitSuccess;
             $response['locate']  = 'parent';
             $this->send($response);
         }
 
         /* 获取需求条目信息、产品线、产品、部门、项目计划、用户和应用系统信息。*/
         $this->view->title       = $this->lang->requirement->review;
         $this->view->requirement = $requirement;
         $this->view->lines       = array('' => '') + $this->loadModel('productline')->getPairsLineAndName();
         $this->view->products    = $this->loadModel('product')->getPairsNameLinkCode();
         $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
         $this->view->projects    = array(0 => '') + $this->loadModel('projectplan')->getPairs();
         $this->view->users       = $this->loadmodel('user')->getPairs('noclosed|noletter');
         $this->view->apps        = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
         $this->display('requirement', 'reviewfeedback');
     }

}