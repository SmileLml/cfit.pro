<?php
include '../../control.php';
class myReview extends review
{
    /**
     *  根据状态 设置 专员
     * @param $type
     * @param $bearDept
     * @param $deptId
     * @param $selectUser
     */
    public function ajaxGetReviewer($type, $bearDept = 0, $deptId = 0, $selectUser = '')
    {
        global $app;
        $users      = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = '';
        if($selectUser){
            $reviewer = $selectUser;
        }else{
            if(!$deptId){
                $deptId = $app->user->dept;
            }
            if(in_array($type, $this->lang->review->customReviewUserTypeList)) { //评审类型是自定义评审专家和专员类型（manage、pro、pmo）
                $isShanghaiDept = $this->loadModel('dept')->getIsShanghaiDept($bearDept);
                if($isShanghaiDept){ //是否是上海分公司
                    $typeReviewer = $type.'Reviewer';
                    $reviewer = $this->lang->review->shanghaiReviewerList[$typeReviewer];
                }else {
                    $list = substr( implode(',',$this->lang->review->reviewerList),1);
                    if(strpos($list,',') !== false){
                        $reviewer = substr($list,0,strpos($list,','));
                    }else{
                        $reviewer = $list;
                    }
                }
            }elseif ($type == 'dept') { //部门评审
                $rev = $this->loadModel('dept')->getByID($deptId);
                $reviewer = $rev->reviewer ? $rev->reviewer: '';
            }else{ //其他类型
                $reviewer = $this->lang->review->otherreviewer;
            }
        }
        echo html::select('reviewer', $users, $reviewer , 'class="form-control chosen" ');
    }


}