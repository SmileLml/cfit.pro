<?php
include '../../control.php';
class myApimeasure extends apimeasure
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数
    const FAIL_CODE      = 999;    //请求失败

    public function getprojectunclosed()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('project' , 'getprojectunclosed');
        // 接口token校验
        $this->checkApiToken();

        // 引入年度计划语言包
        $this->app->loadLang('projectplan');

        // 查询项目和结项信息
        $projectList = $this->dao->select('t1.id,t2.code as number,t1.name,t1.code,t2.type,t1.status as state,t1.PM as manager,t3.dept,t1.begin as planBegin,t1.end as planEnd,t1.realBegan,t1.realEnd')->from(TABLE_PROJECT)->alias('t1')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t2')->on('t1.id=t2.project')
            ->leftJoin(TABLE_PROJECTCREATION)->alias('t3')->on('t2.id=t3.plan')
            ->where('t1.type')->eq('project')
            ->andWhere('t1.status')->eq('closed')
            ->andWhere('t1.deleted')->eq('0')
            ->fetchAll('id');

        // 查询所需项目结项申请时间
        $projectIds   = array_column($projectList, 'id');
        $closingTimes = $this->loadModel('project')->getClosingTimeAll($projectIds);

        // 查询所需项目对应项目成员
        $teamMembers = $this->loadModel('project')->getTeamMembersAll($projectIds);
        $depts = $this->dao->select('id, ldapName')->from(TABLE_DEPT)->fetchPairs();
        $depts[17] = $this->lang->projectplan->CD;
        $depts[26] = $this->lang->projectplan->TJ;
        $depts[30] = $this->lang->projectplan->SH;
        $dataList = [];

        // 项目成员
        foreach($projectList as $projectID => $info){
//            $arr = array_flip(explode(',',$members->members));去除项目经理以及项目主管(项目成员字段中只保留普通成员)
//            unset($arr[$projectList[$members->root]->manager]);
//            unset($arr[$projectList[$members->root]->director]);
//            $projectList[$members->root]->members   = implode(',',array_flip($arr));
            $info->type                = zget($this->lang->projectplan->typeList, $info->type, '');
            $info->state               = zget($this->lang->projectplan->statusList, $info->state, '');
            $info->dept                = zmget($depts, $info->dept, '');
            $info->planBegin           = $info->planBegin == '0000-00-00' ? '' : $info->planBegin;
            $info->planEnd             = $info->planEnd   == '0000-00-00' ? '' : $info->planEnd;
            $info->realBegin           = $info->realBegan == '0000-00-00' ? '' : $info->realBegan;
            $info->realEnd             = $info->realEnd   == '0000-00-00' ? '' : $info->realEnd;
            $info->proposedClosingTime = $closingTimes[$projectID]->proposedClosingTime == '0000-00-00' || empty($closingTimes[$projectID]->proposedClosingTime) ? '' : $closingTimes[$projectID]->proposedClosingTime;
            $info->closingMeetingTime  = $closingTimes[$projectID]->closingMeetingTime  == '0000-00-00' || empty($closingTimes[$projectID]->closingMeetingTime)  ? '' : $closingTimes[$projectID]->closingMeetingTime;
            $info->members             = $teamMembers[$projectID]->members;
            unset($info->id);
            unset($info->bearDept);
            $dataList[]   = $info;
        }

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }
}

