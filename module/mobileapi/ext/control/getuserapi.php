<?php
include '../../control.php';
include '../../../../vendor/autoload.php';
use Firebase\JWT\JWT;
class myMobileApi extends mobileapi
{

    /**
     * 获取用户信息
     */
    public function getUserApi()
    {
        $user = $this->loadModel('mobileapi')->getUser();
        $dept = $this->loadModel('dept')->getByID($user->dept);
        $user->cm = 0;//cm
        if (in_array($user->account,explode(',',$dept->cm))){
            $user->cm = 1;
        }
        $user->groupleader = 0;//组长
        if (in_array($user->account,explode(',',$dept->groupleader))){
            $user->groupleader = 1;
        }
        $user->manager = 0;//部门负责人
        if (in_array($user->account,explode(',',$dept->manager)) || in_array($user->account,explode(',',$dept->manager1))){
            $user->manager = 1;
        }
        $user->leader = 0;//分管领导
        if (in_array($user->account,explode(',',$dept->leader)) || in_array($user->account,explode(',',$dept->leader1))){
            $user->leader = 1;
        }
        $user->executive = 0;//二线专员
        if (in_array($user->account,explode(',',$dept->executive))){
            $user->executive = 1;
        }
        $reviewer = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
        $user->ceo = 0;
        if ($reviewer == $user->account){
            $user->ceo = 1;
        }
        //系统部
        $sysDept = $this->dao->select('id,manager')->from(TABLE_DEPT)->where('name')->eq('系统部')->fetch();
        $cms = explode(',', trim($sysDept->manager, ','));
        $user->sys = 0;
        if (in_array($user->account,$cms)){
            $user->sys = 1;
        }
        $this->loadModel('mobileapi')->response('success', '', array('user' => $user) ,  0, 200,'getUserApi');
    }
}
