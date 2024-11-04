<?php
/**
 * Created by PhpStorm.
 * User: t_xiangyang
 * Date: 2023/1/16
 * Time: 17:00
 */
include '../../control.php';
class myReport extends report
{
    public function refreshReport($method = '', $projectID = ''){
        $this->loadModel('review');

        // 查询所有用户真实姓名
        $accounts = $this->dao->select('account,realname')->from(TABLE_USER)->fetchPairs();

        $reviewStages = [];$needUpdateList = [];$idStr = '(';$deleteSql = '';$lastDate = '';$lastReviewId = '';$reviewStagesCost = [];$lastId = '';$lastStage = ''; $i = 0;
        // 查表中最新数据(若有说明非第一次插入数据)
        $maxInsertTime    = $this->dao->select('insertTime')->from(TABLE_FLOWWORKLOAD)->orderBy('id_desc')->fetch();

        // 查询需要新增的评审数据(包括需要修改的)
        if(!empty($maxInsertTime->insertTime)){
            $reviewUpdateList       = $this->report->getNewReviews($maxInsertTime->insertTime);
            // 无新数据 直接返回成功提醒
            if(empty($reviewUpdateList)){
                $response['result']  = 'success';
                $response['message'] = '刷新报表数据成功';
                $response['locate']  = 'report-'.$method.'-' . $projectID .'.html#app=project';
                $this->sendBack($response);
            }
            foreach($reviewUpdateList as $reviewID){
                $needUpdateList[] =  $reviewID->id;
                $idStr            .= $reviewID->id.',';
            }
            $idStr = substr($idStr,'0','-1');
            $deleteSql = "UPDATE " . TABLE_FLOWWORKLOAD ." SET deleted = 1 WHERE reviewID IN ". $idStr. ");";
            $deleteSql .= "UPDATE " . TABLE_FLOWCOSTWORKLOAD ." SET deleted = 1 WHERE reviewID IN ". $idStr. ");";
            $deleteSql .= "UPDATE " . TABLE_PARTICIPANTSWORKLOAD ." SET deleted = 1 WHERE reviewID IN ". $idStr. ");";
        }
        $list             = empty($maxInsertTime->insertTime)?'':$needUpdateList;
        $reviewList       = $this->report->getReviewInfoByProjectId($list);
        $reviews          = $this->report->getReviewLists($list);
        foreach($reviews as $stage){
            if($lastId != $stage->objectID){
                $i = 0;//说明是新评审
            }
            if($lastId == $stage->objectID && $lastStage == 'preReviewBefore' && $stage->reviewStage != 'preReviewBefore'){
                $i ++;//再一次回到预审前了
            }
            // 预审前工作量累计只取首次提交(再一次回到预审前的数据不累计)
            if(!($lastId == $stage->objectID && $lastStage != 'preReviewBefore' && $stage->reviewStage == 'preReviewBefore')){
                // (再一次回到预审前之后的预审前数据不累计)
                if($stage->reviewStage == 'preReviewBefore'){
                    if(empty($i)){
                        $reviewStages[$stage->objectID][$stage->reviewStage] += $stage->consumed;
                    }
                }else{
                    $reviewStages[$stage->objectID][$stage->reviewStage] += $stage->consumed;
                }
            }
            $lastId        = $stage->objectID;
            $lastStage     = $stage->reviewStage;
        }
        $reviewCost       = $this->report->getReviewStages($list, $begin = '', $end = '');
        foreach($reviewCost as $data){
            if($lastReviewId == $data->objectID){
                $reviewStagesCost[$data->objectID][$data->reviewStage] += $this->loadModel('holiday')->getTimeBetween($lastDate, $data->createdDate, $sec = 'sec');
            }
            $lastReviewId   = $data->objectID;
            $lastDate       = $data->createdDate;
        }
        $reviewConsumedList       = $this->report->getAllReviewConsumedList($list);
        $projectHours = $this->dao->select('id,workHours')->from(TABLE_PROJECT)->fetchpairs();

        // 获取所有申请时间和基线完成时间
        $firstPreReviewDates = $this->report->getNodeCreatedDate($list, 'waitPreReview');
        $baselineDates       = $this->report->getNodeCreatedDate($list, 'reviewpass','before','baseline');
        $onLineExperts       = $this->report->getMember($list,$nodeCode = 'formalReview');
        $verifiers           = $this->report->getMember($list,$nodeCode = 'verify');
        $stageList           = $this->lang->review->reviewStageList;
        $stageStr            = implode(',',$stageList);

        $sessionsql = "set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'; ";
        $sql         = "insert into " . TABLE_FLOWWORKLOAD . " (projectID,projectName,projectMark,projectCode,createdBy,createdDept,reviewID,reviewName,status,`type`,trialDept,trialDeptLiasisonOfficer,trialAdjudicatingOfficer,trialJoinOfficer,owner,qa,qualityCm,onLineExpert,realExpert,verifier,createdDate,firstPreReviewDate,closeTime,baselineDate,suspendTime,renewTime,insertTime,".$stageStr.") values ";
        $sqlCost     = "insert into " . TABLE_FLOWCOSTWORKLOAD . " (projectID,projectName,projectMark,projectCode,createdBy,createdDept,reviewID,reviewName,status,`type`,trialDept,trialDeptLiasisonOfficer,trialAdjudicatingOfficer,trialJoinOfficer,owner,qa,qualityCm,OnLineExpert,realExpert,verifier,createdDate,firstPreReviewDate,closeTime,baselineDate,suspendTime,renewTime,insertTime,reviewDays,preReviewDays,".$stageStr.") values ";
        $sqlConsumed = "insert into " . TABLE_PARTICIPANTSWORKLOAD . " (projectID,projectName,projectMark,projectCode,createdBy,createdDept,reviewID,reviewName,status,`type`,trialDept,trialDeptLiasisonOfficer,trialAdjudicatingOfficer,trialJoinOfficer,blockDept,blockMember,blockTotal,blockPerMonth,owner,qa,qualityCm,onLineExpert,realExpert,verifier,createdDate,firstPreReviewDate,closeTime,baselineDate,suspendTime,renewTime,insertTime) values ";

        // 拼接流转工作量表和流转耗时表sql
        foreach($reviewList as $review){
            $dataTrial = $this->review->getTrial($review->id, $review->version, $accounts , 2);
            $review->trialDept                = $dataTrial['deptid'];
            $review->trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
            $review->trialAdjudicatingOfficer = $dataTrial['deptzs'];
            $review->trialJoinOfficer         = $dataTrial['deptjoin'];
            $review->reviewDays               = $firstPreReviewDates[$review->id] != '0000-00-00 00:00:00' && !empty($firstPreReviewDates[$review->id]) && $review->closeTime != '0000-00-00 00:00:00' && !empty($review->closeTime) ? $this->loadModel('holiday')->getTimeBetween($firstPreReviewDates[$review->id], $review->closeTime) : '' ;
            $review->preReviewDays            = $firstPreReviewDates[$review->id] != '0000-00-00 00:00:00' && !empty($firstPreReviewDates[$review->id]) && $review->createdDate != '0000-00-00 00:00:00' && !empty($review->createdDate) ? $this->loadModel('holiday')->getTimeBetween($review->createdDate, $firstPreReviewDates[$review->id]):'';
            $review->onLineExpert             = $onLineExperts[$review->id];
            $review->verifier                 = $verifiers[$review->id];
            $review->firstPreReviewDate       = $firstPreReviewDates[$review->id] ? $firstPreReviewDates[$review->id] : '0000-00-00 00:00:00';
            $review->baselineDate             = $baselineDates[$review->id] ? $baselineDates[$review->id] : '0000-00-00 00:00:00';
            $review->insertTime               = helper::now();
            $sql .= "('".$review->project."','".$review->name."','".$review->mark."','".$review->code."','".$review->createdBy."','".$review->createdDept."','".$review->id."','".$review->title."','".$review->status."','".$review->type."','".$review->trialDept."','".$review->trialDeptLiasisonOfficer."','".$review->trialAdjudicatingOfficer."','".$review->trialJoinOfficer."','".$review->owner."','".$review->qa."','".$review->qualityCm."','".$review->onLineExpert."','".$review->realExport."','".$review->verifier."','".$review->createdDate."','".$review->firstPreReviewDate."','".$review->closeTime."','".$review->baselineDate."','".$review->suspendTime."','".$review->renewTime."','".$review->insertTime."',";
            $sqlCost .= "('".$review->project."','".$review->name."','".$review->mark."','".$review->code."','".$review->createdBy."','".$review->createdDept."','".$review->id."','".$review->title."','".$review->status."','".$review->type."','".$review->trialDept."','".$review->trialDeptLiasisonOfficer."','".$review->trialAdjudicatingOfficer."','".$review->trialJoinOfficer."','".$review->owner."','".$review->qa."','".$review->qualityCm."','".$review->onLineExpert."','".$review->realExport."','".$review->verifier."','".$review->createdDate."','".$review->firstPreReviewDate."','".$review->closeTime."','".$review->baselineDate."','".$review->suspendTime."','".$review->renewTime."','".$review->insertTime."','".$review->reviewDays."','".$review->preReviewDays."',";
            foreach($stageList as $key =>$name){
                if($reviewStages[$review->id][$key]){
                    $sql .= "'".$reviewStages[$review->id][$key]."',";
                }else{
                    $sql .= "'',";
                }
                if($key == 'preReviewBefore'){
                    $sqlCost .= "'" . $review->preReviewDays . "',";
                }else{
                    if($reviewStagesCost[$review->id][$key]){
                        $sqlCost .= "'" . $this->loadModel('holiday')->secToStr($reviewStagesCost[$review->id][$key]) . "',";
                    }else{
                        $sqlCost .= "'',";
                    }
                }
            }
            $sql = substr($sql,'0','-1');
            $sqlCost = substr($sqlCost,'0','-1');
            $sql .= "),";
            $sqlCost .= "),";
        }
        // 拼接参与人员工作量表sql
        foreach($reviewConsumedList as $reviewConsumed){
            // 无对应账号不统计
            if(empty($accounts[$reviewConsumed->account])){
                continue;
            }
            $dataTrial = $this->review->getTrial($reviewConsumed->id, $reviewConsumed->version, $accounts, 2);
            $reviewConsumed->trialDept                = $dataTrial['deptid'];
            $reviewConsumed->trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
            $reviewConsumed->trialAdjudicatingOfficer = $dataTrial['deptzs'];
            $reviewConsumed->trialJoinOfficer         = $dataTrial['deptjoin'];
            $reviewConsumed->perMonth                 = round(($reviewConsumed->workload / $projectHours[$reviewConsumed->id]) / 8, 2);
            $reviewConsumed->reviewDays               = $firstPreReviewDates[$reviewConsumed->id] != '0000-00-00 00:00:00' && !empty($firstPreReviewDates[$reviewConsumed->id]) && $reviewConsumed->closeTime != '0000-00-00 00:00:00' && !empty($reviewConsumed->closeTime) ? $this->loadModel('holiday')->getTimeBetween($firstPreReviewDates[$reviewConsumed->id], $reviewConsumed->closeTime) : '' ;
            $reviewConsumed->preReviewDays            = $firstPreReviewDates[$reviewConsumed->id] != '0000-00-00 00:00:00' && !empty($firstPreReviewDates[$reviewConsumed->id]) && $reviewConsumed->createdDate != '0000-00-00 00:00:00' && !empty($reviewConsumed->createdDate) ? $this->loadModel('holiday')->getTimeBetween($reviewConsumed->createdDate, $firstPreReviewDates[$reviewConsumed->id]):'';
            $reviewConsumed->onLineExpert             = $onLineExperts[$reviewConsumed->id];
            $reviewConsumed->verifier                 = $verifiers[$reviewConsumed->id];
            $reviewConsumed->firstPreReviewDate       = $firstPreReviewDates[$reviewConsumed->id] ? $firstPreReviewDates[$reviewConsumed->id] : '0000-00-00 00:00:00';
            $reviewConsumed->baselineDate             = $baselineDates[$reviewConsumed->id] ? $baselineDates[$reviewConsumed->id] : '0000-00-00 00:00:00';
            $reviewConsumed->insertTime               = helper::now();
            $sqlConsumed .= "('".$reviewConsumed->project."','".$reviewConsumed->name."','".$reviewConsumed->mark."','".$reviewConsumed->code."','".$reviewConsumed->createdBy."','".$reviewConsumed->createdDept."','".$reviewConsumed->id."','".$reviewConsumed->title."','".$reviewConsumed->status."','".$reviewConsumed->type."','".$reviewConsumed->trialDept."','".$reviewConsumed->trialDeptLiasisonOfficer."','".$reviewConsumed->trialAdjudicatingOfficer."','".$reviewConsumed->trialJoinOfficer."','".$reviewConsumed->deptId."','".$reviewConsumed->account."','".$reviewConsumed->workload."','".$reviewConsumed->perMonth."','".$reviewConsumed->owner."','".$reviewConsumed->qa."','".$reviewConsumed->qualityCm."','".$reviewConsumed->onLineExpert."','".$reviewConsumed->realExport."','".$reviewConsumed->verifier."','".$reviewConsumed->createdDate."','".$reviewConsumed->firstPreReviewDate."','".$reviewConsumed->closeTime."','".$reviewConsumed->baselineDate."','".$reviewConsumed->suspendTime."','".$reviewConsumed->renewTime."','".$reviewConsumed->insertTime."'),";
        }
        $sql = substr($sql,'0','-1');
        $sqlCost = substr($sqlCost,'0','-1');
        $sqlConsumed = substr($sqlConsumed,'0','-1');
        $sql .= ";";
        $sqlCost .= ";";
        $sqlConsumed .= ";";

        if(!empty($deleteSql)){
            $this->dao->query($deleteSql);
        }
        $this->dao->query($sessionsql);
        $this->dao->query($sql);
        $this->dao->query($sqlCost);
        $this->dao->query($sqlConsumed);

        if(!empty($method)){
            $response['result']  = 'success';
            $response['message'] = '刷新报表数据成功';
            $response['locate']  = 'report-'.$method.'-' . $projectID .'.html#app=project';
            $this->sendBack($response);
        }
    }
}