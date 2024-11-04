<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
    .body-modal #mainMenu>.btn-toolbar .page-title {
        width: auto;
    }
    .table-data tbody>tr>th {
        width: 86px;
        padding-left: 0;
        font-weight: 400;
        color: #838a9d;
        text-align: right;
        vertical-align: middle;
    }
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php $browseLink = $app->session->secondorderList != false ? $app->session->secondorderList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $secondorder->code?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->secondorder->summary;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($secondorder->summary) ? nl2br($secondorder->summary) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->secondorder->desc;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($secondorder->desc) ? nl2br($secondorder->desc) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <?php if($secondorder->ifAccept == 1): ?>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->secondorder->acceptanceCondition;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($secondorder->acceptanceCondition) ? nl2br($secondorder->acceptanceCondition) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <?php endif; ?>
        <?php if($secondorder->completeStatus == 1 && !empty($secondorder->completionDescription)): ?>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->secondorder->completionDescription;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($secondorder->completionDescription) ? nl2br($secondorder->completionDescription) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <?php endif; ?>
        <?php if($secondorder->type == 'consult'): ?>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->secondorder->consultRes;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($secondorder->consultRes) ? nl2br($secondorder->consultRes) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        <?php elseif ($secondorder->type == 'test'): ?>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->secondorder->testRes;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($secondorder->testRes) ? nl2br($secondorder->testRes) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        <?php else: ?>
            <div class="detail">
                <div class="detail-title"><?php echo $secondorder->type == 'support' ? $lang->secondorder->supportRes : $lang->secondorder->dealRes;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($secondorder->dealRes) ? nl2br($secondorder->dealRes) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        <?php endif; ?>

        <div class='detail'>
            <div class="detail-title"><?php echo $lang->secondorder->filelist;?></div>
            <div class='detail-content article-content'>
                <?php
                if($secondorder->files){
                    echo $this->fetch('file', 'printFiles', array('files' => $secondorder->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                }else{
                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                }
                ?>
            </div>
        </div>
        <div class='detail'>
            <div class="detail-title"><?php echo $lang->secondorder->deliverable;?></div>
            <div class='detail-content article-content'>
                <?php
                if($secondorder->deliverFiles){
                    echo $this->fetch('file', 'printFiles', array('files' => $secondorder->deliverFiles, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                }else{
                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                }
                ?>
            </div>
        </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->secondorder->progress;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($secondorder->progress) ? nl2br($secondorder->progress) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>


        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=secondorder&objectID=$secondorder->id");?>
    </div>
      <?php if(common::hasPriv('secondorder','getProgressInfo')):?>
          <div class="cell">
              <div class="detail-title"><?php echo $lang->secondorder->conclusionInfo; ?></div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->secondorder->secondLineDevelopmentPlan; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($secondorder->secondLineDevelopmentPlan) ? nl2br($secondorder->secondLineDevelopmentPlan) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->secondorder->progressQA; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($secondorder->progressQA) ? nl2br($secondorder->progressQA) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->secondorder->secondLineDevelopmentStatus; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($secondorder->secondLineDevelopmentStatus) ? zget($lang->secondorder->secondLineDepStatusList,$secondorder->secondLineDevelopmentStatus) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->secondorder->secondLineDevelopmentApproved; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($secondorder->secondLineDevelopmentApproved) ? zget($lang->secondorder->secondLineDepApprovedList,$secondorder->secondLineDevelopmentApproved) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->secondorder->secondLineDevelopmentRecord; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($secondorder->secondLineDevelopmentRecord) ? zget($lang->secondorder->secondLineDevelopmentRecordList,$secondorder->secondLineDevelopmentRecord) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>

          </div>
      <?php endif;?>
    <div class="cell"><?php include '../../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
        $statusList = $secondorder->formType == 'external' ? [
            'toconfirmed'
        ] : [
            'toconfirmed', 'backed'
        ];
        $closeflag = $this->loadModel('secondorder')->isClickable($secondorder, 'close');
        if(in_array($secondorder->status,$statusList) and $app->user->account == $secondorder->createdBy){
            common::printIcon('secondorder', 'edit', "secondorderID=$secondorder->id", $secondorder, 'button');
        }
        $dealUser = explode(',', trim($secondorder->dealUser, ','));
        if($secondorder->status == 'toconfirmed' and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin')){
            common::printIcon('secondorder', 'confirmed', "secondorderID=$secondorder->id", $secondorder, 'list', 'checked', '', 'iframe', true);
        }
        $statusList = ['assigned', 'tosolve'];
        if(in_array($secondorder->status,$statusList) and ($app->user->account == $secondorder->dealUser || $app->user->account == 'admin')){
            common::printIcon('secondorder', 'deal', "secondorderID=$secondorder->id", $secondorder, 'button', 'time', '', 'iframe', true);
        }
        //迭代34：自建工单状态 = 待交付、交付审批中、已交付且是否最终移交为否可以关联工单可以关联多个对外移交
        if(
            (
                ($secondorder->formType == 'external' && $secondorder->status == 'todelivered' ) //外部工单只会有一次待交付，故不再加其他条件判断是否移交过
                || ($secondorder->formType == 'internal' && in_array($secondorder->status, ['todelivered','indelivery','delivered']) && ($secondorder->finallyHandOver == '2' || empty($secondorder->finallyHandOver)))
            )
            && (
                ($secondorder->type != 'support' and $secondorder->type != 'consult')
                or ($secondorder->type == 'consult' and $secondorder->handoverMethod == 'sectransfer')
            )
        ){
            $secondorder->approver = ''; //加此行为了解决跳转sectransfer 检测isClickable 方法时，报未定义approver 属性问题
            common::printIcon('sectransfer', 'create', "secondorderId=$secondorder->id", $secondorder, 'button', 'time', '', 'iframe', true, '', '发起对外移交');
        }
          common::printIcon('secondorder', 'editAssignedTo', "secondorderID=$secondorder->id", $secondorder, 'button', 'hand-right', '', 'iframe', true);
          common::printIcon('secondorder', 'copy', "secondorderID=$secondorder->id", $secondorder, 'button');
        if($secondorder->status == 'returned' and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin')){
            common::printIcon('secondorder', 'returned', "secondorderID=$secondorder->id", $secondorder, 'list', 'back', '', 'iframe', true);
        }
          if($secondorder->status != 'closed') {
              //common::printIcon('secondorder', 'close', "secondorderID=$secondorder->id", $secondorder, 'list','off', '', 'iframe', true);
              if(common::hasPriv('secondorder', 'close'))
              {
                  if($closeflag)
                  {
                      echo "<a  href='javascript:;' onclick='closeCheck(".$secondorder->finallyHandOver.",".$secondorder->id.")' class='btn ' title='{$this->lang->secondorder->close}'><i class='icon-secondorder-close icon-off'></i></a>";
                  }
                  else
                  {
                      common::printIcon('secondorder', 'close', "secondorderID=$secondorder->id", $secondorder, 'button','off', '', 'iframe ', true," disabled");

                  }
              }
          }
          common::printIcon('secondorder', 'delete', "secondorderID=$secondorder->id", $secondorder, 'button', 'trash', '', 'iframe', true);
        common::printIcon('secondorder', 'editSpecialQA', "secondorderID=$secondorder->id", $secondorder, 'button', 'edit', '', 'iframe', true);
        ?>
          <a  data-app="secondorder" href="<?php echo $this->createLink('secondorder','close',"secondorderID=$secondorder->id").'?onlybody=yes';?>" id="closed<?php echo $secondorder->id?>"   class="btn iframe hidden " ></a>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->secondorder->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
            <?php if('external' == $secondorder->formType): ?>
            <!--外部单号-->
            <tr>
                <th class="w-100px"><?php echo $lang->secondorder->externalCode;?></th>
                <td><?php echo $secondorder->externalCode;?></td>
            </tr>
                <?php if($secondorder->subtype != 'a5'):?>
                    <!--任务来源平台-->
                    <tr>
                        <th class="w-100px"><?php echo $lang->secondorder->sourcePlatform;?></th>
                        <td><?php echo $secondorder->sourcePlatform;?></td>
                    </tr>
                <?php endif;?>
            <?php endif; ?>
            <?php if($secondorder->subtype != 'a5'):?>
                <!--任务来源背景-->
                <tr>
                    <th class="w-100px"><?php echo $lang->secondorder->sourceBackground;?></th>
                    <td><?php echo zget($lang->secondorder->sourceBackgroundList, $secondorder->sourceBackground, $secondorder->sourceBackground);?></td>
                </tr>
            <?php endif;?>
            <!--来源方式-->
            <tr>
                <th class="w-100px"><?php echo $lang->secondorder->source;?></th>
                <td><?php echo zget($lang->secondorder->sourceList, $secondorder->source, '');?></td>
            </tr>
            <!--任务分类-->
            <tr>
                <th class="w-100px"><?php echo $lang->secondorder->type;?></th>
                <td><?php echo zget($lang->secondorder->typeList, $secondorder->type, '');?></td>
            </tr>
            <!--任务子类-->
            <tr>
                <th class="w-100px"><?php echo $lang->secondorder->subtype;?></th>
                <td><?php echo zget($childTypeList, $secondorder->subtype, '');?></td>
            </tr>
            <!--应用系统-->
            <tr>
                <th><?php echo $lang->secondorder->app;?></th>
                <td>
                    <?php echo $secondorder->app ? html::a(
                            $this->createLink('application', 'view', 'id=' . $secondorder->app),
                            zget($apps, $secondorder->app)->name, '', "style='color: #0c60e1;'") : ''; ?>
                </td>
            </tr>
            <!--承建单位-->
            <tr>
                <th class="w-100px"><?php echo $lang->secondorder->team;?></th>
                <td><?php echo zget($lang->application->teamList, $secondorder->team);?></td>
            </tr>
            <!--业务司局-->
            <tr>
                <th class="w-100px"><?php echo $lang->secondorder->union;?></th>
                <td><?php echo zget($lang->opinion->unionList, $secondorder->union);?></td>
            </tr>
            <?php if($secondorder->subtype == 'a5'):?>
                <!--请求类别-->
                <tr>
                    <th class="w-100px"><?php echo $lang->secondorder->requestCategory ;?></th>
                    <td><?php echo zget($lang->secondorder->requestCategoryList, $secondorder->requestCategory);?></td>
                </tr>
                <!--来电单位-->
                <tr>
                    <th class="w-100px"><?php echo $lang->secondorder->callUnit ;?></th>
                    <td><?php echo $secondorder->callUnit;?></td>
                </tr>
                <!--来电单位联系方式-->
                <tr>
                    <th class="w-100px"><?php echo $lang->secondorder->callUnitPhone ;?></th>
                    <td><?php echo $secondorder->callUnitPhone;?></td>
                </tr>
                <!--紧迫程度-->
                <tr>
                    <th class="w-100px"><?php echo $lang->secondorder->urgencyLevel ;?></th>
                    <td><?php echo zget($lang->secondorder->urgencyDegreeList, $secondorder->urgencyLevel);?></td>
                </tr>
            <?php endif;?>
            <!--期望完成日期-->
            <tr>
                <th><?php echo $lang->secondorder->exceptDoneDate;?></th>
                <td><?php echo $secondorder->exceptDoneDate != '0000-00-00' ? $secondorder->exceptDoneDate : '';?></td>
            </tr>
            <!--联系人-->
            <tr>
                <th><?php echo $lang->secondorder->contacts;?></th>
                <td><?php echo zget($users, $secondorder->contacts, $secondorder->contacts, $secondorder->contacts);?></td>
            </tr>
            <!--联系电话-->
            <tr>
                <th><?php echo $lang->secondorder->contactsPhone;?></th>
                <td><?php echo $secondorder->contactsPhone;?></td>
            </tr>
            <!--发起部门-->
            <tr>
                <th><?php echo $lang->secondorder->createdDept;?></th>
                <td><?php echo $secondorder->subtype != 'a5'?zget($depts, $secondorder->createdDept, ''):$secondorder->externalDept;?></td>
            </tr>
            <!--配合人员-->
            <tr>
                <th><?php echo $lang->secondorder->relevantUser;?></th>
                <td><?php echo $relevantUsers;?></td>
            </tr>
            <!--所属(外部)项目/任务-->
            <tr>
                <th class="w-100px"><?php echo $lang->secondorder->cbpProject;?></th>
                <td><?php echo zget($outsideplan, $secondorder->cbpProject);?></td>
            </tr>
            <!--计划任务-->
            <tr>
                <th><?php echo $lang->secondorder->taskIdentification;?></th>
                <td><?php echo $lang->secondorder->taskIdentificationList[$secondorder->taskIdentification]; ?></td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->secondorder->flowStatus;?></div>
              <div class='detail-content'>
                  <table class='table table-data'>
                      <tbody>
                      <!--流程状态-->
                      <tr>
                          <th><?php echo $lang->secondorder->status;?></th>
                          <td><?php echo zget($lang->secondorder->statusList, $secondorder->status, '');?></td>
                      </tr>
                      <!--是否受理-->
                      <tr>
                          <th class="w-100px"><?php echo $lang->secondorder->ifAccept;?></th>
                          <td><?php
                              if(!empty($secondorder->ifAccept) || $secondorder->ifAccept === '0'){
                                  echo zget($lang->secondorder->ifAcceptList, $secondorder->ifAccept, '');
                              }elseif (!empty($secondorder->ifReceived)){
                                  echo zget($lang->secondorder->ifReceivedList, $secondorder->ifReceived, '');
                              }else{
                                  echo '';
                              }
                              ?></td>
                      </tr>
                      <!--待处理人-->
                      <tr>
                          <th class="w-100px"><?php echo $lang->secondorder->dealUser;?></th>
                          <td><?php echo zmget($users, $secondorder->dealUser, '');?></td>
                      </tr>
                      <!--受理人-->
                      <tr>
                          <th><?php echo $lang->secondorder->acceptUser;?></th>
                          <td><?php echo zget($users, $secondorder->acceptUser, '');?></td>
                      </tr>
                      <!--受理部门-->
                      <tr>
                          <th><?php echo $lang->secondorder->acceptDept;?></th>
                          <td><?php echo zget($depts, $secondorder->acceptDept, '');?></td>
                      </tr>
                      <!--实现方式-->
                      <?php if($secondorder->type != 'support'): ?>
                          <tr>
                              <th class="w-100px"><?php echo $lang->secondorder->implementationForm;?></th>
                              <td><?php echo zget($lang->secondorder->implementationFormList, $secondorder->implementationForm);?></td>
                          </tr>
                      <?php endif; ?>
                      <!--内部项目-->
                      <?php if($secondorder->type != 'support'): ?>
                          <tr>
                              <th class="w-100px"><?php echo $lang->secondorder->internalProject;?></th>
                              <td><?php echo zget($projectList, $secondorder->internalProject);?></td>
                          </tr>
                      <?php endif;?>
                      <!--完成情况-->
                      <tr>
                          <th><?php echo $lang->secondorder->completeStatus;?></th>
                          <td><?php echo zget($lang->secondorder->completeStatusList, $secondorder->completeStatus, '');?></td>
                      </tr>
                      <!--移交方式-->
                      <tr>
                          <th><?php echo $lang->secondorder->handoverMethod;?></th>
                          <td><?php echo zget($lang->secondorder->handoverMethodList, $secondorder->handoverMethod, '');?></td>
                      </tr>
                      <?php if($secondorder->subtype != 'a5'):?>
                          <!--计划开始时间-->
                          <tr>
                              <th><?php echo $lang->secondorder->planstartDate;?></th>
                              <td><?php echo $secondorder->planstartDate!= '0000-00-00' ? $secondorder->planstartDate : '';?></td>
                          </tr>
                          <!--计划结束时间-->
                          <tr>
                              <th><?php echo $lang->secondorder->planoverDate;?></th>
                              <td><?php echo $secondorder->planoverDate!= '0000-00-00' ? $secondorder->planoverDate : '';?></td>
                          </tr>
                          <!--实际开始时间-->
                          <tr>
                              <th><?php echo $lang->secondorder->startDate;?></th>
                              <td><?php echo $secondorder->startDate!= '0000-00-00' ? $secondorder->startDate : '';?></td>
                          </tr>
                          <!--实际结束时间-->
                          <tr>
                              <th><?php echo $lang->secondorder->overDate;?></th>
                              <td><?php echo $secondorder->overDate!= '0000-00-00' ? $secondorder->overDate : '';?></td>
                          </tr>
                      <?php endif;?>
                      <!--由谁创建-->
                      <tr>
                          <th><?php echo $lang->secondorder->createdBy;?></th>
                          <td><?php echo zget($users, $secondorder->createdBy, '');?></td>
                      </tr>
                      <!--创建时间-->
                      <tr>
                          <th><?php echo $lang->secondorder->createdDate;?></th>
                          <td><?php echo $secondorder->createdDate;?></td>
                      </tr>
                      <?php $editedBy = zget($users, $secondorder->editedBy, ''); if(!empty($editedBy)): ?>
                      <!--由谁编辑-->
                      <tr>
                          <th><?php echo $lang->secondorder->editedBy;?></th>
                          <td><?php echo $editedBy;?></td>
                      </tr>
                      <!--编辑时间-->
                      <tr>
                          <th><?php echo $lang->secondorder->editedDate;?></th>
                          <td><?php echo $secondorder->editedDate;?></td>
                      </tr>
                      <?php endif; ?>
                      <?php $closedBy = zget($users, $secondorder->closedBy, ''); if(!empty($closedBy)): ?>
                      <!--由谁关闭-->
                      <tr>
                          <th><?php echo $lang->secondorder->closedBy;?></th>
                          <td><?php echo $closedBy;?></td>
                      </tr>
                      <!--关闭时间-->
                      <tr>
                          <th><?php echo $lang->secondorder->closedDate;?></th>
                          <td><?php echo $secondorder->closedDate;?></td>
                      </tr>
                      <?php endif; ?>
                      <?php if(!empty($secondorder->closeReason)): ?>
                      <!--关闭原因-->
                      <tr>
                          <th><?php echo $lang->secondorder->closeReason;?></th>
                          <td><?php echo $secondorder->closeReason;?></td>
                      </tr>
                      <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->secondorder->correlation;?></div>
              <div class='detail-content'>
                  <table class='table table-data'>

                      <?php if($secondorder->formType == 'internal' && !empty($protransferDesc)):?>
                      <tr>
                              <th><?php echo $lang->secondorder->finallyHandOver;?></th>
                              <td><?php echo zget($this->lang->sectransfer->finallyHandOverList,$secondorder->finallyHandOver,'')?>

                              <?php echo  common::hasPriv('secondorder','editFinallyHandOver')|| $this->app->user->account =='admin' ? common::printIcon('secondorder', 'editFinallyHandOver', "secondorder=$secondorder->id", $secondorder, 'list','edit','','iframe',true) :'';?>
                              </td>
                      </tr>
                       <?php elseif($secondorder->formType == 'internal' && empty($protransferDesc)):?>
                          <tr>
                              <th><?php echo $lang->secondorder->finallyHandOver;?></th>
                              <td><?php echo zget($this->lang->sectransfer->finallyHandOverList,$secondorder->finallyHandOver,'')?>

                                  <?php echo  $this->app->user->account =='admin' ? common::printIcon('secondorder', 'editFinallyHandOver', "secondorder=$secondorder->id", $secondorder, 'list','edit','','iframe',true) :'';?>
                              </td>
                          </tr>
                      <?php endif;?>

                      <?php if($secondorder->subtype != 'a5'):?>
                        <!--关联对外移交-->
                        <tr>
                            <th><?php echo $lang->secondorder->protransferDesc;?></th>
                            <td>
                              <?php
                              if(!empty($protransferDesc)){
                                  foreach ($protransferDesc as $key => $item){
                                      echo html::a($this->createLink('sectransfer', 'view', 'id=' . $key), $key."(".$item.")", '') . "<br/>";
                                  }
                              }
                              ?>
                            </td>
                        </tr>
                      <?php endif;?>

                      <!--关联征信交付-->
                      <?php  if(!empty($creditList)):?>
                      <tr>
                          <th><?php echo $lang->secondorder->relatedCredit;?></th>
                          <td>
                              <?php
                                  foreach ($creditList as $key => $item){
                                      echo html::a($this->createLink('credit', 'view', 'id=' . $key), $item, '') . "<br/>";
                                  }
                              ?>
                          </td>
                      </tr>
                      <?php endif;?>

                      <!--所属任务-->
                      <tr>
                          <th><?php echo $lang->secondorder->task;?></th>
                          <td><?php if($task)  echo html::a('javascript:void(0)', $task->taskName, '', 'data-app="project" onclick="seturl('.$projectid->project.','.$task->id.')"')?></td>
                          <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondtaskurl"')?></td>
                      </tr>
                      <?php if($secondorder->subtype != 'a5'):?>
                          <!--制版申请-->
                          <tr>
                              <th><?php echo $lang->secondorder->buildName;?></th>
                              <td><?php if(isset($buildAndRelease->buildname) && $buildAndRelease->buildname)  echo html::a('javascript:void(0)', $buildAndRelease->buildname, '', 'data-app="project" onclick="newurl('.$projectid->project.','.$buildAndRelease->bid.')"')?></td>
                              <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondbuildurl"')?></td>
                          </tr>
                      <?php endif;?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      <?php if($secondorder->formType == 'external'): ?>
          <div class="cell">
              <div class="detail">
                  <div class="detail-title"><?php echo $lang->secondorder->feedback;?></div>
                  <div class='detail-content'>
                      <table class='table table-data'>
                          <tbody>
                          <!--外部审批结果-->
                          <tr>
                              <th><?php echo $lang->secondorder->externalStatus;?></th>
                              <td><?php echo zget($lang->secondorder->externalStatusList, $secondorder->externalStatus, '');?></td>
                          </tr>
                          <!--审批时间-->
                          <tr>
                              <th><?php echo $lang->secondorder->externalTime;?></th>
                              <td><?php echo $secondorder->externalTime;?></td>
                          </tr>
                          <?php if(!empty($secondorder->rejectUser)): ?>
                          <!--打回人-->
                          <tr>
                              <th class="w-100px"><?php echo $lang->secondorder->rejectUser;?></th>
                              <td><?php echo zget($users, $secondorder->rejectUser, $secondorder->rejectUser);?></td>
                          </tr>
                          <?php endif; ?>
                          <?php if(!empty($secondorder->rejectReason)): ?>
                          <!--打回原因-->
                          <tr>
                              <th><?php echo $lang->secondorder->rejectReason;?></th>
                              <td><?php echo $secondorder->rejectReason;?></td>
                          </tr>
                          <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      <?php endif;?>
      <?php if(!empty($consumeds)): ?>
      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->secondorder->consumedTitle;?></div>
              <div class='detail-content'>
                  <table class='table table-data'>
                      <tbody>
                      <tr>
                          <th class='w-100px'><?php echo $lang->secondorder->nodeUser;?></th>
                          <td class='text-center'><?php echo $lang->secondorder->before;?></td>
                          <td class='text-center'><?php echo $lang->secondorder->after;?></td>
                          <td class='text-left'><?php echo $lang->actions;?></td>
                      </tr>
                      <?php foreach($consumeds as $index => $c):?>
                          <tr>
                              <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                              <td class='text-center'><?php echo zget($lang->secondorder->statusList, $c->before, '-');?></td>
                              <td class='text-center'><?php echo zget($lang->secondorder->statusList, $c->after, '-');?></td>
                              <td class='c-actions text-left'>
                                  <?php
                                  if($index == count($consumeds) - 1) common::printIcon('secondorder', 'statusedit', "secondorderID={$secondorder->id}&consumedid={$c->id}", $secondorder, 'list', 'edit', '', 'iframe', true);
                                  ?>
                              </td>
                          </tr>
                      <?php endforeach;?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      <?php endif;?>
  </div>
</div>
<script>
    $("body").delegate("#triggerModal",'hide.zui.modal',function(){
        var isres = $('#iframe-triggerModal').contents().find("#reload").hasClass("reload");
        if(isres){
            parent.location.reload();
        }
    })

    function seturl(project,id){
       $.ajaxSettings.async = false;
          $.post(createLink('secondorder', 'ajaxGetProjectId', "project="+ project,''), function(data)
          {

          });
          $.ajaxSettings.async = true;
          var taskurl = createLink('task', 'view', 'id=' + id);
          $('#secondtaskurl').attr('href',taskurl);
          $('#secondtaskurl')[0].click();
    }
    function addurl(project,id){
          $.ajaxSettings.async = false;
          $.post(createLink('secondorder', 'ajaxGetProjectSession', "project="+ project,''), function(data)
          {

          });
          $.ajaxSettings.async = true;
          var releaseurl = createLink('projectrelease', 'view', 'id=' + id);
          $('#secondreleaseurl').attr('href',releaseurl);
          $('#secondreleaseurl')[0].click();
    }
    function newurl(project,id){
          $.ajaxSettings.async = false;
          $.post(createLink('secondorder', 'ajaxGetProjectBuild', "project="+ project,''), function(data)
          {

          });
          $.ajaxSettings.async = true;
          var buildurl = createLink('build', 'view', 'id=' + id);
          $('#secondbuildurl').attr('href',buildurl);
          $('#secondbuildurl')[0].click();
    }
    //关闭时检查工单是否最终移交
    function closeCheck(flag,id){
        if(flag == '2'){
            alert('工单没有完成全部移交，不能关闭！');
            return true;
        }else{
            $('#closed'+id).click();
        }
    }
</script>
<?php include '../../../common/view/footer.html.php';?>
