<?php

class cfjkSectransfer extends sectransferModel
{
    public function getUnPushDataJX()
    {
        $unPushedData = $this->dao->select('*')->from(TABLE_SECTRANSFER)
            ->where('pushStatus')->eq('mediaSuccess')
            ->andWhere('status')->eq('waitDeliver')
            ->andWhere('deleted')->eq(0)
            ->fetchALl('id');  //获取未推送的数据
        if (empty($unPushedData)) {
            return [];
        }
        $res = [];
        foreach ($unPushedData as $data) {
            $code = $this->getOrderUser($data);
            if($code != 2 || 1 == $data->jftype){
                continue;
            }

            $response   = $this->sendsectransferJx($data);
//            $response   = '{"result": "success","message": "操作成功","data": {"id": "'.$data->secondorderId.'"},"code": 0}';
            $resultData = json_decode($response);
            //如果成功修改移交单发送状态
            if (!empty($resultData) && isset($resultData->code) && '0' == $resultData->code) {
                //只有项目移交才有外部单号   工单移交使用工单的外部单号
                $this->dao->update(TABLE_SECONDORDER)
                    ->set('status')->eq('delivered')
                    ->set('dealUser')->eq('')
                    ->set('pushDate')->eq(helper::now())
                    ->where('id')->eq($data->secondorderId)
                    ->exec();
                $this->loadModel('action')->create('secondorder', $data->secondorderId, 'syncstatusbyprotransfer');
                $this->loadModel('consumed')->record('secondorder', $data->secondorderId, 0, 'guestjk', 'indelivery', 'delivered', [], '');
                $this->dao->update(TABLE_SECTRANSFER)
                    ->set('pushStatus')->eq('success')
                    ->set('approver')->eq('guestjx')
                    ->set('status')->eq('alreadyEdliver')
                    ->where('id')->eq($data->id)
                    ->exec();
                $this->loadModel('action')->create('sectransfer', $data->id, 'jxsyncsuccess', $resultData->message);
                $this->loadModel('consumed')->record('sectransfer', $data->id, 0, 'guestjk', 'waitDeliver', 'alreadyEdliver', [], '');
                //更新审批流程
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($data->id)
                    ->andWhere('version')->eq($data->version)
                    ->andWhere('stage')->eq('6')->fetch('id');
                if ($next) {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('syncsuccess')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
                    $this->dao->update(TABLE_REVIEWER)
                        ->set('status')->eq('syncsuccess')
                        ->set('comment')->eq('同步成方金信成功')
                        ->set('reviewTime')->eq(helper::now())
                        ->where('node')->eq($next)->exec();
                }
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                    ->andWhere('objectID')->eq($data->id)
                    ->andWhere('version')->eq($data->version)
                    ->andWhere('stage')->eq('7')->fetch('id');
                if ($next) {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('suspend')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('suspend')->set('comment')->eq('')->set('reviewTime')->eq(null)->where('node')->eq($next)->exec();
                }
            } else {
                //失败次数超过五次，就不重推了
                $failReason = empty($resultData->description) ? $resultData : $resultData->description;
                if ($data->pushNum + 1 >= 5) {
                    $this->dao->update(TABLE_SECTRANSFER)
                        ->set('pushStatus')->eq('fail')
                        ->set('status')->eq('askCenterFailed')
                        ->set('approver')->eq($data->sec)
                        ->set('pushNum')->eq($data->pushNum + 1)
                        ->set('sendFailReason')->eq($failReason)
                        ->where('id')->eq($data->id)
                        ->exec();
                    $this->loadModel('action')->create('sectransfer', $data->id, 'jxsyncfail', $failReason);
                    $this->loadModel('consumed')->record('sectransfer', $data->id, 0, 'guestjk', 'waitDeliver', 'askCenterFailed', [], '');
                    //更新审批流程
                    $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('sectransfer')   //查找下一节点的状态
                        ->andWhere('objectID')->eq($data->id)
                        ->andWhere('version')->eq($data->version)
                        ->andWhere('stage')->eq('6')->fetch('id');
                    if ($next) {
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('syncfail')->where('id')->eq($next)->exec();  //更新下一节点的状态为confirming
                        $this->dao->update(TABLE_REVIEWER)
                            ->set('status')->eq('syncfail')
                            ->set('comment')->eq('同步成方金信失败')
                            ->set('reviewTime')->eq(helper::now())
                            ->where('node')->eq($next)->exec();
                    }
                } else {
                    $this->dao->update(TABLE_SECTRANSFER)
                        ->set('pushNum')->eq($data->pushNum + 1)
                        ->where('id')->eq($data->id)
                        ->exec();
                    $this->loadModel('action')->create('sectransfer', $data->id, 'jxsyncfail', $failReason);
                }
            }

            $response           = json_decode($response);
            $run['sectransfer'] = $data->id;
            $run['response']    = $response;

            $res[] = $run;
        }

        return $res;
    }

