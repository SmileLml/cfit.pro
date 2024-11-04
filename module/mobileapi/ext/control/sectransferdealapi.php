<?php

include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * @param $id
     * @return void
     */
    public function sectransferDealApi()
    {
        $id = $_POST['id'];
        $this->loadModel('sectransfer');
        $this->app->loadLang('sectransfer');

        $transferInfo = $this->sectransfer->getByID($id);
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        if($this->lang->sectransfer->statusList['centerReject'] != $transferInfo->status){
            $this->loadModel('mobileapi')->response('fail', '该状态下不能退回处理', array(),  0, 203,'sectransferDealApi');
        }
        if ($_POST) {
            $changes = $this->sectransfer->deal($id);
            if ($changes) {
                $actionID = $this->loadModel('action')->create('sectransfer', $id, 'dealed',$this->post->comment,'mobile');
                $this->action->logHistory($actionID, $changes);
            }

            if (dao::isError()){
                $errorArray = [];
                $error = dao::getError();
                if(is_array($error)){
                    foreach ($error as $key => $item) {
//                        $error = trim(implode(',',$item),',');
                        $errorArray[] = $item;
                    }
                }
                $this->loadModel('mobileapi')->response('fail',implode(',',$errorArray), array(),  0, 203,'sectransferDealApi');
            }

            $this->loadModel('mobileapi')->response('success', '处理成功', [], 0, 200, 'sectransferDealApi');
        }

        $this->loadModel('mobileapi')->response('success', '处理成功', [], 0, 200, 'sectransferDealApi');
    }

}
