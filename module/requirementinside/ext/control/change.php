<?php
include '../../control.php';
class myRequirement  extends requirementinside
{    
    /**
     * Change a requirement.
     *
     * @param  int    $requirementID
     * @access public
     * @return void
     */
    public function change($requirementID)
    {
        if($_POST)
        {
            $oldRequirement = $this->requirement->getByID($requirementID);
            /* 判断需求条目变更时，评审人是否选填了(系统手动拆分的需求条目才判断)。*/

//            if(!implode('', $_POST['reviewer']) and empty($oldRequirement->entriesCode))
            if(!isset($_POST['reviewer']) and empty($oldRequirement->entriesCode))
            {
                $response = array();
                $response['result']  = 'fail';
                $response['message'] = $this->lang->requirement->reviewerEmpty;
                $this->send($response);
            }
            /* 当请求方式为post时，调用change方法处理变更逻辑，如果处理成功则记录变更动作，然后返回成功信息。*/
            $changes = $this->requirement->change($requirementID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            if($changes)
            {
                /* 判断是否为推送的需求条目。*/
                $newRequirement = $this->requirement->getByID($requirementID);
                $pushEnable     = $this->config->global->pushEnable;
                $pushPromptChange = '';
                if($newRequirement->feedbackCode and $pushEnable == 'enable')
                {
                    $pushPromptChange = $this->lang->requirement->pushPromptChange;
                }
                $actionID = $this->loadModel('action')->create('requirement', $requirementID, 'changed', $pushPromptChange, $newRequirement->version);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->submitSuccess;
            $response['locate']  = inlink('browse');

            $this->send($response);
        }

        $requirement = $this->loadModel('requirement')->getByID($requirementID);
//        $requirement = $this->loadModel('file')->replaceImgURL($requirement, 'analysis,handling,implement');
        if($requirement -> deadLine == '0000-00-00'){
            $requirement->deadLine = '';
        }
        /* 此方法类似feedback方法，需要填写实施部门、归属项目、所属产品线、应用系统、设计产品等，此处代码是获取对应数据到view模板中。*/
        $this->view->title       = $this->lang->requirement->change;
        $this->view->requirement = $requirement;
		$this->view->opinion     = $this->loadModel('opinion')->getByID($requirement->opinion);
        $this->view->lines       = array('' => '') + $this->loadModel('productline')->getPairsLineAndName();
        $this->view->products    = $this->loadModel('product')->getPairsNameLinkCode();
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects    = array(0 => '') + $this->loadModel('projectplan')->getPairs();
        $this->view->users       = $this->loadmodel('user')->getPairs('noclosed');
        $this->view->apps        = array(0 => '') + $this->loadmodel('application')->getapplicationNameCodePairs();
        $this->display('requirement', 'newchange');
    }
}
