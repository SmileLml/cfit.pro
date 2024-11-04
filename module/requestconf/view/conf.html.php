<?php
/**
 * The browse view of requestconf module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author
 * @package     browse
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->requestconf->common?></h2>
  </div>
  <form class="load-indicator main-form form-ajax" method='post'>
  <table class="table table-form">
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushUrl?></th>
      <td><?php echo html::input('pushUrl', $config->global->pushUrl, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
      <td><?php echo html::input('pushAppId', $config->global->pushAppId, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
      <td><?php echo html::input('pushAppSecret', $config->global->pushAppSecret, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushUsername?></th>
      <td><?php echo html::input('pushUsername', $config->global->pushUsername, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
      <td><?php echo html::radio('pushEnable', $lang->requestconf->pushEnableList, $config->global->pushEnable)?></td>
      <td></td>
    </tr>
<!--      增加问题池推送反馈单的请求配置-->
    <tr>
      <th class='w-120px'><h4><?php echo $lang->requestconf->pushProblemFeedback?></h4></th>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushUrl?></th>
      <td><?php echo html::input('pushProblemFeedbackUrl', $config->global->pushProblemFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
      <td><?php echo html::input('pushProblemFeedbackAppId', $config->global->pushProblemFeedbackAppId, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
      <td><?php echo html::input('pushProblemFeedbackAppSecret', $config->global->pushProblemFeedbackAppSecret, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushUsername?></th>
      <td><?php echo html::input('pushProblemFeedbackUsername', $config->global->pushProblemFeedbackUsername, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
      <td><?php echo html::input('pushProblemFileIP', $config->global->pushProblemFileIP, "class='form-control' autocomplete='off'")?></td>
      <td></td>
    </tr>
    <tr>
      <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
      <td><?php echo html::radio('pushProblemFeedbackEnable', $lang->requestconf->pushEnableList, $config->global->pushProblemFeedbackEnable)?></td>
      <td></td>
    </tr>


      <!-- 增加数据获取推送数据获取单的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->pushInfoGain?></h4></th>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushUrl?></th>
          <td><?php echo html::input('pushInfoGainUrl', $config->global->pushInfoGainUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushInfoGainAppId', $config->global->pushInfoGainAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushInfoGainAppSecret', $config->global->pushInfoGainAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushUsername?></th>
          <td><?php echo html::input('pushInfoGainUsername', $config->global->pushInfoGainUsername, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td>
              <?php echo html::input('pushInfoGainFileIP', $config->global->pushInfoGainFileIP, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushInfoGainEnable', $lang->requestconf->pushEnableList, $config->global->pushInfoGainEnable)?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->destroyInfoGain?></h4></th>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushUrl?></th>
          <td><?php echo html::input('destroyInfoGainUrl', $config->global->destroyInfoGainUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('destroyInfoGainAppId', $config->global->destroyInfoGainAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('destroyInfoGainAppSecret', $config->global->destroyInfoGainAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushUsername?></th>
          <td><?php echo html::input('destroyInfoGainUsername', $config->global->destroyInfoGainUsername, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td>
              <?php echo html::input('destroyInfoGainFileIP', $config->global->destroyInfoGainFileIP, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('destroyInfoGainEnable', $lang->requestconf->pushEnableList, $config->global->destroyInfoGainEnable)?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->copyrightqz?></h4></th>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushUrl?></th>
          <td><?php echo html::input('copyrightqzUrl', $config->global->copyrightqzUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('copyrightqzAppId', $config->global->copyrightqzAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('copyrightqzAppSecret', $config->global->copyrightqzAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushUsername?></th>
          <td><?php echo html::input('copyrightqzUsername', $config->global->copyrightqzUsername, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td>
              <?php echo html::input('copyrightqzFileIP', $config->global->copyrightqzFileIP, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('copyrightqzEnable', $lang->requestconf->pushEnableList, $config->global->copyrightqzEnable)?></td>
          <td></td>
      </tr>



    <tr>
    <!-- modifycncc-->
    <tr>
        <th class='w-120px'><h4><?php echo $lang->requestconf->pushModifycncc?></h4></th>
    </tr>
    <tr>
        <th class='w-120px'><?php echo $lang->requestconf->pushUrl?></th>
        <td><?php echo html::input('pushModifycnccUrl', $config->global->pushModifycnccUrl, "class='form-control' autocomplete='off'")?></td>
        <td></td>
    </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->modifycnccstateUrl?></th>
          <td><?php echo html::input('modifycnccstateUrl', $config->global->modifycnccstateUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
    <tr>
        <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
        <td><?php echo html::input('pushModifycnccAppId', $config->global->pushModifycnccAppId, "class='form-control' autocomplete='off'")?></td>
        <td></td>
    </tr>
    <tr>
        <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
        <td><?php echo html::input('pushModifycnccAppSecret', $config->global->pushModifycnccAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
    </tr>
    <tr>
        <th class='w-120px'><?php echo $lang->requestconf->pushUsername?></th>
        <td><?php echo html::input('pushModifycnccUsername', $config->global->pushModifycnccUsername, "class='form-control' autocomplete='off'")?></td>
        <td></td>
    </tr>
    <tr>
        <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
        <td>
            <?php echo html::input('pushModifycnccFileIP', $config->global->pushModifycnccFileIP, "class='form-control' autocomplete='off'")?>
        <td></td>
    </tr>
    <tr>
        <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
        <td><?php echo html::radio('pushModifycnccEnable', $lang->requestconf->pushEnableList, $config->global->pushModifycnccEnable)?></td>
        <td></td>
    </tr>
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->outwardDelivery?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->downloadIP?></th>
          <td>
              <?php echo html::input('downloadIP', $config->global->downloadIP, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->productEnrollPushUrl?></th>
          <td>
              <?php echo html::input('productEnrollPushUrl', $config->global->productEnrollPushUrl, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->testingRequestPushUrl?></th>
          <td>
              <?php echo html::input('testingRequestPushUrl', $config->global->testingRequestPushUrl, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->outwardDeliveryRevoke?></th>
          <td>
              <?php echo html::input('outwardDeliveryRevoke', $config->global->outwardDeliveryRevoke, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushOutwarddeliveryAppId', $config->global->pushOutwarddeliveryAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushOutwarddeliveryAppSecret', $config->global->pushOutwarddeliveryAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushUsername?></th>
          <td><?php echo html::input('pushOutwarddeliveryUsername', $config->global->pushOutwarddeliveryUsername, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td>
              <?php echo html::input('pushOutwarddeliveryFileIP', $config->global->pushOutwarddeliveryFileIP, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushOutwarddeliveryEnable', $lang->requestconf->pushEnableList, $config->global->pushOutwarddeliveryEnable)?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->outwardDeliveryCron;?></th>
          <td><?php echo html::radio('outwardDeliveryCron', $lang->requestconf->outwardDeliveryCronList, $config->global->outwardDeliveryCron)?></td>
          <td></td>
      </tr>

      <!--      增加问题推送金信反馈单的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->jxProblem?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->jxProblemFeedback?></th>
          <td><?php echo html::input('jxProblemFeedbackUrl', $config->global->jxProblemFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->jxProblemReFeedback?></th>
          <td><?php echo html::input('jxProblemReFeedbackUrl', $config->global->jxProblemReFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->jxProblemRejectFeedback?></th>
          <td><?php echo html::input('jxProblemRejectFeedbackUrl', $config->global->jxProblemRejectFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('jxProblemFeedbackAppId', $config->global->jxProblemFeedbackAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('jxProblemFeedbackAppSecret', $config->global->jxProblemFeedbackAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
<!--      <tr>-->
<!--          <th class='w-120px'>--><?php //echo $lang->requestconf->pushUsername?><!--</th>-->
<!--          <td>--><?php //echo html::input('jxProblemFeedbackUsername', $config->global->jxProblemFeedbackUsername, "class='form-control' autocomplete='off'")?><!--</td>-->
<!--          <td></td>-->
<!--      </tr>-->
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td><?php echo html::input('jxProblemFileIP', $config->global->jxProblemFileIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('jxProblemFeedbackEnable', $lang->requestconf->pushEnableList, $config->global->jxProblemFeedbackEnable)?></td>
          <td></td>
      </tr>

      <!--      增加金信生产变更的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->modify?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->modifyInitiatePushUrl?></th>
          <td><?php echo html::input('modifyInitiatePushUrl', $config->global->modifyInitiatePushUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->modifyCommitPushUrl?></th>
          <td><?php echo html::input('modifyCommitPushUrl', $config->global->modifyCommitPushUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->modifyClosePushUrl?></th>
          <td><?php echo html::input('modifyClosePushUrl', $config->global->modifyClosePushUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushModifyAppId', $config->global->pushModifyAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushModifyAppSecret', $config->global->pushModifyAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td><?php echo html::input('pushModifyFileIP', $config->global->pushModifyFileIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushModifyEnable', $lang->requestconf->pushEnableList, $config->global->pushModifyEnable)?></td>
          <td></td>
      </tr>
<!--      2022-10-10-->
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->sftpServerIP?></th>
          <td>
              <?php echo html::input('sftpServerIP', $config->global->sftpServerIP, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
<!--      2022-12-01 驻场支持推送值班日志-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->dutyLog?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->dutyLogPushUrl?></th>
          <td><?php echo html::input('dutyLogPushUrl', $config->global->dutyLogPushUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushDutyLogAppId', $config->global->pushDutyLogAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushDutyLogAppSecret', $config->global->pushDutyLogAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <!--      清总缺陷的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->defect?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->defectFeedbackUrl?></th>
          <td><?php echo html::input('defectFeedbackUrl', $config->global->defectFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->defectReFeedbackUrl?></th>
          <td><?php echo html::input('defectReFeedbackUrl', $config->global->defectReFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushDefectAppId', $config->global->pushDefectAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushDefectAppSecret', $config->global->pushDefectAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushDefectEnable', $lang->requestconf->pushEnableList, $config->global->pushDefectEnable)?></td>
          <td></td>
      </tr>

      <!--      问题单当前进展的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->pushProblemComment?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushProblemCommentUrl?></th>
          <td><?php echo html::input('pushProblemCommentUrl', $config->global->pushProblemCommentUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushProblemCommentAppId', $config->global->pushProblemCommentAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushProblemCommentAppSecret', $config->global->pushProblemCommentAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushProblemCommentEnable', $lang->requestconf->pushEnableList, $config->global->pushDefectEnable)?></td>
          <td></td>
      </tr>

      <!--      二线工单的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->secondorder?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->secondorderFeedbackUrl?></th>
          <td><?php echo html::input('secondorderFeedbackUrl', $config->global->secondorderFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->universalFeedbackUrl?></th>
          <td><?php echo html::input('universalFeedbackUrl', $config->global->universalFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('secondorderAppId', $config->global->secondorderAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('secondorderAppSecret', $config->global->secondorderAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td><?php echo html::input('secondorderFileIP', $config->global->secondorderFileIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->sftpServerIP?></th>
          <td><?php echo html::input('secondorderSftpServerIP', $config->global->secondorderSftpServerIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('secondorderEnable', $lang->requestconf->pushEnableList, $config->global->secondorderEnable)?></td>
          <td></td>
      </tr>
      <!--      金信任务工单的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->secondorderJx?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->secondorderFeedbackUrl?></th>
          <td><?php echo html::input('secondorderFeedbackUrlJx', $config->global->secondorderFeedbackUrlJx, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('secondorderAppIdJx', $config->global->secondorderAppIdJx, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('secondorderAppSecretJx', $config->global->secondorderAppSecretJx, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td><?php echo html::input('secondorderFileIPJx', $config->global->secondorderFileIPJx, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->sftpServerIP?></th>
          <td><?php echo html::input('secondorderSftpServerIPJx', $config->global->secondorderSftpServerIPJx, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('secondorderEnableJx', $lang->requestconf->pushEnableList, $config->global->secondorderEnableJx)?></td>
          <td></td>
      </tr>

      <!--      对外移交的请求配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->protransfer?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->protransferFeedbackUrl?></th>
          <td><?php echo html::input('sectransferFeedbackUrl', $config->global->sectransferFeedbackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('sectransferAppId', $config->global->sectransferAppId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('sectransferAppSecret', $config->global->sectransferAppSecret, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->FileIP?></th>
          <td><?php echo html::input('sectransferFileIP', $config->global->sectransferFileIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->sftpServerIP?></th>
          <td><?php echo html::input('sectransferSftpServerIP', $config->global->sectransferSftpServerIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('sectransferEnable', $lang->requestconf->pushEnableList, $config->global->sectransferEnable)?></td>
          <td></td>
      </tr>

      <!--      周报新建接口-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->weeklyreportPush?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->weeklyreportPushUrl;?></th>
          <td><?php echo html::input('weeklyreportPushUrl', isset($config->global->weeklyreportPushUrl) ? $config->global->weeklyreportPushUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('weeklyreportPushAppId', isset($config->global->weeklyreportPushAppId) ? $config->global->weeklyreportPushAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('weeklyreportPushAppSecret', isset($config->global->weeklyreportPushAppSecret) ? $config->global->weeklyreportPushAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('weeklyreportPushEnable', $lang->requestconf->pushEnableList, $config->global->weeklyreportPushEnable)?></td>
          <td></td>
      </tr>
      <!--      周报修改反馈说明接口-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->weeklyreportPushMark?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->weeklyreportPushUrl;?></th>
          <td><?php echo html::input('weeklyreportPushMarkUrl', isset($config->global->weeklyreportPushMarkUrl) ? $config->global->weeklyreportPushMarkUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('weeklyreportPushMarkAppId', isset($config->global->weeklyreportPushMarkAppId) ? $config->global->weeklyreportPushMarkAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('weeklyreportPushMarkAppSecret', isset($config->global->weeklyreportPushMarkAppSecret) ? $config->global->weeklyreportPushMarkAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('weeklyreportPushMarkEnable', $lang->requestconf->pushEnableList, $config->global->weeklyreportPushMarkEnable)?></td>
          <td></td>
      </tr>
      <!-- 清总评审接口-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->reviewqz?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->feedbackExperts;?></th>
          <td><?php echo html::input('feedbackExpertsUrl', isset($config->global->feedbackExpertsUrl) ? $config->global->feedbackExpertsUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('feedbackExpertsAppId', isset($config->global->feedbackExpertsAppId) ? $config->global->feedbackExpertsAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('feedbackExpertsAppSecret', isset($config->global->feedbackExpertsAppSecret) ? $config->global->feedbackExpertsAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('feedbackExpertsEnable', $lang->requestconf->pushEnableList, $config->global->feedbackExpertsEnable)?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->feedbackUpDataExperts;?></th>
          <td><?php echo html::input('feedbackUpDataExpertsUrl', isset($config->global->feedbackUpDataExpertsUrl) ? $config->global->feedbackUpDataExpertsUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('feedbackUpDataExpertsAppId', isset($config->global->feedbackUpDataExpertsAppId) ? $config->global->feedbackUpDataExpertsAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('feedbackUpDataExpertsAppSecret', isset($config->global->feedbackUpDataExpertsAppSecret) ? $config->global->feedbackUpDataExpertsAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('feedbackUpDataExpertsEnable', $lang->requestconf->pushEnableList, $config->global->feedbackUpDataExpertsEnable)?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->feedbackQzIssues;?></th>
          <td><?php echo html::input('feedbackQzIssuesUrl', isset($config->global->feedbackQzIssuesUrl) ? $config->global->feedbackQzIssuesUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('feedbackQzIssuesAppId', isset($config->global->feedbackQzIssuesAppId) ? $config->global->feedbackQzIssuesAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('feedbackQzIssuesAppSecret', isset($config->global->feedbackQzIssuesAppSecret) ? $config->global->feedbackQzIssuesAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('feedbackQzIssuesEnable', $lang->requestconf->pushEnableList, $config->global->feedbackQzIssuesEnable)?></td>
          <td></td>
      </tr>

      <!-- 人力接口-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->pushProjectInfo?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushProjectInfo;?></th>
          <td><?php echo html::input('pushProjectInfoUrl', isset($config->global->pushProjectInfoUrl) ? $config->global->pushProjectInfoUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushProjectInfoAppId', isset($config->global->pushProjectInfoAppId) ? $config->global->pushProjectInfoAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushProjectInfoAppSecret', isset($config->global->pushProjectInfoAppSecret) ? $config->global->pushProjectInfoAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushProjectInfoEnable', $lang->requestconf->pushEnableList, $config->global->pushProjectInfoEnable)?></td>
          <td></td>
      </tr>
      <!-- 金信投产接口-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->putproduction?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushPutproductionUrl;?></th>
          <td><?php echo html::input('pushPutproductionUrl', isset($config->global->pushPutproductionUrl) ? $config->global->pushPutproductionUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushPutproductionAppId', isset($config->global->pushPutproductionAppId) ? $config->global->pushPutproductionAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushPutproductionAppSecret', isset($config->global->pushPutproductionAppSecret) ? $config->global->pushPutproductionAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushPutproductionEnable', $lang->requestconf->pushEnableList, isset($config->global->pushPutproductionEnable) ? $config->global->pushPutproductionEnable :'')?></td>
          <td></td>
      </tr>
      <!-- cmdb同步接口接口-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->cmdbsync?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushCmdbDealUrl;?></th>
          <td><?php echo html::input('pushCmdbDealUrl', isset($config->global->pushCmdbDealUrl) ? $config->global->pushCmdbDealUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppInfoUrl;?></th>
          <td><?php echo html::input('pushAppInfoUrl', isset($config->global->pushAppInfoUrl) ? $config->global->pushAppInfoUrl : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td><?php echo html::input('pushCmdbAppId', isset($config->global->pushCmdbAppId) ? $config->global->pushCmdbAppId : '', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td><?php echo html::input('pushCmdbAppSecret', isset($config->global->pushCmdbAppSecret) ? $config->global->pushCmdbAppSecret :'', "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushCmdbAppEnable', $lang->requestconf->pushEnableList, isset($config->global->pushCmdbAppEnable) ? $config->global->pushCmdbAppEnable :'')?></td>
          <td></td>
      </tr>
      <!--其他配置-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->other?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->resetPasswordIp?></th>
          <td><?php echo html::input('resetPasswordIp', $config->global->resetPasswordIp, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->jenkinsServerIP?></th>
          <td><?php echo html::input('jenkinsServerIP', $config->global->jenkinsServerIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->sonarcubeServerIP?></th>
          <td><?php echo html::input('sonarcubeServerIP', $config->global->sonarcubeServerIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->networkDiskServerIP?></th>
          <td><?php echo html::input('networkDiskServerIP', $config->global->networkDiskServerIP, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->pushMessageTitle?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushMsgEnable', $lang->requestconf->pushEnableList, $config->global->pushMsgEnable)?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushMessageUrl?></th>
          <td>
              <?php echo html::input('pushMessageUrl', $config->global->pushMessageUrl, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushMsgAppid?></th>
          <td>
              <?php echo html::input('pushMsgAppid', $config->global->pushMsgAppid, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushMsgAppSecret?></th>
          <td>
              <?php echo html::input('pushMsgAppSecret', $config->global->pushMsgAppSecret, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->h5url?></th>
          <td>
              <?php echo html::input('h5url', $config->global->h5url, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pcurl?></th>
          <td>
              <?php echo html::input('pcurl', $config->global->pcurl, "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
     <!--安全资产平台同步发布-->
      <tr>
          <th class='w-120px'><h4><?php echo $lang->requestconf->pushSafeAsset?></h4></th>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushSafeAssetUrl?></th>
          <td>
              <?php echo html::input('pushSafeAssetUrl', isset($config->global->pushSafeAssetUrl) ? $config->global->pushSafeAssetUrl : '', "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppId?></th>
          <td>
              <?php echo html::input('pushSafeAssetAppId', isset($config->global->pushSafeAssetAppId) ? $config->global->pushSafeAssetAppId : '', "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushAppSecret?></th>
          <td>
              <?php echo html::input('pushSafeAssetAppSecret', isset($config->global->pushSafeAssetAppSecret) ? $config->global->pushSafeAssetAppSecret : '', "class='form-control' autocomplete='off'")?>
          <td></td>
      </tr>
      <tr>
          <th class='w-120px'><?php echo $lang->requestconf->pushEnable?></th>
          <td><?php echo html::radio('pushSafeAssetEnable', $lang->requestconf->pushEnableList, isset($config->global->pushSafeAssetEnable) ? $config->global->pushSafeAssetEnable : '')?></td>
          <td></td>
      </tr>
      <tr>
      <th class='w-120px'></th>
      <td class='text-left form-actions'>
        <?php echo html::submitButton();?>
      </td>
    </tr>
  </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
