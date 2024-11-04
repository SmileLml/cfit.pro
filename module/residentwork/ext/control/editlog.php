<?php
include '../../control.php';
class myResidentwork extends residentwork
{
    public function editlog($workId){
        $this->app->loadLang("residentsupport");
        if ($_POST){
            $res = $this->loadModel("residentwork")->editlog($workId);
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
            exit;
        }
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $info = $this->residentwork->getByworkId($workId,'*',true);
        $this->view->info = $info;
        $this->view->depts = $depts;
        $users = $this->loadModel('user')->getPairs('noletter');
        $outsideList1 = $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = $this->loadModel('user')->getUsersNameByType('outside');
        $users = array_merge($users, $outsideList1, $outsideList2);
        $this->view->users = $users;
        $type[$info->type] = $this->lang->residentsupport->typeList[$info->type];
        $subType[$info->subType] = $this->lang->residentsupport->subTypeList[$info->type];
        $this->view->type = $type;
        $this->view->subType = $subType;
        $this->view->dateTypeList = $this->lang->residentsupport->dateTypeList;
        $this->view->areaList = $this->lang->residentsupport->areaList;
        $this->view->title = $this->lang->residentsupport->common;

        $this->display();
    }

}