<?php
/**
 * The edit view of application module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: create.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
    .partitionContainer>div{margin: 0 15px 5px 0}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->application->edit;?></h2>
    </div>
      <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
          <div class="panel">
              <div class="panel-heading">
                  <?php echo $lang->application->basicInfo;?>
              </div>
              <div class="panel-body">
                  <table class="table table-form">
                      <!--                        调整表格格式，勿删-->
                      <tr>
                          <th class='w-100px' style="height: 0;padding:0"></th>
                          <td style="height: 0;padding:0"></td>
                          <th class='w-100px' style="height: 0;padding:0"></th>
                          <td style="height: 0;padding:0"></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->name;?></th>
                          <td><?php echo html::input('name', $application->name, "class='form-control input-product-title' required");?></td>
                          <th><?php echo $lang->application->code;?></th>
                          <td><?php echo html::input('code', $application->code, "class='form-control input-code' required");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->isPayment;?></th>
                          <td><?php echo html::select('isPayment', $lang->application->isPaymentList, $application->isPayment,"class='form-control chosen' required");?></td>
                          <th><?php echo $lang->application->team;?></th>
                          <td><?php echo html::select('team', $lang->application->teamList, $application->team, "class='form-control chosen' required");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->fromUnit;?></th>
                          <td><?php echo html::select('fromUnit', $lang->application->fromUnitList, $application->fromUnit, "class='form-control input-code chosen'");?></td>
                          <th><?php echo $lang->application->belongOrganization;?></th>
                          <td><?php echo html::select('belongOrganization', $lang->application->belongOrganizationList, $application->belongOrganization, "class='form-control chosen'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->securityLevel;?></th>
                          <td><?php echo html::input('securityLevel', $application->securityLevel, "class='form-control'");?></td>
                          <th><?php echo $lang->application->attribute;?></th>
                          <td><?php echo html::select('attribute', $lang->application->attributeList, $application->attribute, "class='form-control input-code chosen'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->isBasicLine;?></th>
                          <td><?php echo html::select('isBasicLine', $lang->application->boolList, $application->isBasicLine, "class='form-control chosen'");?></td>
                          <th><?php echo $lang->application->baselineSystem;?></th>
                          <td><?php echo html::select('baselineSystem', $baseapplicationList, $application->baselineSystem, "class='form-control chosen'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->isSyncJinx;?></th>
                          <td><?php echo html::select('isSyncJinx', $lang->application->boolList,$application->isSyncJinx, "class='form-control chosen'");?></td>
                          <th><?php echo $lang->application->isSyncQz;?></th>
                          <td><?php echo html::select('isSyncQz', $lang->application->boolList,$application->isSyncQz, "class='form-control chosen'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->vendor;?></th>
                          <td><?php echo html::input('vendor', $application->vendor, "class='form-control'");?></td>
                          <th><?php echo $lang->application->vendorContact;?></th>
                          <td><?php echo html::input('vendorContact', $application->vendorContact, "class='form-control'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->systemManager;?></th>
                          <td><?php echo html::select('systemManager[]', $users,$application->systemManager, "class='form-control chosen' multiple");?></td>
                          <th><?php echo $lang->application->systemDept;?></th>
                          <td><?php echo html::select('systemDept', $depts,$application->systemDept, "class='form-control chosen'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->desc;?></th>
                          <td colspan='3'><?php echo html::textarea('desc', $application->desc, "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->feature;?></th>
                          <td colspan='3'><?php echo html::textarea('feature', $application->feature, "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?></td>
                      </tr>
                  </table>
              </div>
          </div>
          <div class="panel">
              <div class="panel-heading">
                  <?php echo $lang->application->externalInfo;?>
              </div>
              <div class="panel-body">
                  <table class="table table-form">
                      <!--                        调整表格格式，勿删-->
                      <tr>
                          <th class='w-100px' style="height: 0;padding:0"></th>
                          <td style="height: 0;padding:0"></td>
                          <th class='w-100px' style="height: 0;padding:0"></th>
                          <td style="height: 0;padding:0"></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->continueLevel;?></th>
                          <td><?php echo html::select('continueLevel', $lang->application->continueLevelList, $application->continueLevel,"class='form-control chosen'");?></td>
                          <th><?php echo $lang->application->protectLevel;?></th>
                          <td><?php echo html::input('protectLevel', $application->protectLevel, "class='form-control'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->range;?></th>
                          <td><?php echo html::input('range', $application->range, "class='form-control'");?></td>
                          <th><?php echo $lang->application->projectMonth;?></th>
                          <td><?php echo html::input('projectMonth', $application->projectMonth, "class='form-control form-date'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->network;?></th>
                          <td><?php echo html::select('network[]', $lang->application->networkList, $application->network, "class='form-control input-code chosen' multiple");?></td>
                          <th><?php echo $lang->application->architecture;?></th>
                          <td><?php echo html::select('architecture[]', $lang->application->architectureList, $application->architecture, "class='form-control chosen' multiple");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->userScope;?></th>
                          <td><?php echo html::select('userScope[]', $lang->application->userScopeList, $application->userScope, "class='form-control chosen' multiple");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->recoveryStrategy;?></th>
                          <td colspan='3'><?php echo html::textarea('recoveryStrategy', $application->recoveryStrategy, "rows='8' class='form-control'");?></td>
                      </tr>
                  </table>
              </div>
          </div>
          <div class="panel">
              <div class="panel-heading">
                  <?php echo $lang->application->onlineInfo;?>
              </div>
              <div class="panel-body">
                  <table class="table table-form">
                      <tr>
                          <th class='w-100px' style="height: 0;padding:0"></th>
                          <td style="height: 0;padding:0"></td>
                          <th class='w-100px' style="height: 0;padding:0"></th>
                          <td style="height: 0;padding:0"></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->version;?></th>
                          <td><?php echo html::input('version', $application->version, "class='form-control'");?></td>
                          <th><?php echo $lang->application->productDate;?></th>
                          <td><?php echo html::input('productDate', $application->productDate, "class='form-control form-date'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->opsDate;?></th>
                          <td><?php echo html::input('opsDate', $application->opsDate, "class='form-control form-date'");?></td>
                          <th><?php echo $lang->application->promote;?></th>
                          <td><?php echo html::input('promote', $application->promote, "class='form-control'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->cityBak;?></th>
                          <td><?php echo html::input('cityBak', $application->cityBak, "class='form-control'");?></td>
                          <th><?php echo $lang->application->offsiteBak;?></th>
                          <td><?php echo html::input('offsiteBak', $application->offsiteBak, "class='form-control'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->retrofit;?></th>
                          <td><?php echo html::input('retrofit', $application->retrofit, "class='form-control'");?></td>
                          <th><?php echo $lang->application->runStatus;?></th>
                          <td><?php echo html::select('runStatus', $lang->application->runStatusList, $application->runStatus, "class='form-control chosen'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->runUnit;?></th>
                          <td><?php echo html::input('runUnit', $application->runUnit, "class='form-control'");?></td>
                          <th><?php echo $lang->application->resourceLocat;?></th>
                          <td><?php echo html::select('resourceLocat', $lang->application->resourceLocatList, $application->resourceLocat, "class='form-control chosen'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->runDept;?></th>
                          <td><?php echo html::input('runDept', $application->runDept, "class='form-control'");?></td>
                          <th><?php echo $lang->application->owner;?></th>
                          <td><?php echo html::input('owner', $application->owner, "class='form-control'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->opsDept;?></th>
                          <td><?php echo html::input('opsDept', $application->opsDept, "class='form-control'");?></td>
                          <th><?php echo $lang->application->opsManager;?></th>
                          <td><?php echo html::input('opsManager', $application->opsManager, "class='form-control'");?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->application->serviceTime;?></th>
                          <td><?php echo html::input('serviceTime', $application->serviceTime, "class='form-control form-datetime'");?></td>
                          <th><?php echo $lang->application->facilitiesStatus;?></th>
                          <td><?php echo html::select('facilitiesStatus', $lang->application->facilitiesStatusList, $application->facilitiesStatus, "class='form-control chosen'");?></td>
                      </tr>
                      <tr>
                          <td colspan='3' class='text-center form-actions'>
                              <?php echo html::submitButton();?>
                              <?php echo html::backButton();?>
                          </td>
                      </tr>
                  </table>
              </div>
          </div>
      </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
