<?php
include '../../control.php';
class myReview extends review
{
    /**
     * 设置qa評審、評審專家、評審參與人員選擇列表
     * @param $id
     * @param $selectUser
     */
    public function ajaxGetCommonList($id)
    {
        global $app;
        $users = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = '';
        if($id === 'qa') {
            $reviewer = $this->loadModel('dept')->getByID($app->user->dept);
            $reviewer = isset($reviewer->qa) ? $reviewer->qa : '';
            echo html::select($id, $users, $reviewer , 'class="form-control chosen" required');
        }else {
            echo html::select($id.'[]', $users, $reviewer , 'class="form-control chosen" multiple');
        }
    }


}