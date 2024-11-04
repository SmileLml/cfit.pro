<?php
include '../../control.php';
class myReview extends review
{
    /**
     *  设置所有会议参会专家
     * @param $type
     *
     */
    public function ajaxsetmeetingexperts($exports)
    {
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        echo html::select("meetingPlanExport[]", $users,$exports, "class='form-control chosen' required multiple");
    }

}
