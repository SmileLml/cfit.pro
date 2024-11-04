<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php $browseLink = $app->session->outsideplanList != false ? $app->session->outsideplanList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php printf('%03d', $plan->id);?></span>
      <span class="text" title='<?php echo $plan->name;?>'><?php echo $plan->name;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">

    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->outsideplan->common;?></div>
        <div class="detail-content article-content">

          <table class='table'>
            <tr>
              <th style="width: 160px;"><?php echo $lang->outsideplan->year;?></th>
              <td ><?php echo $plan->year;?></td>
                <th style="width: 160px;"><?php echo $lang->outsideplan->apptype;?></th>
                <td ><?php echo zget($lang->outsideplan->apptypeList,$plan->apptype);?></td>

            </tr>
              <tr>
                  <th style="width: 160px"><?php echo $lang->outsideplan->code;?></th>
                  <td><?php echo $plan->code;?></td>
                  <th style="width: 160px"><?php echo $lang->outsideplan->historyCode;?></th>
                  <td><?php echo $plan->historyCode;?></td>
              </tr>
            <tr>
              <th><?php echo $lang->outsideplan->name;?></th>
              <td colspan='3'><?php echo $plan->name;?></td>
            </tr>
            <tr>
                  <th><?php echo $lang->outsideplan->begin;?></th>
                  <td><?php echo $plan->begin ;?></td>
                  <th><?php echo $lang->outsideplan->end;?></th>
                  <td><?php echo $plan->end ;?></td>
              </tr>
            <tr>
              <th><?php echo $lang->outsideplan->workload;?></th>
              <td><?php echo $plan->workload ;?></td>
              <th><?php echo $lang->outsideplan->duration;?></th>
              <td><?php echo $plan->duration ;?></td>
            </tr>
            <tr>
                  <th><?php echo $lang->outsideplan->status;?></th>
                  <td><?php echo zget($lang->outsideplan->statusList,$plan->status,'');?></td>
                  <th><?php echo $lang->outsideplan->maintainers;?></th>
                  <td><?php foreach ($maintainersusers as $muser) { echo $muser->realname." ";} ;?></td>
            </tr>

              <tr>
                  <th><?php echo $lang->outsideplan->projectinitplan;?></th>
                  <td colspan='3'><?php echo nl2br($plan->projectinitplan);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->outsideplan->uatplanfinishtime;?></th>
                  <td colspan='3'><?php echo nl2br($plan->uatplanfinishtime);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->outsideplan->materialplanonlinetime;?></th>
                  <td colspan='3'><?php echo nl2br($plan->materialplanonlinetime);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->outsideplan->planonlinetime;?></th>
                  <td colspan='3'><?php echo nl2br($plan->planonlinetime);?></td>
              </tr>

            <tr>
              <th><?php echo $lang->outsideplan->milestone;?></th>
              <td colspan='3'><?php echo $plan->milestone;?></td>
            </tr>

              <tr>
                  <th><?php echo $lang->outsideplan->projectisdelay;?></th>
                  <td ><?php echo zget($lang->outsideplan->projectisdelayList,$plan->projectisdelay);?></td>
                  <th><?php echo $lang->outsideplan->projectischange;?></th>
                  <td ><?php echo zget($lang->outsideplan->projectischangeList,$plan->projectischange);?></td>
              </tr>

              <?php if($plan->projectisdelay == 2){
                  ?>
                  <tr>
                      <th><?php echo $lang->outsideplan->projectisdelaydesc;?></th>
                      <td colspan='3'><?php echo nl2br($plan->projectisdelaydesc) ;?></td>
                  </tr>
              <?php
              }?>
              <?php if($plan->projectischange == 2){
                  ?>
                  <tr>
                      <th><?php echo $lang->outsideplan->projectischangedesc;?></th>
                      <td colspan='3'><?php echo nl2br($plan->projectischangedesc);?></td>
                  </tr>
                  <?php
              }?>


            <tr>
              <th><?php echo $lang->outsideplan->changestatus;?></th>
              <td colspan='3'><?php echo  $plan->changes;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->comment;?></th>
              <td colspan='3'><?php echo $plan->remark;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->outsideplan->filesList;?></th>
              <td colspan='3'>                <div class='detail'>
                      <div class='detail-content article-content'>
                          <?php
                          if($plan->files){
                              echo $this->fetch('file', 'printFiles', array('files' => $plan->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                          }else{
                              echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                          }
                          ?>
                      </div>
                  </div>
              </td>
            </tr>

          </table>
      </div>
            <div class="detail-title">子项目及(外部)子项/子任务</div>
            <div class="detail-content article-content">
        <?php foreach ($plan->subprojects as $subproject) {?>
            <table class='table'>
                <tr>
                    <th style="width: 160px;"><?php echo $lang->outsideplan->subProjectName;?></th>
                    <td colspan='3'><?php echo $subproject->subProjectName;?></td>
                </tr>
                <?php
                if(empty($subproject->tasks)) $subproject->tasks = []; //不报错
                foreach ($subproject->tasks as $task) {?>
                <tr>
                    <th ><?php echo $lang->outsideplan->subTaskName;?></th>
                    <td colspan='3' style="background-color: #eee"><?php echo $task->subTaskName;?></td>
                </tr>
                    <tr>
                        <th ><?php echo $lang->outsideplan->subTaskDesc;?></th>
                        <td colspan='3' ><?php echo html_entity_decode($task->subTaskDesc);?></td>
                    </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->subProjectBegin;?></th>
                    <td><?php echo  $task->subTaskBegin ;?></td>
                    <th style="width: 160px"><?php echo $lang->outsideplan->subProjectEnd;?></th>
                    <td><?php echo  $task->subTaskEnd ;?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->subProjectUnit;?></th>
                    <td><?php
                        $vlist = explode(',', $task->subTaskUnit);
                        $arr = [];
                        foreach ($vlist as $itemv){
                            if(empty($itemv)) continue;
                            $arr[] = zget($lang->outsideplan->subProjectUnitList, $itemv,'');
                        }
                        echo implode(',', $arr);
                        ?></td>
                    <th><?php echo $lang->outsideplan->subProjectBearDept;?></th>
                    <td><?php
                        $vlist = explode(',', $task->subTaskBearDept);
                        $arr = [];
                        foreach ($vlist as $itemv){
                            if(empty($itemv)) continue;
                            $arr[] = zget($lang->application->teamList, $itemv,'') ;
                        }
                        echo implode(',', $arr);
                        ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->subProjectDemandParty;?></th>
                    <td><?php
                        $vlist = explode(',', $task->subTaskDemandParty);
                        $arr = [];
                        foreach ($vlist as $itemv){
                            if(empty($itemv)) continue;
                            $arr[] = zget($lang->outsideplan->subProjectDemandPartyList, $itemv,'') ;
                        }
                        echo implode(',', $arr);
                        ?></td>
                    <th><?php echo $lang->outsideplan->subProjectDemandContact;?></th>
                    <td><?php echo  $task->subTaskDemandContact ;?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->subProjectDemandDeadline;?></th>
                    <td colspan='3'><?php echo  $task->subTaskDemandDeadline;?></td>
                </tr>

                <?php } ?>
            </table>
                <?php } ?>
        </div>
      </div>

    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack(inlink('browse'));?>
        <div class='divider'></div>
        <?php
          common::printIcon('outsideplan', 'edit', "outsideplanID=$plan->id", $plan, 'list');
          common::printIcon('outsideplan', 'delete', "outsideplanID=$plan->id", $plan, 'button', 'trash', 'hiddenwin');
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->outsideplan->linkedInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
            <tr>
                <th class='w-120px'><?php echo $lang->outsideplan->status;?></th>
                <td>
                    <?php echo  zget($lang->outsideplan->statusList, $plan->status,'');?><span class='c-actions text-left'><?php
                    common::printIcon('outsideplan', 'editStatus', "id=$plan->id", $plan, 'list', 'edit', '', 'iframe', true);
                        ?>
                    </span>
                </td>
            </tr>
             <!-- <tr>
                <th><?php /*echo $lang->outsideplan->linkedInnerProjectPlans;*/?></th>
                <td>
                    <?php
/*                    foreach ($plans as $projectplan) {
                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplan->id), $projectplan->name.' ('. zget($lang->projectplan->insideStatusList, $projectplan->insideStatus,'').')', '', "").'<br>';
                    }
                    */?>
                </td>
              </tr>-->
              <tr>
                  <th><?php echo $lang->outsideplan->EndedInnerProjectPlans;?></th>
                  <td>
                      <?php
                      foreach ($plans as $projectplan) {
                          if($projectplan->insideStatus == 'done') echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplan->id), $projectplan->name.' ('. zget($lang->projectplan->insideStatusList, $projectplan->insideStatus,'').')', '', "").'<br>';
                      }
                      ?>
                  </td>
              </tr>
              <tr>
                  <th><?php echo $lang->outsideplan->toDoInnerProjectPlans;?></th>
                  <td>
                      <?php
                      foreach ($plans as $projectplan) {
                          if($projectplan->insideStatus != 'done' && $projectplan->insideStatus != 'cancel' && $projectplan->insideStatus != 'abort')
                          echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplan->id), $projectplan->name.' ('. zget($lang->projectplan->insideStatusList, $projectplan->insideStatus,'').')', '', "").'<br>';
                      }
                      ?>
                  </td>
              </tr>
            <tr>
                  <th><?php echo $lang->outsideplan->deletedInnerProjectPlans;?></th>
                  <td>
                      <?php
                      foreach ($plans as $projectplan) {
                          if($projectplan->insideStatus == 'cancel' || $projectplan->insideStatus == 'abort')
                          echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplan->id), $projectplan->name.' ('. zget($lang->projectplan->insideStatusList, $projectplan->insideStatus,'').')', '', "").'<br>';
                      }
                      ?>
                  </td>
              </tr>
              <tr>
                <th><?php echo $lang->outsideplan->createdBy;?></th>
                <td><?php echo $createdBy = current($createdBy)->realname ?: $plan->createdBy;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->outsideplan->createdDate;?></th>
                <td><?php echo $plan->createdDate;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
