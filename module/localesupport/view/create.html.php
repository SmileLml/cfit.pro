<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.input-group-addon{min-width: 150px;}
.input-group{margin-bottom: 6px;}
.panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
.panel{border-color: #ddd;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo  $lang->localesupport->create;?></h2>
    </div>

      <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
          <table class="table table-form" id="">
              <tbody>
              <tr>
                  <th class='w-140px'><?php echo $lang->localesupport->startDate;?></th>
                  <td><?php echo html::input('startDate',  '' , "class='form-control  form-datetime'");?></td>
                  <th class='w-80px'><?php echo $lang->localesupport->endDate;?></th>
                  <td><?php echo html::input('endDate',  '' , "class='form-control  form-datetime'");?></td>
              </tr>


              <tr>
                  <th><?php echo $lang->localesupport->area;?></th>
                  <td><?php echo html::select('area',  $lang->localesupport->areaList,  '', "class='form-control chosen' onchange='changeArea(this.value);'");?></td>
                  <th><?php echo $lang->localesupport->stype;?></th>
                  <td><?php echo html::select('stype',  $lang->localesupport->stypeList,  '', 'class="form-control chosen"');?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->appIds;?></th>
                  <td colspan='3'>
                      <?php echo html::select('appIds[]',  $appList,  '', 'class="form-control  chosen" multiple');?>
                  </td>
              </tr>
             <!--
              <tr id="owndeptInfo" class="hidden">
                  <th><?php echo $lang->localesupport->owndeptAndSj;?></th>
                  <td colspan='3'>
                      <table class="table table-form table-bordered">
                          <thead>
                          <tr>
                              <th><?php echo $lang->localesupport->appIds;?></th>
                              <th><?php echo $lang->localesupport->owndept;?></th>
                              <th><?php echo $lang->localesupport->sj;?></th>
                          </tr>
                          </thead>
                          <tbody id="owndeptAndSjTBody">

                          </tbody>
                      </table>
                  </td>
              </tr>
              -->

              <tr>
                  <th><?php echo $lang->localesupport->supportUsers;?></th>
                  <td><?php echo html::select('supportUsers[]',  $users,  '', "class='form-control chosen' onchange='changeSupportUsers();'  multiple");?></td>
                  <th><?php echo $lang->localesupport->deptIds;?></th>
                  <td>
                      <input type="hidden" name="deptIds" id="deptIds" value="">
                      <?php echo html::select('tempDeptIds[]',  $deptList, '', "class='form-control chosen' onchange='changeDeptIds();' multiple disabled");?>
                  </td>
              </tr>

              <tr class="reportWorkInfo hidden">
                  <th><?php echo $lang->localesupport->reportWork;?></th>
                  <td colspan='3'>
                      <table class="table table-form table-bordered">
                          <thead>
                          <tr>
                              <th class="w-60px"><?php echo $lang->localesupport->rowNum;?></th>
                              <th><?php echo $lang->localesupport->supportUsers;?></th>
                              <th><?php echo $lang->localesupport->supportDate;?></th>
                              <th><?php echo $lang->localesupport->consumed;?></th>
                              <th class="w-80px">
                                  <?php echo $lang->actions;?>

                              </th>
                          </tr>
                          </thead>
                          <tbody id="supportUserTBody">
                          <tr id="createWorkTr">
                              <td colspan="5" style="text-align: center;">
                                  <a href="javascript:void(0)" onclick="createWork();" class="btn btn-link"   data-id='0'><i class="icon-plus"></i></a>
                              </td>
                          </tr>

                          </tbody>

                      </table>

                  </td>
              </tr>

              <tr class="reportWorkInfo hidden">
                  <th>&nbsp;</th>
                  <td colspan='3'>
                      <div class="text-left" style="color: red"><span> <?php echo $lang->localesupport->workReportTipMessage; ?></span></div>
                  </td>
              </tr>


              <tr>
                  <th><?php echo $lang->localesupport->manufacturer;?></th>
                  <td colspan='3'><?php echo html::input('manufacturer',  '' , "class='form-control'");?></td>
              </tr>

              <tr class="jxInfo hidden">
                  <th><?php echo $lang->localesupport->jxdepart;?></th>
                  <td colspan='3'><?php echo html::input('jxdepart',  '' , "class='form-control'");?></td>
              </tr>

              <tr  class="jxInfo hidden">
                  <th><?php echo $lang->localesupport->sysper;?></th>
                  <td colspan='3'><?php echo html::input('sysper',  '' , "class='form-control'");?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->reason;?></th>
                  <td colspan='3'>
                      <?php echo html::textarea('reason', '', "rows='3' class='form-control' ");?>
                  </td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->remark;?></th>
                  <td colspan='3'>
                      <?php echo html::textarea('remark', '', "rows='3' class='form-control' ");?>
                  </td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->files;?></th>
                  <td  colspan='3'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->deptManagers;?></th>
                  <td colspan='3'>
                      <!--
                      <?php echo html::select('deptManagers[]',  $managerUserList,  array_keys($managerUserList), 'class="form-control chosen" multiple');?>
                      -->
                      <?php echo html::select('deptManagers[]', [],  '', 'class="form-control chosen" multiple');?>
                  </td>
              </tr>


              <!--
              <tr>
                  <th><?php echo $lang->localesupport->mailto;?></th>
                  <td colspan='3'><?php echo html::select('mailto[]',  $users,  '', 'class="form-control chosen" multiple');?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->isUserSelfReportWork;?></th>
                  <td colspan='3'>
                      <?php echo html::radio('isUserSelfReportWork', $lang->localesupport->isUserSelfReportWorkList, 1, "");?>
                  </td>
              </tr>
              -->

              <tr>
                  <th class="w-120px"></th>
                  <td class='form-actions text-center' colspan='3'>
                      <input type="hidden" name="issubmit" value="save">
                      <input type="hidden" name="isWarn" id="isWarn" value="no">
                      <?php
                          echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn buttonInfo') .
                          html::commonButton($lang->submit, '', 'btn btn-wide btn-primary submitBtn buttonInfo') . html::backButton();
                      ?>

                  </td>
                  </td>
              </tr>
              </tbody>
          </table>
      </form>

  </div>
</div>
<?php include 'defaultinfo.html.php';?>
<?php
    js::set('isAllReportWork', $isAllReportWork);
    js::set('appDataList', $appDataList);
    js::set('supportUserIndex', 0);
    js::set('minStartDate',$minStartDate);
    js::set('start', $minStartDate);
    js::set('end','');
    js::set('submitConfirmMsg',$lang->localesupport->submitConfirm);
?>

<?php include '../../common/view/footer.html.php';?>
