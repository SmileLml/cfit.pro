<?php
include '../../control.php';
class myReview extends review
{
    /**
     * autosendmail
     *
     * @access public
     * @return void
     */
    public function autosendmail()
    {
        return $this->loadModel('review')->autosendmail(); //正常调用模块及方法
//        $reviewList = $this->review->getAllReviewList();
//        $dealUsers = '';
//        $dealUserList = array();
//        foreach ($reviewList as $review){
//            $dealUsers .=$review->dealUser."," ;
//        }
//        $currentDate = date('Y-m-d');
//        $dealUserList =Array_filter( array_unique(explode(',',$dealUsers)));
//        //获取配置的日期N和M
//        $emilAlertLevel = $this->lang->review->emilAlert;
//        $realReview1 = array();
//        $realReview2 = array();
//        $realReview3 = array();
//        foreach ($dealUserList as $dealUser) {
//            foreach ($reviewList as $review) {
//                if (in_array($review->status, $this->lang->review->allowAutoDealStatusList)) {
//                    $dealUsers = array_filter(explode(',', $review->dealUser));
//                    if (!empty($dealUsers) && in_array($dealUser, $dealUsers)) {
//                        if ($review->endDate != '0000-00-00 00:00:00') {
//                            $diffDays =  $this->review->getDiffDate($review->endDate,$currentDate);
//                            //if ($diffDays != 0) {
//                                if ($diffDays == -$emilAlertLevel['level1']) {
//                                    if(in_array($review->status, $this->lang->review->timeOutAutoDealStatusList)){
//                                        //最小超时时间
//                                        $minTimeOutDay = $this->loadModel('holiday')->getActualWorkingDate($review->endDate, $emilAlertLevel['level2']);
//                                        //$review->autoDealTime = date('Y-m-d 4:00:00', strtotime("$minTimeOutDay + 1 days")); //如果逾期，逾期的处理时间
//                                        $autoDealDay  = $this->loadModel('holiday')->getActualWorkingDate($minTimeOutDay, 1);
//                                        $review->autoDealTime = date('Y-m-d 4:00:00', strtotime($autoDealDay)); //如果逾期，逾期的处理时间
//                                    }else{
//                                        $review->autoDealTime = '';
//                                    }
//                                    $realReview1[] = $review;
//                                } elseif ($diffDays == $emilAlertLevel['level2']) {
//                                    $realReview2[] = $review;
//                                } elseif ($diffDays > $emilAlertLevel['level2']) {
//                                    $realReview3[] = $review;
//                                }
//                            //}
//                        }
//                    }
//                }
//            }
//            $this->review->sendmail('', '', 1, $dealUser, $realReview1, $realReview2, $realReview3);
//
//            $realReview1 = [];
//            $realReview2 = [];
//            $realReview3 = [];
//        }
//        echo '发送成功';

    }

}
