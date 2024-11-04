<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .td-modify{color: red !important;}
    //.currentDeptTd{background-color: lightyellow !important;}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php
  //$browseLink = inlink('browse');
  $browseLink = $this->session->residentsupportList;
  ?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id">
          <?php echo zget($lang->residentsupport->typeList, $templateInfo->type) ;?>-
          <?php echo zget($lang->residentsupport->subTypeList, $templateInfo->subType) ;?>-日期：
          <?php echo $templateInfo->startDate . '~'. $templateInfo->endDate ;?>
      </span>
    </div>
  </div>

  <div class="btn-toolbar pull-right">
  </div>

</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
        <div class="detail" style="padding: 0px !important;">
            <div class="detail-title">
                <?php
                foreach ($lang->residentsupport->schedulingDeptLabelList as $label => $labelName) {
                    $active = $schedulingDeptType == $label ? 'btn-active-text' : '';
                    echo html::a($this->createLink('residentsupport', 'view', "templateId=$templateId&schedulingDeptType=$label"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
                }
                ?>
            </div>
            <!--
            <div class="detail-title" style="margin-top: 20px !important;"><?php echo $lang->residentsupport->dutyUserTableDetail;?></div>
            -->
            <div class='panel-body  scrollbar-hover detail-content article-content' style="max-height: 300px;">
                <table class='table ops  table-fixed'>
                    <thead>
                        <tr>
                            <th><?php echo $lang->residentsupport->dutyDate ;?></th>
                            <th><?php echo $lang->residentsupport->enable ;?></th>
                            <th><?php echo $lang->residentsupport->dutyGroupLeader;?></th>
                            <th class='w-120px'><?php echo $lang->residentsupport->dutyDept;?></th>
                            <th><?php echo $lang->residentsupport->postTypeInfo;?></th>
                            <th class='w-120px'><?php echo $lang->residentsupport->requireInfo;?></th>
                            <th class='w-160px'><?php echo $lang->residentsupport->timeType ;?></th>
                            <th class='w-140px'><?php echo  $lang->residentsupport->dutyTime ;?></th>
                            <th><?php echo  $lang->residentsupport->dutyUser ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($dutyUserList)):?>
                        <tr>
                            <th colspan="9" style="text-align: center;"><?php echo $lang->noData;?></th>
                        </tr>
                    <?php else:?>
                        <?php
                        foreach($dutyUserList as $dayId => $dayDutyInfo):
                            $dayUserCount = $dayDutyInfo['total'];
                            $dutyDeptList = $dayDutyInfo['data'];
                            $isModify = $dayDutyInfo['isModify'];
                            $dayInfo = zget($dayList, $dayId); //当天信息
                            $dutyDate = isset($dayInfo->dutyDate)?$dayInfo->dutyDate:'';
                            $dutyGroupLeader = isset($dayInfo->dutyGroupLeader)?$dayInfo->dutyGroupLeader:'';
                            $enable = $dayInfo->enable;
                            ?>
                            <tr>
                                <th rowspan="<?php echo $dayUserCount;?>" <?php if($isModify):?> class="td-modify" <?php endif;?>>
                                    <?php echo $dutyDate; ?>
                                </th>
                                <th rowspan="<?php echo $dayUserCount;?>">
                                    <?php echo zget($lang->residentsupport->enableLableList, $enable) ; ?>
                                </th>

                                <th rowspan="<?php echo $dayUserCount;?>">
                                    <?php echo $dutyGroupLeader ? zget($users, $dutyGroupLeader):''; ?>
                                </th>

                            <?php
                            foreach ($dutyDeptList as $deptId => $deptDutyInfo):
                                $deptUserCount = $deptDutyInfo['total'];
                                $currentDeptUerList = $deptDutyInfo['data'];
                                //部门下第一个值班用户信息
                                $deptFirstUerInfo =  $currentDeptUerList[0]; //当前部门下的第一个用户信息
                                $status = $deptFirstUerInfo->status;
                                $currentDeptTdClass = '';
                                if($deptId == $currentUserDeptId){
                                    $currentDeptTdClass = 'currentDeptTd';
                                }
                                ?>
                                    <th class="<?php echo $currentDeptTdClass ?>" rowspan="<?php echo $deptUserCount;?>">
                                        <?php echo zget($depts, $deptId); ?>
                                    </th>
                                    <td class="<?php echo $currentDeptTdClass ?>"><?php echo zget($lang->residentsupport->postType, $deptFirstUerInfo->postType); ?></td>
                                    <td class='text-ellipsis <?php echo $currentDeptTdClass ?>' title='<?php echo $deptFirstUerInfo->requireInfo;?>'>
                                        <?php echo Helper::substr($deptFirstUerInfo->requireInfo, 10,'...'); ?>
                                    </td>
                                    <td class="<?php echo $currentDeptTdClass ?>"><?php echo zget($lang->residentsupport->durationTypeList, $deptFirstUerInfo->timeType); ?></td>
                                    <td class="<?php echo $currentDeptTdClass ?>"><?php echo $deptFirstUerInfo->startTime.'-'.$deptFirstUerInfo->endTime; ?></td>
                                    <td class="<?php echo $currentDeptTdClass ?> <?php if($status == '2'):?> td-modify <?php endif;?>">
                                        <?php echo zget($users, $deptFirstUerInfo->dutyUser) ;?>
                                    </td>
                                </tr>
                                <?php
                                if($deptUserCount > 1):
                                    for($i = 1; $i < $deptUserCount; $i++):
                                        $dutyUserInfo =  $currentDeptUerList[$i];
                                        $status = $dutyUserInfo->status;
                                        ?>
                                        <tr>
                                            <td class="<?php echo $currentDeptTdClass ?>"><?php echo zget($lang->residentsupport->postType, $dutyUserInfo->postType); ?></td>
                                            <td class='text-ellipsis <?php echo $currentDeptTdClass ?>' title='<?php echo $dutyUserInfo->requireInfo;?>'>
                                                <?php echo Helper::substr($dutyUserInfo->requireInfo, 10, '...'); ?>
                                            </td>
                                            <td class="<?php echo $currentDeptTdClass ?>"><?php echo zget($lang->residentsupport->durationTypeList, $dutyUserInfo->timeType); ?></td>
                                            <td class="<?php echo $currentDeptTdClass ?>"><?php echo $dutyUserInfo->startTime.'-'.$dutyUserInfo->endTime; ?></td>
                                            <td class="<?php echo $currentDeptTdClass ?> <?php if($status == '2'):?> td-modify <?php endif;?>">
                                                <?php echo zget($users, $dutyUserInfo->dutyUser);?>
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
                    </tbody>
                </table>
            </div>
        </div>

    </div>

  <div class="cell">
      <div class="detail">
          <div class="detail-title"><?php echo $lang->residentsupport->reviewAdvice;?></div>
          <div class='detail-content article-content'>
              <table class='table ops  table-fixed'>
                  <thead>
                  <tr>
                      <th><?php echo $lang->residentsupport->dutyDept ;?></th>
                      <th><?php echo $lang->residentsupport->reviewVersion;?></th>
                      <th><?php echo $lang->residentsupport->reviewNode;?></th>
                      <th><?php echo $lang->residentsupport->reviewPerson;?></th>
                      <th><?php echo $lang->residentsupport->reviewResult;?></th>
                      <th class='w-120px'><?php echo $lang->residentsupport->reviewOpinion;?></th>
                      <th><?php echo  $lang->residentsupport->reviewDate ;?></th>
                  </tr>
                  </thead>
                  <tbody>
                      <?php if(empty($templateDeptList)):?>
                          <tr>
                              <th colspan="7" style="text-align: center;"><?php echo $lang->noData;?></th>
                          </tr>
                      <?php else:?>
                          <?php
                          foreach($reviewList as $templateDeptId => $templateDeptInfo):
                              $deptReviewCount = $templateDeptInfo['total'];
                              $deptReviewList = $templateDeptInfo['data'];
                              $temDeptInfo = zget($templateDeptList, $templateDeptId);
                              $deptId = $temDeptInfo->deptId;
                              $deptName = zget($depts, $deptId);
                          ?>
                          <?php if($deptReviewCount == 0):?>
                          <tr>
                              <th>
                                  <?php echo $deptName; ?>
                              </th>
                              <td colspan="6">
                                  <?php echo $lang->noData;?>
                              </td>
                          </tr>
                          <?php else:?>
                              <tr>
                                  <th rowspan="<?php echo $deptReviewCount;?>">
                                      <?php echo $deptName; ?>
                                  </th>
                              <?php
                                  foreach ($deptReviewList as $version => $versionDutyInfo):
                                      $versionReviewCount = $versionDutyInfo['total'];
                                      $versionReviewList  = $versionDutyInfo['data'];
                              ?>
                                  <th rowspan="<?php echo $versionReviewCount;?>">
                                      <?php echo $reviewVersionList[$templateDeptId][$version]->createdDate ; ?>
                                  </th>

                                      <?php
                                      foreach ($versionReviewList as $nodeCode => $reviewNodeInfo):
                                          $nodeReviewCount = $reviewNodeInfo['total'];
                                          $nodeReviewList  = $reviewNodeInfo['data'];
                                          $nodeInfo = $reviewNodeInfo['info'];
                                          if($nodeReviewCount > 0):
                                           $nodeFirstReviewInfo = $nodeReviewList[0];
                                          ?>
                                              <th rowspan="<?php echo $nodeReviewCount;?>">
                                                  <?php echo zget($lang->residentsupport->temDeptNodeCodeLableList, $nodeCode) ; ?>
                                              </th>
                                              <td><?php echo zget($users, $nodeFirstReviewInfo->reviewer); ?></td>
                                              <td><?php echo zget($lang->residentsupport->temDeptReviewResultLableLit, $nodeFirstReviewInfo->status);?></td>
                                              <td><?php echo $nodeFirstReviewInfo->comment;?></td>
                                              <td><?php echo $nodeFirstReviewInfo->reviewTime;?></td>
                                          </tr>
                                          <?php
                                            if($nodeReviewCount >1):
                                            unset($nodeReviewList[0]);
                                            foreach ($nodeReviewList as $reviewInfo):
                                          ?>
                                          <tr>
                                              <td><?php echo zget($users, $reviewInfo->reviewer); ?></td>
                                              <td><?php echo zget($lang->residentsupport->temDeptReviewResultLableLit, $reviewInfo->status);?></td>
                                              <td><?php echo $reviewInfo->comment;?></td>
                                              <td><?php echo $reviewInfo->reviewTime;?></td>
                                          </tr>

                                          <?php
                                            endforeach;
                                          endif;
                                          ?>
                                      <?php endif;?>

                                      <?php endforeach;?>

                                  <?php endforeach;?>

                          <?php endif;?>
                         <?php
                          endforeach;
                        ?>
                  <?php endif;?>

                  </tbody>
              </table>
          </div>
      </div>
  </div>

    <div class="cell">

        <?php include '../../common/view/action.html.php';?>

    </div>

  </div>
  <div class="side-col col-4">
      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->residentsupport->basicInfo;?></div>
              <div class='detail-content'>
                  <table class='table table-data'>
                      <tbody>
                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->templateId;?></th>
                              <td><?php echo $templateInfo->id ;?></td>
                          </tr>
                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->name;?></th>
                              <td><?php echo $templateInfo->name ;?></td>
                          </tr>
                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->type;?></th>
                              <td><?php echo zget($lang->residentsupport->typeList, $templateInfo->type);?></td>
                          </tr>
                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->subType;?></th>
                              <td><?php echo zget($lang->residentsupport->subTypeList, $templateInfo->subType);?></td>
                          </tr>
                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->dutyDate;?></th>
                              <td> <?php echo $templateInfo->startDate . '~'. $templateInfo->endDate ;?></td>
                          </tr>

                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->enable;?></th>
                              <td> <?php echo zget($lang->residentsupport->enableList, $templateInfo->enable) ;?></td>
                          </tr>

                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->createdBy;?></th>
                              <td> <?php echo zget($users, $templateInfo->createdBy) ;?></td>
                          </tr>

                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->createdTime;?></th>
                              <td> <?php echo $templateInfo->createdTime ;?></td>
                          </tr>

                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->editBy;?></th>
                              <td> <?php echo zget($users, $templateInfo->editBy) ;?></td>
                          </tr>

                          <tr>
                              <th class='w-120px'><?php echo $lang->residentsupport->editByTime;?></th>
                              <td> <?php echo $templateInfo->editByTime == '' || $templateInfo->editByTime == '0000-00-00 00:00:00'?'':$templateInfo->editByTime ;?></td>
                          </tr>
                      </tbody>
                  </table>

              </div>
          </div>
      </div>
      <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->residentsupport->deptBasicInfo;?></div>
          <div class='detail-content article-content'>
              <table class='table ops  table-fixed'>
                  <thead>
                      <tr>
                          <th class='w-120px'><?php echo $lang->residentsupport->deptId;?></th>
                          <td><?php echo $lang->residentsupport->status;?></td>
                      </tr>
                  </thead>
                  <tbody>

                  <?php if(empty($templateDeptList)):?>
                      <tr>
                          <th colspan="2" style="text-align: center;"><?php echo $lang->noData;?></th>
                      </tr>
                  <?php
                  else:
                      foreach ($templateDeptList as $templateDeptInfo):
                          $deptId = $templateDeptInfo->deptId;
                          $deptName = zget($depts, $deptId);
                          $status = $templateDeptInfo->status;
                  ?>
                      <tr>
                          <th class='w-120px'><?php echo $deptName; ?></th>
                          <td><?php echo zget($lang->residentsupport->temDeptStatusDescList, $status); ?></td>
                      </tr>
                      <?php
                        endforeach;
                      endif;
                      ?>
                  </tbody>
              </table>

          </div>
      </div>
    </div> 

  </div>
</div>
<?php include '../../common/view/footer.html.php';?>

