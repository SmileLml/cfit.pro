<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function putbaseapplicationsysnc()
    {
        $postData = fixer::input('post')->get();
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('cmdbsync' , 'putbaseapplicationsysnc');
        $this->checkApiToken();
        $this->dao->update(TABLE_BASEAPPLICATION)
            ->set('deleted')->eq('1')->exec();
        foreach ($postData as $data){
            $this->dao->insert(TABLE_BASEAPPLICATION)
                ->data($data)
                ->exec();
        }
        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->requestlog->response('success', $this->lang->api->successful, '', $logID);
    }
}
