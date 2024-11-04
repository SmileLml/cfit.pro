<?php
include '../../control.php';
class myApimeasure extends apimeasure
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function syncDpmpReleaseInfo()
    {

        // token以及参数校验
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::FAIL_CODE);
        }

        $releaseBeginDay  = $_POST['releaseBeginDay'];
        $releaseEndDay = $_POST['releaseEndDay'];
        $dpmpReleaseIds = $_POST['dpmpReleaseIds'];
        $dpmpProductIds = $_POST['dpmpProductIds'];

        $where = empty($dpmpReleaseIds) ? "where 1 = 1 and deleted = '0'" : "where 1 = 1";
        $res = $this->dao->select('if(relea.app !="0", relea.app,build.app) dpmpSystemId,app.name name,app.code abbr,app.isPayment type,app.team constructUnit,app.fromUnit demandUnit,app.`attribute` `attribute`,app.belongDeptIds dpmpDeptId,app.systemManager manager,app.`desc` `describe`,if(relea.product !="99999", relea.product,"") dpmpProductId,if(relea.productVersion !="1" && relea.productVersion != "0",relea.productVersion,"")dpmpVersionId,relea.id dpmpReleaseId ,if(relea.product !="99999" ,product.name,"无") productName,relea.productCodeInfo productCode,if(relea.productVersion !="1",plan.title,"无") productVersion,(case when relea.status ="normal" then "waitOnline" else "online" end) status,relea.date releaseDate ,relea.path releaseAddress,relea.deleted isDeleted,build.scmPath gitPaths')
            ->from(TABLE_RELEASE)->alias('relea')
            ->leftJoin(TABLE_APPLICATION)->alias('app')->on('relea.app = app.id')
            ->leftJoin(TABLE_PRODUCT)->alias('product')->on('relea.product = product.id')
            ->leftJoin(TABLE_PRODUCTPLAN)->alias('plan')->on('relea.productVersion = plan.id')
            ->leftjoin(TABLE_BUILD)->alias('build')->on('relea.build = build.id')
            ->where('1 = 1')
            ->beginIF(empty($dpmpReleaseIds))->andWhere('build.deleted')->eq('0')->fi()
            ->beginIF(empty($dpmpReleaseIds))->andWhere('relea.deleted')->eq('0')->fi()
            ->beginIF($releaseBeginDay)->andWhere('relea.date')->ge($releaseBeginDay)->fi()
            ->beginIF($releaseEndDay)->andWhere('relea.date')->le($releaseEndDay)->fi()
            ->beginIF($dpmpReleaseIds)->andWhere('relea.id')->in($dpmpReleaseIds)->fi()
            ->beginIF($dpmpProductIds)->andWhere('relea.product')->in($dpmpProductIds)->fi()
            ->beginIF(($releaseBeginDay || $releaseEndDay ) && empty($dpmpReleaseIds) && empty($dpmpProductIds))->andWhere("relea.id in (select max(id) id from zt_release zr $where group by product )")->fi() //参数只有开始和结束时间，则返回范围内的所有，删除不传
            ->beginIF($dpmpProductIds && (empty($releaseBeginDay) && empty($releaseEndDay) && empty($dpmpReleaseIds)))->andWhere("relea.id in (select max(id) id from zt_release zr $where group by product )")->fi() //参数只有产品，则返回最新，删除不传
            ->beginIF(($releaseBeginDay || $releaseEndDay ) && empty($dpmpReleaseIds) && $dpmpProductIds)->andWhere("relea.id in (select max(id) id from zt_release zr $where group by product )")->fi() //参数开始和结束时间、产品
            ->beginIF(empty($dpmpReleaseIds) && empty($releaseBeginDay) && empty($releaseEndDay) && empty($dpmpProductIds))->andWhere("relea.id in (select max(id) id from zt_release zr $where group by product )")->fi() //不传参数
            ->fetchAll();
        $user = $this->dao->select('account,mobile')->from(TABLE_USER)->where('deleted')->eq('0')->fetchPairs('account','mobile');
        foreach ($res as $key=>$re) {
            $res[$key]->managerTel = zmget($user,$re->manager,'');
            $res[$key]->gitPaths   = $re->gitPaths ? trim(str_replace(PHP_EOL,',',str_replace("\r\n",',',$re->gitPaths)),',') : ''; //20240527 新增制版的git地址
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
        $keyList = array('releaseBeginDay','releaseEndDay','dpmpReleaseIds','dpmpProductIds');
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

        if(isset($_POST['releaseBeginDay']) && $_POST['releaseBeginDay'] && isset($_POST['releaseEndDay']) && $_POST['releaseEndDay']){

            if( strtotime($_POST['releaseEndDay']) < strtotime($_POST['releaseBeginDay'])){
                $errMsg[] = '发布结束时间不能早于发布开始时间!';
            }
        }

        return $errMsg;
    }

}
