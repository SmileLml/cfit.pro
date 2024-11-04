<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const FAIL_CODE   = 999;    //请求失败
    const SUCCESS     = 200;    //成功

    // 接收分区
    public function partition(){
        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('system' , 'getPartition');
        $data = $_POST;
        if (empty($data) || empty($data[0])){
            $this->requestlog->response('fail', '数据不能为空', [], $logID, self::FAIL_CODE);
        }
        //同步系统
        if (isset($data[0]['cname']) && isset($data[0]['ename'])){
            $systems = $this->dao->select("*")->from(TABLE_SYSTEM)->where('deleted')->eq('0')->fetchall('ciKey');
            foreach ($data as $v) {
                $obj = new stdClass();
                $obj->ciKey    = $v['ciKey'];
                $obj->cName    = $v['cname'];
                $obj->eName    = $v['ename'];
                $obj->deleted = $v['status'] == 1 ? 0 : 1;
                if (!isset($systems[$v['ciKey']])){
                    $this->dao->insert(TABLE_SYSTEM)->data($obj)->exec();
                }else{
                    $this->dao->update(TABLE_SYSTEM)->data($obj)->where('ciKey')->eq($v['ciKey'])->andwhere('deleted')->eq('0')->exec();
                    if ($v['cname'] != $systems[$v['ciKey']]->cName){
                        $app = new stdClass();
                        $app->applicationCnName = $v['cname'];
                        //修改系统名称同步更新分区表的系统名称
                        $this->dao->update(TABLE_SYSTEM_PARTITION)->data($app)->where('application')->eq($v['ename'])->andwhere('deleted')->eq('0')->exec();
                    }
                }
            }
        }else{
            $partitions = $this->dao->select('ciKey,name,application,applicationName,applicationCnName,ip,dataOrigin,location,deleted')->from(TABLE_SYSTEM_PARTITION)->where('deleted')->eq('0')->fetchall('ciKey');

            foreach ($data as $v) {
                $obj = new stdClass();
                $obj->name                  = $v['name'];
                $obj->ciKey                 = $v['ciKey'];
                $obj->application           = $v['application'];
                $obj->applicationName       = $v['application'];
                $obj->applicationCnName     = $v['applicationCnName'];
                $obj->deleted               = $v['deleted'];
                $obj->location              = $v['location'];
                $obj->ip                    = $v['ip'];
                $obj->dataOrigin            = 0;
                if(strpos($obj->location, 'NPC') !== false){
                    $obj->dataOrigin        = 1;
                }
                if(strpos($obj->location, 'CCPC') !== false){
                    $obj->dataOrigin        = 2;
                }
                if (!isset($partitions[$v['ciKey']])){
                    $this->dao->insert(TABLE_SYSTEM_PARTITION)->data($obj)->exec();
                }else{
                    $this->dao->update(TABLE_SYSTEM_PARTITION)->data($obj)->where('ciKey')->eq($v['ciKey'])->andwhere('deleted')->eq('0')->exec();
                }
            }
        }
        if(dao::isError()) {
            $this->requestlog->response('fail', dao::getError(), [], $logID, self::FAIL_CODE);
        }
        $this->requestlog->response('success', $this->lang->api->successful,[] , $logID,self::SUCCESS);
    }
}