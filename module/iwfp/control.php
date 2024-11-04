<?php
class iwfp extends control
{
    public function conf()
    {
        $this->lang->admin->menu->system['subModule'] = 'data,safe,cron,timezone,buildIndex,ldap,libreoffice,requestconf,customflow,iwfp';

        if($_POST)
        {
            $this->iwfp->setPush();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->iwfp->common;
        $this->view->position[] = $this->lang->iwfp->common;
        $this->display();
    }

    public function startWorkFlow(){
        $response =  $this->iwfp->startWorkFlow($_POST['objectType'], $_POST['objectId'], $_POST['objectCode'], $_POST['createdBy'], json_decode($_POST['nodeDealUser']), $_POST['processInstanceId']);
        echo $response;
        echo json_encode($response);
    }

    public function completeTaskWithClaim(){
        $response =  $this->iwfp->completeTaskWithClaim($_POST['processInstanceId'], $_POST['dealUser'], $_POST['dealMessage'], $_POST['dealResult'], json_decode($_POST['userVariableList']), $_POST['version']);
        echo $response;
        echo json_encode($response);
    }

    public function listApproveLog(){
        $response =  $this->iwfp->listApproveLog($_POST['objectType'],$_POST['objectID'],$_POST['version']);
        echo json_encode($response);
    }

    public function turnBack(){
        $response =  $this->iwfp->turnBack($_POST['processInstanceId']);
        echo $response;
        echo json_encode($response);
    }

    public function getFreeJumpNodeList(){
        $response =  $this->iwfp->getFreeJumpNodeList($_POST['processInstanceId']);
        echo $response;
        echo json_encode($response);
    }

    public function freeJump(){
        $response =  $this->iwfp->freeJump($_POST['processInstanceId'], $_POST['targetXmlTaskId']);
        echo $response;
        echo json_encode($response);
    }

    public function withDraw(){
        $response =  $this->iwfp->withDraw($_POST['processInstanceId']);
        echo $response;
        echo json_encode($response);
    }

    public function addSignTask(){
        $response =  $this->iwfp->addSignTask($_POST['processInstanceId'],$_POST['addSignType'],$_POST['addUserStr'],$_POST['dealUserNo']);
        echo $response;
        echo json_encode($response);
    }

    public function changeAssigneek(){
        $response =  $this->iwfp->changeAssigneek($_POST['processInstanceId'],$_POST['oldUserNo'],$_POST['newUserNo']);
        echo $response;
        echo json_encode($response);
    }

    public function queryProcessTrackImage(){
        $response =  $this->iwfp->queryProcessTrackImage($_POST['processInstanceId']);
        echo $response;
        echo json_encode($response);
    }

    public function getNodeInfoList(){
        $response =  $this->iwfp->getNodeInfoList($_POST['processInstanceId']);
        echo $response;
        echo json_encode($response);
    }
}

