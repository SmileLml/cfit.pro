<?php
/**
 * The model file of requestconf module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     requestconf
 * @version     $Id: model.php 5079 2013-07-10 00:44:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class custommailModel extends model
{
    public function setProblemMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->problem['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setProblemMail', $data);
    }

    public function setDemandMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->demand['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDemandMail', $data);
    }

    public function setRequirementMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirement['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRequirementMail', $data);
    }

    public function setModifyMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->modify['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setModifyMail', $data);
    }

    public function setModifycnccMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->modifycncc['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setModifycnccMail', $data);
    }

    public function setFixMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->fix['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setFixMail', $data);
    }

    public function setGainMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->gain['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setGainMail', $data);
    }

    public function setGainQzMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->gainqz['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setGainQzMail', $data);
    }

    public function setFixQzMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->fixqz['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setFixQzMail', $data);
    }

    public function setPlanMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanMail', $data);
    }
    public function setPlanRejectMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanRejectMail', $data);
    }

    public function setPlanPassMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanPassMail', $data);
    }

    public function setPlanChangeRejectMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanChangeRejectMail', $data);
    }

    public function setPlanChangePassMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanChangePassMail', $data);
    }

    public function setPlanChangePendingMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanChangePendingMail', $data);
    }

    public function setPlanActionTrigerMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanActionTrigerMail', $data);
    }
    public function setPlanChangeNoReview()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->plan['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPlanChangeNoReview', $data);
    }

    public function setReviewMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->review['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setReviewMail', $data);
    }

    public function setReviewproblemMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->reviewproblem['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setReviewproblemMail', $data);
    }

    /**
     * 会议评审
     */
    public function setReviewmeetingMail(){
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->reviewmeeting['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setReviewmeetingMail', $data);
    }

    public function setChangeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->change['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setChangeMail', $data);
    }

    public function setEntriesMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->entries['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setEntriesMail', $data);
    }

    public function setOpinionMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->opinion['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setOpinionMail', $data);
    }

    public function setOpinionChangeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->opinion['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setOpinionChangeMail', $data);
    }
    public function setOpinionChangeNoticeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->opinionchangenotice['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setOpinionChangeNoticeMail', $data);
    }

    public function setWorkFlowMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->workflow['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setWorkFlowMail', $data);
    }

    public function setOutwardDeLIVERYMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->outwarddelivery['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setOutwardDeliveryMail', $data);
    }
    //邮件通知
    public function setNoticeMail(){
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->notice['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setNoticeMail', $data);
    }

    public function setComponentMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->component['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setComponentMail', $data);
    }

    /**
     *设置驻场支持待办
     */
    public function setResidentSupportBacklogMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->residentsupportbacklog['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setResidentSupportBacklogMail', $data);
    }

    /**
     * 设置驻场支持通知
     */
    public function setResidentSupportNoticeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->residentsupportnotice['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setResidentSupportNoticeMail', $data);
    }
    /**
     * 测试版本模板
     */
    public function setBuildMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->build['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setBuildMail', $data);
    }

    /**
     * 二线工单
     */
    public function setSecondorderMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->secondorder['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setSecondorderMail', $data);
    }
    /**
     * 清总缺陷
     */
    public function setDefectMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->defect['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDefectMail', $data);
    }

    /**
     * 清总缺陷-通知
     */
    public function setDefectnoticeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->defectnotice['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDefectnoticeMail', $data);
    }

    // 需求收集模板
    public function setDemandcollectionMail(){
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->demandcollection['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);

        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDemandcollectionMail', $data);
    }

    public function setDatamanagementMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->datamanagement['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDatamanagementMail', $data);
    }
    public function setRequirementchangeMail(){
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirementchange['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRequirementchangeMail', $data);
    }

    //外部需求任务 自建变更单（非清总同步）
    public function setRequirementOwnChangeMail(){
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirementownchange['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRequirementOwnChangeMail', $data);
    }
    public function setRequirementNoticeMail(){
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirementnotice['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRequirementNoticeMail', $data);
    }

    public function setReviewQzMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->reviewqz['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setReviewQzMail', $data);
    }

    public function setReviewqzIsJoinMeetingMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->reviewqzisjoinmeeting['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setReviewqzIsJoinMeetingMail', $data);
    }

    public function setReviewqzFeedbackQzMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->setReviewqzFeedbackQzMail['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setReviewqzFeedbackQzMail', $data);
    }

    public function setReviewIssueQzMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->reviewissueqz['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setReviewIssueQzMail', $data);
    }

    /**
     * 部门工单
     */
    public function setDeptorderMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->deptorder['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDeptorderMail', $data);
    }
    public function setcopyrightMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->copyrigh['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setcopyrightMail', $data);
    }
    public function setcopyrightqzMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->copyrighqz['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setcopyrighqztMail', $data);
    }

    /**
     * 对外移交
     */
    public function setSectransferMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->sectransfer['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setSectransferMail', $data);
    }

    /**
     * OSSP变更审批
     */
    public function setOsspchangeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->osspchange['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setOsspchangeMail', $data);
    }

    /**
     * 内部项目周报
     */
    public function setWeeklyreportinMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->weeklyreportin['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setWeeklyreportinMail', $data);
    }

    /**
     * (外部)项目/任务周报
     */
    public function setWeeklyreportoutMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->weeklyreportout['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setWeeklyreportoutMail', $data);
    }

    public function setClosingitemMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->closingitem['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setClosingitemMail', $data);
    }

    public function setClosingadviseMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->closingadvise['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setClosingadviseMail', $data);
    }

    public function setProblemOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->problemOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setProblemOutTimeMail', $data);
    }

    public function setProblemToOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->problemToOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setProblemToOutTimeMail', $data);
    }

    public function setInFBOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->inFBOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setInFBOutTimeMail', $data);
    }

    public function setInFBToTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->inFBToTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setInFBToTimeMail', $data);
    }

    public function setOutFBOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->outFBOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setOutFBOutTimeMail', $data);
    }

    public function setOutFBToTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->outFBToTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setOutFBToTimeMail', $data);
    }

    public function setDemandOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->demandOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDemandOutTimeMail', $data);
    }

    public function setDemandToOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->demandToOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setDemandToOutTimeMail', $data);
    }

    //需求任务内部-超时
    public function setRequirementOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirementOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRequirementOutTimeMail', $data);
    }
    //需求任务内部-即将超时
    public function setRequirementToOutTimeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirementToOutTime['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRequirementToOutTimeMail', $data);
    }

    public function setAuthorizationMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->authorization['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setAuthorizationMail', $data);
    }

    //需求任务外部-超时
    public function setRtOutTimeOutsideMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirementOutTimeOutside['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRtOutTimeOutsideMail', $data);
    }
    //需求任务外部-即将超时
    public function setRtToOutTimeOutsideMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requirementToOutTimeOutside['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRtToOutTimeOutsideMail', $data);
    }

    /**
     * 报工周提醒
     */
    public function setWorkReportWeeklyMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->workreportweekly['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setWorkReportWeeklyMail', $data);
    }
    /**
     * 报工月提醒
     */
    public function setWorkReportMonthMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->workreportmonth['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setWorkReportMonthMail', $data);
    }
   /**
     *请求日志失败-通知
     */
    public function setRequestFailLogMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->requestlog['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRequestFailLogMail', $data);
    }

    /**
     * 金信投产待办
     */
    public function setPutproductionMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->putproduction['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setPutproductionMail', $data);
    }

    /**
     * 内部自建投产/变更
     */
    public function setProductionchangeMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->productionchange['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setProductionchangeMail', $data);
    }

    /**
     * 征信交付
     */
    public function setCreditMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->credit['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setCreditMail', $data);
    }

    /**
     *项目管理-问题
     */
    public function setIssueMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->issue['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setIssueMail', $data);
    }

    /**
     *项目管理-风险
     */
    public function setRiskMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->issue['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setRiskMail', $data);
    }
    /**
     *环境部署工单
     */
    public function setEnvironmentOrderMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->issue['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setEnvironmentorderMail', $data);
    }
    /**
     * 现场支持
     */
    public function setLocaleSupportMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->localesupport['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setLocalesupportMail', $data);
    }
    public function setAuthorityapplyMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->issue['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setAuthorityapplyMail', $data);
    }
    /**
     * 安全门禁
     */
    public function setQualityGateMail()
    {
        $data = fixer::input('post')->stripTags($this->config->custommail->editor->qualitygate['id'], $this->config->allowedTags)->remove('uid')->get();
        $data = json_encode($data);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.setQualitygateMail', $data);
    }
 }
