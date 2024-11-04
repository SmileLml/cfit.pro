<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function putproductionReviewApi()
    {
        $id = $_POST['id'];
        $this->loadModel('putproduction');
        $this->app->loadLang('putproduction');

        $info = $this->putproduction->getByID($id);
        $logChanges = $this->putproduction->review($info,true);
        $errorArray = [];
        if(dao::isError()) {
            $error = dao::getError();
            if(is_array($error)){
                foreach ($error as $key => $item) {
//                    $error = trim(implode(',',$item),',');
                    $errorArray[] = $item;
                }
            }
            $this->loadModel('mobileapi')->response('fail',implode(',',$errorArray), array(),  0, 203,'putproductionReviewApi');
        }
        $msg = $this->lang->submitSuccess;
        $this->loadModel('mobileapi')->response('success', $msg, [] ,  0, 200, 'putproductionReviewApi');
    }
}
