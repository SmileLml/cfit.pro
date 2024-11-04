<?php
include '../../control.php';
class myRequirement  extends requirement
{

    /**
     * Feedback a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function feedback($requirementID)
    {
        $requirement = $this->loadModel('requirement')->getByID($requirementID);

        //变更中，不允许操作 2标识锁定 requirementChangeStatus  默认0  1：完成 2：变更进行中 3:已退回
        if($requirement->changeLock == 2){
            $response['result']  = 'fail';
            $response['message'] = $this->lang->requirement->changeIng;
            $this->send($response);
        }

        $flag = (((strstr($requirement->dealUser, $this->app->user->account) !== false
                || strstr($requirement->feedbackDealUser, $this->app->user->account) !== false)
            && in_array($requirement->status,['published','splited','delivered','onlined'])
            && in_array($requirement->feedbackStatus,['tofeedback','returned','syncfail','feedbackfail']))
        || $this->app->user->account == 'admin');
        if(!$flag){
            $response['result']  = 'fail';
            $response['message'] = '没有权限';
            $response['locate']  = inlink('view', "requirementID=$requirementID");
            $this->send($response);
        }
        /* 当请求方式为post时，调用feedback方法处理需求条目的反馈逻辑，成功则记录操作动作并返回成功信息。*/
        if($_POST)
        {
            $changes = $this->requirement->feedback($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                /* 判断是否为推送的需求条目。*/
               /* $newRequirement = $this->requirement->getByID($requirementID);
                $pushEnable     = $this->config->global->pushEnable;
                $pushPrompt     = '';
                if($newRequirement->feedbackCode and $pushEnable == 'enable')
                {
                    $pushPrompt = $this->lang->requirement->pushPrompt;
                }*/

                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'createfeedbacked');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('view', "requirementID=$requirementID");

            $this->send($response);
        }

        if($requirement -> deadLine == '0000-00-00'){
            $requirement->deadLine = '';
        }

        //所属CBP项目
        $this->view->cbpprojectList     = array('' => '') +  array('暂无' => '暂无') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->ne(1)->fetchPairs();
        //部门审批人
        $deptInfo = $this->loadModel('dept')->getByID($this->app->user->dept);
        if($deptInfo != null){
            $requirement->feedbackDealUser = explode(',',$deptInfo->manager);
        }else{
            //若没有部门，就设置本身作为审批人
            $requirement->feedbackDealUser = $this->app->user->account;
        }
        $ownuser = $this->loadModel('user')->getById($this->app->user->account);
        $managerUser = array();
        if($deptInfo != null){
            $managerList = explode(',',$deptInfo->manager);
            foreach ($managerList as $manager){
                $managerValue = $this->loadModel('user')->getById($manager);
                $managerUser[$manager] = $managerValue->realname;
            }
        }else{
            //若没有部门，就设置本身作为审批人
            $managerUser[$this->app->user->account] = $ownuser->realname;
        }
        if(!empty($ownuser->partDept)){
            $partDeptArray = explode(',',$ownuser->partDept);
            foreach ($partDeptArray as $partDept){
                $deptInfo = $this->loadModel('dept')->getByID($partDept);
                if($deptInfo != null){
                    $managerList = explode(',',$deptInfo->manager);
                    foreach ($managerList as $manager){
                        $managerValue = $this->loadModel('user')->getById($manager);
                        $managerUser[$manager] = $managerValue->realname;
                    }
                }
            }
        }
        $this->view->managerUser       = $managerUser;
        /* 此方法需要填写实施部门、归属项目、所属产品线、应用系统、设计产品等，此处代码是获取对应数据到view模板中。*/
        $this->view->title       = $this->lang->requirement->feedback;
        $this->view->requirement = $requirement;
        $this->view->opinion     = $this->loadModel('opinion')->getByID($requirement->opinion);
        $this->view->lines       = array('' => '') + $this->loadModel('productline')->getPairsLineAndName();
        $this->view->products    = $this->loadModel('product')->getPairsNameLinkCode();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        if($requirement->method == 'project' || $requirement->method == ''){
            $this->view->projects    = array(0 => '') +  $this->loadModel('projectplan')->getAliveProjects(false);
        }else{
            $this->view->projects    = array(0 => '') +  $this->loadModel('projectplan')->getAliveProjects(true);
        }

        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed');
        $this->view->apps        = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->display('requirement', 'newfeedback');
    }
}
