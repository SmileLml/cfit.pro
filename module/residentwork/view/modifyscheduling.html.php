<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px;">
  <div class='center-block'>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
      <?php
      else:
          $params = "browseType=$browseType&param=$param&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"

      ?>
        <div class='main-header'>
          <h2>
            <span class='label label-id'>排班模板<?php echo $tempDayInfo->templateId;?>
                【
                <?php echo zget($lang->residentsupport->typeList, $tempDayInfo->templateInfo->type);?> -
                <?php echo zget($lang->residentsupport->subTypeList, $tempDayInfo->templateInfo->subType);?>
                】
            </span>
            <span><?php echo $dutyDate;?></span>
            <small><?php echo $lang->arrow . $lang->residentwork->modifyScheduling;?></small>
          </h2>
            <div style="clear: both"><?php echo $lang->residentsupport->modifySchedulingTips;?></div>
        </div>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <div class='detail' style="padding: 0px !important;">
                  <div class="detail-title">
                      <?php
                      $i = 0;
                      foreach ($lang->residentsupport->schedulingDeptLabelList as $label => $labelName) {
                          $active = $schedulingDeptType == $label ? 'btn-active-text' : '';
                          echo html::a($this->createLink('residentwork', 'modifyScheduling', "dayId=$dayId&schedulingDeptType=$label&".$params), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
                      }
                      ?>
                  </div>
                  <div class="detail-content article-content ">
                      <table class='table ops  scheduling-table'>
                          <thead>
                          <tr>
                              <th><?php echo $lang->residentsupport->dutyDate ;?></th>
                              <th><?php echo $lang->residentsupport->dutyGroupLeader;?></th>
                              <th class='w-120px'><?php echo $lang->residentsupport->dutyDept;?></th>
                              <th><?php echo $lang->residentsupport->postTypeInfo;?></th>
                              <th class='w-120px'><?php echo $lang->residentsupport->requireInfo;?></th>
                              <th class='w-180px'><?php echo $lang->residentsupport->timeType ;?></th>
                              <th><?php echo  $lang->residentsupport->dutyTime ;?></th>
                              <th><?php echo  $lang->residentsupport->dutyUser ?></th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php if(empty($dutyUserList)):?>
                              <tr>
                                  <th colspan="8" style="text-align: center;"><?php echo $lang->noData;?></th>
                              </tr>
                          <?php else:?>
                              <?php
                              $keySort = 0;
                              foreach($dutyUserList as $dayId => $dayDutyInfo):
                                  $dayUserCount = $dayDutyInfo['total'];
                                  $dutyDeptList = $dayDutyInfo['data'];
                                  $dayInfo = $tempDayInfo;
                                  $dutyDate = $dayInfo->dutyDate;
                                  $dutyGroupLeader = $dayInfo->dutyGroupLeader;

                                  ?>
                                  <tr>
                                  <th rowspan="<?php echo $dayUserCount;?>" style="vertical-align: middle;">
                                      <?php echo $dutyDate; ?>
                                  </th>

                                  <th rowspan="<?php echo $dayUserCount;?>" style="vertical-align: middle;">
                                      <?php echo $dutyGroupLeader ? zget($users, $dutyGroupLeader):''; ?>
                                  </th>

                                  <?php
                                  foreach ($dutyDeptList as $deptId => $deptDutyInfo):
                                      $deptUserCount = $deptDutyInfo['total'];
                                      $currentDeptUerList = $deptDutyInfo['data'];
                                      //部门下第一个值班用户信息
                                      $deptFirstUerInfo =  $currentDeptUerList[0];
                                      $currentDeptUsers = zget($deptUserList, $deptId);
                                      array_unshift($currentDeptUsers, '选择值班人员');
                                      $keySort++;
                                      ?>
                                      <th rowspan="<?php echo $deptUserCount;?>" style="vertical-align: middle;">
                                          <?php echo zget($depts, $deptId); ?>
                                      </th>
                                      <td><?php echo zget($lang->residentsupport->postType, $deptFirstUerInfo->postType); ?></td>
                                      <td title='<?php echo $deptFirstUerInfo->requireInfo;?>' class='text-ellipsis'>
                                          <?php echo Helper::substr($deptFirstUerInfo->requireInfo, 10,'...'); ?>
                                      </td>
                                      <td><?php echo zget($lang->residentsupport->durationTypeList, $deptFirstUerInfo->timeType); ?></td>
                                      <td><?php echo $deptFirstUerInfo->startTime.'-'.$deptFirstUerInfo->endTime; ?></td>
                                      <td>
                                           <?php if(in_array($deptId, $allowModifyDeptIds)):?>
                                                <?php echo html::select("dutyUsers[$deptFirstUerInfo->id]", $currentDeptUsers, $deptFirstUerInfo->dutyUser, "class='form-control chosen'");?>
                                               <?php echo html::hidden("sortKeys[$deptFirstUerInfo->id]", $keySort);?>
                                          <?php else:?>
                                               <?php echo html::select("temp[]", $currentDeptUsers, $deptFirstUerInfo->dutyUser, "class='form-control chosen' disabled='disabled' readonly='readonly'");?>
                                          <?php endif;?>
                                      </td>
                                  </tr>
                                      <?php
                                      if($deptUserCount > 1):
                                          for($i = 1; $i < $deptUserCount; $i++):
                                              $dutyUserInfo =  $currentDeptUerList[$i];
                                              $keySort++;
                                              ?>
                                              <tr>
                                                  <td><?php echo zget($lang->residentsupport->postType, $dutyUserInfo->postType); ?></td>
                                                  <td title='<?php echo $dutyUserInfo->requireInfo;?>' class='text-ellipsis'>
                                                      <?php echo Helper::substr($dutyUserInfo->requireInfo, 10, '...'); ?>
                                                  </td>
                                                  <td><?php echo zget($lang->residentsupport->durationTypeList, $dutyUserInfo->timeType); ?></td>
                                                  <td><?php echo $dutyUserInfo->startTime.'-'.$dutyUserInfo->endTime; ?></td>
                                                  <td>
                                                       <?php if(in_array($deptId, $allowModifyDeptIds)):?>
                                                           <?php echo html::select("dutyUsers[$dutyUserInfo->id]", $currentDeptUsers, $dutyUserInfo->dutyUser, "class='form-control chosen'");?>
                                                           <?php echo html::hidden("sortKeys[$dutyUserInfo->id]", $keySort);?>
                                                      <?php else:?>
                                                           <?php echo html::select("temp[]", $currentDeptUsers, $dutyUserInfo->dutyUser, "class='form-control chosen' disabled='disabled' readonly='readonly'");?>
                                                      <?php endif;?>
                                                  </td>
                                              </tr>
                                          <?php
                                          endfor;
                                      endif;
                                      ?>
                                  <?php
                                  endforeach;
                                  ?>

                              <?php
                              endforeach;
                              ?>
                          <?php endif;?>

                          <tr>
                              <td class='form-actions text-center' colspan='8'>
                                  <input type="hidden" id="templateId" name = "templateId" value="<?php echo $tempDayInfo->templateId; ?>">
                                  <input type="hidden" id="dayId" name = "dayId" value="<?php echo $tempDayInfo->id; ?>">
                                  <?php
                                    $label = '提交';
                                    if($userType == 1){
                                        $label = '提交审批';
                                    }
                                  ?>
                                  <?php echo html::submitButton($label, '', 'btn btn-wide btn-primary modifyScheduling'). html::backButton('取消','onclick="return returnBack()"');?>
                              </td>
                          </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </form>

      <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<?php js::set('backUrl',  $this->createLink('residentwork', 'browse', $params));?>
<script type="application/javascript">
    /**
     * 返回
     */
    function returnBack() {
        window.location.href = backUrl;
        return false;
    }
</script>
