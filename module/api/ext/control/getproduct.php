<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function getproduct()
    {
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('product' , 'getproduct');
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->requestlog->response('fail', implode(',',$errMsg), [], $logID, self::FAIL_CODE);
        }

        $products = $this->dao->select('id,name,app as appId,code')->from(TABLE_PRODUCT)
            ->where('app')->in($_POST['appIdList'])
            ->beginIF($_POST['deleted'] == '0')->andWhere('deleted')->eq('0')->fi()
            ->fetchAll();


        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->requestlog->response('success', $this->lang->api->successful, $products, $logID);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        //校验是否存在异常字段
        foreach ($_POST as $key => $v)
        {
            if(!isset($this->config->api->getproductFields[$key])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->config->api->getproductFields as $k => $v)
        {
            if($this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k.'不可以为空';
            }
        }
        return $errMsg;
    }
}
