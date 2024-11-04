<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php
echo js::set('authStatusError', $this->lang->problem->authStatusError);
?>
<style>.body-modal #mainMenu>
    .btn-toolbar .page-title {width: auto;}
    .btn-nodes{display: inline !important;}

</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php if(!isonlybody()):?>
    <?php $browseLink = $app->session->problemList != false ? $app->session->problemList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
  <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $problem->code?></span>
    </div>
  </div>
  <?php if(!isonlybody()):?>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('problem', 'exportWord')) echo html::a($this->createLink('problem', 'exportWord', "problemID=$problem->id"), "<i class='icon-export'></i> {$lang->problem->exportWord}", '', "class='btn btn-primary'");?>
  </div>
  <?php endif;?>
</div>
<?php
   //内部问题但
   if(empty($problem->IssueId )) {
?>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
        <div class="detail">
            <div class="detail-title"><?php echo $lang->problem->abstract;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($problem->abstract) ? $problem->abstract: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->problem->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($problem->desc) ? $problem->desc: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->problem->reason;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($problem->reason) ? $problem->reason : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->problem->solution;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($problem->solution) ? $problem->solution: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <!--问题退回原因-->
        <?php if(!empty($problem->ReasonOfIssueRejecting)): ?>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->problem->ReasonOfIssueRejecting;?></div>
            <div class="detail-content article-content">
                <?php echo nl2br($problem->ReasonOfIssueRejecting);?>
            </div>
        </div>
       <?php endif; ?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->problem->plateMakInfo;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($problem->plateMakInfo) ? $problem->plateMakInfo : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php if(in_array($this->app->user->account,$progressLook) || $this->app->user->account == 'admin'):?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->problem->progress;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($problem->progress) ? $problem->progress : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php endif;?>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->problem->baseChangeTip; ?></div>
            <div class="detail-content article-content">
                <?php if (!empty($changeInfo)): ?>
                    <table class="table changeInfo " style="table-layout: fixed">
                        <tr>
                            <th class="w-110px"><?php echo $this->lang->problem->changeVersion; ?></th>
                            <td colspan="2.5"><?php echo max($changeInfo)->changeVersion;?></td>
                            <th><?php echo $this->lang->problem->successVersion; ?></th>
                            <td colspan="2.5"><?php echo max($changeInfo)->successVersion;?></td>
                        </tr>
                        <tr>
                            <th class="w-70px"><?php echo $this->lang->problem->baseChangeUser; ?></th>
                            <th class="w-70px"><?php echo $this->lang->problem->changeReason; ?></th>
                            <th class="w-100px"><?php echo $this->lang->problem->baseChangeContent; ?></th>
                            <th class="w-150px"><?php echo $this->lang->problem->changeCommunicate; ?></th>
                            <th class="w-80px"><?php echo sprintf($this->lang->problem->delayStatus,$lang->problem->changeName); ?></th>
                            <th class="w-100px"><?php echo $this->lang->problem->actionTime; ?></th>
                        </tr>
                        <?php foreach ($changeInfo as $key => $item):?>


                            <tr>
                                <td><?php echo zget($users,$item->changeUser);?></td>
                                <td ><?php echo $item->changeReason;?></td>
                                <td><?php echo $item->changeContent;?></td>
                                <td><?php echo $item->changeCommunicate;?></td>
                                <td><?php echo sprintf(zget($lang->problem->delayStatusList,$item->changeStatus),$lang->problem->changeName);?></td>
                                <td><?php echo $item->changeDate;?></td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                <?php else: ?>
                    <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                <?php endif; ?>

            </div>
        </div>
      <?php if($problem->files) echo $this->fetch('file', 'printFiles', array('files' => $problem->files, 'fieldset' => 'true'));?>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=problem&objectID=$problem->id");?>
    </div>
      <!-- 审核变更申请单 -->
      <?php if (!empty($changeNodes)): ?>
          <div class="cell">
              <div class="detail">
                  <div class="clearfix">
                      <div class="detail-title pull-left"><?php echo sprintf($lang->problem->delayreviewOpinion,$lang->problem->changeName); ?></div>
                      <div class="detail-title pull-right">
                          <?php
                          if(common::hasPriv('problem', 'showdelayHistoryNodes')) echo html::a($this->createLink('problem', 'showdelayHistoryNodes', 'id='.$problem->id.'&type=problemChange', '', true), "<i class='icon icon-history'></i>".sprintf($lang->problem->showdelayHistoryNodes,$lang->problem->changeName), '', "data-toggle='modal' data-type='iframe' data-width='70%'  class='btn btn-primary btn-nodes iframe'");
                          ?>
                      </div>
                  </div>
                  <div class="detail-content article-content">
                      <table class="table ops">
                          <tr>
                              <th class="w-180px"><?php echo $lang->problem->statusOpinion; ?></th>
                              <th class="w-180px"><?php echo $lang->problem->reviewer; ?></th>
                              <th class="w-180px"><?php echo $lang->problem->reviewResult; ?></th>
                              <th style="width:370px;"><?php echo $lang->problem->dealOpinion; ?></th>
                              <th class="w-180px"><?php echo $lang->problem->reviewOpinionTime; ?></th>
                          </tr>
                          <?php
                          if($changeNodes[200]->nodeCode!='toManager'){
                              $lang->problem->reviewNodeStatusLableList['toManager']='公司领导处理';
                              $lang->problem->reviewNodeStatusList[200]='toProductManager';
                          }else{
                              $lang->problem->reviewNodeStatusLableList['toManager']='分管领导处理';
                              $lang->problem->reviewNodeStatusList[200]='toManager';
                          }
                          ?>
                          <?php foreach ($lang->problem->reviewNodeStatusList as $key => $reviewNode):
                              $reviewerUserTitle = '';
                              $reviewerUsersShow = '';
                              $realReviewer = new stdClass();
                              $realReviewer->status = '';
                              $realReviewer->comment = '';
                              if (isset($changeNodes[$key])) {
                                  $currentNode = $changeNodes[$key];
                                  $reviewers = $currentNode->reviewers;
                                  if (!(is_array($reviewers) && !empty($reviewers))) {
                                      continue;
                                  }
                                  //所有审核人
                                  $reviewersArray = array_column($reviewers, 'reviewer');
                                  $userCount = count($reviewersArray);
                                  if ($userCount > 0) {
                                      $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                      $reviewerUserTitle = implode(',', $reviewerUsers);
                                      $subCount = 10;
                                      $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                      //获得实际审核人
                                      $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                      if($realReviewer->status == 'wait'){
                                          continue;
                                      }
                                  }
                              }else{
                                  continue;
                              }
                              ?>
                              <tr>

                                  <th><?php echo $reviewNode=zget($lang->problem->reviewNodeStatusLableList, $reviewNode); ?></th>
                                  <td title="<?php echo $reviewerUserTitle; ?>"><?php echo $reviewerUsersShow; ?></td>
                                  <?php
                                  if($reviewNode=='产品创新部处理'){
                                      $lang->problem->reviewStatusList['pass']='通过（不上报）';
                                  }else{
                                      $lang->problem->reviewStatusList['pass']='通过';
                                  }
                                  ?>
                                  <td><?php  $status = zget($lang->problem->reviewStatusList, $realReviewer->status, '');echo $status; ?>
                                      <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'||$realReviewer->status == 'report'): ?>
                                          &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                      <?php endif; ?>
                                  </td>
                                  <td><?php echo $realReviewer->comment ?></td>
                                  <td><?php echo $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: ''?></td>
                              </tr>
                          <?php endforeach; ?>
                      </table>
                  </div>
              </div>
          </div>
      <?php endif; ?>
      <!-- 审核延期申请单 -->
      <?php if (!empty($delayNodes)): ?>
          <div class="cell">
              <div class="detail">
                  <div class="clearfix">
                      <div class="detail-title pull-left"><?php echo sprintf($lang->problem->delayreviewOpinion,$lang->problem->delayName); ?></div>
                      <div class="detail-title pull-right">
                          <?php
                          if(common::hasPriv('problem', 'showdelayHistoryNodes')) echo html::a($this->createLink('problem', 'showdelayHistoryNodes', 'id='.$problem->id, '', true), sprintf($lang->problem->showdelayHistoryNodes,$lang->problem->delayName), '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                          ?>
                      </div>
                  </div>
                  <div class="detail-content article-content">
                      <table class="table ops">
                          <tr>
                              <td class="w-180px"><?php echo $lang->problem->statusOpinion; ?></td>
                              <td class="w-180px"><?php echo $lang->problem->reviewer; ?></td>
                              <td class="w-180px"><?php echo $lang->problem->reviewResult; ?></td>
                              <td style="width:370px;"><?php echo $lang->problem->dealOpinion; ?></td>
                              <td class="w-180px"><?php echo $lang->problem->reviewOpinionTime; ?></td>
                          </tr>
                          <?php foreach ($lang->problem->reviewNodeStatusList as $key => $reviewNode):
                              $reviewerUserTitle = '';
                              $reviewerUsersShow = '';
                              $realReviewer = new stdClass();
                              $realReviewer->status = '';
                              $realReviewer->comment = '';
                              if (isset($delayNodes[$key])) {
                                  $currentNode = $delayNodes[$key];
                                  $reviewers = $currentNode->reviewers;
                                  if (!(is_array($reviewers) && !empty($reviewers))) {
                                      continue;
                                  }
                                  //所有审核人
                                  $reviewersArray = array_column($reviewers, 'reviewer');
                                  $userCount = count($reviewersArray);
                                  if ($userCount > 0) {
                                      $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                      $reviewerUserTitle = implode(',', $reviewerUsers);
                                      $subCount = 10;
                                      $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                      //获得实际审核人
                                      $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                  }
                              }else{
                                  continue;
                              }
                              ?>
                              <tr>
                                  <th><?php echo zget($lang->problem->reviewNodeStatusLableList, $reviewNode); ?></th>
                                  <td title="<?php echo $reviewerUserTitle; ?>"><?php echo $reviewerUsersShow; ?></td>
                                  <td><?php  $status = zget($lang->problem->reviewStatusList, $realReviewer->status, '');echo $status ? sprintf($status,$lang->problem->delayName): ''; ?>
                                      <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'||$realReviewer->status == 'report'): ?>
                                          &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                      <?php endif; ?>
                                  </td>
                                  <td><?php echo $realReviewer->comment ?></td>
                                  <td><?php echo $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: ''?></td>
                              </tr>
                          <?php endforeach; ?>
                      </table>
                  </div>
              </div>
          </div>
      <?php endif; ?>
      <?php if(common::hasPriv('problem','getProgressInfo')):?>
          <div class="cell">
              <div class="detail-title"><?php echo $lang->problem->conclusionInfo; ?></div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentPlan; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($problem->secondLineDevelopmentPlan) ? nl2br($problem->secondLineDevelopmentPlan) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->problem->progressQA; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($problem->progressQA) ? nl2br($problem->progressQA) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentStatus; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($problem->secondLineDevelopmentStatus) ? zget($lang->problem->secondLineDepStatusList,$problem->secondLineDevelopmentStatus) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentApproved; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($problem->secondLineDevelopmentApproved) ? zget($lang->problem->secondLineDepApprovedList,$problem->secondLineDevelopmentApproved) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentRecord; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($problem->secondLineDevelopmentRecord) ? zget($lang->problem->secondLineDevelopmentRecordList,$problem->secondLineDevelopmentRecord) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>

          </div>
      <?php endif;?>
    <div class="cell"><?php include '../../../common/view/action.html.php';?></div>

    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php if(!isonlybody()):?>
        <?php common::printBack($browseLink);?>
        <?php endif;?>
        <div class='divider'></div>
        <?php
          common::printIcon('problem', 'edit', "problemID=$problem->id", $problem, 'button');
            $status = array('confirmed','assigned','toclose'); //20220930 待分配和待分析 或待开发且不是问题 高亮
            if(( $this->app->user->admin or ( $this->app->user->account == $problem->dealUser && (in_array($problem->status,$status) or ($problem->status == 'feedbacked' && $problem->type == 'noproblem'))))  && $problem->status != 'deleted')//非当前处理人，图标置灰不能操作
            {
                echo '<button type="button" class="btn" title="' . $lang->problem->deal . '" onclick="isClickable('.$problem->id.', \'deal\')"><i class="icon-common-suspend icon-time"></i></button>';
                common::printIcon('problem', 'deal', "problemID=$problem->id", $problem, 'button', 'time', '', 'iframe hidden', true, 'id=isClickable_deal' . $problem->id);
            }
            else
            {
                echo '<button type="button" class="disabled btn" title="' . $lang->problem->deal . '"><i class="icon-common-suspend disabled icon-time"></i><span class="text">&nbsp' . $lang->problem->deal .'</span></button>';
            }
            if((in_array($this->app->user->account,$progressLook) && 'closed' != $problem->status) || $this->app->user->account == 'admin'){
                common::printIcon('problem', 'editSpecial', "problemID=$problem->id", $problem, 'button', 'edit', '', 'iframe', true);
            }
            else
            {
                echo common::hasPriv('problem','editSpecial') ?  '<button type="button" class="disabled btn" title="' . $lang->problem->editSpecial . '"><i class="icon-common-editspecial disabled icon-edit"></i><span class="text">&nbsp' . $lang->problem->editSpecial .'</span></button></button>' : '';
            }
        if(common::hasPriv('problem','editSpecialQA') || $this->app->user->account == 'admin'){
            common::printIcon('problem', 'editSpecialQA', "problemID=$problem->id", $problem, 'button', 'edit', '', 'iframe', true);
        }
        $delayFlag = common::hasPriv('problem', 'reviewdelay') && in_array($problem->changeStatus, array_keys($this->lang->problem->reviewNodeStatusLableList)) && in_array($this->app->user->account, explode(',', $problem->changeDealUser)) ;
        if($delayFlag){
            common::printIcon('problem', 'reviewdelay', "problemID=$problem->id", $problem, 'button', 'glasses', '', 'iframe', true);
        }
        //common::printIcon('problem', 'redeal', "problemID=$problem->id", $problem, 'button', 'time', '', 'iframe', true);
        common::printIcon('problem', 'editAssignedTo', "problemID=$problem->id", $problem, 'button', 'hand-right', '', 'iframe', true);
            //延期审批按钮
//        echo '<button type="button" class="btn" title="' . $lang->problem->postpone . '" onclick="delayCheck()"><i class="icon-common-delay icon-delay"></i><span class="text">&nbsp' . $lang->problem->postpone .'</span></button>';
        $disabled = empty($delayErrorMsg) ? '' : 'disabled';
        $disabled = '';
        //内部问题单 待开发、待分析、已关闭closed现在放开了 不高亮
//        if(in_array($problem->status,array('confirmed','assigned','returned'))){
                if(in_array($problem->status,array('confirmed','assigned','closed','returned'))){
            $disabled = 'disabled';
        }
        common::hasPriv('problem','delay') ? common::printIcon('problem', 'delay', "problemID=$problem->id", $problem, 'button', 'delay', '', 'iframe ' . $disabled, true, 'id=delayCheck', $lang->problem->postpone) :'';

        if( $problem->IssueId != null && $this->app->user->account == 'admin'){
            common::printIcon('problem', 'delete', "problemID=$problem->id", $problem, 'button', 'trash', '', 'iframe', true);
          }elseif(empty($problem->IssueId)){
            common::printIcon('problem', 'delete', "problemID=$problem->id", $problem, 'button', 'trash', '', 'iframe', true);
          }else{
            echo common::hasPriv('problem','delete') ?  '<button type="button" class="disabled btn" title="' . $lang->problem->delete . '"><i class="icon-common-suspend disabled icon-trash"></i></button>' : '';
          }
          common::printIcon('problem', 'close', "problemID=$problem->id", $problem, 'button', 'off', '', 'iframe', true);

        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->problem->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class="w-140px"><?php echo $lang->problem->type;?></th>
                <td><?php echo zget($lang->problem->typeList, $problem->type, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->problem->SolutionFeedback;?></th>
                  <td><?php echo zget($lang->problem->solutionFeedbackList, $problem->SolutionFeedback, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->problem->problemCause;?></th>
                  <td><?php echo zget($lang->problem->problemCauseList, $problem->problemCause, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->problem->repeatProblem;?></th>
                  <td><?php echo $repeat;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->occurDate;?></th>
                <td><?php echo $problem->occurDate;?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->problem->source;?></th>
                <td><?php echo zget($lang->problem->sourceList, $problem->source, '');?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->problem->dealUser;?></th>
                <td><?php echo zmget($users, $problem->dealUser)?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->status;?></th>
                <td><?php echo zget($lang->problem->statusList, $problem->status, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->ifRecive;?></th>
                  <td><?php echo zget($lang->problem->ifReturnList, $problem->ifReturn, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->app;?></th>
                <td>
                <?php
                $as = [];
                foreach(explode(',', $problem->app) as $app)
                {
                    if(!$app) continue;
                    $as[] = zget($apps, $app , "",$apps[$app]->name);
                }
                $app = implode(', ', $as);
                echo $app;
                ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->isPayment;?></th>
                <td>
                <?php
                $as = [];
                foreach(explode(',', $problem->isPayment) as $apptype)
                {
                    if(!$apptype) continue;
                    $as[] = zget($lang->application->isPaymentList, $apptype, "");
                }
                $applicationtype = implode(',', $as);
                echo $applicationtype;
                ?>
                </td>
              </tr>
              <!--tr>
                <th><?php echo $lang->problem->consumed;?></th>
                <td><?php echo $problem->consumed ? $problem->consumed : '';?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->state;?></th>
                <td><?php echo zget($lang->problem->stateList, $problem->state, '');?></td>
              </tr-->
              <tr>
                <th><?php echo $lang->problem->buildTimes;?></th>
                <td><?php echo $problem->buildTimes;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->changeVersion;?></th>
                  <td><?php echo empty($problem->changeVersion) ? 0 : $problem->changeVersion;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->successVersion;?></th>
                  <td><?php echo empty($problem->successVersion) ? 0 : $problem->successVersion;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->fixType;?></th>
                <td><?php echo zget($lang->problem->fixTypeList, $problem->fixType, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->projectPlan;?></th>
                <td>
                  <?php if($problem->projectPlan):?>
                  <?php echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplanid), $plan->name, '', "data-app='platform' style='color: #0c60e1;'");?>
                  <?php endif;?>
                </td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->stage;?></th>
                  <td><?php echo zget($executions,$problem->execution,'');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->task;?></th>
                  <td><?php if($task)  {
                      $i = 1;
                      foreach ($task as $key => $item) {
                           if(!$item) continue;
                            echo html::a('javascript:void(0)', $i.'、'.$item->taskName, '', 'data-app="project" onclick="seturl('.$problem->projectPlan.','.$item->id.')"').'；'.'<br>';
                           $i++;
                           }
                      }?>
                  </td>
                  <td class="hidden"><?php echo html::a('','','','data-app="project"  id="taskurl"')?></td>
              </tr>
              <tr>
                 <th><?php echo $lang->problem->buildName;?></th>
                 <td>
                     <?php  if(isset($buildAndRelease)):
                       foreach ($buildAndRelease as $item):
                          if(isset($item->buildname) && $item->buildname)
                          echo html::a('javascript:void(0)', $item->buildname, '', 'data-app="project" onclick="newurl('.$problem->projectPlan.','.$item->bid.')"').' <br>';
                       ?>
                      <?php endforeach;endif;?>
                 </td>
                 <td class="hidden"><?php echo html::a('','','','data-app="project"  id="problembuildurl"')?></td>
              </tr>
              <tr>
                 <th><?php echo $lang->problem->releaseName;?></th>
                 <td>
                     <?php  if(isset($buildAndRelease)):
                         foreach ($buildAndRelease as $item):
                             if(isset($item->releasename) && $item->releasename)
                                 echo html::a('javascript:void(0)', $item->releasename, '', 'data-app="project" onclick="addurl('.$problem->projectPlan.','.$item->rid.')"').' <br>';
                         ?>
                         <?php endforeach;endif;?>
                 </td>
                 <td class="hidden"><?php echo html::a('','','','data-app="project"  id="problemreleaseurl"')?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->product;?></th>
                  <td><?php if($problem->product) echo  $productName;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->productPlan;?></th>
                  <td><?php if($problem->productPlan) echo $productPlan;?></td>
              </tr>
              <!--20220311 新增--><!--需求收集 3569隐藏该字段-->
<!--              <tr class="--><?php //if('2024-01-31' < $problem->createdDate) echo 'hidden'; ?><!--">-->
<!--                  <th class='w-100px'>--><?php //echo $lang->problem->systemverify;?><!--</th>-->
<!--                  <td>--><?php //echo zget($lang->problem->needOptions, $problem->systemverify, '');?><!--</td>-->
<!--              </tr>-->
              <tr>
                  <th class='w-100px'><?php echo $lang->problem->verifyperson;?></th>
                  <td><?php echo zget($users, $problem->verifyperson, '');?></td>
              </tr>
<!--              <tr>-->
<!--                  <th class='w-100px'>--><?php //echo $lang->problem->laboratorytest;?><!--</th>-->
<!--                  <td>--><?php //echo zget($users, $problem->laboratorytest, '');?><!--</td>-->
<!--              </tr>-->
              <tr>
                <th><?php echo $lang->problem->relationModify;?></th>
                <td>
                  <?php
                  if(isset($objects['modify'])){
                  foreach($objects['modify'] as $objectID => $object):?>
                  <p><?php echo html::a($this->createLink('modify', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                  <?php endforeach; } ?>
                  <?php
                  if(isset($objects['modifycncc'])){
                  foreach($objects['modifycncc'] as $objectID => $object):?>
                  <p><?php echo html::a($this->createLink('modifycncc', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                  <?php endforeach; } ?>
                </td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->relationOutwardDelivery;?></th>
                  <td>
                      <?php
                      if(isset($objects['outwardDelivery'])){
                      foreach($objects['outwardDelivery'] as $objectID => $object): if($object == "") continue; ?>
                          <p><?php echo html::a($this->createLink('outwarddelivery', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                      <?php endforeach; } ?>
                  </td>
              </tr>
              <?php if(isset($objects['outwardDelivery']) && isset($objects['fix']) && $objects['fix']):?>
              <tr>
                <th><?php echo $lang->problem->relationFix;?></th>
                <td>
                  <?php
                  if(isset($objects['outwardDelivery'])){
                  foreach($objects['fix'] as $objectID => $object):?>
                  <p><?php echo html::a($this->createLink('info', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                  <?php endforeach; } ?>
                </td>
              </tr>
              <?php endif?>

              <tr>
                <th><?php echo $lang->problem->relationGain;?></th>
                <td>
                  <?php
                  if(isset($objects['gain'])){
                  foreach($objects['gain'] as $objectID => $object):?>
                  <p><?php echo html::a($this->createLink('info', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                  <?php endforeach; } ?>
                    <?php if(isset($objects['infoQz'])):?>
                    <?php foreach($objects['infoQz'] as $objectID => $object):?>
                        <p><?php echo html::a($this->createLink('infoqz', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                    <?php endforeach;?>
                    <?php endif;?>
                    <?php if(isset($objects['gainQz'])):?>
                        <?php foreach($objects['gainQz'] as $objectID => $object):?>
                            <p><?php echo html::a($this->createLink('infoqz', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                        <?php endforeach;?>
                    <?php endif;?>
                </td>
              </tr>

              <tr>
                  <th><?php echo $lang->problem->relationCredit;?></th>
                  <td>
                      <?php if(isset($objects['credit'])):?>
                          <?php foreach($objects['credit'] as $objectID => $object):?>
                              <p><?php echo html::a($this->createLink('credit', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                          <?php endforeach;?>
                      <?php endif;?>
                  </td>
              </tr>

              <tr>
                <th><?php echo $lang->problem->acceptUser;?></th>
                <td><?php echo zget($users, $problem->acceptUser, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->acceptDept;?></th>
                <td><?php echo zget($depts, $problem->acceptDept, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->createdBy;?></th>
                <td><?php echo zget($users, $problem->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->createdDate;?></th>
                <td><?php echo $problem->createdDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->editedBy;?></th>
                <td><?php echo zget($users, $problem->editedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->editedDate;?></th>
                <td><?php echo $problem->editedDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->closedBy;?></th>
                <td><?php echo zget($users, $problem->closedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->closedDate;?></th>
                <td><?php echo $problem->closedDate;?></td>
              </tr>
              <?php if(common::hasPriv('problem', 'updateStatusLinkage') || $this->app->user->account == 'admin'):?>
              <tr>
                  <th><?php echo $lang->problem->secureStatusLinkage;?></th>
                  <td>
                      <?php echo zget($this->lang->problem->secureStatusLinkageList,$problem->secureStatusLinkage,'');?>
                      <?php echo common::printIcon('problem', 'updateStatusLinkage', "problemID=$problem->id", $problem, 'list','edit','','iframe',true);?>
                  </td>
              </tr>
              <?php endif;?>
              <tr>
                  <th><?php echo $lang->problem->isExceedByTime;?>
                      <i title="<?php echo $lang->problem->isExceedByTimeHelp;?>" class="icon icon-help"></i>
                  </th>
                  <td>
                      <?php
                      $solvedTime   = strpos($problem->solvedTime,'0000-00-00') === false ? $problem->solvedTime : '';
                      $dealAssigned = strpos($problem->dealAssigned,'0000-00-00') === false ? $problem->dealAssigned : '';
                      if(empty($dealAssigned)){
                          echo $problem->isExceedByTime;
                      }else{
                          echo $problem->isExceedByTime . '(' . $dealAssigned . ' ~ ' . $solvedTime . ')';
                      }
                      ?>
                  </td>
              </tr>
              <?php if(isset($this->lang->problem->isExtendedUserList[$this->app->user->account]) || $this->app->user->account == 'admin'):?>
                  <tr>
                      <th><?php echo $lang->problem->isExtended;?></th>
                      <td>
                          <?php echo zget($this->lang->problem->isExtendedList,$problem->isExtended,'');?>
<!--                          --><?php //echo common::printIcon('problem', 'isExtended', "problemID=$problem->id", $problem, 'list','edit','','iframe',true);?>
                          <?php echo html::a($this->createLink('problem', 'isExtended', "problemID=$problem->id", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                      </td>
                  </tr>
              <?php endif;?>
              <tr>
                  <th><?php echo $lang->problem->PlannedTimeOfChange;?></th>
                  <td><?php echo strpos($problem->PlannedTimeOfChange,'0000-00-00') === false ? $problem->PlannedTimeOfChange : '';?></td>
              </tr>
             <?php if(in_array($this->app->user->account,explode(',',$this->lang->problem->examinationResultUpdateList['userList'])) || $this->app->user->account == 'admin'):?>
              <tr>
                  <th><?php echo $lang->problem->dealAssigned;?></th>
                  <td><?php echo strpos($problem->dealAssigned,'0000-00-00') === false ? $problem->dealAssigned : '';?></td>
              </tr>
              <?php endif;?>
              <tr>
                  <th><?php echo $lang->problem->solvedTime;?></th>
                  <td><?php echo strpos($problem->solvedTime,'0000-00-00') === false ? $problem->solvedTime : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->actualOnlineDate;?></th>
                  <td><?php echo strpos($problem->actualOnlineDate,'0000-00-00') === false ? $problem->actualOnlineDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->problem->completedPlan;?><span ><i class='icon icon-help' title="<?php echo $lang->problem->completedPlanTip ;?>"></i></span></th>
                  <td><?php echo zget($this->lang->problem->completedPlanList,$problem->completedPlan,'');?></td>
              </tr>
              <?php if(in_array($this->app->user->account,explode(',',$this->lang->problem->examinationResultUpdateList['userList'])) || $this->app->user->account == 'admin'):?>
              <tr>
                  <th><?php echo $lang->problem->examinationResult;?></th>
                  <td>
                      <?php echo zget($this->lang->problem->examinationResultList,$problem->examinationResult,'');?>
                      <?php echo common::hasPriv('problem', 'editExaminationResult')|| $this->app->user->account == 'admin'  ? common::printIcon('problem', 'editExaminationResult', "problemID=$problem->id", $problem, 'list','edit','','iframe',true)  : ''?>
                  </td>
              </tr>
            <?php endif;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
      <!--根据需求去掉-->
      <?php if(!empty($problem->delayResolutionDate)):?>
          <div class="cell">
              <div class="detail">
                  <div class="detail-title"><?php echo $lang->problem->delayInfo;?></div>
                  <div class='detail-content'>
                      <table class='table table-data '>
                          <tbody>
                          <tr>
                              <th class='w-140px'><?php echo $lang->problem->originalResolutionDate; ?></th>
                              <td><?php echo date('Y-m-d', strtotime($problem->originalResolutionDate)); ?></td>
                          </tr>
                          <tr>
                              <th><?php echo $lang->problem->delayResolutionDate;?></th>
                              <td><?php echo date('Y-m-d', strtotime($problem->delayResolutionDate)); ?></td>
                          </tr>
                          <tr>
                              <th><?php echo $lang->problem->delayReason;?></th>
                              <td><?php echo  $problem->delayReason;?></td>
                          </tr>
                          <tr>
                              <th><?php echo $lang->problem->delayUser;?></th>
                              <td><?php echo zget($users,$problem->delayUser,'');?></td>
                          </tr>
                          <tr>
                              <th><?php echo sprintf($lang->problem->delayDate,$lang->problem->delayName);?></th>
                              <td><?php echo $problem->delayDate;?></td>
                          </tr>
                          <tr>
                              <th><?php echo  sprintf($lang->problem->delayStatus,$lang->problem->delayName);?></th>
                              <td><?php echo sprintf(zget($lang->problem->delayStatusList,$problem->delayStatus,''),$lang->problem->delayName);?></td>
                          </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      <?php endif;?>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->problem->statusMove ;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->problem->nodeUser;?></th>
               <!-- <td class='text-right'><?php /*echo $lang->problem->consumed;*/?></td>-->
                <td class='text-center'><?php echo $lang->problem->before;?></td>
                <td class='text-center'><?php echo $lang->problem->after;?></td>
                <td class='text-left'><?php echo $lang->actions;?></td>
              </tr>
              <?php foreach($problem->consumed as $index => $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
               <!-- <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                <td class='text-center'><?php echo zget($lang->problem->nowConsumedstatusList, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->problem->nowConsumedstatusList, $c->after, '-');?></td>
                <td class='c-actions text-left'>
                  <?php
                  common::printIcon('problem', 'workloadEdit', "problemID={$problem->id}&consumedid={$c->id}", $problem, 'list', 'edit', '', 'iframe', true);
                  if($index) common::printIcon('problem', 'workloadDelete', "problemID=$problem->id&consumedid={$c->id}", $problem, 'list', 'trash', '', 'iframe', true);
                  common::printIcon('problem', 'workloadDetails', "problemID={$problem->id}&consumedid={$c->id}", $problem, 'list', 'glasses', '', 'iframe', true);
                  ?>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

      <?php if(!empty($problem->changeResolutionDate)):?>
          <div class="cell">
              <div class="detail">
                  <div class="detail-title"><?php echo sprintf($lang->problem->delayMove,$lang->problem->changeConsumed);?></div>
                  <div class='detail-content'>
                      <table class='table table-data'>
                          <tbody>
                          <tr>
                              <th class='w-100px'><?php echo $lang->problem->nodeUser;?></th>
                              <td class='text-center'><?php echo $lang->problem->before;?></td>
                              <td class='text-center'><?php echo $lang->problem->after;?></td>
                          </tr>
                          <?php foreach($problem->changeConsumed as $index => $c):?>
                              <tr>
                                  <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                                  <?php
                                  if(($c->before=='toDepart'&&$c->after=='toManager')||($problem->changeConsumed[$index-1]->before=='toDepart'&&$c->before=='toManager')){
                                      $lang->problem->delayStatusList['toManager']='分管领导处理';
                                      $lang->problem->delayStatusList=array_merge($lang->problem->delayStatusList,['success'=>'通过']);
                                  }else{
                                      $lang->problem->delayStatusList['toManager']='公司领导处理';
//                                               $lang->problem->delayStatusList=array_merge($lang->problem->delayStatusList,['success'=>'通过（不上报）']);
                                  }
                                  ?>
                                  <td class='text-center'><?php
                                      echo $a = zget($lang->problem->delayStatusList, $c->before, '-');
                                      ?></td>
                                  <td class='text-center'><?php
                                      $b =  zget($lang->problem->delayStatusList, $c->after, '-');
                                      echo $a=='产品创新部处理'&&$b =='通过'?'通过（不上报）':$b;
                                      ?></td>
                              </tr>
                          <?php endforeach;?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      <?php endif; ?>

       <?php if(!empty($problem->delayResolutionDate)):?>
      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo sprintf($lang->problem->delayMove,$lang->problem->delayConsumed);?></div>
              <div class='detail-content'>
                  <table class='table table-data'>
                      <tbody>
                      <tr>
                          <th class='w-100px'><?php echo $lang->problem->nodeUser;?></th>
                          <td class='text-center'><?php echo $lang->problem->before;?></td>
                          <td class='text-center'><?php echo $lang->problem->after;?></td>
                      </tr>
                      <?php foreach($problem->delayConsumed as $index => $c):?>
                          <tr>
                              <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                              <td class='text-center'><?php echo sprintf(zget($lang->problem->delayStatusList, $c->before, '-'),$lang->problem->delayName);?></td>
                              <td class='text-center'><?php echo sprintf(zget($lang->problem->delayStatusList, $c->after, '-'),$lang->problem->delayName);?></td>
                          </tr>
                      <?php endforeach;?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
        <?php endif; ?>
  </div>
</div>
<?php
    //外部问题单
   }else{
?>
       <div id="mainContent" class="main-row">
           <div class="main-col col-8">
               <div class="cell">
                   <!--问题摘要-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->abstract;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->abstract) ? nl2br($problem->abstract): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--问题现象-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->desc;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->desc) ? nl2br($problem->desc): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--发生原因-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->reason;?></div>
                       <div class="detail-content article-content"><p style="white-space: pre-wrap;">
                           <?php echo !empty($problem->reason) ? $problem->reason : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                           </p>
                       </div>
                   </div>
                   <!--业务影响-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->EffectOfService;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->EffectOfService) ? html_entity_decode(str_replace("\n","<br/>",$problem->EffectOfService)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--引发该问题的变更-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->ChangeIdRelated;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->ChangeIdRelated) ? html_entity_decode(str_replace("\n","<br/>",$problem->ChangeIdRelated)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--引发该问题的事件-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->IncidentIdRelated;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->IncidentIdRelated) ? html_entity_decode(str_replace("\n","<br/>",$problem->IncidentIdRelated)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--引发该问题的演练-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->DrillCausedBy;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->DrillCausedBy) ? html_entity_decode(str_replace("\n","<br/>",$problem->DrillCausedBy)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--优化及改进建议-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->Optimization;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->Optimization) ? html_entity_decode(str_replace("\n","<br/>",$problem->Optimization)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--初步反馈-->
                   <div class="detail <?php if($problem->createdBy == 'guestjx') echo 'hide' ?>">
                       <div class="detail-title"><?php echo $lang->problem->Tier1Feedback;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->Tier1Feedback) ? nl2br($problem->Tier1Feedback) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--解决方案-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->solution;?></div>
                       <div class="detail-content article-content" style="white-space: pre-wrap;">
                           <?php echo !empty($problem->solution) ? nl2br($problem->solution) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--解决该问题的变更-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->ChangeSolvingTheIssue;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->ChangeSolvingTheIssue) ? nl2br($problem->ChangeSolvingTheIssue) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--问题退回原因-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->ReasonOfIssueRejecting;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->ReasonOfIssueRejecting) ? nl2br($problem->ReasonOfIssueRejecting) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--影响范围-->
                   <div class="detail <?php if($problem->createdBy == 'guestjx') echo 'hide' ?>">
                       <div class="detail-title"><?php echo $lang->problem->EditorImpactscope;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->EditorImpactscope) ? nl2br($problem->EditorImpactscope) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--修订记录-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->revisionRecord;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->revisionRecord) ? nl2br($problem->revisionRecord) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--制版申请-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->plateMakAp;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->plateMakAp) ? nl2br($problem->plateMakAp) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--制版信息-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->plateMakInfo;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->plateMakInfo) ? $problem->plateMakInfo : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                  <!--当前进展-->
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->progress;?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->progress) ? $problem->progress : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                       </div>
                   </div>
                   <!--问题单附件-->
                   <?php if($problem->files) echo $this->fetch('file', 'printFilesByName', array('files' => $problem->files, 'fieldset' => 'true','fileName' => '问题单附件'));?>
                   <!--反馈单附件-->
                   <?php if($problem->RelationFiles) echo $this->fetch('file', 'printFilesByName', array('files' => $problem->RelationFiles, 'fieldset' => 'true','fileName' => '反馈单附件'));?>
                   <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=problem&objectID=$problem->id");?>
               </div>

               <div class="cell">
                   <div class="detail">
                       <?php if(!empty($nodes)):?>
                       <div class="pull-right">
                           <?php if(common::hasPriv('problem','historyRecord')) common::printLink('problem', 'historyRecord', "problemID=$problem->id", "<i class='icon icon-history'></i>" . $lang->problem->historyRecord, '', "class='btn btn-primary btn-nodes iframe' data-width='90%' ",'',true);?>
                       </div>
                      <?php endif;?>
                       <div class="detail-title"><?php echo $lang->problem->feedbackReviewComment;?></div>
                       <div class="detail-content article-content">
                           <?php if(!empty($nodes)):?>
                           <table class="table ops">
                               <tr>
                                   <th class="w-200px"><?php echo $lang->problem->reviewNode;?></th>
                                   <th><?php echo $lang->problem->reviewer;?></th>
                                   <th><?php echo $lang->problem->reviewResult;?></th>
                                   <th><?php echo $lang->problem->reviewComment;?></th>
                                   <th><?php echo $lang->problem->reviewdate; ?></th>
                               </tr>
                               <?php
                                    //循环数据
                                    foreach ($lang->problem->reviewNodeLabelList as $key => $reviewNode):
                                        $reviewerUserTitle = '';
                                        $reviewerUsersShow = '';
                                        $realReviewer = new stdClass();
                                        $realReviewer->status = '';
                                        $realReviewer->comment = '';
                                        if(isset($nodes[$key])) {
                                            $currentNode = $nodes[$key];
                                            $reviewers = $currentNode->reviewers;
                                            if(is_array($reviewers) || !empty($reviewers)) {
                                                //所有审核人
                                                $reviewersArray = array_column($reviewers, 'reviewer');
                                                $userCount = count($reviewersArray);
                                                if ($userCount > 0) {
                                                    $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                                    $reviewerUserTitle = implode(',', $reviewerUsers);
                                                    $subCount = 3;
                                                    $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $userCount);
                                                    //获得实际审核人
                                                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                                }
                                            }
                                        }
                               ?>
                                        <tr>
                                            <th><?php echo $reviewNode;?></th>
                                            <td title="<?php echo $reviewerUserTitle; ?>">
                                                <?php echo $reviewerUsersShow; ?>
                                            </td>
                                            <td>
                                                <?php echo zget($lang->problem->confirmResultList, $realReviewer->status, '');?>
                                                <?php
                                                if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'approvesuccess' || $realReviewer->status == 'externalsendback' || $realReviewer->status == 'closed' || $realReviewer->status == 'suspend' || $realReviewer->status == 'feedbacked'
                                                    || $realReviewer->status == 'firstpassed' || $realReviewer->status == 'finalpassed'):
                                                    ?>
                                                    &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                        if( $realReviewer->status == 'externalsendback'  && $reviewNode =='外部审批' && $problem->approverName) {
                                            echo "打回人：".$problem->approverName.'<br> 审批意见：' ;
                                          }
                                        ?>
                                               <?php echo $realReviewer->comment; ?></td>
                                            <td><?php echo $realReviewer->reviewTime; ?></td>
                                        </tr>

                                    <?php endforeach;?>

                               <?php else:?>
                                   <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                               <?php endif;?>
                           </table>
                       </div>
                   </div>
               </div>
               <div class="cell">
               <div class="detail">
                   <div class="detail-title"><?php echo $lang->problem->baseChangeTip; ?></div>
                   <div class="detail-content article-content">
                       <?php if (!empty($changeInfo)): ?>
                           <table class="table changeInfo" style="table-layout: fixed">
                               <tr>
                                   <th class="w-110px"><?php echo $this->lang->problem->changeVersion; ?></th>
                                   <td colspan="2.5"><?php echo max($changeInfo)->changeVersion;?></td>
                                   <th><?php echo $this->lang->problem->successVersion; ?></th>
                                   <td colspan="2.5"><?php echo max($changeInfo)->successVersion;?></td>
                               </tr>
                               <tr>
                                   <th class="w-70px"><?php echo $this->lang->problem->baseChangeUser; ?></th>
                                   <th class="w-70px"><?php echo $this->lang->problem->changeReason; ?></th>
                                   <th class="w-100px"><?php echo $this->lang->problem->baseChangeContent; ?></th>
                                   <th class="w-150px"><?php echo $this->lang->problem->changeCommunicate; ?></th>
                                   <th class="w-80px"><?php echo sprintf($this->lang->problem->delayStatus,$lang->problem->changeName); ?></th>
                                   <th class="w-100px"><?php echo $this->lang->problem->actionTime; ?></th>
                               </tr>
                               <?php foreach ($changeInfo as $key => $item):?>

                                   <tr>
                                       <td><?php echo zget($users,$item->changeUser);?></td>
                                       <td><?php echo $item->changeReason;?></td>
                                       <td><?php echo $item->changeContent;?></td>
                                       <td><?php echo $item->changeCommunicate;?></td>
                                       <td class="w-80px"><?php echo  sprintf(zget($lang->problem->delayStatusList,$item->changeStatus),$lang->problem->changeName); ?></td>
                                       <td><?php echo $item->changeDate;?></td>
                                   </tr>
                               <?php endforeach;?>
                           </table>
                       <?php else: ?>
                           <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                       <?php endif; ?>

                   </div>
               </div>
               </div>
               <!-- 审核变更申请单 -->
               <?php if (!empty($changeNodes)): ?>
                   <div class="cell">
                       <div class="detail">
                           <div class="clearfix">
                               <div class="detail-title pull-left"><?php echo sprintf($lang->problem->delayreviewOpinion,$lang->problem->changeName); ?></div>
                               <div class="detail-title pull-right">
                                   <?php
                                   if(common::hasPriv('problem', 'showdelayHistoryNodes')) echo html::a($this->createLink('problem', 'showdelayHistoryNodes', 'id='.$problem->id.'&type=problemChange', '', true), "<i class='icon icon-history'></i>".sprintf($lang->problem->showdelayHistoryNodes,$lang->problem->changeName), '', "data-toggle='modal' data-type='iframe' data-width='70%'  class='btn btn-primary btn-nodes iframe'");
                                   ?>
                               </div>
                           </div>
                           <div class="detail-content article-content">
                               <table class="table ops">
                                   <tr>
                                       <th class="w-180px"><?php echo $lang->problem->statusOpinion; ?></th>
                                       <th class="w-180px"><?php echo $lang->problem->reviewer; ?></th>
                                       <th class="w-180px"><?php echo $lang->problem->reviewResult; ?></th>
                                       <th style="width:370px;"><?php echo $lang->problem->dealOpinion; ?></th>
                                       <th class="w-180px"><?php echo $lang->problem->reviewOpinionTime; ?></th>
                                   </tr>
                                   <?php

                                   if($changeNodes[200]->nodeCode!='toManager'){
                                       $lang->problem->reviewNodeStatusLableList['toManager']='公司领导处理';
                                       $lang->problem->reviewNodeStatusList[200]='toProductManager';
                                   }else{
                                       $lang->problem->reviewNodeStatusLableList['toManager']='分管领导处理';
                                       $lang->problem->reviewNodeStatusList[200]='toManager';
                                   }
                                   ?>
                                   <?php foreach ($lang->problem->reviewNodeStatusList as $key => $reviewNode):
                                       $reviewerUserTitle = '';
                                       $reviewerUsersShow = '';
                                       $realReviewer = new stdClass();
                                       $realReviewer->status = '';
                                       $realReviewer->comment = '';
                                       if (isset($changeNodes[$key])) {
                                           $currentNode = $changeNodes[$key];
                                           $reviewers = $currentNode->reviewers;
                                           if (!(is_array($reviewers) && !empty($reviewers))) {
                                               continue;
                                           }
                                           //所有审核人
                                           $reviewersArray = array_column($reviewers, 'reviewer');
                                           $userCount = count($reviewersArray);
                                           if ($userCount > 0) {
                                               $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                               $reviewerUserTitle = implode(',', $reviewerUsers);
                                               $subCount = 10;
                                               $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                               //获得实际审核人
                                               $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                           }


                                       }else{
                                           continue;
                                       }
                                       ?>
                                   <?php if ($realReviewer->status!='wait'):?>


                                       <tr>
                                           <th><?php echo $reviewNode = zget($lang->problem->reviewNodeStatusLableList, $reviewNode); ?></th>
                                           <td title="<?php echo $reviewerUserTitle; ?>"><?php echo $reviewerUsersShow; ?></td>
                                           <?php
                                           if($reviewNode=='产品创新部处理'){
                                               $lang->problem->reviewStatusList['pass']='通过（不上报）';
                                           }else{
                                               $lang->problem->reviewStatusList['pass']='通过';
                                           }

                                           ?>
                                           <td><?php  $status = zget($lang->problem->reviewStatusList, $realReviewer->status, '');echo $status; ?>
                                               <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'||$realReviewer->status == 'report'): ?>
                                                   &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                               <?php endif; ?>
                                           </td>
                                           <td><?php echo $realReviewer->comment ?></td>
                                           <td><?php echo $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: ''?></td>
                                       </tr>
                                       <?php endif;?>
                                   <?php endforeach; ?>
                               </table>
                           </div>
                       </div>
                   </div>
               <?php endif; ?>
               <!-- 审核延期申请单 -->
               <?php if (!empty($delayNodes)): ?>
                   <div class="cell">
                       <div class="detail">
                           <div class="clearfix">
                               <div class="detail-title pull-left"><?php echo sprintf($lang->problem->delayreviewOpinion,$lang->problem->delayName); ?></div>
                               <div class="detail-title pull-right">
                                   <?php
                                   if(common::hasPriv('problem', 'showdelayHistoryNodes')) common::printLink('problem', 'showdelayHistoryNodes', "problemID=$problem->id", "<i class='icon icon-history'></i>" . sprintf($lang->problem->showdelayHistoryNodes,$lang->problem->delayName), '', "class='btn btn-primary btn-nodes iframe' data-width='90%' ",'',true);
                                   ?>
                               </div>
                           </div>
                           <div class="detail-content article-content">
                               <table class="table ops">
                                   <tr>
                                       <td class="w-180px"><?php echo $lang->problem->statusOpinion; ?></td>
                                       <td class="w-180px"><?php echo $lang->problem->reviewer; ?></td>
                                       <td class="w-180px"><?php echo $lang->problem->reviewResult; ?></td>
                                       <td style="width:370px;"><?php echo $lang->problem->dealOpinion; ?></td>
                                       <td class="w-180px"><?php echo $lang->problem->reviewOpinionTime; ?></td>
                                   </tr>
                                   <?php foreach ($lang->problem->reviewNodeStatusList as $key => $reviewNode):
                                       $reviewerUserTitle = '';
                                       $reviewerUsersShow = '';
                                       $realReviewer = new stdClass();
                                       $realReviewer->status = '';
                                       $realReviewer->comment = '';
                                       if (isset($delayNodes[$key])) {
                                           $currentNode = $delayNodes[$key];
                                           $reviewers = $currentNode->reviewers;
                                           if (!(is_array($reviewers) && !empty($reviewers))) {
                                               continue;
                                           }
                                           //所有审核人
                                           $reviewersArray = array_column($reviewers, 'reviewer');
                                           $userCount = count($reviewersArray);
                                           if ($userCount > 0) {
                                               $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                               $reviewerUserTitle = implode(',', $reviewerUsers);
                                               $subCount = 10;
                                               $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                               //获得实际审核人
                                               $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                           }
                                       }else{
                                           continue;
                                       }
                                       ?>
                                       <tr>
                                           <th><?php echo zget($lang->problem->reviewNodeStatusLableList, $reviewNode); ?></th>
                                           <td title="<?php echo $reviewerUserTitle; ?>"><?php echo $reviewerUsersShow; ?></td>
                                           <td><?php  $status = zget($lang->problem->reviewStatusList, $realReviewer->status, '');echo $status ? sprintf($status,$lang->problem->delayName): ''; ?>
                                               <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'||$realReviewer->status == 'report'): ?>
                                                   &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                               <?php endif; ?>
                                           </td>
                                           <td><?php echo $realReviewer->comment ?></td>
                                           <td><?php echo $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: ''?></td>
                                       </tr>
                                   <?php endforeach; ?>
                               </table>
                           </div>
                       </div>
                   </div>
               <?php endif; ?>
               <?php if(common::hasPriv('problem','getProgressInfo')):?>
               <div class="cell">
                   <div class="detail-title"><?php echo $lang->problem->conclusionInfo; ?></div>
                   <div class="detail" >
                       <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentPlan; ?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->secondLineDevelopmentPlan) ? nl2br($problem->secondLineDevelopmentPlan) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                       </div>
                   </div>
                   <div class="detail" >
                       <div class="detail-title"><?php echo $lang->problem->progressQA; ?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->progressQA) ? nl2br($problem->progressQA) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                       </div>
                   </div>
                   <div class="detail" >
                       <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentStatus; ?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->secondLineDevelopmentStatus) ? zget($lang->problem->secondLineDepStatusList,$problem->secondLineDevelopmentStatus) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                       </div>
                   </div>
                   <div class="detail" >
                       <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentApproved; ?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->secondLineDevelopmentApproved) ? zget($lang->problem->secondLineDepApprovedList,$problem->secondLineDevelopmentApproved) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                       </div>
                   </div>
                   <div class="detail" >
                       <div class="detail-title"><?php echo $lang->problem->secondLineDevelopmentRecord; ?></div>
                       <div class="detail-content article-content">
                           <?php echo !empty($problem->secondLineDevelopmentRecord) ? zget($lang->problem->secondLineDevelopmentRecordList,$problem->secondLineDevelopmentRecord) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                       </div>
                   </div>

               </div>
             <?php endif;?>
               <div class="cell"><?php include '../../../common/view/action.html.php';?></div>

               <div class='main-actions'>
                   <div class="btn-toolbar" style="overflow: visible">
                       <?php common::printBack($browseLink);?>
                       <div class='divider'></div>
                       <?php
                       common::printIcon('problem', 'edit', "problemID=$problem->id", $problem, 'button');
                       $status = array('confirmed','assigned','toclose'); //20220930 待分配和待分析 或待开发且不是问题 高亮
                       if(($this->app->user->admin or ( $this->app->user->account == $problem->dealUser && (in_array($problem->status,$status) or ($problem->status == 'feedbacked' && $problem->type == 'noproblem')) )) && $problem->status != 'deleted' )//非当前处理人，图标置灰不能操作
                       {
                           echo '<button type="button" class="btn" title="' . $lang->problem->deal . '" onclick="isClickable('.$problem->id.', \'deal\')"><i class="icon-common-suspend icon-time">' . $lang->problem->deal . '</i></button>';
                           common::printIcon('problem', 'deal', "problemID=$problem->id", $problem, 'button', 'time', '', 'iframe hidden', true, 'id=isClickable_deal' . $problem->id);
                       }
                       //新建问题反馈单
                       if($problem->status != 'suspend' and 'closed' != $problem->status and ($_SESSION['user']->admin or $_SESSION['user']->account == $problem->acceptUser)
                           and ($problem->ReviewStatus == 'tofeedback' or $problem->ReviewStatus == 'todeptapprove' or $problem->ReviewStatus == 'sendback' or $problem->ReviewStatus == 'firstpassed' or $problem->ReviewStatus == 'externalsendback' or ($problem->ReviewStatus == 'approvesuccess' and $problem->IfultimateSolution == '0' ))
                           and ($problem->IssueId != null)){
                           common::printIcon('problem', 'createfeedback', "problemID=$problem->id", $problem, 'button', 'feedback', '', 'iframe', true);
                       }
                       $delayFlag = common::hasPriv('problem', 'reviewdelay') && in_array($problem->changeStatus, array_keys($this->lang->problem->reviewNodeStatusLableList)) && in_array($this->app->user->account, explode(',', $problem->changeDealUser));

                       if($delayFlag && $feedbackFlag){
                           $str =  '<div class="btn-group">';
                           $str .= "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown'><i class='icon icon-glasses'></i><span>" . $this->lang->problem->review . "</span></button>";
                           $str .= '<ul class="dropdown-menu" style="top: -250%;">';
                           $str .= '<li>' . html::a($this->createLink('problem', 'reviewdelay', 'problemId=' . $problem->id , '', true), $this->lang->problem->reviewdelay, '', "data-toggle='modal' data-type='iframe' ") . '</li>';
                           $str .= '<li>' . html::a($this->createLink('problem', 'approvefeedback', 'problemID=' . $problem->id , '', true), $this->lang->problem->approvefeedback, '', "data-toggle='modal' data-type='iframe' ") . '</li>';
                           $str .= '</ul></div>';
                           echo $str;
                       }elseif($delayFlag){
                           common::printIcon('problem', 'reviewdelay', "problemID=$problem->id", $problem, 'button', 'glasses', '', 'iframe', true);
                       }elseif ($feedbackFlag){
                           common::printIcon('problem', 'approvefeedback', "problemID=$problem->id", $problem, 'button', 'glasses', '', 'iframe', true);
                       }
                       if(in_array($this->app->user->account,$progressLook) || $this->app->user->account == 'admin'){
                           common::printIcon('problem', 'editSpecial', "problemID=$problem->id", $problem, 'button', 'edit', '', 'iframe', true);
                       }
                       else
                       {
                           echo common::hasPriv('problem','editSpecial') ?  '<button type="button" class="disabled btn" title="' . $lang->problem->editSpecial . '"><i class="icon-common-editspecial disabled icon-edit"></i><span class="text">&nbsp' . $lang->problem->editSpecial .'</span></button></button>' : '';
                       }
                       if( common::hasPriv('problem','editSpecialQA') || $this->app->user->account == 'admin'){
                           common::printIcon('problem', 'editSpecialQA', "problemID=$problem->id", $problem, 'button', 'edit', '', 'iframe', true);
                       }
                       //common::printIcon('problem', 'redeal', "problemID=$problem->id", $problem, 'button', 'time', '', 'iframe', true);
                       common::printIcon('problem', 'editAssignedTo', "problemID=$problem->id", $problem, 'button', 'hand-right', '', 'iframe', true);
                       if($problem->ReviewStatus == 'syncfail' || $problem->ReviewStatus == 'jxsyncfail') {
                           common::printIcon('problem', 'push',  "problemID=$problem->id", $problem, 'button', 'share', '', 'iframe', true);
                       }
                       //延期审批按钮
//                       echo '<button type="button" class="btn" title="' . $lang->problem->postpone . '" onclick="delayCheck()"><i class="icon-common-delay icon-delay"></i><span class="text">&nbsp' . $lang->problem->postpone .'</span></button>';
                       $disabled = empty($delayErrorMsg) ? '' : 'disabled';
                       $disabled = '';
                       //金信 外部反馈通过 按钮高亮
//                       if($problem->createdBy == 'guestjx' && (!in_array($problem->ReviewStatus,array('approvesuccess','closed')) || $problem->status =='returned')){
                         if($problem->createdBy == 'guestjx' && ($problem->ReviewStatus != 'approvesuccess' || $problem->status =='returned' ||$problem->status =='closed')){
                           $disabled = 'disabled';
                       }
//                         elseif($problem->createdBy == 'guestcn' && (!in_array($problem->ReviewStatus ,array('firstpassed','finalpassed','closed')) || $problem->status =='returned')){
                           elseif($problem->createdBy == 'guestcn' && (!in_array($problem->ReviewStatus ,array('firstpassed','finalpassed')) || $problem->status =='returned' ||$problem->status =='closed')){
                           //清总 初步解决反馈通过 或最终解决反馈通过 按钮高亮
                           $disabled = 'disabled';
                       }
                       common::hasPriv('problem','delay') ?   common::printIcon('problem', 'delay', "problemID=$problem->id", $problem, 'button', 'delay', '', 'iframe ' . $disabled, true, 'id=delayCheck', $lang->problem->postpone) :'';

                       if( $problem->IssueId != null && $this->app->user->account == 'admin'){
                           common::printIcon('problem', 'delete', "problemID=$problem->id", $problem, 'button', 'trash', '', 'iframe', true);
                       }elseif(empty($problem->IssueId)){
                           common::printIcon('problem', 'delete', "problemID=$problem->id", $problem, 'button', 'trash', '', 'iframe', true);
                       }else{
                           echo common::hasPriv('problem','delete') ? '<button type="button" class="disabled btn" title="' . $lang->problem->delete . '"><i class="icon-common-suspend disabled icon-trash"></i></button>' :'';
                       }
                       common::printIcon('problem', 'close', "problemID=$problem->id", $problem, 'button', 'off', '', 'iframe', true);
                       //反馈期限维护
                       echo common::hasPriv('problem','feedbacktimeedit') ? common::printIcon('problem', 'feedbackTimeEdit', "problemID=$problem->id", $problem, 'button', '', '', 'iframe', true, '', $lang->problem->feedbackTimeEdit) :'';
                       ?>
                   </div>
               </div>
           </div>
           <div class="side-col col-4">
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->sysncInfo;?></div>
                       <div class='detail-content'>
                           <table class = 'table table-data'>
                               <tr>
                                   <th class="w-140px"><?php echo $lang->problem->IssueId;?></th>
                                   <td><?php echo $problem->IssueId;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->DepIdofIssueCreator;?></th>
                                   <td><?php echo $problem->DepIdofIssueCreator;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->CataOfIssue;?></th>
                                   <td><?php echo zget($lang->problem->typeList, $problem->type, $problem->type);?></td>
                               </tr>
                               <tr>
                                   <th class="w-100px"><?php echo $lang->problem->SolutionFeedback;?></th>
                                   <td><?php echo zget($lang->problem->solutionFeedbackList, $problem->SolutionFeedback, '');?></td>
                               </tr>
                               <tr>
                                   <th class="w-100px"><?php echo $lang->problem->problemCause;?></th>
                                   <td><?php echo zget($lang->problem->problemCauseList, $problem->problemCause, '');?></td>
                               </tr>
                               <tr>
                                   <th class="w-100px"><?php echo $lang->problem->repeatProblem;?></th>
                                   <td><?php echo $repeat;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->source;?></th>
                                   <td><?php echo zget($lang->problem->sourceList, $problem->source, $problem->source);?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->ProblemSource;?></th>
                                   <td><?php echo $problem->ProblemSource;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->RecoveryTime;?></th>
                                   <td><?php echo $problem->RecoveryTime;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->IssueCreator;?></th>
                                   <td><?php echo $problem->IssueCreator;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->TeleNoOfCreator;?></th>
                                   <td><?php echo $problem->TeleNoOfCreator;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->app;?></th>
                                   <td>
                                       <?php
                                       $as = [];
                                       foreach(explode(',', $problem->app) as $app)
                                       {
                                           if(!$app) continue;
                                           $as[] = zget($apps, $app , "",$apps[$app]->name);
                                       }
                                       $app = implode(', ', $as);
                                       echo $app;
                                       ?>
                                   </td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->severity;?></th>
                                   <td><?php echo zget($lang->problem->severityList, $problem->severity, $problem->severity);?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->NodeIdOfIssue;?></th>
                                   <td><?php echo $problem->NodeIdOfIssue;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->TimeOfOccurrence;?></th>
                                   <td><?php echo $problem->occurDate;?></td>
                               </tr>
                               <tr>
                                   <th><?php if($problem->createdBy != 'guestjx') {
                                       echo $lang->problem->TimeOfReport;
                                       }else {
                                               echo $lang->problem->jxTimeOfReport;
                                           }
                                       ?>
                                   </th>
                                   <td><?php echo $problem->TimeOfReport;?></</td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->IssueStatus;?></th>
                                   <td><?php echo zget($lang->problem->IssueStatusList,$problem->IssueStatus,$problem->IssueStatus);?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->TimeOfClosing;?></th>
                                   <td><?php echo $problem->TimeOfClosing;?></td>
                               </tr>
                           </table>
                       </div>
                   </div>

               </div>
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->basicInfo;?></div>
                       <div class='detail-content'>
                           <table class='table table-data'>
                               <tbody>
                               <tr>
                                   <th class="w-140px"><?php echo $lang->problem->dealUser;?></th>
                                   <td><?php echo zget($users, $problem->dealUser, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->status;?></th>
                                   <td><?php echo zget($lang->problem->statusList, $problem->status, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->ifRecive;?></th>
                                   <td><?php echo zget($lang->problem->ifReturnList, $problem->ifReturn, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->isPayment;?></th>
                                   <td>
                                       <?php
                                       $as = [];
                                       foreach(explode(',', $problem->isPayment) as $apptype)
                                       {
                                           if(!$apptype) continue;
                                           $as[] = zget($lang->application->isPaymentList, $apptype, "");
                                       }
                                       $applicationtype = implode(',', $as);
                                       echo $applicationtype;
                                       ?>
                                   </td>
                               </tr>
                               <!--tr>
                <th><?php echo $lang->problem->consumed;?></th>
                <td><?php echo $problem->consumed ? $problem->consumed : '';?></td>
              </tr>
              <tr>
                <th><?php echo $lang->problem->state;?></th>
                <td><?php echo zget($lang->problem->stateList, $problem->state, '');?></td>
              </tr-->
                               <tr>
                                   <th><?php echo $lang->problem->buildTimes;?></th>
                                   <td><?php echo $problem->buildTimes;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->changeVersion;?></th>
                                   <td><?php echo empty($problem->changeVersion) ? 0 : $problem->changeVersion;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->successVersion;?></th>
                                   <td><?php echo empty($problem->successVersion) ? 0 : $problem->successVersion;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->fixType;?></th>
                                   <td><?php echo zget($lang->problem->fixTypeList, $problem->fixType, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->projectPlan;?></th>
                                   <td>
                                       <?php if($problem->projectPlan):?>
                                       <?php echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplanid), $plan->name, '', "data-app='platform' style='color: #0c60e1;'");?>
                                       <?php endif;?>
                                   </td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->stage;?></th>
                                   <td><?php echo zget($executions,$problem->execution,'');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->task;?></th>
                                   <td><?php if($task)  {
                                           $i = 1;
                                           foreach ($task as $key => $item) {
                                               if(!$item) continue;
                                               echo html::a('javascript:void(0)', $i.'、'.$item->taskName, '', 'data-app="project" onclick="seturl('.$problem->projectPlan.','.$item->id.')"').'；'.'<br>';
                                           $i++;
                                           }
                                       }?>
                                   </td>
                                   <td class="hidden"><?php echo html::a('','','','data-app="project"  id="taskurl"')?></td>
                               </tr>
                               <!--<tr>
                  <th><?php /*echo $lang->problem->buildName;*/?></th>
                  <td><?php /*if(isset($buildAndRelease->buildname) && $buildAndRelease->buildname)  echo html::a('javascript:void(0)', $buildAndRelease->buildname, '', 'data-app="project" onclick="newurl('.$problem->projectPlan.','.$buildAndRelease->bid.')"')*/?></td>
                  <td class="hidden"><?php /*echo html::a('','','','data-app="project"  id="problembuildurl"')*/?></td>
              </tr>
              <tr>
                  <th><?php /*echo $lang->problem->releaseName;*/?></th>
                  <td><?php /*if(isset($buildAndRelease->releasename) && $buildAndRelease->releasename)  echo html::a('javascript:void(0)', $buildAndRelease->releasename, '', 'data-app="project" onclick="addurl('.$problem->projectPlan.','.$buildAndRelease->rid.')"')*/?></td>
                  <td class="hidden"><?php /*echo html::a('','','','data-app="project"  id="problemreleaseurl"')*/?></td>
              </tr>-->
                               <tr>
                                   <th><?php echo $lang->problem->buildName;?></th>
                                   <td>
                                       <?php  if(isset($buildAndRelease)):
                                           foreach ($buildAndRelease as $item):
                                               if(isset($item->buildname) && $item->buildname)
                                                   echo html::a('javascript:void(0)', $item->buildname, '', 'data-app="project" onclick="newurl('.$problem->projectPlan.','.$item->bid.')"').' <br>';
                                               ?>
                                           <?php endforeach;endif;?>
                                   </td>
                                   <td class="hidden"><?php echo html::a('','','','data-app="project"  id="problembuildurl"')?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->releaseName;?></th>
                                   <td>
                                       <?php  if(isset($buildAndRelease)):
                                           foreach ($buildAndRelease as $item):
                                               if(isset($item->releasename) && $item->releasename)
                                                   echo html::a('javascript:void(0)', $item->releasename, '', 'data-app="project" onclick="addurl('.$problem->projectPlan.','.$item->rid.')"').' <br>';
                                               ?>
                                           <?php endforeach;endif;?>
                                   </td>
                                   <td class="hidden"><?php echo html::a('','','','data-app="project"  id="problemreleaseurl"')?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->product;?></th>
                                   <td><?php if($problem->product) echo  $productName;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->productPlan;?></th>
                                   <td><?php if($problem->productPlan) echo $productPlan;?></td>
                               </tr>
                               <!--20220311 新增--><!--需求收集 3569隐藏该字段-->
<!--                               <tr class="--><?php //if('2024-01-31' < $problem->createdDate) echo 'hidden'; ?><!--">-->
<!--                                   <th class='w-140px'>--><?php //echo $lang->problem->systemverify;?><!--</th>-->
<!--                                   <td>--><?php //echo zget($lang->problem->needOptions, $problem->systemverify, '');?><!--</td>-->
<!--                               </tr>-->
                               <tr>
                                   <th class='w-140px'><?php echo $lang->problem->verifyperson;?></th>
                                   <td><?php echo zget($users, $problem->verifyperson, '');?></td>
                               </tr>
<!--                               <tr>-->
<!--                                   <th class='w-140px'>--><?php //echo $lang->problem->laboratorytest;?><!--</th>-->
<!--                                   <td>--><?php //echo zget($users, $problem->laboratorytest, '');?><!--</td>-->
<!--                               </tr>-->
                               <tr>
                                   <th><?php echo $lang->problem->relationModify;?></th>
                                   <td>
                                       <?php if(isset($objects['modify'])): ?>
                                       <?php foreach($objects['modify'] as $objectID => $object):?>
                                           <p><?php echo html::a($this->createLink('modify', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                       <?php endforeach;?>
                                       <?php endif;?>
                                       <?php if(isset($objects['modifycncc'])): ?>
                                       <?php foreach($objects['modifycncc'] as $objectID => $object):?>
                                           <p><?php echo html::a($this->createLink('modifycncc', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                       <?php endforeach; ?>
                                       <?php endif; ?>
                                   </td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->relationOutwardDelivery;?></th>
                                   <td>
                                       <?php if(isset($objects['outwardDelivery'])): ?>
                                       <?php  foreach($objects['outwardDelivery'] as $objectID => $object): if($object == "") continue; ?>
                                           <p><?php echo html::a($this->createLink('outwarddelivery', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                       <?php endforeach;?>
                                       <?php endif; ?>
                                   </td>
                               </tr>
                              <?php if(isset($objects['fix']) && $objects['fix']):?>
                               <tr>
                                   <th><?php echo $lang->problem->relationFix;?></th>
                                   <td>
                                       <?php foreach($objects['fix'] as $objectID => $object):?>
                                           <p><?php echo html::a($this->createLink('info', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                       <?php endforeach;?>
                                   </td>
                               </tr>
                               <?php endif; ?>
                               <tr>
                                   <th><?php echo $lang->problem->relationGain;?></th>
                                   <td>
                                       <?php if(isset($objects['gain'])): ?>
                                       <?php foreach($objects['gain'] as $objectID => $object):?>
                                           <p><?php echo html::a($this->createLink('info', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                       <?php endforeach;?>
                                       <?php endif;?>
                                       <?php if(isset($objects['gainQz'])): ?>
                                       <?php foreach ($objects['gainQz'] as $objectID => $object): ?>
                                           <p><?php echo html::a($this->createLink('infoqz', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                       <?php endforeach; ?>
                                       <?php endif; ?>
                                   </td>
                               </tr>

                               <tr>
                                   <th><?php echo $lang->problem->relationCredit;?></th>
                                   <td>
                                       <?php if(isset($objects['credit'])):?>
                                           <?php foreach($objects['credit'] as $objectID => $object):?>
                                               <p><?php echo html::a($this->createLink('credit', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                           <?php endforeach;?>
                                       <?php endif;?>
                                   </td>
                               </tr>

                               <tr>
                                   <th><?php echo $lang->problem->acceptUser;?></th>
                                   <td><?php echo zget($users, $problem->acceptUser, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->acceptDept;?></th>
                                   <td><?php echo zget($depts, $problem->acceptDept, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->createdBy;?></th>
                                   <td><?php echo zget($users, $problem->createdBy, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->createdDate;?></th>
                                   <td><?php echo $problem->createdDate;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->editedBy;?></th>
                                   <td><?php echo zget($users, $problem->editedBy, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->editedDate;?></th>
                                   <td><?php echo $problem->editedDate;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->closedBy;?></th>
                                   <td><?php echo zget($users, $problem->closedBy, '');?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->closedDate;?></th>
                                   <td><?php echo $problem->closedDate;?></td>
                               </tr>
                               <?php if(common::hasPriv('problem', 'updateStatusLinkage') || $this->app->user->account == 'admin'):?>
                               <tr>
                                   <th><?php echo $lang->problem->secureStatusLinkage;?></th>
                                   <td>
                                       <?php echo zget($this->lang->problem->secureStatusLinkageList,$problem->secureStatusLinkage,'');?>
                                       <?php echo  common::printIcon('problem', 'updateStatusLinkage', "problemID=$problem->id", $problem, 'list','edit','','iframe',true) ;?>
                                   </td>
                               </tr>
                               <?php endif;?>
                               <tr>
                                   <th><?php echo $lang->problem->isExceedByTime;?>
                                       <i title="<?php echo $lang->problem->isExceedByTimeHelp;?>" class="icon icon-help"></i>
                                   </th>
                                   <td>
                                       <?php
                                       $solvedTime   = strpos($problem->solvedTime,'0000-00-00') === false ? $problem->solvedTime : '';
                                       $dealAssigned = strpos($problem->dealAssigned,'0000-00-00') === false ? $problem->dealAssigned : '';
                                       if(empty($dealAssigned)){
                                           echo $problem->isExceedByTime;
                                       }else{
                                           echo $problem->isExceedByTime . '(' . $dealAssigned . ' ~ ' . $solvedTime . ')';
                                       }
                                       ?>
                                   </td>
                               </tr>
                               <?php if(isset($this->lang->problem->isExtendedUserList[$this->app->user->account]) || $this->app->user->account == 'admin'):?>
                                   <tr>
                                       <th><?php echo $lang->problem->isExtended;?></th>
                                       <td>
                                           <?php echo zget($this->lang->problem->isExtendedList,$problem->isExtended,'');?>
                                           <?php echo html::a($this->createLink('problem', 'isExtended', "problemID=$problem->id", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                       </td>
                                   </tr>
                                   <tr>
                                       <th><?php echo $lang->problem->isBackExtended;?></th>
                                       <td>
                                           <?php echo zget($this->lang->problem->isBackExtendedList,$problem->isBackExtended,'');?>
                                           <?php echo html::a($this->createLink('problem', 'isBackExtended', "problemID=$problem->id", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                       </td>
                                   </tr>
                               <?php endif;?>
                               <?php if(in_array($this->app->user->account,explode(',',$this->lang->problem->examinationResultUpdateList['userList'])) || $this->app->user->account == 'admin'):?>
                               <tr>
                                   <th><?php echo $lang->problem->dealAssigned;?></th>
                                   <td><?php echo strpos($problem->dealAssigned,'0000-00-00') === false ? $problem->dealAssigned : '';?></td>
                               </tr>
                              <?php endif;?>
                               <tr>
                                   <th><?php echo $lang->problem->solvedTime;?></th>
                                   <td><?php echo strpos($problem->solvedTime,'0000-00-00') === false ? $problem->solvedTime : '';?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->actualOnlineDate;?></th>
                                   <td><?php echo strpos($problem->actualOnlineDate,'0000-00-00') === false ? $problem->actualOnlineDate : '';?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->completedPlan;?><span><i class='icon icon-help' title="<?php echo $lang->problem->completedPlanTip ;?>"></i></span></th>
                                   <td><?php echo zget($this->lang->problem->completedPlanList,$problem->completedPlan,'');?></td>
                               </tr>
                               <?php if(in_array($this->app->user->account,explode(',',$this->lang->problem->examinationResultUpdateList['userList'])) || $this->app->user->account == 'admin'):?>
                               <tr>
                                   <th><?php echo $lang->problem->examinationResult;?></th>
                                   <td>
                                       <?php echo zget($this->lang->problem->examinationResultList,$problem->examinationResult,'');?>
                                       <?php echo common::hasPriv('problem', 'editExaminationResult')|| $this->app->user->account == 'admin'  ? common::printIcon('problem', 'editExaminationResult', "problemID=$problem->id", $problem, 'list','edit','','iframe',true)  : ''?>
                                   </td>
                               </tr>
                               <?php endif;?>
                               </tbody>
                           </table>
                       </div>
                   </div>
               </div>
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->problemFeedbackInfor;?></div>
                       <div class='detail-content'>
                           <table class = 'table table-data'>
                               <tr>
                                   <th class="w-140px"><?php echo $lang->problem->problemFeedbackId;?></th>
                                   <td><?php echo $problem->IssueId;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->ReviewStatus;?></th>
                                   <td><?php echo zget($lang->problem->feedbackStatusList, $problem->ReviewStatus); ?></td>
                               </tr>

                               <tr>
                                   <th><?php echo $lang->problem->feedbackToHandle;?></th>
                                   <td><?php echo $problem->feedbackToHandle;?></td>
                               </tr>
                            <?php if($problem->createdBy != 'guestjx'): ?>
                               <tr>
                                   <th><?php echo $lang->problem->IfultimateSolution;?></th>
                                   <td><?php echo zget($lang->problem->ifultimateSolutionList, $problem->IfultimateSolution); ?></td>
                               </tr>
                            <?php endif; ?>
                               <tr>
                                   <th><?php echo $lang->problem->TeleOfIssueHandler;?></th>
                                   <td><?php echo $problem->TeleOfIssueHandler;?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->PlannedTimeOfChange;?></th>
                                   <td><?php echo $problem->PlannedTimeOfChange;?></td>
                               </tr>
                               <?php if($problem->createdBy != 'guestjx'): ?>
                                   <tr>
                                       <th><?php echo $lang->problem->PlannedDateOfChangeReport;?></th>
                                       <td><?php echo $problem->PlannedDateOfChangeReport;?></td>
                                   </tr>
                                   <tr>
                                       <th><?php echo $lang->problem->PlannedDateOfChange;?></th>
                                       <td><?php echo $problem->PlannedDateOfChange;?></td>
                                   </tr>
                                   <tr>
                                       <th><?php echo $lang->problem->CorresProduct;?></th>
                                       <td><?php echo $problem->CorresProduct;?></td>
                                   </tr>
                               <?php endif;?>
                               <tr>
                                   <th><?php echo $lang->problem->feedbackNum;?></th>
                                   <td><?php echo $problem->feedbackNum>0?$problem->feedbackNum-1:0;?></td>
                               </tr>
                               <?php if($problem->createdDate >= '2022-08-01'): ?>
                               <tr>
                                   <th><?php echo $lang->problem->outsideFeedbackDate;?></th>
                                   <td><?php echo $problem->ifOverDate['end'];?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->ifOverTime;?></th>
                                   <td><?php echo $problem->ifOverDate['string'];?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->insideFeedbackDate;?></th>
                                   <td><?php echo $problem->ifOverDateInside['end'];?></td>
                               </tr>
                               <tr>
                                   <th><?php echo $lang->problem->ifOverTimeInside;?></th>
                                   <td><?php echo $problem->ifOverDateInside['string'];?></td>
                               </tr>
                                <?php endif; ?>
                           </table>
                       </div>
                   </div>
               </div>
               <?php if(!empty($problem->delayResolutionDate)):?>
                   <div class="cell">
                       <div class="detail">
                           <div class="detail-title"><?php echo $lang->problem->delayInfo;?></div>
                           <div class='detail-content'>
                               <table class='table table-data '>
                                   <tbody>
                                   <tr>
                                       <th class='w-140px'><?php echo $lang->problem->originalResolutionDate; ?></th>
                                       <td><?php echo date('Y-m-d', strtotime($problem->originalResolutionDate)); ?></td>
                                   </tr>
                                   <tr>
                                       <th><?php echo $lang->problem->delayResolutionDate;?></th>
                                       <td><?php echo date('Y-m-d', strtotime($problem->delayResolutionDate)); ?></td>
                                   </tr>
                                   <tr>
                                       <th><?php echo $lang->problem->delayReason;?></th>
                                       <td><?php echo  $problem->delayReason;?></td>
                                   </tr>
                                   <tr>
                                       <th><?php echo $lang->problem->delayUser;?></th>
                                       <td><?php echo zget($users,$problem->delayUser,'');?></td>
                                   </tr>
                                   <tr>
                                       <th><?php echo sprintf($lang->problem->delayDate,$lang->problem->delayName);?></th>
                                       <td><?php echo $problem->delayDate;?></td>
                                   </tr>
                                   <tr>
                                       <th><?php echo  sprintf($lang->problem->delayStatus,$lang->problem->delayName);?></th>
                                       <td><?php echo sprintf(zget($lang->problem->delayStatusList,$problem->delayStatus,''),$lang->problem->delayName);?></td>
                                   </tr>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   </div>
               <?php endif;?>
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->statusMove ;?></div>
                       <div class='detail-content'>
                           <table class='table table-data'>
                               <tbody>
                               <tr>
                                   <th class='w-100px'><?php echo $lang->problem->nodeUser;?></th>
                                 <!--  <td class='text-right'><?php /*echo $lang->problem->consumed;*/?></td>-->
                                   <td class='text-center'><?php echo $lang->problem->before;?></td>
                                   <td class='text-center'><?php echo $lang->problem->after;?></td>
                                   <td class='text-left'><?php echo $lang->actions;?></td>
                               </tr>
                               <?php foreach($problem->consumed as $index => $c):?>
                                   <tr>
                                       <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                                      <!-- <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                                       <td class='text-center'><?php echo zget($lang->problem->nowConsumedstatusList, $c->before, '-');?></td>
                                       <td class='text-center'><?php echo zget($lang->problem->nowConsumedstatusList, $c->after, '-');?></td>
                                       <td class='c-actions text-left'>
                                           <?php
                                           common::printIcon('problem', 'workloadEdit', "problemID={$problem->id}&consumedid={$c->id}", $problem, 'list', 'edit', '', 'iframe', true);
                                           if($index) common::printIcon('problem', 'workloadDelete', "problemID=$problem->id&consumedid={$c->id}", $problem, 'list', 'trash', '', 'iframe', true);
                                           common::printIcon('problem', 'workloadDetails', "problemID={$problem->id}&consumedid={$c->id}", $problem, 'list', 'glasses', '', 'iframe', true);
                                           ?>
                                       </td>
                                   </tr>
                               <?php endforeach;?>
                               </tbody>
                           </table>
                       </div>
                   </div>
               </div>
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->feedBackMove;?></div>
                       <div class='detail-content'>
                           <table class='table table-data'>
                               <tbody>
                               <tr>
                                   <th class='w-100px'><?php echo $lang->problem->nodeUser;?></th>
                                 <!--  <td class='text-right'><?php /*echo $lang->problem->consumed;*/?></td>-->
                                   <td class='text-center'><?php echo $lang->problem->before;?></td>
                                   <td class='text-center'><?php echo $lang->problem->after;?></td>
                               </tr>
                               <?php
                               $lang->problem->nowConsumedstatusList['waitsync'] = '待同步外部';
                               ?>
                               <?php foreach($problem->feeckBackConsumed as $index => $c):?>
                                   <tr>
                                       <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                                       <td class='text-center'><?php echo zget($lang->problem->nowConsumedstatusList, $c->before, '-');?></td>
                                       <td class='text-center'><?php echo zget($lang->problem->nowConsumedstatusList, $c->after, '-');?></td>
                                   </tr>
                               <?php endforeach;?>
                               </tbody>
                           </table>
                       </div>
                   </div>
               </div>
               <?php if(!empty($problem->changeResolutionDate)):?>
                   <div class="cell">
                       <div class="detail">
                           <div class="detail-title"><?php echo sprintf($lang->problem->delayMove,$lang->problem->changeConsumed);?></div>
                           <div class='detail-content'>
                               <table class='table table-data'>
                                   <tbody>
                                   <tr>
                                       <th class='w-100px'><?php echo $lang->problem->nodeUser;?></th>
                                       <td class='text-center'><?php echo $lang->problem->before;?></td>
                                       <td class='text-center'><?php echo $lang->problem->after;?></td>
                                   </tr>

                                   <?php foreach($problem->changeConsumed as $index => $c):?>
                                       <tr>
                                           <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                                           <?php
                                           if(($c->before=='toDepart'&&$c->after=='toManager')||($problem->changeConsumed[$index-1]->before=='toDepart'&&$c->before=='toManager')){
                                               $lang->problem->delayStatusList['toManager']='分管领导处理';
                                               $lang->problem->delayStatusList=array_merge($lang->problem->delayStatusList,['success'=>'通过']);
                                           }else{
                                               $lang->problem->delayStatusList['toManager']='公司领导处理';
//                                               $lang->problem->delayStatusList=array_merge($lang->problem->delayStatusList,['success'=>'通过（不上报）']);
                                           }
                                           ?>
                                           <td class='text-center'><?php
                                               echo $a = zget($lang->problem->delayStatusList, $c->before, '-');
                                               ?></td>
                                           <td class='text-center'><?php
                                              $b =  zget($lang->problem->delayStatusList, $c->after, '-');
                                               echo $a=='产品创新部处理'&&$b =='通过'?'通过（不上报）':$b;
                                               ?></td>
                                       </tr>
                                   <?php endforeach;?>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   </div>
               <?php endif; ?>
               <?php if(!empty($problem->delayResolutionDate)):?>
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo sprintf($lang->problem->delayMove,$lang->problem->delayConsumed);?></div>
                       <div class='detail-content'>
                           <table class='table table-data'>
                               <tbody>
                               <tr>
                                   <th class='w-100px'><?php echo $lang->problem->nodeUser;?></th>
                                   <td class='text-center'><?php echo $lang->problem->before;?></td>
                                   <td class='text-center'><?php echo $lang->problem->after;?></td>
                               </tr>
                               <?php foreach($problem->delayConsumed as $index => $c):?>
                                   <tr>
                                       <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                                       <td class='text-center'><?php echo sprintf(zget($lang->problem->delayStatusList, $c->before, '-'),$lang->problem->delayName);?></td>
                                       <td class='text-center'><?php echo sprintf(zget($lang->problem->delayStatusList, $c->after, '-'),$lang->problem->delayName);?></td>
                                   </tr>
                               <?php endforeach;?>
                               </tbody>
                           </table>
                       </div>
                   </div>
               </div>
       <?php endif; ?>
           </div>
       </div>

<?php
   }
?>
<?php js::set('delayErrorMsg', $delayErrorMsg); ?>
<script>
    function delayCheck()
    {
        if(delayErrorMsg !== ''){
            let errorMsg = new $.zui.Messager({
                type:'success',
                time: 3000,
            })
            errorMsg.show(delayErrorMsg);
        }else {
            $('#delayCheck').click();
        }

        // window.location.href = '/cfit-lsj/problem-delay-2479.html?onlybody=yes';
    }
</script>
<?php include '../../../common/view/footer.html.php';?>
