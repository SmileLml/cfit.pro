<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function creditReviewApi()
    {
        $id = $_POST['id'];
        $this->loadModel('credit');
        $this->app->loadLang('credit');
        $logChanges = $this->credit->review($id,true);
        $errorArray = [];
        if(dao::isError()) {
            $error = dao::getError();
            if(is_array($error)){
                foreach ($error as $key => $item) {
//                    $error = trim(implode(',',$item),',');
                    $errorArray[] = $item;
                }
            }
            $this->loadModel('mobileapi')->response('fail',implode(',',$errorArray), array(),  0, 203,'creditReviewApi');
        }
        $msg = $this->lang->submitSuccess;
        $this->loadModel('mobileapi')->response('success', $msg, [] ,  0, 200, 'creditReviewApi');
    }
}