    /**
     * 金信工单接口发送
     * @param $data
     */
    public function sendsectransferJx($data)
    {
        $this->loadModel('requestlog');
        $pushEnable = $this->config->global->secondorderEnableJx;
        $response   = '';
        //判断是否开启发送反馈
        if ('enable' == $pushEnable) {
            $url = $this->config->global->secondorderFeedbackUrlJx;
            $pushAppId = $this->config->global->secondorderAppIdJx;
            $pushAppSecret = $this->config->global->secondorderAppSecretJx;
            $ts = time();
            $uuid = common::create_guid();
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            //请求头
            $headers = [
                'appId: ' . $pushAppId,
                'appSecret: ' . $pushAppSecret,
                'ts: ' . $ts,
                'nonce: ' . $uuid,
                'sign: ' . $sign,
            ];
            $users       = $this->loadModel('user')->getPairs('noletter|noclosed');
            $secondOrder = $this->dao->select('externalCode,type,consultRes,testRes,dealRes')->from(TABLE_SECONDORDER)->where('id')->eq($data->secondorderId)->fetch();
            //数据体
            $pushData = [
                'processName' => '任务单',
                'idUnique' => $secondOrder->externalCode,
                'nodeName' => '研效平台受理任务单',
                'isAgree'  => 1,
                'comment'  => '',
                'nodeDataMap' => [
                    'operator' => zget($users,$this->app->user->account,''),
                    'completeStatus' => '',
                ]
            ];
            if($secondOrder->type == 'consult'){
                $pushData['nodeDataMap']['completeStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondOrder->consultRes,ENT_QUOTES)))));
            }else if($secondOrder->type == 'test'){
                $pushData['nodeDataMap']['completeStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondOrder->testRes,ENT_QUOTES)))));
            }else{
                $pushData['nodeDataMap']['completeStatus'] = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($secondOrder->dealRes,ENT_QUOTES)))));
            }

            $object     = 'sectransfer';
            $objectType = 'sectransfersync';
            $status = 'fail';
            $extra  = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', [], $headers);

