<?php

include '../../control.php';
class myReview extends review
{
    /**
     *  根据状态 设置 主席
     * @param $type
     * @param $bearDept 项目承担部门
     * @param $deptId
     * @param $selectUser
     */
    public function ajaxGetOwner($type, $bearDept = 0, $deptId = 0, $selectUser = ''){
        global $app;
        $users  = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = '';
        if($selectUser){
            $reviewer = $selectUser;
        }else{
            if(!$deptId){
                $deptId =  $app->user->dept;
            }
            if(in_array($type, $this->lang->review->customReviewUserTypeList)){ //评审类型是自定义评审专家和专员类型（manage、pro、pmo）
                $isShanghaiDept = $this->loadModel('dept')->getIsShanghaiDept($bearDept);
                if($isShanghaiDept){ //是否是上海分公司
                    $typeOwner = $type.'Owner';
                    $reviewer = $this->lang->review->shanghaiReviewOwnerList[$typeOwner];
                }else {
                    $typeOwner = $type.'reviewer';
                    $reviewer = $this->lang->review->$typeOwner;
                }
            }else{
                $rev = $this->loadModel('dept')->getByID($deptId);
                $reviewer = $rev->manager1 ? $rev->manager1 : '';
            }
        }
        echo html::select('owner', $users, $reviewer, " class='form-control chosen' onchange='ajaxgetmailto(this.value)' required");
    }


}