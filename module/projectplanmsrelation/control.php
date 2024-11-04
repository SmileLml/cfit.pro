<?php

class projectPlanMSRelation extends control
{

    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $isSecondline = 0)
    {
        $browseType = strtolower($browseType);

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;

        $actionURL = $this->createLink('projectplanmsrelation', 'browse', "browseType=bySearch&param=myQueryID");

        $this->projectplanmsrelation->buildSearchForm($queryID, $actionURL);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->session->set('projectplanmsrelationList', $this->app->getURI(true));

        $userDept = $this->loadModel('user')->getUserDeptName($this->app->user->account);
        $relationList = $this->projectplanmsrelation->getList($browseType, $queryID, $orderBy, $pager, $isSecondline);


        //分管领导获取以及总经理获取
//        $users = $this->loadModel('user')->getPairs('noletter');
//        $deptInfo = $this->loadModel('dept')->getDeptPairs();
//        $deptIds = implode(',',array_keys($deptInfo));

        $projectplanList = $this->loadModel("projectplan")->getAllIncludeDeleteList();
        $this->view->projectplanList = array_column($projectplanList,null,'id');


        $this->view->title      = $this->lang->projectplan->common;
        $this->view->relationList      = $relationList;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;

        $this->view->isSecondline      = $isSecondline;
        $this->view->browseType = $browseType;
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->users      = $this->user->getPairs('noletter|noclosed');
        $this->view->userDept   = $userDept->deptName == '平台架构部' ? true : false;

        $this->display();
    }

    public function maintenanceRelation($planID=0){


        if($_POST){

            $result = $this->projectplanmsrelation->maintenanceRelation();

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('projectplanmsrelation', $result['dataID'], 'maintenancerelation', $result['type']);
            if($result['type'] == 'update'){
                $this->loadModel('action')->logHistory($actionID, $result['changes']);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';

            $this->send($response);

        }
        $projectplanList = $this->loadModel("projectplan")->getAllList();
        $this->view->projectplanList = array_column($projectplanList,'name','id');
        $this->view->planID = $planID;

        $this->display();
    }



    public function edit($relationID){

        if($this->server->REQUEST_METHOD == 'POST'){
            $result = $this->projectplanmsrelation->update($relationID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('projectplanmsrelation', $relationID, 'edited');
            if($result){
                $this->loadModel('action')->logHistory($actionID, $result);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse', "");;

            $this->send($response);
        }

        $this->view->relationInfo = $this->projectplanmsrelation->getByID($relationID);
        $projectplanList = $this->loadModel("projectplan")->getAllList();
        $this->view->projectplanList = array_column($projectplanList,'name','id');
        $this->display();
    }

    public function delete($relationID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->projectplanmsrelation->confirmDelete, $this->createLink('projectplanmsrelation', 'delete', "planID=$relationID&confirm=yes"), '');
            exit;
        }
        else
        {
            $this->projectplanmsrelation->delete(TABLE_PROJECTPLANMSRELATION, $relationID);
//            $this->dao->update(TABLE_PROJECTPLAN)->set('status')->eq('deleted')->where('id')->eq($planID)->exec();

            die(js::locate(inlink('browse'), 'parent'));
        }
    }
    public function ajaxGetSlaveProjectplan(){
       $relationInfo =  $this->projectplanmsrelation->ajaxGetSlaveProjectplan();

        $projectplanList = $this->loadModel("projectplan")->getAllList();

        $projectplanList = array_column($projectplanList,'name','id');

       if($relationInfo){
           die(html::select('slavePlanID[]', $projectplanList, $relationInfo->slavePlanID, " class='form-control chosen' multiple"));
       }else{
           die(html::select('slavePlanID[]', $projectplanList, "", " class='form-control chosen' multiple"));
       }


    }


}