            if (!empty($result)) {
                $resultData = json_decode($result);
                if (isset($resultData->code) && $resultData->code == '0') {
                    $status = 'success';
                }
                $response = $result;
            } else {
                $response = '对方无响应';
            }

            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra, $data->id);
        }

        return $response;
    }

    /**
     * 同步附件
     * @param $secondOrder
     * @return mixed|true
     */
    public function pushFileJx($secondOrder)
    {
        $pushEnable   = $this->config->global->secondorderEnableJx;
        //判断是否开启发送反馈
        if ('enable' == $pushEnable) {
            $url = $this->config->global->secondorderSftpServerIPJx;
            $pushAppId = $this->config->global->secondorderAppIdJx;
            $pushAppSecret = $this->config->global->secondorderAppSecretJx;

            $files = $this->loadModel('file')->getByObject('secondorderDeliver', $secondOrder->id);
            if(empty($files)){
                return true;
            }

            foreach ($files as $file) {
                $ts      = time();
                $uuid    = common::create_guid();
                $sign    = md5('appId=' . $pushAppId . '&nonce=' . $uuid . '&ts=' . $ts . '&appSecret=' . $pushAppSecret);
                $headers = [
                    'appId: ' . $pushAppId,
                    'ts: ' . $ts,
                    'nonce: ' . $uuid,
                    'sign: ' . $sign,
                    'Content-Type: multipart/form-data',
                ];
                $pushData = [//数据体
                    'processType' => 'work_order',
                    'idUnique' => $secondOrder->externalCode,
                    'file' => new CURLFile($file->realPath,  'application/octet-stream', $file->title),
                    'md5' => md5_file($file->realPath),
                    'type' => '',
                ];

                $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'data', [], $headers);
                $result = json_decode($result);
                if(!isset($result->code) || $result->code != 0){
                    break;
                }
            }
        }

        return $result ?? true;
    }

    /**
     * 判断任务工单状态是否能置为已交付
     * @param $id
     * @param $orderId
     * @return bool
     */
    public function isDeliveredByOrder($id, $orderId)
    {
        $list = $this->dao
            ->select('id,status')
            ->from(TABLE_SECTRANSFER)
            ->where('status')->ne('alreadyEdliver')
            ->andWhere('id')->ne($id)
            ->andWhere('secondorderId')->eq($orderId)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();

        return empty($list);
    }

    /**
     * 统计报表
     */
    public function monthReport($start, $end, $type = 1)
    {
        $projects = $this->dao->select('t1.project,t1.bearDept,t3.name as deptName')->from(TABLE_PROJECTPLAN)->alias('t1')
            ->leftjoin(TABLE_DEPT)->alias('t3')
            ->on('t1.bearDept=t3.id')
            ->where('t1.deleted')->eq(0)
            ->fetchAll('project');

        $data = [];
        if($type == 1){
            $data = $this->infoRepost($projects, $start, $end, $data);
            $data = $this->infoQZRepost($projects, $start, $end, $data);
            $data = $this->productenrollReport($projects, $start, $end, $data);
        }else{
            $data = $this->sectransferReport($projects, $start, $end, $data);
        }

        foreach ($data as $key => $value){
            $str = '';
            foreach ($value['projectInfo'] as $val){
                $str .= '【单号：' . $val['code'] . '; 当前状态：' . $val['status'] . '; 次数：' . $val['num'] . '】';
            }
            $data[$key]['projectInfo'] = $str;

            $str = '';
            foreach ($value['projectPassSumInfo'] as $val){
                $str .= '【单号：' . $val['code'] . '; 当前状态：' . $val['status'] . '; 次数：' . $val['num'] . '】';
            }
            $data[$key]['projectPassSumInfo'] = $str;

            $str = '';
            foreach ($value['projectFailNumInfo'] as $val){
                $str .= '【单号：' . $val['code'] . '; 当前状态：' . $val['status'] . '】';
            }
            $data[$key]['projectFailNumInfo'] = $str;

            $str = '';
            foreach ($value['secondInfo'] as $val){
                $str .= '【单号：' . $val['code'] . '; 当前状态：' . $val['status'] . '; 次数：' . $val['num'] . '】';
            }
            $data[$key]['secondInfo'] = $str;

            $str = '';
            foreach ($value['secondPassSumInfo'] as $val){
                $str .= '【单号：' . $val['code'] . '; 当前状态：' . $val['status'] . '; 次数：' . $val['num'] . '】';
            }
            $data[$key]['secondPassSumInfo'] = $str;

            $str = '';
            foreach ($value['secondFailNumInfo'] as $val){
                $str .= '【单号：' . $val['code'] . '; 当前状态：' . $val['status'] . '】';
            }
            $data[$key]['secondFailNumInfo'] = $str;
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
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');
        //金信数据获取单信息
        $infoData = $this->dao
            ->select('*')
            ->from(TABLE_INFO)
            ->where('status')->ne('deleted')
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
            //创建没有对应部门报表
            if(!isset($data[$project->deptName])){
                $keyArr = ['projectInfo', 'projectPassSumInfo', 'projectFailNumInfo', 'secondInfo', 'secondPassSumInfo', 'secondFailNumInfo'];
                $keys = array_keys($this->lang->sectransfer->exportFileds);
                $data[$project->deptName]['deptName'] = $project->deptName;
                foreach ($keys as $key){
                    if($key == 'deptName') continue;
                    $data[$project->deptName][$key] = in_array($key, $keyArr) ? [] : 0;
                }
            }
            //项目报表
            if($info->fixType == 'project'){
                //交付次数
                if($item->after == 'productsuccess'){
                    $data[$project->deptName]['projectNum']++;
                    if(!isset($data[$project->deptName]['projectInfo'][$info->code])){
                        $data[$project->deptName]['projectInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->info->statusList, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['projectInfo'][$info->code]['num']++;
                }
                //通过次数
                if($item->after == 'fetchsuccess'){
                    $data[$project->deptName]['projectPassNum']++;
                    if($infoNum[$info->id] == 1){
                        $data[$project->deptName]['projectOne']++;
                    }elseif ($infoNum[$info->id] == 2){
                        $data[$project->deptName]['projectTwo']++;
                    }else{
                        $data[$project->deptName]['projectThree']++;
                    }
                    $data[$project->deptName]['projectPassSum'] = $data[$project->deptName]['projectPassSum'] + $infoNum[$info->id];

                    if(!isset($data[$project->deptName]['projectPassSumInfo'][$info->code])){
                        $data[$project->deptName]['projectPassSumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->info->statusList, $info->status, ''),
                            'num' => $infoNum[$info->id],
                        ];
                    }
                }
                //异常单数(时间范围内有异常记录的)
                if($item->after == 'fetchfail'){
                    $data[$project->deptName]['projectFailNum']++;
                    if(!isset($data[$project->deptName]['projectFailNumInfo'][$info->code])){
                        $data[$project->deptName]['projectFailNumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->info->statusList, $info->status, ''),
                        ];
                    }
                }
            }else{//二线项目
                //交付次数
                if($item->after == 'productsuccess'){
                    $data[$project->deptName]['secondNum']++;
                    if(!isset($data[$project->deptName]['secondInfo'][$info->code])){
                        $data[$project->deptName]['secondInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->info->statusList, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['secondInfo'][$info->code]['num']++;
                }
                //通过次数
                if($item->after == 'fetchsuccess'){
                    $data[$project->deptName]['secondPassNum']++;
                    if($infoNum[$info->id] == 1){
                        $data[$project->deptName]['secondOne']++;
                    }elseif ($infoNum[$info->id] == 2){
                        $data[$project->deptName]['secondTwo']++;
                    }else{
                        $data[$project->deptName]['secondThree']++;
                    }
                    $data[$project->deptName]['secondPassSum'] = $data[$project->deptName]['secondPassSum'] + $infoNum[$info->id];

                    if(!isset($data[$project->deptName]['secondPassSumInfo'][$info->code])){
                        $data[$project->deptName]['secondPassSumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->info->statusList, $info->status, ''),
                            'num' => $info->version,
                        ];
                    }
                }
                //异常单数(时间范围内有异常记录的)
                if($item->after == 'fetchfail'){
                    $data[$project->deptName]['secondFailNum']++;
                    if(!isset($data[$project->deptName]['secondFailNumInfo'][$info->code])){
                        $data[$project->deptName]['secondFailNumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->info->statusList, $info->status, ''),
                        ];
                    }
                }
            }
        }

        return $data;
    }

    public function infoQZRepost($projects, $start, $end, $data)
    {
        $this->app->loadLang('infoqz');
        //查询清总数据获取--交付单
        $infoList = $this->dao
            ->select('*')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('infoqz')
            ->andWhere('`after`')->in(['withexternalapproval', 'fetchsuccess', 'fetchsuccesspart', 'qingzongsynfailed', 'fetchfail', 'outreject', 'fetchcancel'])
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->andWhere('deleted')->eq('0')
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');

        $infoData = $this->dao
            ->select('*')
            ->from(TABLE_INFO_QZ)
            ->where('status')->ne('deleted')
            ->andWhere('id')->in($infoIds)
            ->fetchall('id');

        $infoNum = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('infoqz')
            ->andWhere('after')->in(['fetchsuccess', 'fetchsuccesspart', 'fetchfail', 'outreject', 'fetchcancel'])
            ->andWhere('objectID')->in($infoIds)
            ->groupby('objectID')
            ->fetchpairs();

        foreach ($infoList as $item){
            $info = $infoData[$item->objectID] ?? '';
            if(empty($info)) continue;

            $project = $projects[$info->project];
            if(!isset($data[$project->deptName])){
                $keyArr = ['projectInfo', 'projectPassSumInfo', 'projectFailNumInfo', 'secondInfo', 'secondPassSumInfo', 'secondFailNumInfo'];
                $keys = array_keys($this->lang->sectransfer->exportFileds);
                $data[$project->deptName]['deptName'] = $project->deptName;
                foreach ($keys as $key){
                    if($key == 'deptName') continue;
                    $data[$project->deptName][$key] = in_array($key, $keyArr) ? [] : 0;
                }
            }

            if($info->fixType == 'project'){
                //交付次数
                if($item->after == 'withexternalapproval'){
                    $data[$project->deptName]['projectNum']++;
                    if(!isset($data[$project->deptName]['projectInfo'][$info->code])){
                        $data[$project->deptName]['projectInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->infoqz->statusList, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['projectInfo'][$info->code]['num']++;
                }
                //通过次数
                if(in_array($item->after, ['fetchsuccess', 'fetchsuccesspart'])){
                    $data[$project->deptName]['projectPassNum']++;
                    if($infoNum[$info->id] == 1){
                        $data[$project->deptName]['projectOne']++;
                    }elseif ($infoNum[$info->id] == 2){
                        $data[$project->deptName]['projectTwo']++;
                    }else{
                        $data[$project->deptName]['projectThree']++;
                    }
                    $data[$project->deptName]['projectPassSum'] = $data[$project->deptName]['projectPassSum'] + $infoNum[$info->id];

                    if(!isset($data[$project->deptName]['projectPassSumInfo'][$info->code])){
                        $data[$project->deptName]['projectPassSumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->infoqz->statusList, $info->status, ''),
                            'num' => $infoNum[$info->id],
                        ];
                    }
                }
                //异常单数(时间范围内有异常记录的)
                if(in_array($item->after, ['qingzongsynfailed', 'fetchfail', 'outreject', 'fetchcancel'])){
                    $data[$project->deptName]['projectFailNum']++;
                    if(!isset($data[$project->deptName]['projectFailNumInfo'][$info->code])){
                        $data[$project->deptName]['projectFailNumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->infoqz->statusList, $info->status, ''),
                        ];
                    }
                }
            }else{
                //交付次数
                if($item->after == 'withexternalapproval'){
                    $data[$project->deptName]['secondNum']++;
                    if(!isset($data[$project->deptName]['secondInfo'][$info->code])){
                        $data[$project->deptName]['secondInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->infoqz->statusList, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['secondInfo'][$info->code]['num']++;
                }
                //通过次数
                if(in_array($item->after, ['fetchsuccess', 'fetchsuccesspart'])){
                    $data[$project->deptName]['secondPassNum']++;
                    if($infoNum[$info->id] == 1){
                        $data[$project->deptName]['secondOne']++;
                    }elseif ($infoNum[$info->id] == 2){
                        $data[$project->deptName]['secondTwo']++;
                    }else{
                        $data[$project->deptName]['secondThree']++;
                    }
                    $data[$project->deptName]['secondPassSum'] = $data[$project->deptName]['secondPassSum'] + $infoNum[$info->id];

                    if(!isset($data[$project->deptName]['secondPassSumInfo'][$info->code])){
                        $data[$project->deptName]['secondPassSumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->infoqz->statusList, $info->status, ''),
                            'num' => $infoNum[$info->id],
                        ];
                    }
                }
                //异常单数(时间范围内有异常记录的)
                if(in_array($item->after, ['qingzongsynfailed', 'fetchfail', 'outreject', 'fetchcancel'])){
                    $data[$project->deptName]['secondFailNum']++;
                    if(!isset($data[$project->deptName]['secondFailNumInfo'][$info->code])){
                        $data[$project->deptName]['secondFailNumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => zget($this->lang->infoqz->statusList, $info->status, ''),
                        ];
                    }
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
        //查询清总数据获取--交付单
        $infoList = $this->dao
            ->select('*')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('outwarddelivery')
            ->andWhere('`after`')->in(['withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'qingzongsynfailed', 'cancel'])
            ->andWhere('extra')->eq('产品登记单')
            ->andWhere('createdDate')->ge($start)
            ->andWhere('createdDate')->le($end)
            ->andWhere('deleted')->eq('0')
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');

        $infoData = $this->dao->select('t1.id as id, t2.id as pid,t2.code,t2.status,t1.implementationForm,t1.projectPlanId,t2.returnTimes')->from(TABLE_OUTWARDDELIVERY)->alias('t1')
            ->leftjoin(TABLE_PRODUCTENROLL)->alias('t2')
            ->on('t1.productEnrollId=t2.id and t1.isNewProductEnroll = 1')
            ->where('t1.deleted')->eq(0)
            ->andwhere('t2.deleted')->eq(0)
            ->andWhere('t1.id')->in($infoIds)
            ->fetchAll('id');

        foreach ($infoList as $item){
            $info = $infoData[$item->objectID] ?? '';
            if(empty($info)) continue;

            $project = $projects[$info->projectPlanId];
            if(!isset($data[$project->deptName])){
                $keyArr = ['projectInfo', 'projectPassSumInfo', 'projectFailNumInfo', 'secondInfo', 'secondPassSumInfo', 'secondFailNumInfo'];
                $keys = array_keys($this->lang->sectransfer->exportFileds);
                $data[$project->deptName]['deptName'] = $project->deptName;
                foreach ($keys as $key){
                    if($key == 'deptName') continue;
                    $data[$project->deptName][$key] = in_array($key, $keyArr) ? [] : 0;
                }
            }

            if($info->implementationForm == 'project'){
                //交付次数
                if($item->after == 'withexternalapproval'){
                    $data[$project->deptName]['projectNum']++;
                    if(!isset($data[$project->deptName]['projectInfo'][$info->code])){
                        $data[$project->deptName]['projectInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => '产品登记单' . zget($this->lang->outwarddelivery->statusList, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['projectInfo'][$info->code]['num']++;
                }
                //通过次数
                if(in_array($item->after, $passStatus)){
                    if(isset($data[$project->deptName]['projectPassSumInfo'][$info->code])) continue;
                    $data[$project->deptName]['projectPassNum']++;
                    $returnTimes = empty($info->returnTimes) ? 1 : $info->returnTimes + 1;
                    if($returnTimes == 1){
                        $data[$project->deptName]['projectOne']++;
                    }elseif ($returnTimes == 2){
                        $data[$project->deptName]['projectTwo']++;
                    }else{
                        $data[$project->deptName]['projectThree']++;
                    }
                    $data[$project->deptName]['projectPassSum'] = $data[$project->deptName]['projectPassSum'] + $returnTimes;

                    $data[$project->deptName]['projectPassSumInfo'][$info->code] = [
                        'code' => $info->code,
                        'status' => '产品登记单' . zget($this->lang->outwarddelivery->statusList, $info->status, ''),
                        'num' => $returnTimes,
                    ];
                }
                //异常单数(时间范围内有异常记录的)
                if(in_array($item->after, $failStatus)){
                    $data[$project->deptName]['projectFailNum']++;
                    if(!isset($data[$project->deptName]['projectFailNumInfo'][$info->code])){
                        $data[$project->deptName]['projectFailNumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => '产品登记单' . zget($this->lang->outwarddelivery->statusList, $info->status, ''),
                        ];
                    }
                }
            }else{
                //交付次数
                if($item->after == 'withexternalapproval'){
                    $data[$project->deptName]['secondNum']++;
                    if(!isset($data[$project->deptName]['secondInfo'][$info->code])){
                        $data[$project->deptName]['secondInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => '产品登记单' . zget($this->lang->outwarddelivery->statusList, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['secondInfo'][$info->code]['num']++;
                }
                //通过次数
                if(in_array($item->after, $passStatus)){
                    if(isset($data[$project->deptName]['secondPassSumInfo'][$info->code])) continue;
                    $data[$project->deptName]['secondPassNum']++;
                    $returnTimes = empty($info->returnTimes) ? 1 : $info->returnTimes + 1;
                    if($returnTimes == 1){
                        $data[$project->deptName]['secondOne']++;
                    }elseif ($returnTimes == 2){
                        $data[$project->deptName]['secondTwo']++;
                    }else{
                        $data[$project->deptName]['secondThree']++;
                    }
                    $data[$project->deptName]['secondPassSum'] = $data[$project->deptName]['secondPassSum'] + $returnTimes;
                    $data[$project->deptName]['secondPassSumInfo'][$info->code] = [
                        'code' => $info->code,
                        'status' => '产品登记单' . zget($this->lang->outwarddelivery->statusList, $info->status, ''),
                        'num' => $returnTimes,
                    ];
                }
                //异常单数(时间范围内有异常记录的)
                if(in_array($item->after, $failStatus)){
                    $data[$project->deptName]['secondFailNum']++;
                    if(!isset($data[$project->deptName]['secondFailNumInfo'][$info->code])){
                        $data[$project->deptName]['secondFailNumInfo'][$info->code] = [
                            'code' => $info->code,
                            'status' => '产品登记单' . zget($this->lang->outwarddelivery->statusList, $info->status, ''),
                        ];
                    }
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
            ->fetchall();
        $infoIds = array_column((array)$infoList, 'objectID');

        //查询对外移交单-交付次数
        $infoDeliverList = $this->dao->select("*")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('sectransfer')
            ->andWhere('after')->eq('alreadyEdliver')
            ->andWhere('`before`')->ne('alreadyEdliver')
            ->andWhere('objectID')->in($infoIds)
            ->groupby('objectID')
            ->fetchpairs('id');

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
            t2.createdBy,
            t2.implementationForm,
            t2.internalProject as orderProject,
            t2.createdBy as orderCreatedBy')
            ->from(TABLE_SECTRANSFER)->alias('t1')
            ->leftjoin(TABLE_SECONDORDER)->alias('t2')
            ->on('t1.secondorderId=t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t1.id')->in($infoIds)
            ->fetchAll('id');

        $infoNum = $this->dao->select("objectID,count(objectID) as num")->from(TABLE_CONSUMED)
            ->where('deleted')->eq('0')
            ->andWhere('objectType')->eq('sectransfer')
            ->andWhere('after')->in(['alreadyEdliver', 'askCenterFailed', 'centerReject'])
            ->andWhere('`before`')->ne('alreadyEdliver')
            ->andWhere('objectID')->in($infoIds)
            ->groupby('objectID')
            ->fetchpairs();

        foreach ($infoList as $item){
            $info = $infoData[$item->objectID] ?? '';
            if(empty($info)) continue;
            if(empty($info->inproject) && empty($info->orderProject)) continue;
            if($info->jftype == 1) {
                $projectId = $projectPlans[$info->inproject];
                $project = $projects[$projectId];
            }else{
                $project = $projects[$info->orderProject];
            }

            if(!isset($data[$project->deptName])){
                $keyArr = ['projectInfo', 'projectPassSumInfo', 'projectFailNumInfo', 'secondInfo', 'secondPassSumInfo', 'secondFailNumInfo'];
                $keys = array_keys($this->lang->sectransfer->exportFileds);
                $data[$project->deptName]['deptName'] = $project->deptName;
                foreach ($keys as $key){
                    if($key == 'deptName') continue;
                    $data[$project->deptName][$key] = in_array($key, $keyArr) ? [] : 0;
                }
            }

            if ($info->jftype == 1 || ($info->jftype == 2 && $info->implementationForm == 'project')){
                //交付次数
                if($item->after == 'alreadyEdliver' && isset($infoDeliverList[$item->id])){
                    $data[$project->deptName]['projectNum']++;
                    if(!isset($data[$project->deptName]['projectInfo'][$info->id])){
                        $data[$project->deptName]['projectInfo'][$info->id] = [
                            'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                            'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['projectInfo'][$info->id]['num']++;
                }
                //通过次数
                //如果移交方式为项目移交，并且外部接收方为清算总中心，通过状态流转前后都是已交付状态
                if(
                    ($info->jftype == 1 && $info->externalRecipient == 2)
                    || ($info->jftype == 2 && in_array($info->orderCreatedBy, ['guestcn','guestjx']))
                ){
                    if($item->before == 'alreadyEdliver' && $item->after == 'alreadyEdliver'){
                        $data[$project->deptName]['projectPassNum']++;
                        if($infoNum[$info->id] == 1){
                            $data[$project->deptName]['projectOne']++;
                        }elseif ($infoNum[$info->id] == 2){
                            $data[$project->deptName]['projectTwo']++;
                        }else{
                            $data[$project->deptName]['projectThree']++;
                        }
                        $data[$project->deptName]['projectPassSum'] = $data[$project->deptName]['projectPassSum'] + $infoNum[$info->id];

                        if(!isset($data[$project->deptName]['projectPassSumInfo'][$info->id])){
                            $data[$project->deptName]['projectPassSumInfo'][$info->id] = [
                                'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                                'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                                'num' => $infoNum[$info->id],
                            ];
                        }
                    }
                }else{
                    if($item->after == 'alreadyEdliver'){
                        $data[$project->deptName]['projectPassNum']++;
                        if($infoNum[$info->id] == 1){
                            $data[$project->deptName]['projectOne']++;
                        }elseif ($infoNum[$info->id] == 2){
                            $data[$project->deptName]['projectTwo']++;
                        }else{
                            $data[$project->deptName]['projectThree']++;
                        }
                        $data[$project->deptName]['projectPassSum'] = $data[$project->deptName]['projectPassSum'] + $infoNum[$info->id];

                        if(!isset($data[$project->deptName]['projectPassSumInfo'][$info->id])){
                            $data[$project->deptName]['projectPassSumInfo'][$info->id] = [
                                'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                                'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                                'num' => $infoNum[$info->id],
                            ];
                        }
                    }
                }
                //异常单数(时间范围内有异常记录的)
                if(in_array($item->after, $failStatus)){
                    $data[$project->deptName]['projectFailNum']++;
                    if(!isset($data[$project->deptName]['projectFailNumInfo'][$info->id])){
                        $data[$project->deptName]['projectFailNumInfo'][$info->id] = [
                            'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                            'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                        ];
                    }
                }
            }elseif($info->jftype == 2 && $info->implementationForm == 'second'){
                //交付次数
                if($item->after == 'alreadyEdliver' && isset($infoDeliverList[$item->id])){
                    $data[$project->deptName]['secondNum']++;
                    if(!isset($data[$project->deptName]['secondInfo'][$info->id])){
                        $data[$project->deptName]['secondInfo'][$info->id] = [
                            'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                            'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                            'num' => 0,
                        ];
                    }
                    $data[$project->deptName]['secondInfo'][$info->id]['num']++;
                }
                //通过次数
                //如果移交方式为项目移交，并且外部接收方为清算总中心，通过状态流转前后都是已交付状态
                if(
                    ($info->jftype == 1 && $info->externalRecipient == 2)
                    || ($info->jftype == 2 && in_array($info->orderCreatedBy, ['guestcn','guestjx']))
                ){
                    if($item->before == 'alreadyEdliver' && $item->after == 'alreadyEdliver'){
                        $data[$project->deptName]['secondPassNum']++;
                        if($infoNum[$info->id] == 1){
                            $data[$project->deptName]['secondOne']++;
                        }elseif ($infoNum[$info->id] == 2){
                            $data[$project->deptName]['secondTwo']++;
                        }else{
                            $data[$project->deptName]['secondThree']++;
                        }
                        $data[$project->deptName]['secondPassSum'] = $data[$project->deptName]['secondPassSum'] + $infoNum[$info->id];

                        if(!isset($data[$project->deptName]['secondPassSumInfo'][$info->id])){
                            $data[$project->deptName]['secondPassSumInfo'][$info->id] = [
                                'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                                'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                                'num' => $infoNum[$info->id],
                            ];
                        }
                    }
                }else{
                    if($item->after == 'alreadyEdliver'){
                        $data[$project->deptName]['secondPassNum']++;
                        if($infoNum[$info->id] == 1){
                            $data[$project->deptName]['secondOne']++;
                        }elseif ($infoNum[$info->id] == 2){
                            $data[$project->deptName]['secondTwo']++;
                        }else{
                            $data[$project->deptName]['secondThree']++;
                        }
                        $data[$project->deptName]['secondPassSum'] = $data[$project->deptName]['secondPassSum'] + $infoNum[$info->id];

                        if(!isset($data[$project->deptName]['secondPassSumInfo'][$info->id])){
                            $data[$project->deptName]['secondPassSumInfo'][$info->id] = [
                                'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                                'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                                'num' => $infoNum[$info->id],
                            ];
                        }
                    }
                }
                //异常单数(时间范围内有异常记录的)
                if(in_array($item->after, $failStatus)){
                    $data[$project->deptName]['secondFailNum']++;
                    if(!isset($data[$project->deptName]['secondFailNumInfo'][$info->id])){
                        $data[$project->deptName]['secondFailNumInfo'][$info->id] = [
                            'code' => $info->id . '-' . zget($this->lang->application->teamList, $info->department,'空'),
                            'status' => zget($this->lang->sectransfer->statusListName, $info->status, ''),
                        ];
                    }
                }
            }
        }

        return $data;
    }
}
