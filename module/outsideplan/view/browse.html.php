<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    foreach($lang->outsideplan->labelList as $label => $labelName)
    {   
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('outsideplan', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
        <?php
        $class = common::hasPriv('outsideplan', 'export') ? '' : "class=disabled";
        $misc  = common::hasPriv('outsideplan', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";        
        $link  = common::hasPriv('outsideplan', 'export') ? $this->createLink('outsideplan', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->outsideplan->export, '', $misc) . "</li>";

        $class = common::hasPriv('outsideplan', 'exportTemplate') ? '' : "class='disabled'";
        $link  = common::hasPriv('outsideplan', 'exportTemplate') ? $this->createLink('outsideplan', 'exportTemplate') : '#';
        $misc  = common::hasPriv('outsideplan', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
        echo "<li $class>" . html::a($link, $lang->outsideplan->exportTemplate, '', $misc) . '</li>';
        ?>  
      </ul>
      <?php if(common::hasPriv('outsideplan', 'import')) echo html::a($this->createLink('outsideplan', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->outsideplan->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
    </div>
    <?php if(common::hasPriv('outsideplan', 'create')) echo html::a($this->createLink('outsideplan', 'create'), "<i class='icon-plus'></i> {$lang->outsideplan->create}", '', "class='btn btn-primary'");?>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='outsideplan'></div>
    <?php if(empty($plans)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='outsideplanForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='outsideplans'>
        <thead>
          <tr>
            <th class='w-40px' rowspan="2" ><?php common::printOrderLink('id', $orderBy, $vars, $lang->outsideplan->id);?></th>
            <th class='w-40px' rowspan="2" ><?php common::printOrderLink('year', $orderBy, $vars, $lang->outsideplan->year);?></th>
            <th class='w-120px' rowspan="2" ><?php common::printOrderLink('name', $orderBy, $vars, $lang->outsideplan->name);?></th>
            <th class='w-100px' rowspan="2" ><?php common::printOrderLink('code', $orderBy, $vars, $lang->outsideplan->code);?></th>
            <th class='w-100px' rowspan="2" ><?php echo $lang->outsideplan->historyCode;?></th>
            <th class='w-100px' rowspan="2" ><?php echo $lang->outsideplan->relatedDepts;?></th>
            <th class='w-100px' rowspan="2" ><?php echo $lang->outsideplan->relatedUnits;?></th>
            <th class='w-80px' rowspan="2" > (外部)项目/任务计划<br>开始时间</th>
            <th class='w-80px' rowspan="2" > (外部)项目/任务计划<br>结束时间</th>
            <th class='w-80px' rowspan="2" >  (外部)项目/任务<br>计划状态 </th>
            <th class='w-80px' rowspan="2" > <?php echo ($lang->outsideplan->maintainers);?></th>
            <th style="width: 60px;!important;" rowspan="2" ><?php echo $lang->outsideplan->subTaskName;?><br>数量</th>
            <th class='w-200px' colspan="4"><?php echo $lang->outsideplan->linkedInnerProjectPlanNum;?></th>
            <th class='text-center w-100px'  rowspan="2" ><?php echo $lang->actions;?></th>
          </tr>
        <tr>
            <td><?php echo $lang->outsideplan->linkedInnerProjectPlanNumTotal;?></td>
            <td><?php echo $lang->outsideplan->linkedInnerProjectPlanNumDone;?></td>
            <td><?php echo $lang->outsideplan->linkedInnerProjectPlanNumUndone;?></td>
            <td><?php echo $lang->outsideplan->deletedInnerProjectPlanNumUndone;?></td>
        </tr>
        </thead>

          <tbody>
          <?php foreach($plans as $plan):?>
              <tr>
                  <td><?php echo $plan->id;?></td>
                  <td><?php echo $plan->year;?></td>
                  <td class="text-ellipsis <?php if(!empty($plan->children)) echo 'has-child';?>" title="<?php echo $plan->name;?>">
                      <?php
                      echo '<span id="toggleid'.$plan->id.'" onclick="showsub('.$plan->id.')" class="table-nest-child-hide table-nest-icon icon table-nest-toggle collapsed" > </span>';
                      echo common::hasPriv('opinion', 'view') ? html::a(inlink('view', "opinionID=$plan->id"), $plan->name) : $plan->name;
                      ?>
                  </td>

                              <td title=<?php echo $plan->code;?>><?php echo $plan->code;?></td>
                              <td title=<?php echo $plan->historyCode;?>><?php echo $plan->historyCode;?></td>
                              <td title="<?php if(isset($unitsAndDepts[$plan->id]['depts'])) { echo implode(',', array_unique($unitsAndDepts[$plan->id]['depts'])); }?>"><?php if(isset($unitsAndDepts[$plan->id]['depts'])) { echo implode(',', array_unique($unitsAndDepts[$plan->id]['depts'])); }?></td>
                              <td title="<?php if(isset($unitsAndDepts[$plan->id]['units'])) { echo implode(',', array_unique($unitsAndDepts[$plan->id]['units'])); }?>"><?php if(isset($unitsAndDepts[$plan->id]['units'])) { echo implode(',', array_unique($unitsAndDepts[$plan->id]['units'])); }?></td>
                              <td><?php if(!helper::isZeroDate($plan->begin)) echo $plan->begin;?></td>
                              <td><?php if(!helper::isZeroDate($plan->end)) echo $plan->end;?></td>
                                <td><?php echo zget($lang->outsideplan->statusList, $plan->status, '');?></td>
                              <?php $maintainers = ''; foreach (explode(',', $plan->maintainers) as $plan->maintainer) { $maintainers .= zget($users, $plan->maintainer,''). ' ';} ?>
                              <td  title = "<?php echo $maintainers; ?>" ><?php echo $maintainers; ?></td>
                              <td> <?php echo $taskNum = $unitsAndDepts[$plan->id]['taskNum'] ?? 0; ?></td>
                              <td style="text-align: center"> <?php
                                  $projectPlanIds = explode(',', $plan->linkedPlan);
                                  $projectPlanNum = 0; //一共的
                                  $projectDeletedPlanNum = 0; //撤销删除的
                                  $projectPlanDoneNum = 0; // 完成的
                                  foreach ($projectPlanIds as $projectPlanId)
                                  {
                                      // || empty($projectPlanInsideStatus[$projectPlanId])
                                      if(empty($projectPlanId) || !isset($projectPlanInsideStatus[$projectPlanId])) continue;
                                      $projectPlanNum++;
                                      if($projectPlanInsideStatus[$projectPlanId] == 'done'){ //已经完成的
                                          $projectPlanDoneNum ++;
                                      }
                                      if($projectPlanInsideStatus[$projectPlanId] == 'cancel' || $projectPlanInsideStatus[$projectPlanId] == "abort"){ //已经完成的
                                          $projectDeletedPlanNum ++;
                                      }
                                  }
                                  echo $projectPlanNum;
                              ?></td>
                              <td style="text-align: center"> <?php echo ($projectPlanDoneNum); ?></td>
<!--                                一共 - 完成- 删除= 未完成-->
                              <td style="text-align: center"> <?php echo ($projectPlanNum-$projectPlanDoneNum-$projectDeletedPlanNum); ?></td>
                              <td style="text-align: center"> <?php echo $projectDeletedPlanNum; ?></td>

                              <td class='c-actions'>
                                <?php

                                common::printIcon('outsideplan', 'edit', "outsideplanID=$plan->id", $plan, 'list');
                                common::printIcon('outsideplan', 'delete', "outsideplanID=$plan->id", $plan, 'list', 'trash', 'hiddenwin');
                                ?>
                  </td>
              </tr>
              <?php if(!empty($plan->children)):;?>

                  <?php foreach($plan->children as $key => $task):?>

                      <tr class='subpro<?php echo $plan->id?>' style="display: none">
                          <td> </td>
                          <td> </td>
                          <td  colspan="3" class="text-ellipsis" title="<?php echo $task->subProjectName;?>" ><span style="margin-left: 50px;"><small style="font-size: xx-small; color: #9d3a3a">(<?php echo $lang->outsideplan->subProjectName;?>)</small><?php echo $task->subProjectName;?></span></td>

                              <?php
                            $subBegin = '';
                            $subEnd   = '';
                            $taskUnitNames   = [];
                            $taskBearDepsNames   = [];
                            foreach($task->tasks as $onetask){
                                if($subBegin == '' || $onetask->subTaskBegin < $subBegin) { $subBegin = $onetask->subTaskBegin; }
                                if($subEnd == '' || $onetask->subTaskEnd > $subEnd) { $subEnd = $onetask->subTaskEnd; }

                                if(isset($onetask->subTaskUnit)) {
                                    $taskunits = array_unique(explode(',', $onetask->subTaskUnit));
                                    foreach ($taskunits as $taskunit){
                                        if(empty($taskunit)) continue;
                                        $taskUnitNames[] = zget($lang->outsideplan->subProjectUnitList, $taskunit);
                                    }
                                }
                                if(isset($onetask->subTaskBearDept)) {
                                    $taskBearDeps = array_unique(explode(',', $onetask->subTaskBearDept));
                                    foreach ($taskBearDeps as $taskBearDep){
                                        if(empty($taskBearDep)) continue;
                                        $taskBearDepsNames[] = zget($lang->application->teamList, $taskBearDep);
                                    }
                                }
                            }
                          ?>
                          <td title="<?php echo implode(',',array_unique($taskBearDepsNames));?>"><?php echo implode(',',array_unique($taskBearDepsNames));?></td>
                          <td title="<?php echo implode(',',array_unique($taskUnitNames));?>"><?php  echo implode(',',array_unique($taskUnitNames));  ?></td>
                          <td ><?php echo $subBegin;?></td>
                          <td ><?php echo $subEnd;?></td>

                          <td colspan="6"></td>
                          <td class='c-actions' colspan="2">
                              <?php
                              common::printIcon('outsideplan', 'createTask', "subProjectId=$task->id&planID=$plan->id", $plan, 'list', 'split', '');
                              common::printIcon('outsideplan', 'copySub', "subProjectId=$task->id&planID=$plan->id", $plan, 'list', 'copy', '','iframe',true);
//                              common::printIcon('outsideplan', 'moveSub', "subProjectId=$task->id&planID=$plan->id", $plan, 'list', 'move', '','iframe',true);
                              common::printIcon('outsideplan', 'deleteSub', "subProjectId=$task->id&planID=$plan->id", $plan, 'list', 'trash', 'hiddenwin');
                              ?>
                          </td>
                      </tr>
                      <?php foreach($task->tasks as $onetask):?>

                          <tr class='subpro<?php echo $plan->id?>' style="display: none">
                              <td> </td>
                              <td> </td>
                              <td  colspan="3" class="text-ellipsis" title="<?php echo $onetask->subTaskName;?>" ><span style="margin-left: 100px;"><small style="font-size: xx-small; color: #9d3a3a">(<?php echo $lang->outsideplan->subTaskName;?>)</small><?php echo $onetask->subTaskName;?></span></td>
                              <td title="<?php
                              $taskUnitNames = [];
                              if(isset($onetask->subTaskBearDept)) {
                                  $taskBearDeps = array_unique(explode(',', $onetask->subTaskBearDept));
                                  foreach ($taskBearDeps as $taskBearDep){
                                      if(empty($taskBearDep)) continue;
                                      $taskUnitNames[] = zget($lang->application->teamList, $taskBearDep);
                                  }
                              }
                              ?>"><?php echo implode(',',$taskUnitNames); ?></td>
                              <td title="<?php
                              $taskUnitNames = [];
                              if(isset($onetask->subTaskUnit)) {
                                  $taskunits = array_unique(explode(',', $onetask->subTaskUnit));
                                  foreach ($taskunits as $taskunit){
                                      if(empty($taskunit)) continue;
                                      $taskUnitNames[] = zget($lang->outsideplan->subProjectUnitList, $taskunit);
                                  }
                              }
                              ?>"><?php echo implode(',',$taskUnitNames); ?></td>
                              <td><?php echo $onetask->subTaskBegin;?></td>
                              <td colspan="7"><?php echo $onetask->subTaskEnd;?></td>
                              <td class='c-actions' colspan="2">
                                  <?php
                                  common::printIcon('outsideplan', 'editTask', "subProjectId=$onetask->id&planID=$plan->id", $plan, 'list', 'edit', '');
                                  common::printIcon('outsideplan', 'copyTask', "subProjectId=$onetask->id&planID=$plan->id", $plan, 'list', 'copy', '','iframe',true);
                                  common::printIcon('outsideplan', 'bindprojectplan', "TaskID=$onetask->id&planID=$plan->id", $plan, 'list', 'treemap', '','iframe',true);
//                                  common::printIcon('outsideplan', 'moveTask', "subProjectId=$onetask->id&planID=$plan->id", $plan, 'list', 'move', '','iframe',true);

                                  common::printIcon('outsideplan', 'deleteTask', "subProjectId=$onetask->id&planID=$plan->id", $plan, 'list', 'trash', 'hiddenwin');
                                  ?>
                              </td>
                          </tr>

                      <?php endforeach;?>
                  <?php endforeach;?>
              <?php endif;?>
          <?php endforeach;?>
          </tbody>
      </table>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
