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
    <h2><?php echo $lang->iwfp->common?></h2>
  </div>
  <form class="load-indicator main-form form-ajax" method='post'>
  <table class="table table-form">
      <!--请求头配置-->
      <tr>
          <th class='w-180px'><h3><?php echo $lang->iwfp->requestHeaderConfig?></h3></th>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->tenantId?></th>
          <td><?php echo html::input('tenantId', $config->global->tenantId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->AuthorizationKey?></th>
          <td><?php echo html::input('AuthorizationKey', $config->global->AuthorizationKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <!--接口配置-->
      <tr>
          <th class='w-180px'><h3><?php echo $lang->iwfp->interfaceConfig?></h3></th>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->startWorkFlowUrl?></th>
          <td><?php echo html::input('startWorkFlowUrl', $config->global->startWorkFlowUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->getButtonListUrl?></th>
          <td><?php echo html::input('getButtonListUrl', $config->global->getButtonListUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->completeTaskWithClaimUrl?></th>
          <td><?php echo html::input('completeTaskWithClaimUrl', $config->global->completeTaskWithClaimUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->getToDoTaskListUrl?></th>
          <td><?php echo html::input('getToDoTaskListUrl', $config->global->getToDoTaskListUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->listApproveLogUrl?></th>
          <td><?php echo html::input('listApproveLogUrl', $config->global->listApproveLogUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->turnBackUrl?></th>
          <td><?php echo html::input('turnBackUrl', $config->global->turnBackUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->getFreeJumpNodeListUrl?></th>
          <td><?php echo html::input('getFreeJumpNodeListUrl', $config->global->getFreeJumpNodeListUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->freeJumpUrl?></th>
          <td><?php echo html::input('freeJumpUrl', $config->global->freeJumpUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->withDrawUrl?></th>
          <td><?php echo html::input('withDrawUrl', $config->global->withDrawUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->addSignTaskUrl?></th>
          <td><?php echo html::input('addSignTaskUrl', $config->global->addSignTaskUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->changeAssigneekUrl?></th>
          <td><?php echo html::input('changeAssigneekUrl', $config->global->changeAssigneekUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->queryProcessTrackImageUrl?></th>
          <td><?php echo html::input('queryProcessTrackImageUrl', $config->global->queryProcessTrackImageUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->completeTaskUrl?></th>
          <td><?php echo html::input('completeTaskUrl', $config->global->completeTaskUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->getTaskDefListUrl?></th>
          <td><?php echo html::input('getTaskDefListUrl', $config->global->getTaskDefListUrl, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <!--模版配置-->
      <tr>
          <th class='w-180px'><h3><?php echo $lang->iwfp->templateConfig?></h3></th>
      </tr>
      <!--金信投产配置-->
      <tr>
          <th class='w-180px'><h4><?php echo $lang->iwfp->jxPutproduction?></h4></th>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->jxPutproductionKey?></th>
          <td><?php echo html::input('jxPutproductionKey', $config->global->jxPutproductionKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->jxPutproductionId?></th>
          <td><?php echo html::input('jxPutproductionId', $config->global->jxPutproductionId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <!--环境部署工单配置-->
      <tr>
          <th class='w-180px'><h4><?php echo $lang->iwfp->environmentorder?></h4></th>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->environmentorderKey?></th>
          <td><?php echo html::input('environmentorderKey', $config->global->environmentorderKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->environmentorderId?></th>
          <td><?php echo html::input('environmentorderId', $config->global->environmentorderId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->environmentorderTempId?></th>
          <td><?php echo html::input('environmentorderTempId', $config->global->environmentorderTempId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <!--权限申请配置-->
      <tr>
          <th class='w-180px'><h4><?php echo $lang->iwfp->authorityapply?></h4></th>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->authorityapplyKey?></th>
          <td><?php echo html::input('authorityapplyKey', $config->global->authorityapplyKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->authorityapplyId?></th>
          <td><?php echo html::input('authorityapplyId', $config->global->authorityapplyId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->authorityapplyTempId?></th>
          <td><?php echo html::input('authorityapplyTempId', $config->global->authorityapplyTempId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <!--征信交付配置-->
      <tr>
          <th class='w-180px'><h4><?php echo $lang->iwfp->tjCredit?></h4></th>
      </tr>

      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->tjCreditKey?></th>
          <td><?php echo html::input('tjCreditKey', $config->global->tjCreditKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->tjCreditId?></th>
          <td><?php echo html::input('tjCreditId', $config->global->tjCreditId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <!--现场支持配置-->
      <tr>
          <th class='w-180px'><h4><?php echo $lang->iwfp->localesupport?></h4></th>
      </tr>

      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->localesupportKey?></th>
          <td><?php echo html::input('localesupportKey', $config->global->localesupportKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->localesupportId?></th>
          <td><?php echo html::input('localesupportId', $config->global->localesupportId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->localesupportTempId?></th>
          <td><?php echo html::input('localesupportTempId', $config->global->localesupportTempId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>


      <!--内部自建投产/变更配置-->
      <tr>
          <th class='w-180px'><h4><?php echo $lang->iwfp->preproduction?></h4></th>
      </tr>

      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->preproductionKey?></th>
          <td><?php echo html::input('productionchangeKey', $config->global->productionchangeKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->preproductionId?></th>
          <td><?php echo html::input('productionchangeId', $config->global->productionchangeId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>
      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->preproductionTempId?></th>
          <td><?php echo html::input('preproductionTempId', $config->global->preproductionTempId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <!--质量门禁配置-->
      <tr>
          <th class='w-180px'><h4><?php echo $lang->iwfp->qualitygate?></h4></th>
      </tr>

      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->qualitygateKey?></th>
          <td><?php echo html::input('qualitygateKey', $config->global->qualitygateKey, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <tr>
          <th class='w-180px'><?php echo $lang->iwfp->qualitygateId?></th>
          <td><?php echo html::input('qualitygateId', $config->global->qualitygateId, "class='form-control' autocomplete='off'")?></td>
          <td></td>
      </tr>

      <tr>
      <th class='w-180px'></th>
      <td class='text-left form-actions'>
        <?php echo html::submitButton();?>
      </td>
    </tr>
  </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
