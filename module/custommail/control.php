<?php
/**
 * The control file of custommail currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     requestconf
 * @version     $Id: control.php 5107 2013-07-12 01:46:12Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class custommail extends control
{
    public function __construct($module = '', $method = '')
    {
        parent::__construct($module, $method);

        // 设置导航菜单高亮。
        $this->lang->admin->menu->message['subModule'] = 'message,mail,webhook,sms,custommail';
    }

    public function problem()
    {
        if($_POST)
        {
            $this->custommail->setProblemMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setProblemMail) ? $this->config->global->setProblemMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'problem';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function demand()
    {
        if($_POST)
        {
            $this->custommail->setDemandMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setDemandMail) ? $this->config->global->setDemandMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'demand';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    
    public function requirement()
    {
        if($_POST)
        {
            $this->custommail->setRequirementMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setRequirementMail) ? $this->config->global->setRequirementMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirement';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function modify()
    {
        if($_POST)
        {
            $this->custommail->setModifyMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setModifyMail) ? $this->config->global->setModifyMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'modify';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function modifycncc()
    {
        if($_POST)
        {
            $this->custommail->setModifycnccMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setModifycnccMail) ? $this->config->global->setModifycnccMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'modifycncc';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function fix()
    {
        if($_POST)
        {
            $this->custommail->setFixMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setFixMail) ? $this->config->global->setFixMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'fix';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function gain()
    {
        if($_POST)
        {
            $this->custommail->setGainMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setGainMail) ? $this->config->global->setGainMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'gain';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function gainQz()
    {
        if($_POST)
        {
            $this->custommail->setGainQzMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setGainQzMail) ? $this->config->global->setGainQzMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'gainQz';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function fixQz()
    {
        if($_POST)
        {
            $this->custommail->setFixQzMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setFixQzMail) ? $this->config->global->setFixQzMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'fixQz';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function plan()
    {
        if($_POST)
        {
            $this->custommail->setPlanMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanMail) ? $this->config->global->setPlanMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'plan';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function planReject()
    {
        if($_POST)
        {
            $this->custommail->setPlanRejectMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanRejectMail) ? $this->config->global->setPlanRejectMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'planReject';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function planPass()
    {
        if($_POST)
        {
            $this->custommail->setPlanPassMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanPassMail) ? $this->config->global->setPlanPassMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'planPass';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function planChangeNoReview()
    {
        if($_POST)
        {
            $this->custommail->setPlanChangeNoReview();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanChangeNoReview) ? $this->config->global->setPlanChangeNoReview : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'planChangeNoReview';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function planChangeReject()
    {
        if($_POST)
        {
            $this->custommail->setPlanChangeRejectMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanChangeRejectMail) ? $this->config->global->setPlanChangeRejectMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'planChangeReject';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function planChangePass()
    {
        if($_POST)
        {
            $this->custommail->setPlanChangePassMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanChangePassMail) ? $this->config->global->setPlanChangePassMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'planChangePass';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function planChangePending()
    {
        if($_POST)
        {
            $this->custommail->setPlanChangePendingMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanChangePendingMail) ? $this->config->global->setPlanChangePendingMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'planChangePending';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function planActionTriger()
    {
        if($_POST)
        {
            $this->custommail->setPlanActionTrigerMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPlanActionTrigerMail) ? $this->config->global->setPlanActionTrigerMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'planActionTriger';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function review()
    {
        if($_POST)
        {
            $this->custommail->setReviewMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setReviewMail) ? $this->config->global->setReviewMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'review';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function reviewproblem()
    {
        if($_POST)
        {
            $this->custommail->setReviewproblemMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setReviewproblemMail) ? $this->config->global->setReviewproblemMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'reviewproblem';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function reviewmeeting(){
        if($_POST)
        {
            $this->custommail->setReviewmeetingMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setReviewmeetingMail) ? $this->config->global->setReviewmeetingMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'reviewmeeting';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function change()
    {
        if($_POST)
        {
            $this->custommail->setChangeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setChangeMail) ? $this->config->global->setChangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'change';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function entries()
    {
        if($_POST)
        {
            $this->custommail->setEntriesMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setEntriesMail) ? $this->config->global->setEntriesMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'entries';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function opinion()
    {
        if($_POST)
        {
            $this->custommail->setOpinionMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setOpinionMail) ? $this->config->global->setOpinionMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'opinion';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function opinionchange()
    {
        if($_POST)
        {
            $this->custommail->setOpinionChangeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setOpinionChangeMail) ? $this->config->global->setOpinionChangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'opinionchange';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function opinionchangenotice()
    {
        if($_POST)
        {
            $this->custommail->setOpinionChangeNoticeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setOpinionChangeNoticeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'opinionchangenotice';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function requirementownchange()
    {
        if($_POST)
        {
            $this->custommail->setRequirementOwnChangeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setRequirementOwnChangeMail) ? $this->config->global->setRequirementOwnChangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirementownchange';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function requirementnotice()
    {
        if($_POST)
        {
            $this->custommail->setRequirementNoticeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setRequirementNoticeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirementnotice';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function workflow()
    {
        if($_POST)
        {
            $this->custommail->setWorkFlowMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setWorkFlowMail) ? $this->config->global->setWorkFlowMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'workFlow';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function ajaxPreview($browseType = '')
    {
        $confObject = 'set' . ucwords($browseType) . 'Mail';
        $mailConf = isset($this->config->global->$confObject) ? $this->config->global->$confObject : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = $browseType;
        $this->view->mailConf   = $mailConf;
        if($browseType == 'review'){
            $this->app->loadLang('review');
        }
        $this->display('custommail', 'ajaxpreview' . $browseType);
    }

    public function outwarddelivery()
    {
        if($_POST)
        {
            $this->custommail->setOutwardDeliveryMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setOutwardDeliveryMail) ? $this->config->global->setOutwardDeliveryMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'outwarddelivery';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function notice(){
        if($_POST)
        {
            $this->custommail->setNoticeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }
        $mailConf = isset($this->config->global->setNoticeMail) ? $this->config->global->setNoticeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'notice';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function component()
    {
        if($_POST)
        {
            $this->custommail->setComponentMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setComponentMail) ? $this->config->global->setComponentMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'component';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 驻场支持待办
     */
    public function residentsupportbacklog()
    {
        if($_POST)
        {
            $this->custommail->setResidentSupportBacklogMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setResidentSupportBacklogMail) ? $this->config->global->setResidentSupportBacklogMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'residentsupportbacklog';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 驻场支持通知
     */
    public function residentsupportnotice()
    {
        if($_POST)
        {
            $this->custommail->setResidentSupportNoticeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setResidentSupportNoticeMail) ? $this->config->global->setResidentSupportNoticeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'residentsupportnotice';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 测试版本通知
     */
    public function build()
    {
        if($_POST)
        {
            $this->custommail->setBuildMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setBuildMail) ? $this->config->global->setBuildMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'build';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function secondorder()
    {
        if($_POST)
        {
            $this->custommail->setSecondorderMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setSecondorderMail) ? $this->config->global->setSecondorderMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'secondorder';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function defect()
    {
        if($_POST)
        {
            $this->custommail->setDefectMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setDefectMail) ? $this->config->global->setDefectMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'defect';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function defectnotice()
    {
        if($_POST)
        {
            $this->custommail->setDefectnoticeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setDefectnoticeMail) ? $this->config->global->setDefectnoticeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'defectnotice';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    // 需求收集模板
    public function demandcollection()
    {
        if($_POST)
        {
            $this->custommail->setDemandcollectionMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setDemandcollectionMail) ? $this->config->global->setDemandcollectionMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'demandcollection';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function datamanagement()
    {
        if($_POST)
        {
            $this->custommail->setDatamanagementMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setDatamanagementMail) ? $this->config->global->setDatamanagementMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'datamanagement';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function copyright()
    {
        if($_POST)
        {
            $this->custommail->setcopyrightMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setcopyrightMail) ? $this->config->global->setcopyrightMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'copyright';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function copyrightqz()
    {
        if($_POST)
        {
            $this->custommail->setcopyrightqzMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setcopyrighqztMail) ? $this->config->global->setcopyrighqztMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'copyrightqz';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function requirementchange()
    {
        if($_POST)
        {
            $this->custommail->setRequirementchangeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setRequirementchangeMail) ? $this->config->global->setRequirementchangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirementchange';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function reviewqz(){
        if($_POST)
        {
            $this->custommail->setReviewQzMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }
        $mailConf = isset($this->config->global->setReviewQzMail) ? $this->config->global->setReviewQzMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'reviewqz';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function reviewqzIsJoinMeeting(){
        if($_POST)
        {
            $this->custommail->setReviewqzIsJoinMeetingMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }
        $mailConf = isset($this->config->global->setReviewqzIsJoinMeetingMail) ? $this->config->global->setReviewqzIsJoinMeetingMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'reviewqzIsJoinMeeting';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function reviewqzFeedbackQz(){
        if($_POST)
        {
            $this->custommail->setReviewqzFeedbackQzMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }
        $mailConf = isset($this->config->global->setReviewqzFeedbackQzMail) ? $this->config->global->setReviewqzFeedbackQzMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'reviewqzFeedbackQz';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function reviewissueqz(){
        if($_POST)
        {
            $this->custommail->setReviewIssueQzMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }
        $mailConf = isset($this->config->global->setReviewIssueQzMail) ? $this->config->global->setReviewIssueQzMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'reviewissueqz';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function deptorder()
    {
        if($_POST)
        {
            $this->custommail->setDeptorderMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setDeptorderMail) ? $this->config->global->setDeptorderMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'deptorder';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function sectransfer()
    {
        if($_POST)
        {
            $this->custommail->setSectransferMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setSectransferMail) ? $this->config->global->setSectransferMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'sectransfer';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function osspchange()
    {
        if($_POST)
        {
            $this->custommail->setOsspchangeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setOsspchangeMail) ? $this->config->global->setOsspchangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'osspchange';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function weeklyreportin()
    {
        if($_POST)
        {
            $this->custommail->setWeeklyreportinMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setWeeklyreportinMail) ? $this->config->global->setWeeklyreportinMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'weeklyreportin';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function weeklyreportout()
    {
        if($_POST)
        {
            $this->custommail->setWeeklyreportoutMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setWeeklyreportoutMail) ? $this->config->global->setWeeklyreportoutMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'weeklyreportout';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function closingitem()
    {
        if($_POST)
        {
            $this->custommail->setClosingitemMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setClosingitemMail) ? $this->config->global->setClosingitemMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'closingitem';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function closingadvise()
    {
        if($_POST)
        {
            $this->custommail->setClosingadviseMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setClosingadviseMail) ? $this->config->global->setClosingadviseMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'closingadvise';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function problemOutTime()
    {
        if($_POST)
        {
            $this->custommail->setProblemOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setProblemOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'problemOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    public function problemToOutTime()
    {
        if($_POST)
        {
            $this->custommail->setProblemToOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setProblemToOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'problemToOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 问题单内部反馈-超时
     * @return void
     */
    public function inFBOutTime()
    {
        if($_POST)
        {
            $this->custommail->setInFBOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setInFBOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'inFBOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 问题单内部反馈-即将超时
     * @return void
     */
    public function inFBToTime()
    {
        if($_POST)
        {
            $this->custommail->setInFBToTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setInFBToTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'inFBToTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 问题单外部反馈-超时
     * @return void
     */
    public function outFBOutTime()
    {
        if($_POST)
        {
            $this->custommail->setOutFBOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setOutFBOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'outFBOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 问题单外部反馈-即将超时
     * @return void
     */
    public function outFBToTime()
    {
        if($_POST)
        {
            $this->custommail->setOutFBToTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setOutFBToTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'outFBToTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function demandOutTime()
    {
        if($_POST)
        {
            $this->custommail->setDemandOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setDemandOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'demandOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function demandToOutTime()
    {
        if($_POST)
        {
            $this->custommail->setDemandToOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setDemandToOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'demandToOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    //需求任务内部-超时
    public function requirementOutTime()
    {
        if($_POST)
        {
            $this->custommail->setRequirementOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setRequirementOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirementOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    //需求任务内部-即将超时
    public function requirementToOutTime()
    {
        if($_POST)
        {
            $this->custommail->setRequirementToOutTimeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setRequirementToOutTimeMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirementToOutTime';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    public function authorization()
    {
        if ($_POST) {
            $this->custommail->setAuthorizationMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }
        $mailConf = $this->config->global->setAuthorizationMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->browseType = 'authorization';
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    //需求任务外部-超时
    public function requirementOutTimeOutside()
    {
        if($_POST)
        {
            $this->custommail->setRtOutTimeOutsideMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setRtOutTimeOutsideMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirementOutTimeOutside';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    //需求任务外部-即将超时
    public function requirementToOutTimeOutside()
    {
        if($_POST)
        {
            $this->custommail->setRtToOutTimeOutsideMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = $this->config->global->setRtToOutTimeOutsideMail ?? '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requirementToOutTimeOutside';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 报工周提醒
     */
    public function workReportWeekly()
    {
        if($_POST)
        {
            $this->custommail->setWorkReportWeeklyMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setWorkReportWeeklyMail) ? $this->config->global->setWorkReportWeeklyMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'workReportWeekly';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    /**
     * 报工月提醒
     */
    public function workReportMonth()
    {
        if($_POST)
        {
            $this->custommail->setWorkReportMonthMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setWorkReportMonthMail) ? $this->config->global->setWorkReportMonthMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'workReportMonth';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
   /**
     * 请求日志失败-通知
     */
    public function requestFailLog()
    {
        if($_POST)
        {
            $this->custommail->setRequestFailLogMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setRequestFailLogMail) ? $this->config->global->setRequestFailLogMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'requestFailLog';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * @Notes:金信投产对外移交待办
     * @Date: 2024/1/10
     * @Time: 16:18
     * @Interface putproduction
     */
    public function putproduction()
    {
        if($_POST)
        {
            $this->custommail->setPutproductionMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setPutproductionMail) ? $this->config->global->setPutproductionMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'putproduction';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * @Notes:内部自建投产/变更
     * @Date: 2024/1/10
     * @Time: 16:18
     * @Interface productionchange
     */
    public function productionchange()
    {
        if($_POST)
        {
            $this->custommail->setProductionchangeMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setProductionchangeMail) ? $this->config->global->setProductionchangeMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'productionchange';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * @Notes:交付管理-征信交付
     * @Date: 2024/4/10
     * @Time: 16:18
     * @Interface credit
     */
    public function credit()
    {
        if($_POST)
        {
            $this->custommail->setCreditMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setCreditMail) ? $this->config->global->setCreditMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'credit';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 项目管理-问题
     */
    public function issue()
    {
        if($_POST)
        {
            $this->custommail->setIssueMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setIssueMail) ? $this->config->global->setIssueMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'issue';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * 项目管理-风险
     */
    public function risk()
    {
        if($_POST)
        {
            $this->custommail->setRiskMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setRiskMail) ? $this->config->global->setRiskMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'risk';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    /**
     * 环境部署工单
     */
    public function environmentorder()
    {
        if($_POST)
        {
            $this->custommail->setEnvironmentOrderMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setEnvironmentorderMail) ? $this->config->global->setEnvironmentorderMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'environmentorder';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * @Notes:现场支持
     * @Date: 2024/7/5
     * @Time: 16:18
     * @Interface credit
     */
    public function localesupport()
    {
        if($_POST)
        {
            $this->custommail->setLocaleSupportMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setLocalesupportMail) ? $this->config->global->setLocalesupportMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'localesupport';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
    /**
     * 权限申请
     */
    public function authorityapply()
    {
        if($_POST)
        {
            $this->custommail->setAuthorityapplyMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setAuthorityapplyMail) ? $this->config->global->setAuthorityapplyMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);
        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'authorityapply';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }

    /**
     * @Notes:安全门禁
     * @Date: 2024/9/4
     * @Time: 16:18
     * @Interface qualitygate
     */
    public function qualitygate()
    {
        if($_POST)
        {
            $this->custommail->setQualityGateMail();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $mailConf = isset($this->config->global->setQualitygateMail) ? $this->config->global->setQualitygateMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf = json_decode($mailConf);

        $this->view->title      = $this->lang->custommail->common;
        $this->view->position[] = $this->lang->custommail->common;
        $this->view->browseType = 'qualitygate';
        $this->view->mailConf   = $mailConf;
        $this->display();
    }
}
