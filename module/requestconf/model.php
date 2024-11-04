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
class requestconfModel extends model
{
    public function setPush()
    {
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.pushUrl', $this->post->pushUrl);
        $this->setting->setItem('system.common.global.pushAppId', $this->post->pushAppId);
        $this->setting->setItem('system.common.global.pushAppSecret', $this->post->pushAppSecret);
        $this->setting->setItem('system.common.global.pushUsername', $this->post->pushUsername);
        $this->setting->setItem('system.common.global.pushEnable', $this->post->pushEnable);

        $this->setting->setItem('system.common.global.jxProblemReFeedbackUrl', $this->post->jxProblemReFeedbackUrl);
        $this->setting->setItem('system.common.global.jxProblemRejectFeedbackUrl', $this->post->jxProblemRejectFeedbackUrl);
        $this->setting->setItem('system.common.global.jxProblemFeedbackUrl', $this->post->jxProblemFeedbackUrl);
        $this->setting->setItem('system.common.global.jxProblemFeedbackAppId', $this->post->jxProblemFeedbackAppId);
        $this->setting->setItem('system.common.global.jxProblemFeedbackAppSecret', $this->post->jxProblemFeedbackAppSecret);
//        $this->setting->setItem('system.common.global.jxProblemFeedbackUsername', $this->post->jxProblemFeedbackUsername);
        $this->setting->setItem('system.common.global.jxProblemFeedbackEnable', $this->post->jxProblemFeedbackEnable);
        $this->setting->setItem('system.common.global.jxProblemFileIP', $this->post->jxProblemFileIP);


        $this->setting->setItem('system.common.global.pushProblemFeedbackUrl', $this->post->pushProblemFeedbackUrl);
        $this->setting->setItem('system.common.global.pushProblemFeedbackAppId', $this->post->pushProblemFeedbackAppId);
        $this->setting->setItem('system.common.global.pushProblemFeedbackAppSecret', $this->post->pushProblemFeedbackAppSecret);
        $this->setting->setItem('system.common.global.pushProblemFeedbackUsername', $this->post->pushProblemFeedbackUsername);
        $this->setting->setItem('system.common.global.pushProblemFeedbackEnable', $this->post->pushProblemFeedbackEnable);
        $this->setting->setItem('system.common.global.pushProblemFileIP', $this->post->pushProblemFileIP);
        
        $this->setting->setItem('system.common.global.pushInfoGainUrl', $this->post->pushInfoGainUrl);
        $this->setting->setItem('system.common.global.pushInfoGainAppId', $this->post->pushInfoGainAppId);
        $this->setting->setItem('system.common.global.pushInfoGainAppSecret', $this->post->pushInfoGainAppSecret);
        $this->setting->setItem('system.common.global.pushInfoGainUsername', $this->post->pushInfoGainUsername);
        $this->setting->setItem('system.common.global.pushInfoGainEnable', $this->post->pushInfoGainEnable);
        $this->setting->setItem('system.common.global.pushInfoGainFileIP', $this->post->pushInfoGainFileIP);

        $this->setting->setItem('system.common.global.copyrightqzUrl', $this->post->copyrightqzUrl);
        $this->setting->setItem('system.common.global.copyrightqzAppId', $this->post->copyrightqzAppId);
        $this->setting->setItem('system.common.global.copyrightqzAppSecret', $this->post->copyrightqzAppSecret);
        $this->setting->setItem('system.common.global.copyrightqzUsername', $this->post->copyrightqzUsername);
        $this->setting->setItem('system.common.global.copyrightqzEnable', $this->post->copyrightqzEnable);
        $this->setting->setItem('system.common.global.copyrightqzFileIP', $this->post->copyrightqzFileIP);

        $this->setting->setItem('system.common.global.destroyInfoGainUrl', $this->post->destroyInfoGainUrl);
        $this->setting->setItem('system.common.global.destroyInfoGainAppId', $this->post->destroyInfoGainAppId);
        $this->setting->setItem('system.common.global.destroyInfoGainAppSecret', $this->post->destroyInfoGainAppSecret);
        $this->setting->setItem('system.common.global.destroyInfoGainUsername', $this->post->destroyInfoGainUsername);
        $this->setting->setItem('system.common.global.destroyInfoGainEnable', $this->post->destroyInfoGainEnable);
        $this->setting->setItem('system.common.global.destroyInfoGainFileIP', $this->post->destroyInfoGainFileIP);

        $this->setting->setItem('system.common.global.pushModifycnccUrl', $this->post->pushModifycnccUrl);
        $this->setting->setItem('system.common.global.modifycnccstateUrl', $this->post->modifycnccstateUrl);
        $this->setting->setItem('system.common.global.pushModifycnccAppId', $this->post->pushModifycnccAppId);
        $this->setting->setItem('system.common.global.pushModifycnccAppSecret', $this->post->pushModifycnccAppSecret);
        $this->setting->setItem('system.common.global.pushModifycnccUsername', $this->post->pushModifycnccUsername);
        $this->setting->setItem('system.common.global.pushModifycnccEnable', $this->post->pushModifycnccEnable);
        $this->setting->setItem('system.common.global.pushModifycnccFileIP', $this->post->pushModifycnccFileIP);

        $this->setting->setItem('system.common.global.downloadIP',      $this->post->downloadIP);
        $this->setting->setItem('system.common.global.testingRequestPushUrl',      $this->post->testingRequestPushUrl);
        $this->setting->setItem('system.common.global.outwardDeliveryRevoke',      $this->post->outwardDeliveryRevoke);
        $this->setting->setItem('system.common.global.productEnrollPushUrl',      $this->post->productEnrollPushUrl);
        $this->setting->setItem('system.common.global.pushOutwarddeliveryEnable', $this->post->pushOutwarddeliveryEnable);
        $this->setting->setItem('system.common.global.pushOutwarddeliveryAppId', $this->post->pushOutwarddeliveryAppId);
        $this->setting->setItem('system.common.global.pushOutwarddeliveryAppSecret', $this->post->pushOutwarddeliveryAppSecret);
        $this->setting->setItem('system.common.global.pushOutwarddeliveryUsername', $this->post->pushOutwarddeliveryUsername);
        $this->setting->setItem('system.common.global.pushOutwarddeliveryFileIP', $this->post->pushOutwarddeliveryFileIP);
        $this->setting->setItem('system.common.global.outwardDeliveryCron', $this->post->outwardDeliveryCron);

        $this->setting->setItem('system.common.global.modifyInitiatePushUrl',      $this->post->modifyInitiatePushUrl);
        $this->setting->setItem('system.common.global.modifyCommitPushUrl',      $this->post->modifyCommitPushUrl);
        $this->setting->setItem('system.common.global.modifyClosePushUrl',      $this->post->modifyClosePushUrl);
        $this->setting->setItem('system.common.global.pushModifyEnable', $this->post->pushModifyEnable);
        $this->setting->setItem('system.common.global.pushModifyAppId', $this->post->pushModifyAppId);
        $this->setting->setItem('system.common.global.pushModifyAppSecret', $this->post->pushModifyAppSecret);
        $this->setting->setItem('system.common.global.pushModifyUsername', $this->post->pushModifyUsername);
        $this->setting->setItem('system.common.global.pushModifyFileIP', $this->post->pushModifyFileIP);

        $this->setting->setItem('system.common.global.sftpServerIP', $this->post->sftpServerIP); //2022-10-10

        $this->setting->setItem('system.common.global.defectFeedbackUrl',      $this->post->defectFeedbackUrl);
        $this->setting->setItem('system.common.global.defectReFeedbackUrl',      $this->post->defectReFeedbackUrl);
        $this->setting->setItem('system.common.global.pushDefectAppId', $this->post->pushDefectAppId);
        $this->setting->setItem('system.common.global.pushDefectAppSecret', $this->post->pushDefectAppSecret);
        $this->setting->setItem('system.common.global.pushDefectEnable', $this->post->pushDefectEnable);
        //2022-12-1主场支持推送值班日志
        $this->setting->setItem('system.common.global.dutyLogPushUrl', $this->post->dutyLogPushUrl);
        $this->setting->setItem('system.common.global.pushDutyLogAppId', $this->post->pushDutyLogAppId);
        $this->setting->setItem('system.common.global.pushDutyLogAppSecret', $this->post->pushDutyLogAppSecret);
        //2023-02-17 其他服务配置
        $this->setting->setItem('system.common.global.resetPasswordIp', $this->post->resetPasswordIp);
        $this->setting->setItem('system.common.global.jenkinsServerIP', $this->post->jenkinsServerIP);
        $this->setting->setItem('system.common.global.sonarcubeServerIP', $this->post->sonarcubeServerIP);
        $this->setting->setItem('system.common.global.networkDiskServerIP', $this->post->networkDiskServerIP);
        //2023-03-27 清总评审接口地址
        $this->setting->setItem('system.common.global.feedbackExpertsUrl', $this->post->feedbackExpertsUrl);
        $this->setting->setItem('system.common.global.feedbackExpertsAppId', $this->post->feedbackExpertsAppId);
        $this->setting->setItem('system.common.global.feedbackExpertsAppSecret', $this->post->feedbackExpertsAppSecret);
        $this->setting->setItem('system.common.global.feedbackExpertsEnable', $this->post->feedbackExpertsEnable);
        $this->setting->setItem('system.common.global.feedbackUpDataExpertsUrl', $this->post->feedbackUpDataExpertsUrl);
        $this->setting->setItem('system.common.global.feedbackUpDataExpertsAppId', $this->post->feedbackUpDataExpertsAppId);
        $this->setting->setItem('system.common.global.feedbackUpDataExpertsAppSecret', $this->post->feedbackUpDataExpertsAppSecret);
        $this->setting->setItem('system.common.global.feedbackUpDataExpertsEnable', $this->post->feedbackUpDataExpertsEnable);
        $this->setting->setItem('system.common.global.feedbackQzIssuesUrl', $this->post->feedbackQzIssuesUrl);
        $this->setting->setItem('system.common.global.feedbackQzIssuesAppId', $this->post->feedbackQzIssuesAppId);
        $this->setting->setItem('system.common.global.feedbackQzIssuesAppSecret', $this->post->feedbackQzIssuesAppSecret);
        $this->setting->setItem('system.common.global.feedbackQzIssuesEnable', $this->post->feedbackQzIssuesEnable);
        //问题单当前进展
        $this->setting->setItem('system.common.global.pushProblemCommentUrl',      $this->post->pushProblemCommentUrl);
        $this->setting->setItem('system.common.global.pushProblemCommentAppId', $this->post->pushProblemCommentAppId);
        $this->setting->setItem('system.common.global.pushProblemCommentAppSecret', $this->post->pushProblemCommentAppSecret);
        $this->setting->setItem('system.common.global.pushProblemCommentEnable', $this->post->pushProblemCommentEnable);
        //2023-05-08 二线工单接口地址
        $this->setting->setItem('system.common.global.secondorderFeedbackUrl', $this->post->secondorderFeedbackUrl);
        $this->setting->setItem('system.common.global.universalFeedbackUrl', $this->post->universalFeedbackUrl);
        $this->setting->setItem('system.common.global.secondorderAppId', $this->post->secondorderAppId);
        $this->setting->setItem('system.common.global.secondorderAppSecret', $this->post->secondorderAppSecret);
        $this->setting->setItem('system.common.global.secondorderEnable', $this->post->secondorderEnable);
        $this->setting->setItem('system.common.global.secondorderFileIP', $this->post->secondorderFileIP);
        $this->setting->setItem('system.common.global.secondorderSftpServerIP', $this->post->secondorderSftpServerIP);
        //2023-05-08 金信任务工单接口地址
        $this->setting->setItem('system.common.global.secondorderFeedbackUrlJx', $this->post->secondorderFeedbackUrlJx);
        $this->setting->setItem('system.common.global.secondorderAppIdJx', $this->post->secondorderAppIdJx);
        $this->setting->setItem('system.common.global.secondorderAppSecretJx', $this->post->secondorderAppSecretJx);
        $this->setting->setItem('system.common.global.secondorderEnableJx', $this->post->secondorderEnableJx);
        $this->setting->setItem('system.common.global.secondorderFileIPJx', $this->post->secondorderFileIPJx);
        $this->setting->setItem('system.common.global.secondorderSftpServerIPJx', $this->post->secondorderSftpServerIPJx);
        //2023-05-08 对外移交接口地址
        $this->setting->setItem('system.common.global.sectransferFeedbackUrl', $this->post->sectransferFeedbackUrl);
        $this->setting->setItem('system.common.global.sectransferAppId', $this->post->sectransferAppId);
        $this->setting->setItem('system.common.global.sectransferAppSecret', $this->post->sectransferAppSecret);
        $this->setting->setItem('system.common.global.sectransferEnable', $this->post->sectransferEnable);
        $this->setting->setItem('system.common.global.sectransferFileIP', $this->post->sectransferFileIP);
        $this->setting->setItem('system.common.global.sectransferSftpServerIP', $this->post->sectransferSftpServerIP);

        //2023-06-06 清总周报新建接口地址
        $this->setting->setItem('system.common.global.weeklyreportPushUrl', $this->post->weeklyreportPushUrl);
        $this->setting->setItem('system.common.global.weeklyreportPushAppId', $this->post->weeklyreportPushAppId);
        $this->setting->setItem('system.common.global.weeklyreportPushAppSecret', $this->post->weeklyreportPushAppSecret);
        $this->setting->setItem('system.common.global.weeklyreportPushEnable', $this->post->weeklyreportPushEnable);
        //2023-06-06 清总周报修改反馈说明接口地址
        $this->setting->setItem('system.common.global.weeklyreportPushMarkUrl', $this->post->weeklyreportPushMarkUrl);
        $this->setting->setItem('system.common.global.weeklyreportPushMarkAppId', $this->post->weeklyreportPushMarkAppId);
        $this->setting->setItem('system.common.global.weeklyreportPushMarkAppSecret', $this->post->weeklyreportPushMarkAppSecret);
        $this->setting->setItem('system.common.global.weeklyreportPushMarkEnable', $this->post->weeklyreportPushMarkEnable);

        //2023-06-20 人力接口对接地址
        $this->setting->setItem('system.common.global.pushProjectInfoUrl', $this->post->pushProjectInfoUrl);
        $this->setting->setItem('system.common.global.pushProjectInfoAppId', $this->post->pushProjectInfoAppId);
        $this->setting->setItem('system.common.global.pushProjectInfoAppSecret', $this->post->pushProjectInfoAppSecret);
        $this->setting->setItem('system.common.global.pushProjectInfoEnable', $this->post->pushProjectInfoEnable);

        //2024-01-23 金信投产对接地址
        $this->setting->setItem('system.common.global.pushPutproductionUrl', $this->post->pushPutproductionUrl);
        $this->setting->setItem('system.common.global.pushPutproductionAppId', $this->post->pushPutproductionAppId);
        $this->setting->setItem('system.common.global.pushPutproductionAppSecret', $this->post->pushPutproductionAppSecret);
        $this->setting->setItem('system.common.global.pushPutproductionEnable', $this->post->pushPutproductionEnable);

        // 推送审核人员至数字金科
        $this->setting->setItem('system.common.global.pushMessageUrl', $this->post->pushMessageUrl);
        $this->setting->setItem('system.common.global.pushMsgEnable', $this->post->pushMsgEnable);
        $this->setting->setItem('system.common.global.pushMsgAppid', $this->post->pushMsgAppid);
        $this->setting->setItem('system.common.global.pushMsgAppSecret', $this->post->pushMsgAppSecret);
        $this->setting->setItem('system.common.global.h5url', $this->post->h5url);
        $this->setting->setItem('system.common.global.pcurl', $this->post->pcurl);

        //cmdb同步接口
        $this->setting->setItem('system.common.global.pushCmdbDealUrl', $this->post->pushCmdbDealUrl);
        $this->setting->setItem('system.common.global.pushAppInfoUrl', $this->post->pushAppInfoUrl);
        $this->setting->setItem('system.common.global.pushCmdbAppId', $this->post->pushCmdbAppId);
        $this->setting->setItem('system.common.global.pushCmdbAppSecret', $this->post->pushCmdbAppSecret);
        $this->setting->setItem('system.common.global.pushCmdbAppEnable', $this->post->pushCmdbAppEnable);
       
        //推送发布至安全资产平台
        $this->setting->setItem('system.common.global.pushSafeAssetUrl', $this->post->pushSafeAssetUrl);
        $this->setting->setItem('system.common.global.pushSafeAssetAppId', $this->post->pushSafeAssetAppId);
        $this->setting->setItem('system.common.global.pushSafeAssetAppSecret', $this->post->pushSafeAssetAppSecret);
        $this->setting->setItem('system.common.global.pushSafeAssetEnable', $this->post->pushSafeAssetEnable);
    }
}
