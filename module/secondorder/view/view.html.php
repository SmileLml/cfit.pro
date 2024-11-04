<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
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
        <?php if($secondorder->completeStatus == 1): ?>
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
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
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
        if($secondorder->status == 'todelivered' && (($secondorder->type != 'support' and $secondorder->type != 'consult') or ($secondorder->type == 'consult' and $secondorder->handoverMethod == 'sectransfer'))){
            common::printIcon('sectransfer', 'create', "secondorderId=$secondorder->id", $secondorder, 'button', 'time', '', 'iframe', true, '', '发起对外移交');
        }
        if($secondorder->status == 'returned' and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin')){
            common::printIcon('secondorder', 'returned', "secondorderID=$secondorder->id", $secondorder, 'list', 'back', '', 'iframe', true);
        }

          common::printIcon('secondorder', 'editAssignedTo', "secondorderID=$secondorder->id", $secondorder, 'button', 'hand-right', '', 'iframe', true);
          common::printIcon('secondorder', 'copy', "secondorderID=$secondorder->id", $secondorder, 'list');
          if($secondorder->status != 'closed') common::printIcon('secondorder', 'close', "secondorderID=$secondorder->id", $secondorder, 'list','off', '', 'iframe', true);
          common::printIcon('secondorder', 'delete', "secondorderID=$secondorder->id", $secondorder, 'button', 'trash', '', 'iframe', true);
        ?>
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
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->externalCode;?></th>
                  <td><?php echo $secondorder->externalCode;?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->sourcePlatform;?></th>
                  <td><?php echo $secondorder->sourcePlatform;?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->sourceBackground;?></th>
                  <td><?php echo zget($lang->secondorder->sourceBackgroundList, $secondorder->sourceBackground, $secondorder->sourceBackground);?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->secondorder->type;?></th>
                <td><?php echo zget($lang->secondorder->typeList, $secondorder->type, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->subtype;?></th>
                  <td><?php echo zget($childTypeList, $secondorder->subtype, '');?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->secondorder->source;?></th>
                <td><?php echo zget($lang->secondorder->sourceList, $secondorder->source, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->team;?></th>
                  <td><?php echo zget($lang->application->teamList, $secondorder->team);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->union;?></th>
                  <td><?php echo zget($lang->opinion->unionList, $secondorder->union);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->exceptDoneDate;?></th>
                  <td><?php echo $secondorder->exceptDoneDate != '0000-00-00' ? $secondorder->exceptDoneDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->completeStatus;?></th>
                  <td><?php echo zget($lang->secondorder->completeStatusList, $secondorder->completeStatus, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->secondorder->app;?></th>
                <td>
                <?php echo $secondorder->app ? html::a($this->createLink('application', 'view', 'id=' . $secondorder->app), zget($apps, $secondorder->app)->name, '', "style='color: #0c60e1;'") : ''; ?>
                </td>
              </tr>
              <?php if($secondorder->type != 'support'): ?>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->implementationForm;?></th>
                  <td><?php echo zget($lang->secondorder->implementationFormList, $secondorder->implementationForm);?></td>
              </tr>
              <?php endif; ?>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->cbpProject;?></th>
                  <td><?php echo zget($outsideplan, $secondorder->cbpProject);?></td>
              </tr>
              <?php if($secondorder->type != 'support'): ?>
              <tr>
                  <th class="w-100px"><?php echo $lang->secondorder->internalProject;?></th>
                  <td><?php echo zget($projectList, $secondorder->internalProject);?></td>
              </tr>
                  <tr>
                      <?php $executionName = $this->dao->select('name')->from(TABLE_EXECUTION)->where('id')->eq($secondorder->execution)->fetch(); ?>
                      <th class="w-100px"><?php echo $lang->secondorder->execution;?></th>
                      <td><?php echo zget($executions, $secondorder->execution, $executionName->name);?></td>
                  </tr>
              <?php endif;?>
              <tr>
                  <th><?php echo $lang->secondorder->taskIdentification;?></th>
                  <td>
                      <?php echo $lang->secondorder->taskIdentificationList[$secondorder->taskIdentification]; ?>
                  </td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->protransferDesc;?></th>
                  <td>
                      <?php
                      if(!empty($protransferDesc)){
                          foreach ($protransferDesc as $key => $item){
                              echo html::a($this->createLink('sectransfer', 'view', 'id=' . $key), $item, '') . "<br/>";
                          }
                      }
                      ?>
                  </td>
              </tr>
              <tr>
                 <th><?php echo $lang->secondorder->task;?></th>
                <td><?php if($task)  echo html::a('javascript:void(0)', $task->taskName, '', 'data-app="project" onclick="seturl('.$projectid->project.','.$task->id.')"')?></td>
                <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondtaskurl"')?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->buildName;?></th>
                  <td><?php if(isset($buildAndRelease->buildname) && $buildAndRelease->buildname)  echo html::a('javascript:void(0)', $buildAndRelease->buildname, '', 'data-app="project" onclick="newurl('.$projectid->project.','.$buildAndRelease->bid.')"')?></td>
                  <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondbuildurl"')?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->releaseName;?></th>
                  <td><?php if(isset($buildAndRelease->releasename) && $buildAndRelease->releasename)  echo html::a('javascript:void(0)', $buildAndRelease->releasename, '', 'data-app="project" onclick="addurl('.$projectid->project.','.$buildAndRelease->rid.')"')?></td>
                  <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondreleaseurl"')?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->contacts;?></th>
                  <td><?php echo zget($users, $secondorder->contacts, $secondorder->contacts, $secondorder->contacts);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->contactsPhone;?></th>
                  <td><?php echo $secondorder->contactsPhone;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->acceptUser;?></th>
                  <td><?php echo zget($users, $secondorder->acceptUser, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->acceptDept;?></th>
                  <td><?php echo zget($depts, $secondorder->acceptDept, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->planstartDate;?></th>
                  <td><?php echo $secondorder->planstartDate!= '0000-00-00' ? $secondorder->planstartDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->planoverDate;?></th>
                  <td><?php echo $secondorder->planoverDate!= '0000-00-00' ? $secondorder->planoverDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->startDate;?></th>
                  <td><?php echo $secondorder->startDate!= '0000-00-00' ? $secondorder->startDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->overDate;?></th>
                  <td><?php echo $secondorder->overDate!= '0000-00-00' ? $secondorder->overDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->secondorder->createdDept;?></th>
                  <td><?php echo zget($depts, $secondorder->createdDept, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->secondorder->relevantUser;?></th>
                <td><?php echo $relevantUsers;?></td>
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
                      <tr>
                          <th><?php echo $lang->secondorder->status;?></th>
                          <td><?php echo zget($lang->secondorder->statusList, $secondorder->status, '');?></td>
                      </tr>
                      <tr>
                          <th class="w-100px"><?php echo $lang->secondorder->dealUser;?></th>
                          <td><?php echo zmget($users, $secondorder->dealUser, '');?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->secondorder->createdBy;?></th>
                          <td><?php echo zget($users, $secondorder->createdBy, '');?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->secondorder->createdDate;?></th>
                          <td><?php echo $secondorder->createdDate;?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->secondorder->editedBy;?></th>
                          <td><?php echo zget($users, $secondorder->editedBy, '');?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->secondorder->editedDate;?></th>
                          <td><?php echo $secondorder->editedDate;?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->secondorder->closedBy;?></th>
                          <td><?php echo zget($users, $secondorder->closedBy, '');?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->secondorder->closedDate;?></th>
                          <td><?php echo $secondorder->closedDate;?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->secondorder->closeReason;?></th>
                          <td><?php echo $secondorder->closeReason;?></td>
                      </tr>
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
                          <tr>
                              <th><?php echo $lang->secondorder->externalStatus;?></th>
                              <td><?php echo zget($lang->secondorder->externalStatusList, $secondorder->externalStatus, '');?></td>
                          </tr>
                          <tr>
                              <th class="w-100px"><?php echo $lang->secondorder->rejectUser;?></th>
                              <td><?php echo zget($users, $secondorder->rejectUser, $secondorder->rejectUser);?></td>
                          </tr>
                          <tr>
                              <th><?php echo $lang->secondorder->rejectReason;?></th>
                              <td><?php echo $secondorder->rejectReason;?></td>
                          </tr>
                          <tr>
                              <th><?php echo $lang->secondorder->externalTime;?></th>
                              <td><?php echo $secondorder->externalTime;?></td>
                          </tr>
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
</script>
<?php include '../../common/view/footer.html.php';?>
