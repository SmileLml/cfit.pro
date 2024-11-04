<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>.body-modal #mainMenu>.btn-toolbar .page-title {width: auto;}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php $browseLink = $app->session->deptorderList != false ? $app->session->deptorderList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $deptorder->code?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->deptorder->summary;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($deptorder->summary) ? $deptorder->summary : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->deptorder->progress;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($deptorder->progress) ? $deptorder->progress : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <div class="detail">
            <div class="detail-title"><?php echo $Res;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($deptorder->Res) ? $deptorder->Res : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->deptorder->desc;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($deptorder->desc) ? $deptorder->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>

        <?php if($deptorder->files) echo $this->fetch('file', 'printFiles', array('files' => $deptorder->files, 'fieldset' => 'true'));?>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=deptorder&objectID=$deptorder->id");?>
    </div>
      <?php if(common::hasPriv('deptorder','getProgressInfo')):?>
          <div class="cell">
              <div class="detail-title"><?php echo $lang->deptorder->conclusionInfo; ?></div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->deptorder->secondLineDevelopmentPlan; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($deptorder->secondLineDevelopmentPlan) ? nl2br($deptorder->secondLineDevelopmentPlan) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->deptorder->progressQA; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($deptorder->progressQA) ? nl2br($deptorder->progressQA) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->deptorder->secondLineDevelopmentStatus; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($deptorder->secondLineDevelopmentStatus) ? zget($lang->deptorder->secondLineDepStatusList,$deptorder->secondLineDevelopmentStatus) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->deptorder->secondLineDevelopmentApproved; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($deptorder->secondLineDevelopmentApproved) ? zget($lang->deptorder->secondLineDepApprovedList,$deptorder->secondLineDevelopmentApproved) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>
              <div class="detail" >
                  <div class="detail-title"><?php echo $lang->deptorder->secondLineDevelopmentRecord; ?></div>
                  <div class="detail-content article-content">
                      <?php echo !empty($deptorder->secondLineDevelopmentRecord) ? zget($lang->deptorder->secondLineDevelopmentRecordList,$deptorder->secondLineDevelopmentRecord) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                  </div>
              </div>

          </div>
      <?php endif;?>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
          if(($deptorder->status == 'assigned' or $deptorder->status == 'backed') and $this->app->user->account == $deptorder->createdBy) common::printIcon('deptorder', 'edit', "deptorderID=$deptorder->id", $deptorder, 'button');
          if(($deptorder->status != 'closed' and $deptorder->status != 'backed') and $this->app->user->account == $deptorder->dealUser) common::printIcon('deptorder', 'deal', "deptorderID=$deptorder->id", $deptorder, 'button', 'time', '', 'iframe', true);
          common::printIcon('deptorder', 'editAssignedTo', "deptorderID=$deptorder->id", $deptorder, 'button', 'hand-right', '', 'iframe', true);
          common::printIcon('deptorder', 'copy', "deptorderID=$deptorder->id", $deptorder, 'button');
          if($deptorder->status != 'closed') common::printIcon('deptorder', 'close', "deptorderID=$deptorder->id", $deptorder, 'button','off', '', 'iframe', true);
          common::printIcon('deptorder', 'delete', "deptorderID=$deptorder->id", $deptorder, 'button', 'trash', '', 'iframe', true);
        common::printIcon('deptorder', 'editSpecialQA', "deptorderID=$deptorder->id", $deptorder, 'button', 'edit', '', 'iframe', true);

        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->deptorder->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class="w-100px"><?php echo $lang->deptorder->ifAccept;?></th>
                <td><?php echo zget($lang->deptorder->ifAcceptList,$deptorder->ifAccept);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->status;?></th>
                  <td><?php echo zget($lang->deptorder->statusList, $deptorder->status, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->deptorder->dealUser;?></th>
                  <td><?php echo zget($users, $deptorder->dealUser, '');?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->deptorder->type;?></th>
                <td><?php echo zget($lang->deptorder->typeList, $deptorder->type, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->deptorder->subtype;?></th>
                  <td><?php echo zget($childTypeList, $deptorder->subtype, '');?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->deptorder->source;?></th>
                <td><?php echo zget($lang->deptorder->sourceList, $deptorder->source, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->deptorder->team;?></th>
                  <td><?php $userList = '';foreach(explode(',', trim($deptorder->team, ',')) as $user) $userList .= $users[$user] . ',';$userList = trim($userList, ',');echo $userList;;?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->deptorder->union;?></th>
                  <td><?php echo zget($lang->deptorder->unionList, $deptorder->union);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->exceptDoneDate;?></th>
                  <td><?php echo $deptorder->exceptDoneDate != '0000-00-00' ? $deptorder->exceptDoneDate : '';?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->app;?></th>
                <td>
                <?php echo $deptorder->app ? html::a($this->createLink('application', 'view', 'id=' . $deptorder->app), zget($apps, $deptorder->app)->name, '', "style='color: #0c60e1;'") : ''; ?>
                </td>
              </tr>
              <tr>
                 <th><?php echo $lang->deptorder->task;?></th>
                <td><?php if($task)  echo html::a('javascript:void(0)', $task->taskName, '', 'data-app="project" onclick="seturl('.$projectid->project.','.$task->id.')"')?></td>
                <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondtaskurl"')?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->buildName;?></th>
                  <td><?php if(isset($buildAndRelease->buildname) && $buildAndRelease->buildname)  echo html::a('javascript:void(0)', $buildAndRelease->buildname, '', 'data-app="project" onclick="newurl('.$projectid->project.','.$buildAndRelease->bid.')"')?></td>
                  <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondbuildurl"')?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->releaseName;?></th>
                  <td><?php if(isset($buildAndRelease->releasename) && $buildAndRelease->releasename)  echo html::a('javascript:void(0)', $buildAndRelease->releasename, '', 'data-app="project" onclick="addurl('.$projectid->project.','.$buildAndRelease->rid.')"')?></td>
                  <td class="hidden"><?php echo html::a('','','','data-app="project"  id="secondreleaseurl"')?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->acceptUser;?></th>
                  <td><?php echo zget($users, $deptorder->acceptUser, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->acceptDept;?></th>
                  <td><?php echo zget($depts, $deptorder->acceptDept, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->planstartDate;?></th>
                  <td><?php echo $deptorder->planstartDate!= '0000-00-00' ? $deptorder->planstartDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->planoverDate;?></th>
                  <td><?php echo $deptorder->planoverDate!= '0000-00-00' ? $deptorder->planoverDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->startDate;?></th>
                  <td><?php echo $deptorder->startDate!= '0000-00-00' ? $deptorder->startDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->overDate;?></th>
                  <td><?php echo $deptorder->overDate!= '0000-00-00' ? $deptorder->overDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->createdDept;?></th>
                  <td><?php echo zget($depts, $deptorder->createdDept, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->createdBy;?></th>
                <td><?php echo zget($users, $deptorder->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->createdDate;?></th>
                <td><?php echo $deptorder->createdDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->editedBy;?></th>
                <td><?php echo zget($users, $deptorder->editedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->editedDate;?></th>
                <td><?php echo $deptorder->editedDate;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->deptorder->closeReason;?></th>
                  <td><?php echo $deptorder->closeReason;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->closedBy;?></th>
                <td><?php echo zget($users, $deptorder->closedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->closedDate;?></th>
                <td><?php echo $deptorder->closedDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->deptorder->relevantUser;?></th>
                <td><?php echo $relevantUsers;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div> 
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->deptorder->consumedTitle;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->deptorder->nodeUser;?></th>
                <td class='text-center'><?php echo $lang->deptorder->before;?></td>
                <td class='text-center'><?php echo $lang->deptorder->after;?></td>
                <td class='text-left'><?php echo $lang->actions;?></td>
              </tr>
              <?php foreach($consumeds as $index => $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                <td class='text-center'><?php echo zget($lang->deptorder->statusList, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->deptorder->statusList, $c->after, '-');?></td>
                <td class='c-actions text-left'>
                  <?php
                  if($index == count($consumeds) - 1) common::printIcon('deptorder', 'statusedit', "deptorderID={$deptorder->id}&consumedid={$c->id}", $deptorder, 'list', 'edit', '', 'iframe', true);
                  ?>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div> 
  </div>
</div>
<script>
    function seturl(project,id){
       $.ajaxSettings.async = false;
          $.post(createLink('deptorder', 'ajaxGetProjectId', "project="+ project,''), function(data)
          {

          });
          $.ajaxSettings.async = true;
          var taskurl = createLink('task', 'view', 'id=' + id);
          $('#secondtaskurl').attr('href',taskurl);
          $('#secondtaskurl')[0].click();
    }
    function addurl(project,id){
          $.ajaxSettings.async = false;
          $.post(createLink('deptorder', 'ajaxGetProjectSession', "project="+ project,''), function(data)
          {

          });
          $.ajaxSettings.async = true;
          var releaseurl = createLink('projectrelease', 'view', 'id=' + id);
          $('#secondreleaseurl').attr('href',releaseurl);
          $('#secondreleaseurl')[0].click();
    }
    function newurl(project,id){
          $.ajaxSettings.async = false;
          $.post(createLink('deptorder', 'ajaxGetProjectBuild', "project="+ project,''), function(data)
          {

          });
          $.ajaxSettings.async = true;
          var buildurl = createLink('build', 'view', 'id=' + id);
          $('#secondbuildurl').attr('href',buildurl);
          $('#secondbuildurl')[0].click();
    }
</script>
<?php include '../../common/view/footer.html.php';?>
