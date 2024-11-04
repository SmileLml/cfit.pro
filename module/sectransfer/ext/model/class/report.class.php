<?php

class reportSectransfer extends sectransferModel
{
    /**
     * 统计报表
     * @param mixed $start
     * @param mixed $end
     * @param mixed $type
     */
    public function monthReport($start, $end, $type = 1)
    {
        $projects = $this->dao->select('t1.project,t1.bearDept,t1.mark, t3.name as deptName')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_DEPT)->alias('t3')
            ->on('t1.bearDept=t3.id')
            ->where('t1.deleted')->eq(0)
            ->fetchAll('project');

        $data = [];
        if (1 == $type) {
            $data = $this->infoRepost($projects, $start, $end, $data);
            $data = $this->infoQZRepost($projects, $start, $end, $data);
            $data = $this->productenrollReport($projects, $start, $end, $data);
        } else {
            $data = $this->sectransferReport($projects, $start, $end, $data);
        }

        return array_values($data);
    }


    public function infoRepost($projects, $start, $end, $data)
    {
        $this->app->loadLang('info');
        //查询金信数据获取交付状态
        $infoList = $this->dao
            ->select('*')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('info')
            ->andWhere('`after`')->in(['productsuccess', 'fetchsuccess', 'fetchfail'])
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');
        //金信数据获取单信息
        $infoData = $this->dao
            ->select('*')
            ->from(TABLE_INFO)
            ->where('status')->in(['fetchsuccess', 'fetchfail'])
            ->andWhere('id')->in($infoIds)
            ->fetchall('id');
        //金信数据获取单交付次数
        $infoNum = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('info')
            ->andWhere('after')->in(['fetchsuccess', 'fetchfail'])
            ->andWhere('objectID')->in($infoIds)
            ->groupby('objectID')
            ->fetchpairs();


        foreach ($infoList as $item){
            $info = $infoData[$item->objectID] ?? '';
            if(empty($info)) continue;

            $project = $projects[$info->project];

            //通过次数
            if(in_array($item->after, ['fetchsuccess', 'fetchfail'])){
                if (!isset($data[$info->code])) {
                    $data[$info->code] = [
                        'code'                => $info->code,
                        'status'              => zget($this->lang->info->statusList, $info->status, ''),
                        'statusKey'           => $info->status,
                        'orderType'           => '其他类型',
                        'isPutproductionFail' => '/',
                        'isModifyFail'        => '/',
                        'deliveryNum'         => $infoNum[$info->id],
                        'projectNum'          => 1,
                        'returnNum'           => $info->status == 'fetchsuccess' ? $infoNum[$info->id] - 1 : $infoNum[$info->id],
                        'isCBP'               => '否',
                        'changeType'          => '/',
                        'productCode'         => '/',
                        'fixType'             => $info->fixType,
                        'projectCode'         => $project->mark,
                        'deptName'            => $project->deptName,
                        'deptId'              => $project->bearDept,
                        'endTime'             => $item->createdDate,
                        'rejectReason'        => '',
                        'reportType'          => 'info',
                    ];
                }
            }
        }

        return $data;
    }

    public function infoQZRepost($projects, $start, $end, $data)
    {
        $endStatus = ['fetchsuccess', 'fetchsuccesspart', 'fetchfail', 'fetchcancel'];
        $this->app->loadLang('infoqz');
        //查询清总数据获取--交付单
        $infoList = $this->dao
            ->select('*')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('infoqz')
            ->andWhere('`after`')->in(['withexternalapproval', 'fetchsuccess', 'fetchsuccesspart', 'fetchfail', 'fetchcancel'])
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');

        $infoData = $this->dao
            ->select('*')
            ->from(TABLE_INFO_QZ)
            ->where('status')->in($endStatus)
            ->andWhere('id')->in($infoIds)
            ->fetchall('id');

        $infoNum = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('infoqz')
            ->andWhere('after')->in($endStatus)
            ->andWhere('objectID')->in($infoIds)
            ->groupby('objectID')
            ->fetchpairs();

        foreach ($infoList as $item){
            $info = $infoData[$item->objectID] ?? '';
            if(empty($info)) continue;

            $project = $projects[$info->project];

                //通过次数
                if(in_array($item->after, $endStatus)){

                    if (!isset($data[$info->code])) {
                        $data[$info->code] = [
                            'code'                => $info->code,
                            'status'              => zget($this->lang->infoqz->statusList, $info->status, ''),
                            'statusKey'           => $info->status,
                            'orderType'           => '其他类型',
                            'isPutproductionFail' => '/',
                            'isModifyFail'        => '/',
                            'deliveryNum'         => $infoNum[$info->id],
                            'projectNum'          => 1,
                            'returnNum'           => $info->status == 'fetchsuccess' ? $infoNum[$info->id] - 1 : $infoNum[$info->id],
                            'isCBP'               => '是',
                            'changeType'          => '/',
                            'productCode'         => '/',
                            'fixType'             => $info->fixType,
                            'projectCode'         => $project->mark,
                            'deptName'            => $project->deptName,
                            'deptId'              => $project->bearDept,
                            'endTime'             => $item->createdDate,
                            'rejectReason'        => '',
                            'reportType'          => 'infoqz',
                        ];
                    }

                }
        }

        return $data;
    }

    public function productenrollReport($projects, $start, $end, $data)
    {
        $this->app->loadLang('outwarddelivery');
        $passStatus = ['emispass', 'giteepass'];
        $failStatus = ['productenrollreject', 'qingzongsynfailed', 'cancel'];
        $statusList = ['emispass', 'giteepass', 'cancel'];

        //查询清总产品登记--交付单
        $infoList = $this->dao
            ->select('*')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('outwarddelivery')
            ->andWhere('`after`')->in(['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'qingzongsynfailed', 'cancel'])
            ->andWhere('extra')->eq('产品登记单')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');

        $infoData = $this->dao->select('t1.id as id, t2.id as pid,t2.code,t2.status,t1.implementationForm,t1.projectPlanId,t2.returnTimes,t2.productId,t2.returnCase,t1.productInfoCode')
            ->from(TABLE_OUTWARDDELIVERY)->alias('t1')
            ->leftjoin(TABLE_PRODUCTENROLL)->alias('t2')
            ->on('t1.productEnrollId=t2.id and t1.isNewProductEnroll = 1')
            ->where('t1.deleted')->eq(0)
            ->andwhere('t2.deleted')->eq(0)
            ->andwhere('t2.giteeId')->ne('')
            ->andWhere('t2.status')->in($statusList)
            ->andWhere('t1.id')->in($infoIds)
            ->fetchAll('id');

        foreach ($infoList as $item){
            $info = $infoData[$item->objectID] ?? '';
            if(empty($info)) continue;

            $project = $projects[$info->projectPlanId];

                //通过次数
                if(in_array($item->after, $statusList)){
                    if(isset($data[$project->deptName]['projectPassSumInfo'][$info->code])) continue;
                    $returnTimes = $info->status == 'cancel' ? $info->returnTimes : $info->returnTimes + 1;
                    if (!isset($data[$info->code])) {
                        $data[$info->code] = [
                            'code'                => $info->code,
                            'status'              => zget($this->lang->outwarddelivery->statusList, $info->status, ''),
                            'statusKey'           => $info->status,
                            'orderType'           => '其他类型',
                            'isPutproductionFail' => '/',
                            'isModifyFail'        => '/',
                            'deliveryNum'         => $returnTimes,
                            'projectNum'          => 1,
                            'returnNum'           => $info->returnTimes,
                            'isCBP'               => '是',
                            'fixType'             => $info->implementationForm,
                            'changeType'          => '/',
                            'productCode'         => $info->productInfoCode,
                            'projectCode'         => $project->mark,
                            'deptName'            => $project->deptName,
                            'deptId'              => $project->bearDept,
                            'endTime'             => $item->createdDate,
                            'rejectReason'        => $info->returnCase,
                            'reportType'          => 'productenroll',
                        ];
                    }
                }
        }

        return $data;
    }

    public function sectransferReport($projects, $start, $end, $data)
    {
        $this->app->loadLang('application');
        $failStatus = ['askCenterFailed', 'centerReject'];

        $projectPlans = $this->dao->select('id,project')->from(TABLE_PROJECTPLAN)
            ->where('status')->ne('deleted')
            ->orderBy('id_desc')
            ->fetchPairs();

        $infoList = $this->dao
            ->select('*')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('sectransfer')
            ->andWhere('`after`')->in(['alreadyEdliver', 'askCenterFailed', 'centerReject'])
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');

        //查询对外移交
        $infoData = $this->dao
            ->select('
            t1.id as id,
            t1.secondorderId,
            t1.status,
            t1.jftype,
            t1.inproject,
            t1.externalRecipient,
            t1.department,
            t1.rejectReason,
            t2.createdBy,
            t2.implementationForm,
            t2.internalProject as orderProject,
            t2.createdBy as orderCreatedBy')
            ->from(TABLE_SECTRANSFER)->alias('t1')
            ->leftjoin(TABLE_SECONDORDER)->alias('t2')
            ->on('t1.secondorderId=t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t1.id')->in($infoIds)
            ->andWhere('t1.status')->eq('alreadyEdliver')
            ->fetchAll('id');

        $infoNum = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('sectransfer')
            ->andWhere('after')->in(['alreadyEdliver', 'centerReject'])
            ->andWhere('`before`')->ne('alreadyEdliver')
            ->andWhere('objectID')->in($infoIds)
            ->groupby('objectID')
            ->fetchpairs();

        foreach ($infoList as $item){
            $info = $infoData[$item->objectID] ?? '';
            if(empty($info)) continue;
            if(empty($info->inproject) && empty($info->orderProject)){
                $project           = new stdClass();
                $project->project  = '';
                $project->bearDept = '';
                $project->mark     = '';
                $project->deptName = '';
            }else{
                if($info->jftype == 1) {
                    $projectId = $projectPlans[$info->inproject];
                    $project = $projects[$projectId];
                }else{
                    $project = $projects[$info->orderProject];
                }
            }

            if ($info->jftype == 1 || ($info->jftype == 2 && $info->implementationForm == 'project')){
                //通过次数
                //如果移交方式为项目移交，并且外部接收方为清算总中心，通过状态流转前后都是已交付状态
                if(
                    ($info->jftype == 1 && $info->externalRecipient == 2)
                    || ($info->jftype == 2 && in_array($info->orderCreatedBy, ['guestcn','guestjx']))
                ){
                    if($item->before == 'alreadyEdliver' && $item->after == 'alreadyEdliver'){
                        $data = $this->getSectransferData($data, $info, $infoNum, $project, $item->createdDate);
                    }
                }else{
                    if($item->after == 'alreadyEdliver'){
                        $data = $this->getSectransferData($data, $info, $infoNum, $project, $item->createdDate);
                    }
                }
            }elseif($info->jftype == 2){
                //通过次数
                if(in_array($info->orderCreatedBy, ['guestcn','guestjx'])){
                    if($item->before == 'alreadyEdliver' && $item->after == 'alreadyEdliver'){
                        $data = $this->getSectransferData($data, $info, $infoNum, $project, $item->createdDate);
                    }
                }else{
                    if($item->after == 'alreadyEdliver'){
                        $data = $this->getSectransferData($data, $info, $infoNum, $project, $item->createdDate);
                    }
                }
            }
        }

        return $data;
    }

    private function getSectransferData($data, $info, $infoNum, $project, $endTime)
    {
        $CBP_department = ['qszzx', 'qszzxzh', 'qszzxzf'];

        if (!isset($data[$info->id])) {
            $data[$info->id] = [
                'code'                => $info->id,
                'status'              => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                'statusKey'           => $info->status,
                'orderType'           => '其他类型',
                'isPutproductionFail' => '/',
                'isModifyFail'        => '/',
                'deliveryNum'         => $infoNum[$info->id],
                'projectNum'          => 1,
                'returnNum'           => $infoNum[$info->id] - 1,
                'isCBP'               => in_array($info->department, $CBP_department) ? '是' : '否',
                'fixType'             => $info->implementationForm,
                'changeType'          => '/',
                'productCode'         => '/',
                'projectCode'         => $project->mark,
                'deptName'            => $project->deptName,
                'deptId'              => $project->bearDept,
                'endTime'             => $endTime,
                'rejectReason'        => $info->rejectReason,
                'reportType'          => 'sectransfer',
            ];
        }

        return $data;
    }
}
