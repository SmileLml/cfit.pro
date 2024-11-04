<?php
include '../../control.php';
class myApi extends api
{
    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR   = 1002; //缺少参数

    public function weeklyReportFeedBack(){
        $logID  = $this->loadModel('requestlog')->insideSaveRequestLog('weeklyreportout' , 'weeklyReportFeedBack');

        //周报id
        $outweeklyreportID = $_POST['weeklyReportId'];
        //反馈意见
        $feedback = $_POST['feedback'];
        //反馈人
        $feedbackUser = $_POST['approverName'];


        $outreport = $this->loadModel('weeklyreportout')->getByID($outweeklyreportID,0);


        if(!$outreport){
            $this->requestlog->response('fail', '该周报不存在！', array('weeklyReportId' => $outweeklyreportID), $logID);
        }

        $updata = [
            'outFeedbackTime'=>helper::now(),
            'outFeedbackView'=>$feedback,
            'outFeedbackUser'=>$feedbackUser,
        ];
        $res = $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT)->data($updata)->where('id')->eq($outweeklyreportID)->exec();

        if($res){
            $newOutreport = $this->loadModel('weeklyreportout')->getByID($outweeklyreportID,0);
            $actionID = $this->loadModel('action')->create('weeklyreportout', $outweeklyreportID, 'weeklyReportFeedBack', '','','guestcn');
            $changes = common::createNewChanges($outreport, $newOutreport);
            if($changes){
                $this->action->logHistory($actionID, $changes);
            }

            $this->requestlog->response('success', $this->lang->api->successful, array('weeklyReportId' => $outweeklyreportID), $logID);

        }

       /* if(!$res['result']) {
            $this->requestlog->response('fail', $res['message'], [], 0, self::PARAMS_ERROR);
        }else{
            $actionID = $this->loadModel('action')->create($objectType, $reviewId, $action, '清总评审单号:' . $qzReviewId,'',$account);
            if((isset($res['data']['logChanges'])) && !empty($res['data']['logChanges'])){
                $this->action->logHistory($actionID, $res['data']['logChanges']);
            }
            //返回
            $this->requestlog->response('success', $this->lang->api->successful, array('Review_ID' => $qzReviewId), $logID);
        }*/
    }



}