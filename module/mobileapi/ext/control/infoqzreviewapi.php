<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function infoqzReviewApi()
    {
        $id = $_POST['id'];
        $this->loadModel('infoqz');
        $this->app->loadLang('infoqz');
        $logChanges = $this->infoqz->review($id);
        $errorArray = [];
        if(dao::isError()) {
            $error = dao::getError();
            if(is_array($error)){
                foreach ($error as $key => $item) {
//                    $error = trim(implode(',',$item),',');
                    $errorArray[] = $item;
                }
            }
            $this->loadModel('mobileapi')->response('fail',implode(',',$errorArray), array(),  0, 203,'infoqzReviewApi');
        }
        $msg = $this->lang->submitSuccess;
        $actionID = $this->loadModel('action')->create('infoqz', $id, 'review', $this->post->comment,'mobile');
        $this->loadModel('mobileapi')->response('success', $msg, [] ,  0, 200, 'infoqzReviewApi');
    }
}
