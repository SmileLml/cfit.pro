<?php
include '../../control.php';
class myResidentwork extends residentwork
{
    public function createlog(){
        $this->app->loadLang("residentsupport");
        if ($_POST){
            $res = $this->loadModel("residentwork")->createlog();
            if (!$res['result']){
                $response['result']  = 'fail';
                $response['message'] = $res['message'];
                $this->send($response);
                exit;
            }
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $response['result']  = 'success';
            $response['message'] = "保存成功";
            $response['locate']  = 'parent';
            $response['locate'] = $this->createLink("residentwork","view","id=".$res['workId']);
            $this->send($response);
            exit;
        }
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $emptyArr = array('0'=>'');
        $this->view->title = $this->lang->residentsupport->common;
        $this->view->typeList = $emptyArr + $this->lang->residentsupport->typeList;
        $this->view->subTypeList = $emptyArr + $this->lang->residentsupport->subTypeList;
        $this->view->dateTypeList = $emptyArr + $this->lang->residentsupport->dateTypeList;
        $this->view->areaList = $emptyArr + $this->lang->residentsupport->areaList;
        $this->view->users = $users;
        $this->display();
    }

}