<?php
include '../../control.php';
include '../../../../vendor/autoload.php';
use Firebase\JWT\JWT;
class myMobileApi extends mobileapi
{

    /**
     * 登录接口
     */
    public function loginApi()
    {
        //$logID = $this->loadModel('requestlog')->insideSaveRequestLog('problem' , '问题同步'); //todo 测试不加日志
        $this->app->loadConfig('cas');
        //$gotoUrl = $this->config->cas->loginUrl . "?service=" . urlencode($this->config->cas->serviceUrl);
        $gotoUrl = $this->config->cas->loginUrl . "?service=" .$this->config->cas->mobileServiceUrl."?module=workreport";
       // $gotoUrl = $this->config->cas->loginUrl . "?service=" ."http://".$_SERVER['HTTP_HOST'].$_SERVER['CONTEXT_PREFIX']."/mobileapi-tokenloginapi.html";
       // $gotoUrl = $this->config->cas->loginUrl . "?service=" .$this->config->cas->mobileServiceUrl."?redirecturl=".urlencode($this->lang->api->h5Url);

        //$this->locate($gotoUrl);
        $this->loadModel('mobileapi')->response('success', $this->lang->api->successful, array('url' => $gotoUrl) ,  0, 200,'loginApi');
    }
}
