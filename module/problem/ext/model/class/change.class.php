<?php

class changeProblem extends problemModel
{
    public function changeBySecondLineV3()
    {
        //如果时间限制开关打开，联动创建时间在一年以内的数据
        $flag = isset($this->config->statusYearSwitch) && $this->config->statusYearSwitch == 1;
        $this->loadModel('review');
        //获取所有有效的问题单
        $problems = $this->dao->select('id, status, code, actualOnlineDate, dealUser, solvedTime')
            ->from(TABLE_PROBLEM)
            ->where('status')
            ->notIN($this->lang->problem->statusArr['problemNotIn']) //根据迭代25要求 待分配、待分析、待关闭、已挂起、已关闭、已退回不做联动
            ->beginIF($flag)
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime('-1 year')))
            ->fi()
            ->andWhere('secureStatusLinkage')->eq('0')
            ->fetchAll('id');

        $problemIds       = array_keys($problems); //取所有id
        $feedbackedIdList = []; //开发中
        $testingIdList    = []; //测试中
        $releaseIdList    = []; //已发布
        $deliveryIdList   = []; //已交付
        $onlineIdList     = []; //已上线
        $onlineTimeList   = []; //上线时间
        $exceptionIdList  = []; //变更异常
        $emptyIdList      = []; //交付时间置空
        $updateIdList     = []; //交付时间修改
        foreach ($problemIds as $problemId) {
            //如果是待关闭或已关闭状态调用更新交付时间方法
            if(in_array($problems[$problemId]->status, ['toclose', 'closed'])){
                $updateIdList[] = $problemId;
                continue;
            }

            $statusList[$problemId]['feedbacked']    = 0;  //开发中 关联数量
            $statusList[$problemId]['releaseSecond'] = 0;  //已发布 关联数量
            $statusList[$problemId]['releaseBuild']  = 0;  //已发布 关联数量
            $statusList[$problemId]['delivery']      = 0;  //已交付 关联数量
            $statusList[$problemId]['online']        = 0;  //已上线 关联数量
            $statusList[$problemId]['testing']       = 0;  //测试中 关联数量
            $statusList[$problemId]['exception']     = 0;  //变更异常 关联数量

            //region 二线关联状态联动
            //取本单所有的二线关联
            $relations = $this->dao->select('relationID as last_relation_id, relationType')
                ->from(TABLE_SECONDLINE)
                ->where('objectType')->eq('problem')
                ->andwhere('objectID')->eq($problemId)
                ->andwhere('deleted')->eq(0)
                ->andwhere('relationType')->in($this->lang->problem->statusArr['relationType'])
                ->orderBY('id_asc')
                ->fetchAll();
            foreach ($relations as $relation) {
                if (empty($relation)) {
                    continue;
                }
                //region 二线金信
                if (in_array($relation->relationType, ['gain'])) { //如果是金信数据获取
                    $info = $this->dao->select('id, `status`, actualEnd, reviewStage, version,code')
                        ->from(TABLE_INFO)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andwhere('action')->in('gain')
                        ->andwhere('status')->notIN('closed,deleted') //已关闭 已删除不做联动
                        ->andWhere('problemCancelLinkage')->eq(0)//数据获取解除状态联动后不做联动
                        ->fetch();
                    if (empty($info)) {
                        continue;
                    }

                    //联动为已发布
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoGainReleased'])) {
                        ++$statusList[$problemId]['releaseSecond'];
                        $releaseIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为已交付
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoGainDelivery'])) {
                        ++$statusList[$problemId]['delivery'];
                        $deliveryIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为上线成功
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoGainOnlineSuccess'])) {
                        ++$statusList[$problemId]['online'];
                        $onlineIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                        $actualEnd                              = substr($info->actualEnd, 0, 10);
                        if (empty($onlineTimeList[$problemId]) || $actualEnd > $onlineTimeList[$problemId]) {
                            $onlineTimeList[$problemId] = $actualEnd;
                        }
                    }
                    //联动为变更异常
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoGainException'])) {
                        ++$statusList[$problemId]['exception'];
                        $exceptionIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                } elseif ('modify' == $relation->relationType) { //如果是金信生产变更
                    $info = $this->dao->select('id,code,status, actualEnd, realEndTime, dealUser, abnormalCode')
                        ->from(TABLE_MODIFY)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andWhere('abnormalCode')->eq('')
                        ->andWhere('status')->notIN('waitsubmitted,deleted,modifycancel') //待提交、变更取消 已删除不做联动
                        ->andWhere('problemCancelLinkage')->eq(0)//生产变更单解除状态联动后不做联动
                        ->fetch();
                    if (empty($info)) {
                        continue;
                    }

                    //联动为已发布
                    if (in_array($info->status, $this->lang->problem->statusLinkage['modifyReleased'])) {
                        ++$statusList[$problemId]['releaseSecond'];
                        $releaseIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为已交付
                    if (in_array($info->status, $this->lang->problem->statusLinkage['modifyDelivery'])) {
                        ++$statusList[$problemId]['delivery'];
                        $deliveryIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为上线成功
                    if (in_array($info->status, $this->lang->problem->statusLinkage['modifyOnlineSuccess'])) {
                        ++$statusList[$problemId]['online'];
                        $onlineIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                        $realEndTime                            = substr($info->realEndTime, 0, 10);
                        if (empty($onlineTimeList[$problemId]) || $realEndTime > $onlineTimeList[$problemId]) {
                            $onlineTimeList[$problemId] = $realEndTime;
                        }
                    }
                    //联动为变更异常
                    if (in_array($info->status, $this->lang->problem->statusLinkage['modifyException']) && empty($info->abnormalCode)) {
                        ++$statusList[$problemId]['exception'];
                        $exceptionIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                } elseif ('gainQz' == $relation->relationType) {  //清总数据获取
                    $info = $this->dao->select('id, code,`status`, externalStatus, actualEnd, version, reviewStage')
                        ->from(TABLE_INFO_QZ)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andwhere('action')->eq('gain')
                        ->andwhere('status')->notIN('closed,fetchclose,deleted,fetchcancel') //已关闭 数据获取关闭 已删除 数据获取取消 不做联动
                        ->andWhere('problemCancelLinkage')->eq(0)//数据获取解除状态联动后不做联动
                        ->fetch();
                    if (empty($info)) {
                        continue;
                    }

                    //联动为已发布
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoQzReleased'])) {
                        ++$statusList[$problemId]['releaseSecond'];
                        $releaseIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为已交付
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoQzDelivery'])) {
                        ++$statusList[$problemId]['delivery'];
                        $deliveryIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为上线成功
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoQzOnlineSuccess'])) {
                        ++$statusList[$problemId]['online'];
                        $onlineIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                        $actualEnd                              = substr($info->actualEnd, 0, 10);
                        if (empty($onlineTimeList[$problemId]) || $actualEnd > $onlineTimeList[$problemId]) {
                            $onlineTimeList[$problemId] = $actualEnd;
                        }
                    }
                    //联动为变更异常
                    if (in_array($info->status, $this->lang->problem->statusLinkage['infoQzException'] )) {
                        ++$statusList[$problemId]['exception'];
                        $exceptionIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                } elseif ('outwarddelivery' == strtolower($relation->relationType)) { //清总对外交付
                    $info = $this->dao->select('id,code,status,closed,productEnrollId,testingRequestId,modifycnccId,dealUser,abnormalCode')
                        ->from(TABLE_OUTWARDDELIVERY)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andWhere('abnormalCode')->eq('')
                        ->andWhere('status')->notIN('waitsubmitted,closed,modifycancel') //待提交 已关闭 变更取消不做联动
                        ->andWhere('deleted')->eq(0)
                        ->andWhere('problemCancelLinkage')->eq(0)
                        ->fetch();
                    if (empty($info) || $info->closed) {
                        continue;
                    }

                    if ($info->modifycnccId > 0) { //对外交付只处理生产变更
                        //联动为已发布
                        if (in_array($info->status, $this->lang->problem->statusLinkage['modifycnccReleased'])) {
                            ++$statusList[$problemId]['releaseSecond'];
                            $releaseIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                        }
                        //联动为已交付
                        if (in_array($info->status, $this->lang->problem->statusLinkage['modifycnccDelivery'])) {
                            ++$statusList[$problemId]['delivery'];
                            $deliveryIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                        }
                        //联动为上线成功
                        if (in_array($info->status, $this->lang->problem->statusLinkage['modifycnccOnlineSuccess'])) {
                            ++$statusList[$problemId]['online'];
                            $dealUserList[$problemId]['online']     = '';
                            $onlineIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                            $lastDealDate                           = $this->dao->select('actualEnd')->from(TABLE_MODIFYCNCC)->where('id')->eq($info->modifycnccId)->fetch('actualEnd');
                            if (empty($onlineTimeList[$problemId]) || $lastDealDate > $onlineTimeList[$problemId]) {
                                $onlineTimeList[$problemId] = $lastDealDate;
                            }
                        }
                        //联动为变更异常
                        if (in_array($info->status, $this->lang->problem->statusLinkage['modifycnccException']) && empty($info->abnormalCode)) {
                            ++$statusList[$problemId]['exception'];
                            $exceptionIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                        }
                    }
                } elseif ('credit' == $relation->relationType) { //征信交付
                    $info = $this->dao->select('id, code , status , actualEndTime,onlineTime, dealUsers, abnormalId')
                        ->from(TABLE_CREDIT)
                        ->where('id')->eq($relation->last_relation_id)
                        ->andWhere('status')->notIN('waitsubmit,cancel') //待提交、变更取消 已删除不做联动
                        ->andWhere('deleted')->eq(0)//生产变更单解除状态联动后不做联动
                        ->andWhere('abnormalId')->eq(0)
                        ->andWhere('demandCancelLinkage')->eq(0)
                        ->fetch();
                    if (empty($info)) {
                        continue;
                    }

                    //联动为已发布
                    if (in_array($info->status, $this->lang->problem->statusLinkage['creditReleased'])) {
                        ++$statusList[$problemId]['releaseSecond'];
                        $releaseIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为已交付
                    if (in_array($info->status, $this->lang->problem->statusLinkage['creditDelivery'])) {
                        ++$statusList[$problemId]['delivery'];
                        $deliveryIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                    //联动为上线成功
                    if (in_array($info->status, $this->lang->problem->statusLinkage['creditOnlineSuccess'])) {
                        ++$statusList[$problemId]['online'];
                        $onlineIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                        $realEndTime                            = substr($info->onlineTime, 0, 10);
                        if (empty($onlineTimeList[$problemId]) || $realEndTime > $onlineTimeList[$problemId]) {
                            $onlineTimeList[$problemId] = $realEndTime;
                        }
                    }
                    //联动为变更异常
                    if (in_array($info->status, $this->lang->problem->statusLinkage['creditException'])) {
                        ++$statusList[$problemId]['exception'];
                        $exceptionIdList[$problemId]['codeSecond'] = $info->code . "({$info->id})";
                    }
                }
            }

            //region 关联制版
            //取所有关联的任务制版
            $builds = $this->dao->select('t.id,t.`name`,t.`status`,t.dealuser')
                ->from(TABLE_BUILD)->alias('t')
                ->where('t.problemid')->like("%{$problems[$problemId]->code}%")
                ->andwhere("((t.`status` != 'wait' and t.dealuser !='') or t.status ='released' )")
                ->andWhere("id in(select max(id) from zt_build where project = t.project and app = t.app and product = t.product and  problemid like '%{$problems[$problemId]->code}%' and  deleted = '0' and ((`status` != 'wait' and dealuser != '') or status = 'released' ) group by app,product,`version`)")
                ->andwhere('t.deleted')->eq(0)
                ->fetchAll('id');
            if ($builds) {
                foreach ($builds as $build) {
                    if ('released' == $build->status) {
                        ++$statusList[$problemId]['releaseBuild'];
                        $releaseIdList[$problemId]['code'][] = $build->name . "({$build->id})";
                    } else {
                        ++$statusList[$problemId]['testing'];
                        $testingIdList[$problemId]['code'][] = $build->name . "({$build->id})";
                    }
                }
            } else {
                ++$statusList[$problemId]['feedbacked'];
                $feedbackedIdList[$problemId]['code'][] = $this->lang->problem->noBuildAndSecond;
            }
            //endregion
            //region 核心联动逻辑
            //重点：如果制版和二线同时存在 ,制版不是已发布 ，则取制版状态。反之，取二线状态  状态联动最小原则 ： 开发中 ->测试中 ->已发布 ->已交付 ->上线成功
            if (
                ($statusList[$problemId]['releaseBuild'] || $statusList[$problemId]['testing'])
                && ($statusList[$problemId]['releaseSecond']
                    || $statusList[$problemId]['delivery']
                    || $statusList[$problemId]['online']
                    || $statusList[$problemId]['exception'])
            ) {//制版和二线同时存在
                //如果制版 有测试中的 状态就是测试(build)
                if ($statusList[$problemId]['testing']) {
                    //判断交付时间是否需要置空
                    if(!empty($problems[$problemId]->solvedTime) && strpos($problems[$problemId]->solvedTime, '0000') === false){
                        $emptyIdList[] = $problemId;
                    }

                    if ('build' != $problems[$problemId]->status) { //如果状态不是测试中
                        //为备注创建
                        $testingIdList = $this->createToArr($problemId, $testingIdList, $problems[$problemId]->status, 'build');
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('build')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'build');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果二线中有变更异常状态为变更异常
                if($statusList[$problemId]['exception']){
                    //更新交付时间
                    $updateIdList[] = $problemId;
                    if ('exception' != $problems[$problemId]->status) { //如果状态不是测试中
                        //为备注创建
                        $exceptionIdList = $this->createToArr($problemId, $exceptionIdList, $problems[$problemId]->status, 'exception');
                        $exceptionIdList[$problemId]['code'][] = $exceptionIdList[$problemId]['codeSecond'] ?? '';
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('exception')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'exception');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果有制版发布 && 有二线已发布.则问题单状态为 二线已发布
                if ($statusList[$problemId]['releaseBuild'] && $statusList[$problemId]['releaseSecond']) {
                    //判断交付时间是否需要置空
                    if(!empty($problems[$problemId]->solvedTime) && strpos($problems[$problemId]->solvedTime, '0000') === false){
                        $emptyIdList[] = $problemId;
                    }

                    if ('released' != $problems[$problemId]->status) {
                        unset($releaseIdList[$problemId]['code']);
                        //为备注创建
                        $releaseIdList                       = $this->createToArr($problemId, $releaseIdList, $problems[$problemId]->status, 'released');
                        $releaseIdList[$problemId]['code'][] = $releaseIdList[$problemId]['codeSecond'] ?? '';
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'released');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.有二线已交付 则问题单状态为 已交付
                if ($statusList[$problemId]['releaseBuild'] && !$statusList[$problemId]['releaseSecond'] && $statusList[$problemId]['delivery']) {
                    //更新交付时间
                    $updateIdList[] = $problemId;

                    if (count($builds) == $statusList[$problemId]['releaseBuild'] && 0 != count($builds) && 'delivery' != $problems[$problemId]->status) { //如果状态不是已交付
                        //为备注创建
                        $deliveryIdList                       = $this->createToArr($problemId, $deliveryIdList, $problems[$problemId]->status, 'delivery');
                        $deliveryIdList[$problemId]['code'][] = $deliveryIdList[$problemId]['codeSecond'] ?? '';
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.有二线上线成功 则问题单状态为 上线成功
                if ($statusList[$problemId]['releaseBuild'] && !$statusList[$problemId]['releaseSecond'] && $statusList[$problemId]['online']) {
                    $updateIdList[] = $problemId;

                    if (count($builds) == $statusList[$problemId]['releaseBuild']
                        && 0 != count($builds)
                        && 'onlinesuccess' != $problems[$problemId]->status) { //如果状态不是上线成功
                        //为备注创建
                        $onlineIdList                       = $this->createToArr($problemId, $onlineIdList, $problems[$problemId]->status, 'onlinesuccess');
                        $onlineIdList[$problemId]['code'][] = $onlineIdList[$problemId]['codeSecond'] ?? '';
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinesuccess')->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    //更新上线时间
                    if (!empty($onlineTimeList[$problemId]) && $onlineTimeList[$problemId] > $problems[$problemId]->actualOnlineDate) {
                        $this->dao->update(TABLE_PROBLEM)->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                }
            } elseif (
                ($statusList[$problemId]['releaseBuild'] || $statusList[$problemId]['testing'])
                && (0 == $statusList[$problemId]['releaseSecond']
                    && 0 == $statusList[$problemId]['delivery']
                    && 0 == $statusList[$problemId]['online']
                    && 0 == $statusList[$problemId]['exception'])
            ) { //只有制版
                //如果制版 有测试中的 状态就是测试(build)
                if ($statusList[$problemId]['testing']) {
                    //判断交付时间是否需要置空
                    if(!empty($problems[$problemId]->solvedTime) && strpos($problems[$problemId]->solvedTime, '0000') === false){
                        $emptyIdList[] = $problemId;
                    }

                    if ('build' != $problems[$problemId]->status) { //如果状态不是测试中
                        //为备注创建
                        $testingIdList = $this->createToArr($problemId, $testingIdList, $problems[$problemId]->status, 'build');
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('build')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'build');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果有发布,且所有制版状态都是已发布 &&没有二线已发布.则问题单状态为 已发布
                if ($statusList[$problemId]['releaseBuild']) {
                    //判断交付时间是否需要置空
                    if(!empty($problems[$problemId]->solvedTime) && strpos($problems[$problemId]->solvedTime, '0000') === false){
                        $emptyIdList[] = $problemId;
                    }

                    if (count($builds) == $statusList[$problemId]['releaseBuild'] && 0 != count($builds) && 'released' != $problems[$problemId]->status) { //如果状态不是已发布
                        //为备注创建
                        $releaseIdList = $this->createToArr($problemId, $releaseIdList, $problems[$problemId]->status, 'released');
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'released');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                }
            } elseif (
                ($statusList[$problemId]['releaseSecond']
                    || $statusList[$problemId]['delivery']
                    || $statusList[$problemId]['online']
                    || $statusList[$problemId]['exception'])
                && (0 == $statusList[$problemId]['releaseBuild'] && 0 == $statusList[$problemId]['testing'])
            ) {//只有二线
                if (isset($feedbackedIdList[$problemId]) && !isset($feedbackedIdList[$problemId]['status'])) {
                    unset($feedbackedIdList[$problemId]);
                }
                //如果二线中有变更异常状态为变更异常
                if($statusList[$problemId]['exception']){
                    $updateIdList[] = $problemId;

                    if ('exception' != $problems[$problemId]->status) { //如果状态不是变更异常
                        //为备注创建
                        $exceptionIdList = $this->createToArr($problemId, $exceptionIdList, $problems[$problemId]->status, 'exception');
                        $exceptionIdList[$problemId]['code'][] = $exceptionIdList[$problemId]['codeSecond'] ?? '';
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('exception')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'exception');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果有二线已发布.则问题单状态为 二线已发布
                if ($statusList[$problemId]['releaseSecond']) {
                    //判断交付时间是否需要置空
                    if(!empty($problems[$problemId]->solvedTime) && strpos($problems[$problemId]->solvedTime, '0000') === false){
                        $emptyIdList[] = $problemId;
                    }

                    if ('released' != $problems[$problemId]->status) {
                        if (isset($releaseIdList[$problemId]['code'])) {
                            unset($releaseIdList[$problemId]['code']);
                        }
                        //为备注创建
                        $releaseIdList                       = $this->createToArr($problemId, $releaseIdList, $problems[$problemId]->status, 'released');
                        $releaseIdList[$problemId]['code'][] = $releaseIdList[$problemId]['codeSecond'] ?? '';
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'released');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果当前状态不是已交付 && 无其他关联状态 改为已交付状态
                if ($statusList[$problemId]['delivery']) {
                    $updateIdList[] = $problemId;

                    if ('delivery' != $problems[$problemId]->status) { //如果状态改变
                        //为备注创建
                        $deliveryIdList                       = $this->createToArr($problemId, $deliveryIdList, $problems[$problemId]->status, 'delivery');
                        $deliveryIdList[$problemId]['code'][] = $deliveryIdList[$problemId]['codeSecond'] ?? '';
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'delivery');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                    continue;
                }
                //如果其他都没有只有上线成功 状态改为上线成功
                if ($statusList[$problemId]['online']) {
                    $updateIdList[] = $problemId;

                    if ('onlinesuccess' != $problems[$problemId]->status) {
                        //为备注创建
                        $onlineIdList                       = $this->createToArr($problemId, $onlineIdList, $problems[$problemId]->status, 'onlinesuccess');
                        $onlineIdList[$problemId]['code'][] = $onlineIdList[$problemId]['codeSecond'] ?? '';
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('onlinesuccess')->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'onlinesuccess');
                    }
                    //更新上线时间
                    if (!empty($onlineTimeList[$problemId]) && $onlineTimeList[$problemId] > $problems[$problemId]->actualOnlineDate) {
                        $this->dao->update(TABLE_PROBLEM)->set('actualOnlineDate')->eq($onlineTimeList[$problemId])->where('id')->eq($problemId)->exec();
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                }
            } else {
                //制版和二线都不存在
                //如果没有制版，问题单状态更新为开发中（迭代25）
                if ($statusList[$problemId]['feedbacked']) {
                    //判断交付时间是否需要置空
                    if(!empty($problems[$problemId]->solvedTime) && strpos($problems[$problemId]->solvedTime, '0000') === false){
                        $emptyIdList[] = $problemId;
                    }

                    if ('feedbacked' != $problems[$problemId]->status) {
                        //为备注创建
                        $feedbackedIdList = $this->createToArr($problemId, $feedbackedIdList, $problems[$problemId]->status, 'feedbacked');
                        $this->dao->update(TABLE_PROBLEM)->set('status')->eq('feedbacked')->set('actualOnlineDate')->eq(null)->where('id')->eq($problemId)->exec();
                        $this->loadModel('consumed')->recordAuto('problem', $problemId, 0, $problems[$problemId]->status, 'feedbacked');
                    }
                    //清除不满足数组条件的
                    $clearArr         = $this->newClearToArr($onlineIdList, $deliveryIdList, $releaseIdList, $testingIdList, $feedbackedIdList, $exceptionIdList, $problemId);
                    $onlineIdList     = $clearArr['online'];
                    $deliveryIdList   = $clearArr['delivery'];
                    $releaseIdList    = $clearArr['release'];
                    $testingIdList    = $clearArr['testing'];
                    $feedbackedIdList = $clearArr['feedbacked'];
                    $exceptionIdList  = $clearArr['exception'];
                }
            }
        }
        //开发中
        if ($feedbackedIdList) {
            $this->loadModel('action')->createActions('problem', $feedbackedIdList, 'feedback', $this->lang->problem->nowConsumedstatusList);
        }
        //测试中
        if ($testingIdList) {
            $this->loadModel('action')->createActions('problem', $testingIdList, 'build', $this->lang->problem->nowConsumedstatusList);
        }
        //已交付
        if ($deliveryIdList) {
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('delivery')->set('actualOnlineDate')->eq(null)->where('id')->in(array_filter(array_unique(array_keys($deliveryIdList))))->exec();
            $this->loadModel('action')->createActions('problem', $deliveryIdList, 'delivery', $this->lang->problem->nowConsumedstatusList);
        }
        //上线成功
        if ($onlineIdList) { //需要处理每个上线时间 不能统一执行
            $this->loadModel('action')->createActions('problem', $onlineIdList, 'onlinesuccess', $this->lang->problem->nowConsumedstatusList);
        }
        //已发布统一处理
        if ($releaseIdList) {
            $this->dao->update(TABLE_PROBLEM)->set('status')->eq('released')->set('actualOnlineDate')->eq(null)->where('id')->in(array_filter(array_unique(array_keys($releaseIdList))))->exec();
            $this->loadModel('action')->createActions('problem', $releaseIdList, 'released', $this->lang->problem->nowConsumedstatusList);
        }
        //变更异常
        if ($exceptionIdList) {
            $this->loadModel('action')->createActions('problem', $exceptionIdList, 'exception', $this->lang->problem->nowConsumedstatusList);
        }

        $this->editSolvedTime($emptyIdList, $updateIdList);

        return [
            'problem onlinesuccess' => $onlineIdList,
            'problem delivery'      => $deliveryIdList,
            'problem released'      => $releaseIdList,
            'problem testing'       => $testingIdList,
            'problem feedback'      => $feedbackedIdList,
            'problem exception'     => $exceptionIdList,
        ];
    }
    /**
     * 清除不满足要求数组
     * @param $onlineIdList   上线成功
     * @param $deliveryIdList 已交付
     * @param $releaseIdList  已发布
     * @param $testingIdList  测试中
     * @param $feedbackedIdList 开发中
     * @param $exceptionIdList 变更异常
     * @param $problemID
     */
    public function newClearToArr($onlineIdList,$deliveryIdList,$releaseIdList,$testingIdList,$feedbackedIdList, $exceptionIdList, $problemID){
        if(isset($onlineIdList[$problemID]) && !isset($onlineIdList[$problemID]['status'])){
            unset($onlineIdList[$problemID]);
        }
        if(isset($deliveryIdList[$problemID]) && !isset($deliveryIdList[$problemID]['status'])){
            unset($deliveryIdList[$problemID]);
        }
        if(isset($releaseIdList[$problemID]) && !isset($releaseIdList[$problemID]['status'])){
            unset($releaseIdList[$problemID]);
        }
        if(isset($testingIdList[$problemID]) && !isset($testingIdList[$problemID]['status'])){
            unset($testingIdList[$problemID]);
        }
        if(isset($feedbackedIdList[$problemID]) && !isset($feedbackedIdList[$problemID]['status'])){
            unset($feedbackedIdList[$problemID]);
        }
        if(isset($exceptionIdList[$problemID]) && !isset($exceptionIdList[$problemID]['status'])){
            unset($exceptionIdList[$problemID]);
        }
        return array(
            'online' => $onlineIdList,
            'delivery' =>$deliveryIdList,
            'release' =>$releaseIdList,
            'testing' =>$testingIdList,
            'feedbacked' =>$feedbackedIdList,
            'exception' =>$exceptionIdList,
        );
    }

    public function editSolvedTime($emptyIds, $updateIds)
    {
        if(!empty($emptyIds)){
            $this->dao->update(TABLE_PROBLEM)->set('solvedTime')->eq('')->where('id')->in($emptyIds)->exec();
            foreach ($emptyIds as $emptyId){
                $this->loadModel('action')->create('problem', $emptyId, 'updatesolvetime', $this->lang->problem->solveTimeEmptyTip,'','guestjk');
            }
        }

        if(!empty($updateIds)){
            foreach ($updateIds as $updateId){
                $this->getAllSecondSolveTime($updateId,'problem');
            }
        }


    }
}
