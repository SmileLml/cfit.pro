<?php
include '../../control.php';
include '../../../../vendor/autoload.php';
use Firebase\JWT\JWT;
class myMobileApi extends mobileapi
{

    /**
     * 刷新token接口
     */
    public function refreshTokenApi()
    {
        $refreshToken =  $this->loadModel('mobileapi')->decodeRefreshToken();
        $this->loadModel('mobileapi')->response('success', $this->lang->api->successful, $data = array('token' => $refreshToken),  0, 200,'refreshTokenApi');
    }
}
