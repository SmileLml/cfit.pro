<?php
include '../../control.php';
class myApimeasure extends apimeasure
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function syncDpmpSystemInfo()
    {

        // token以及参数校验
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::FAIL_CODE);
        }

        $updateTime = $_POST['updateTime'];

        $createBeginDay  = $_POST['createBeginDay'];
        $createEndDay = $_POST['createEndDay'];
        $dpmpSystemIds = $_POST['dpmpSystemIds'];
        $res = $this->dao->select('id dpmpSystemId,name,code abbr,isPayment type,team constructUnit,fromUnit demandUnit,`attribute` `attribute`,belongDeptIds dpmpDeptId,`desc` `describe`,systemManager manager,
                                    updateTime')
            ->from(TABLE_APPLICATION)->alias('app')
            ->where('deleted')->eq('0')
            ->beginIF($createBeginDay)->andWhere('createdDate')->ge($createBeginDay)->fi()
            ->beginIF($createEndDay)->andWhere('createdDate')->le($createEndDay)->fi()
            ->beginIF($dpmpSystemIds)->andWhere('id')->in($dpmpSystemIds)->fi()
            ->beginIF($updateTime)->andWhere("case when app.updateTime is not null then app.updateTime >= '".$updateTime."' else app.createdDate >= '".$updateTime."' end")->fi()
            ->fetchAll();
        $user = $this->dao->select('account,mobile')->from(TABLE_USER)->where('deleted')->eq('0')->fetchPairs('account','mobile');
        foreach ($res as $key=>$re) {
            $res[$key]->managerTel = zmget($user,$re->manager,'');
            $res[$key]->constructUnit =  $re->constructUnit != 'null' ?  $re->constructUnit : '';
            $res[$key]->sysId = intval($re->dpmpSystemId);
            $res[$key]->sysName = $re->name;
            $res[$key]->sysCode = $re->abbr;
        }
        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res,0,200);
    }

    /**
     * 校验
     * @return void
     */
    private function checkInput(){
        $errMsg = [];
        $keyList = array('createBeginDay','createEndDay','dpmpSystemIds', 'updateTime');
        $param = array_keys($_POST);

        foreach ($keyList as $item) {
            if(!in_array($item,$param)){
                $errMsg[] = "缺少『{$item}』参数";
            }
        }

        foreach ($param as $par) {
            if(!in_array($par,$keyList)){
                $errMsg[] = $par."不是协议字段";
            }
        }

        if(isset($_POST['createBeginDay']) && $_POST['createBeginDay'] && isset($_POST['createEndDay']) && $_POST['createEndDay']){

            if( strtotime($_POST['createEndDay']) < strtotime($_POST['createBeginDay'])){
                $errMsg[] = '新建结束时间不能早于新建开始时间!';
            }
        }

        return $errMsg;
    }

}
