<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function changeReviewApi()
    {
        $id = $_POST['id'];
        $this->loadModel('change');
        $this->app->loadLang('change');
        $logChanges = $this->change->review($id);
        $actionID = $this->loadModel('action')->create('change', $id, 'reviewed', $this->post->comment, 'mobile');
        $errorArray = [];
        if(dao::isError()) {
            $error = dao::getError();
            if(is_array($error)){
                foreach ($error as $key => $item) {
//                    $error = trim(implode(',',$item),',');
                    $errorArray[] = $item;
                }
            }
            $this->loadModel('mobileapi')->response('fail',implode(',',$errorArray), array(),  0, 203,'changeReviewApi');
        }
        $msg = $this->lang->submitSuccess;
        $this->loadModel('mobileapi')->response('success', $msg, [] ,  0, 200, 'changeReviewApi');
    }
}
