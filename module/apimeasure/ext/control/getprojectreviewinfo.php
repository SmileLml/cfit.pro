<?php
include '../../control.php';
class myApimeasure extends apimeasure
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function getprojectreviewinfo()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('project' , 'getprojectreviewinfo');
        // token以及参数校验
        $this->checkApiToken();
        $errMsg = $this->checkInput();
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::FAIL_CODE);
        }

        // 引入评审语言包
        $this->app->loadLang('review');
        $tmpList = [];$dataList = new stdClass();$resList = [];

        // 查询项目下关联的评审
        $codeList = explode(',',$_POST['projectNumber']);
        $projectList = $this->dao->select('t2.id as reviewId, t2.type, t2.title as reviewTitle, t1.code')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')->on('t1.project=t2.project')
            ->where('t1.code')->in($codeList)
            ->andWhere('t2.closeTime')->ne('0000-00-00 00:00:00')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t2.type')->in($this->lang->review->reviewInfoType)
            ->fetchAll();

        // 查询所有相关评审被退回节点
        $reviewIds = array_filter(array_column($projectList, 'reviewId'));
        $reviewRejectNodeList = $this->loadModel('review')->getReviewRejectNodes($reviewIds);

        foreach($projectList as $info){
            $info->reviewType = zget($this->lang->review->typeList, $info->type, '');
            if(isset($reviewRejectNodeList[$info->reviewId])){
                foreach($reviewRejectNodeList[$info->reviewId] as $nodes){
                    // 给接口返回1或者0 1代表一次通过 0代表非一次通过
                    $info->isInitialReviewOnePassed      = in_array($nodes->nodeCode,$this->lang->review->rejectCheckFirstReviewList) ? 0 : 1;
                    $info->isFormalReviewOnePassed       = in_array($nodes->nodeCode,$this->lang->review->rejectCheckFormalReviewList) ? 0 : 1;
                    $info->isVerificationReviewOnePassed = in_array($nodes->nodeCode,$this->lang->review->rejectCheckVerifyList) ? 0 : 1;
                }
            }else{
                // 无相关退回节点说明都是一次通过
                $info->isInitialReviewOnePassed      = 1;
                $info->isFormalReviewOnePassed       = 1;
                $info->isVerificationReviewOnePassed = 1;
            }
            $tmpList[$info->code][] = $info;
            unset($info->type);
            unset($info->code);
        }

        // 处理数据格式
        foreach($tmpList as $code => $list){
            $dataList->projectNumber = $code;
            $dataList->data          = $list;
            $list       = clone($dataList);
            $resList[]  = $list;
        }

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $resList, 0);
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
            if(!isset($this->config->api->getProjectReviewFields[$key])){
                $errMsg[] = $key.$this->lang->api->nameError;
            }
        }

        if(!empty($errMsg)){
            return $errMsg;
        }

        foreach ($this->config->api->getProjectReviewFields as $k => $v)
        {
            if($this->post->$k == ''){
                $errMsg[] = $k.$v['name'].$this->post->$k.$this->lang->api->emptyError;
            }
        }
        return $errMsg;
    }
}
