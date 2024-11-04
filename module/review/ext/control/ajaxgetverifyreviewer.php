<?php
include '../../control.php';
class myReview extends review
{
    /**
     *  验证人
     */
    public function ajaxGetVerifyReviewer($reviewer)
    {
        $users      = $this->loadModel('user')->getPairs('noclosed');
        $reviewer = explode(',', $reviewer);
        $res = [];
        foreach ($reviewer as $value){
        foreach ($users as $key => $user) {
                if($key == $value){
                    $res[$key] = $user;
                    unset($users[$key]);
                }
            }
        }
        $users =array_merge($res,$users);
       // $reviewer = '';
        echo html::select('verifyReviewers[]', $users, $reviewer , 'class="form-control chosen " multiple required');
    }


}