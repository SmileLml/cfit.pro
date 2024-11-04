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
      <h2><?php echo  $lang->localesupport->edit;?></h2>
    </div>
      <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
          <table class="table table-form" id="">
              <tbody>
              <tr>
                  <th class='w-140px'><?php echo $lang->localesupport->startDate;?></th>
                  <td><?php echo html::input('startDate',  $info->startDate != '0000-00-00 00:00:00'? $info->startDate: '', "class='form-control  form-datetime'");?></td>
                  <th class='w-80px'><?php echo $lang->localesupport->endDate;?></th>
                  <td><?php echo html::input('endDate',  $info->endDate != '0000-00-00 00:00:00'? $info->endDate: '', "class='form-control  form-datetime'");?></td>
              </tr>


              <tr>
                  <th><?php echo $lang->localesupport->area;?></th>
                  <td><?php echo html::select('area',  $lang->localesupport->areaList,  $info->area, "class='form-control chosen' onchange='changeArea(this.value);'");?></td>
                  <th><?php echo $lang->localesupport->stype;?></th>
                  <td><?php echo html::select('stype',  $lang->localesupport->stypeList,  $info->stype, 'class="form-control chosen"');?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->appIds;?></th>
                  <td colspan='3'><?php echo html::select('appIds[]',  $appList,  $info->appIds, 'class="form-control  chosen" multiple');?></td>
              </tr>

              <!--
              <tr id="owndeptInfo" <?php if (!$info->appIds): ?>class="hidden" <?php endif;?>>
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
                          <?php
                          if($info->appIds):
                                $appIds = explode(',', $info->appIds);
                                $owndept = json_decode($info->owndept, true);
                                $sj = json_decode($info->sj, true);
                                foreach ($appIds as $appId):
                          ?>
                                    <tr id="owndeptAndSjInfo_<?php echo $appId; ?>">
                                        <td>
                                            <?php echo html::select('tempAppId[]',  $appList,  $appId, "id='tempAppId{$appId}' data-index='{$appId}' class='form-control chosen' disabled");?>
                                            <input type="hidden" name="appId[]" id="appId<?php echo $appId; ?>" value="<?php echo $appId; ?>">
                                        </td>

                                        <td>
                                            <?php echo html::select('owndept[]',  $lang->application->teamList, $owndept[$appId], "id='owndept{$appId}' data-index='{$appId}' class='form-control chosen'");?>
                                        </td>

                                        <td>
                                            <?php echo html::select('sj[]',  $lang->application->fromUnitList, $sj[$appId] , "id='sj{$appId}' data-index='{$appId}' class='form-control chosen'");?>
                                        </td>

                                    </tr>

                          <?php
                                endforeach;
                            endif;
                          ?>
                          </tbody>
                      </table>
                  </td>
              </tr>
              -->

              <tr>
                  <th><?php echo $lang->localesupport->supportUsers;?></th>
                  <td><?php echo html::select('supportUsers[]',  $users,  $info->supportUsers, "class='form-control chosen' onchange='changeSupportUsers();'  multiple");?></td>
                  <th><?php echo $lang->localesupport->deptIds;?></th>
                  <td>
                      <input type="hidden" name="deptIds" id="deptIds" value="<?php echo $info->deptIds;?>">
                      <?php echo html::select('tempDeptIds[]',  $deptList, $info->deptIds, "class='form-control chosen' onchange='changeDeptIds();' multiple disabled");?>
                  </td>
              </tr>

              <tr class="reportWorkInfo  <?php if (!$info->supportUsers): ?> hidden <?php endif;?>">
                  <th><?php echo $lang->localesupport->reportWork;?></th>
                  <td colspan='3'>
                      <table class="table table-form table-bordered">
                          <thead>
                          <tr>
                              <th class="w-60px"><?php echo $lang->localesupport->rowNum;?></th>
                              <th><?php echo $lang->localesupport->supportUsers;?></th>
                              <th><?php echo $lang->localesupport->supportDate;?></th>
                              <th><?php echo $lang->localesupport->consumed;?></th>
                              <th class="w-80px"><?php echo $lang->actions;?></th>
                          </tr>
                          </thead>
                          <tbody id="supportUserTBody">
                          <tr id="createWorkTr" <?php if($info->workReportList):?> class="hidden" <?php endif;?>>
                              <td colspan="5" style="text-align: center;">
                                  <a href="javascript:void(0)" onclick="createWork();" class="btn btn-link"   data-id='0'><i class="icon-plus"></i></a>
                              </td>
                          </tr>
                          <?php
                          if($info->workReportList):
                              $workReportList = $info->workReportList;
                              foreach ($workReportList as $key => $workReportInfo):
                                  ?>

                                  <tr id="supportUserInfo_<?php echo $key+1;?>">
                                      <td><?php echo $key+1;?></td>
                                      <td>
                                          <?php echo html::select('supportUser[]',  $supportUsersList,  $workReportInfo->supportUser, " id='supportUser{$key}' data-index='{$key}' class='form-control chosen'");?>
                                      </td>
                                      <td>
                                          <?php echo html::input('supportDate[]',  $workReportInfo->supportDate == '0000-00-00'? '': $workReportInfo->supportDate, "class='form-control  form-date'  data-id = '' readonly");?>
                                      </td>
                                      <td><?php echo html::input('consumed[]',  $workReportInfo->consumed, "class='form-control'");?></td>

                                      <td>
                                          <div class="input-group">
                                              <input type="hidden" name="workReportId[]" value="<?php echo $workReportInfo->id;?>">
                                              <a href="javascript:void(0)" onclick="addWork(this)" class="btn btn-link"  id="addWorkItem<?php echo $key;?>"  data-id='<?php echo $key;?>'><i class="icon-plus"></i></a>
                                              <a href="javascript:void(0)" onclick="delWork(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                          </div>
                                      </td>
                                  </tr>
                              <?php endforeach;?>
                          <?php endif;?>
                          </tbody>
                      </table>

                  </td>
              </tr>

              <tr class="reportWorkInfo <?php if (!$info->supportUsers): ?> hidden <?php endif;?>" >
                  <th>&nbsp;</th>
                  <td colspan='3'>
                      <div class="text-left" style="color: red"><span> <?php echo $lang->localesupport->workReportTipMessage; ?></span></div>
                  </td>
              </tr>




              <tr>
                  <th><?php echo $lang->localesupport->manufacturer;?></th>
                  <td colspan='3'><?php echo html::input('manufacturer',  $info->manufacturer , "class='form-control'");?></td>
              </tr>

              <tr class="jxInfo hidden">
                  <th><?php echo $lang->localesupport->jxdepart;?></th>
                  <td colspan='3'><?php echo html::input('jxdepart',  $info->jxdepart , "class='form-control'");?></td>
              </tr>

              <tr  class="jxInfo hidden">
                  <th><?php echo $lang->localesupport->sysper;?></th>
                  <td colspan='3'><?php echo html::input('sysper',  $info->sysper , "class='form-control'");?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->reason;?></th>
                  <td colspan='3'>
                      <?php echo html::textarea('reason',  $info->reason , "rows='3' class='form-control' ");?>
                  </td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->remark;?></th>
                  <td colspan='3'>
                      <?php echo html::textarea('remark',  $info->remark, "rows='3' class='form-control' ");?>
                  </td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->filelist;?></th>
                  <td colspan='3'>
                      <div class='detail'>
                          <div class='detail-left article-left'>
                              <?php
                              if($info->files){
                                  echo $this->fetch('file', 'printFiles', array('files' => $info->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                              }else{
                                  echo "<div class='text-left text-muted'>" . $lang->noData . '</div>';
                              }
                              ?>
                          </div>
                      </div>
                  </td>

              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->files;?></th>
                  <td  colspan='3'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->localesupport->deptManagers;?></th>
                  <td colspan='3'>
                      <?php echo html::select('deptManagers[]',  $managerUserList,  $info->deptManagers, 'class="form-control chosen" multiple');?>
                  </td>
              </tr>





              <!--
              <tr>
                  <th><?php echo $lang->localesupport->mailto;?></th>
                  <td colspan='3'><?php echo html::select('mailto[]',  $users,  $info->mailto, 'class="form-control chosen" multiple');?></td>
              </tr>
              -->

              <!--
              <tr>
                  <th><?php echo $lang->localesupport->isUserSelfReportWork;?></th>
                  <td colspan='3'>
                      <?php echo html::radio('isUserSelfReportWork', $lang->localesupport->isUserSelfReportWorkList, $info->isUserSelfReportWork, "");?>
                  </td>
              </tr>
               -->
              <tr>
                  <th>
                  </th>
                  <td class='form-actions text-center' colspan='3'>
                      <input type="hidden" name="id" value=" <?php echo $info->id;?>">
                      <input type="hidden" name="issubmit" value="save">
                      <input type="hidden" name="isWarn" id="isWarn" value="no">
                      <?php
                        echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn buttonInfo') .
                          html::commonButton($lang->submit, '', 'btn btn-wide btn-primary submitBtn buttonInfo') . html::backButton();
                      ?>
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
    js::set('supportUserIndex', count($info->workReportList)); //基数开始值
    js::set('area', $info->area);
    js::set('minStartDate',$minStartDate);
    js::set('start', $info->startDate != '0000-00-00 00:00:00' ? $info->startDate: $minStartDate);
    js::set('end', $info->endDate);
    js::set('submitConfirmMsg',$lang->localesupport->submitConfirm);
?>

<?php include '../../common/view/footer.html.php';?>
