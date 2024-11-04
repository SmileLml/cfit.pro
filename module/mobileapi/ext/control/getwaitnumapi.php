<?php
include '../../control.php';
include '../../../../vendor/autoload.php';
use Firebase\JWT\JWT;
class myMobileApi extends mobileapi
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    /**
     * 获取用户信息
     */
    public function getWaitNumApi()
    {
        $this->checkApiToken();
        $param = isset($_POST['user']) ? $_POST['user'] : '';
        if ($param == ''){
            $this->loadModel('mobileapi')->response('fail', '用户名不能为空', [] ,  0, self::FAIL_CODE,'getWaitNumApi');
        }
        $userArr = explode(',',$param);
        $user = $this->dao->select('*')->from(TABLE_USER)->where('account')->in($userArr)->fetchAll();
        if (empty($user) || count($userArr) != count($user)){
            $this->loadModel('mobileapi')->response('fail', '用户不存在', [] ,  0, self::FAIL_CODE,'getWaitNumApi');
        }
        if (count($user) > 5){
            $this->loadModel('mobileapi')->response('fail', '一次最多只能同时查询5位用户待办数量', [] ,  0, self::FAIL_CODE,'getWaitNumApi');
        }
        $data = [];
        foreach ($userArr as $item) {
            $user = $this->dao->select('*')->from(TABLE_USER)->where('ldap')->eq($item)->fetch();
            if ($user){
                $this->app->user = $user;
                $modifys              = $this->loadModel('modify')->getModifyWaitListApi();
                $outwarddeliverys     = $this->loadModel('outwarddelivery')->getWaitListApi();
                $sectransfers         = $this->loadModel('sectransfer')->getWaitListApi();
                $data[$item]['modifys']          = count($modifys);
                $data[$item]['outwarddelivery']  = count($outwarddeliverys);
                $data[$item]['sectransfer']      = count($sectransfers);
            }
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('mobileapi')->response('success', '', $data ,  0, 200,'getUserApi');
    }
}
