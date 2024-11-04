<?php
include '../../control.php';
class myRequirement  extends requirementinside
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

        $requirement = $this->loadModel('requirement')->getByID($requirementID);
//        $requirement = $this->loadModel('file')->replaceImgURL($requirement, 'analysis,handling,implement');
        if($requirement -> deadLine == '0000-00-00'){
            $requirement->deadLine = '';
        }
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
