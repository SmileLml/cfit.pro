<?php
include '../../control.php';
class myApimeasure extends apimeasure
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE   = 999;    //请求失败

    public function getprojectworkloadbug()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('project' , 'getprojectworkloadbug');
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
        $projectList = $this->dao->select('t1.project, t1.code, t2.workHours')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t2.id=t1.project')
            ->where('t1.code')->in($codeList)
            ->fetchAll('project');
        $projectIds = array_keys($projectList);

        // 根据项目查询用户工作量
        $workloads = $this->dao->select('project, cast(sum(consumed) as decimal(11,2)) as workload')->from(TABLE_EFFORT)
            ->where('project')->in($projectIds)
            ->andWhere('objectType')->eq('task')
            ->andWhere('deleted')->eq('0')
            ->groupBy('project')
            ->fetchPairs();

        // 查询所有相关bug(状态不是已关闭, bug分类是安全缺陷, 问题严重性是P0和P1)
        $bugs = $this->dao->select('project, count(1) as bugNum')->from(TABLE_BUG)
            ->where('project')->in($projectIds)
            ->andWhere('status')->ne('closed')
            ->andWhere('type')->eq('security')
            ->andWhere('severity')->in('1,2')
            ->andWhere('deleted')->eq('0')
            ->groupBy('project')
            ->fetchAll('project');

        foreach($projectList as $project => $info){
            $info->actualWorkload          = empty($workloads[$project])    ? '' : (string)round(($workloads[$project] / $info->workHours) / 8, 2);
            $info->highRiskSafetyBugNum    = empty($bugs[$project]->bugNum) ? '' :  $bugs[$project]->bugNum;
            $tmpList[$info->code] = $info;
            unset($info->project);
            unset($info->code);
            unset($info->workHours);
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
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $resList);
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
