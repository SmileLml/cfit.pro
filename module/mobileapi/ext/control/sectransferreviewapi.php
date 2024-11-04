<?php
include '../../control.php';
class myMobileApi extends mobileapi
{
    public function sectransferReviewApi()
    {
        $id = $_POST['id'];
        $this->loadModel('sectransfer');
        $this->app->loadLang('sectransfer');

        $transfer = $this->sectransfer->getByID($id);
        $logChanges = $this->sectransfer->review($id);
        $errorArray = [];
        if(dao::isError()) {
            $error = dao::getError();
            if(is_array($error)){
                foreach ($error as $key => $item) {
//                    $error = trim(implode(',',$item),',');
                    $errorArray[] = $item;
                }
            }
            $this->loadModel('mobileapi')->response('fail',implode(',',$errorArray), array(),  0, 203,'sectransferReviewApi');
        }
        $commit = $this->lang->sectransfer->reviewResult.'：'
            .$this->lang->sectransfer->reviewList[$this->post->result].'<br>'
            .$this->lang->sectransfer->dealOpinion.'：'.$this->post->suggest;
        if($this->post->sftpPath != ''){
            $commit = $commit.'<br>'.$this->lang->sectransfer->sftpPath.'：'.$this->post->sftpPath;
        }
        $actionID = $this->loadModel('action')->create('sectransfer', $id, 'reviewed', $commit,'mobile');
        $this->action->logHistory($actionID, $logChanges);

        if($transfer->status == $this->lang->sectransfer->statusList['waitSecApprove']){
            $examine = $this->lang->sectransfer->dealed;
        }else{
            $examine = in_array($transfer->status,$this->lang->sectransfer->examineList) ? $this->lang->sectransfer->examine : $this->lang->sectransfer->leaderExamine;
        }
        $this->view->transfer = $transfer;
        $this->view->examine  = $examine;

        // 二线专员通过时新增提示语
        if($transfer->status == $this->lang->sectransfer->statusList['waitSecApprove'] && $this->post->result == 'pass'){
            $msg = $this->lang->sectransfer->secNotice;
        }else{
            $msg = $this->lang->saveSuccess;
        }

        $this->loadModel('mobileapi')->response('success', $msg, [] ,  0, 200, 'sectransferReviewApi');
    }
}
