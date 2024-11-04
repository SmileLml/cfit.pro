<?php include '../../common/view/header.html.php';?>
<style>
   #outlookTable { cursor: pointer;}
   #outlookTable td{ width: 100% !important; white-space: normal !important; word-wrap: break-word !important; word-break: break-all !important;}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    if(common::hasPriv('outsideplan', 'chart')) echo html::a($this->createLink('outsideplan', 'chart', ""), '<span class="text">统计信息</span>', '', "class='btn btn-link '");
//    echo html::a($this->createLink('outsideplan', 'outlook', ""), '<span class="text">一览表</span>', '', "class='btn btn-link active'");
    ?>

      <div class="btn-group"><a href="javascript:;" data-toggle="dropdown" class="btn btn-link active" style="border-radius: 4px;">一览表【外部视角】<span class="caret"></span></a>
          <ul class="dropdown-menu">
              <li><a href="outsideplan-outlook.html" class="btn btn-link "><span class="text">一览表【外部视角】</span></a></li>
              <li><a href="outsideplan-inlook.html" class="btn btn-link "><span class="text">一览表【内部视角】</span></a></li>

          </ul></div>

    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
    <?php if(common::hasPriv('outsideplan', 'exportOutlook')) { ?>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
        <?php
        $class = common::hasPriv('outsideplan', 'exportOutlook') ? '' : "class=disabled";
        $misc  = common::hasPriv('outsideplan', 'exportOutlook') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
        $link  = common::hasPriv('outsideplan', 'exportOutlook') ? $this->createLink('outsideplan', 'exportOutlook', "browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->outsideplan->export, '', $misc) . "</li>";
        ?>  
      </ul>
 </div>
 </div>
    <?php } ?>
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
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
    <div style="overflow-x: scroll;height: calc(100vh - 165px);">
<!--        transform: scaleY(-1);-->
        <table id='outlookTable' class="table table-datatable table-bordered table-condensed table-striped table-fixed table-hover  " >
            <thead style="position:sticky;top:0px;background-color: #DDDDDD !important;">
            <tr class=" text-center" >
                        <th class='w-200px th-blue'><?php echo $lang->outsideplan->name;?></th>
                        <th class='w-100px th-blue'><?php echo $lang->outsideplan->code;?></th>
                        <th class='w-100px th-blue'>(外部)项目/任务计划<br>开始日期</th>
                        <th class='w-100px th-blue'>(外部)项目/任务计划<br>完成日期</th>
                        <th class='w-80px th-blue'> (外部)项目/任务<br>计划状态</th>
                        <th class='w-180px th-blue'>(外部)子项/子任务</th>
                        <th class='w-180px th-blue'>(外部)子项/子任务名称</th>
                        <th class='w-200px th-blue'>业务司局</th>
                        <th class='w-200px th-blue'>承建单位</th>
                        <th class='w-200px'>内部项目名称</th>
                        <th class='w-120px'>项目计划编号</th>
                        <th class='w-60px'>是否<br>重点项目</th>
                        <th class='w-100px'>内部计划<br>开始时间</th>
                        <th class='w-100px'>内部计划<br>完成时间</th>
                        <th class='w-90px'>内部计划<br>工作量(人/月)</th>
                        <th class='w-200px'>承建部门</th>
                        <th class='w-100px'>项目负责人</th>
                        <th class='w-180px'>项目负责人联系方式</th>
                        <th class='w-150px'>内部项目状态</th>
                        <th class='w-100px'>项目代号</th>
                        <th class='w-100px'>项目编号</th>
                        <th class='w-100px'>参与人员</th>
                        <th class='w-200px'>人员所在部门</th>
                        <th class='w-100px'>项目计划<br>工作量（小时）</th>
                        <th class='w-100px'>工作量<br>（小时）</th>
                        <th class='w-100px'>项目预算<br>收入</th>
                        <th class='w-100px'>进度</th>
            </tr>
            </thead>
            <tbody style="background-color: #FFFFFF !important;">
            <?php foreach($plans as $plan) { ?>
              <tr>
                <td rowspan="<?php echo $plan->row; ?>" title ="<?php echo  $plan->name;?>"><?php echo  $plan->name;?></td>
                <td rowspan="<?php echo $plan->row; ?>" title="<?php echo  $plan->code;?>"><?php echo  $plan->code;?></td>
                <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->begin;?></td>
                <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->end;?></td>
                <td rowspan="<?php echo $plan->row; ?>"><?php echo  zget($lang->outsideplan->statusList, $plan->status,'');?></td>
                <?php
                $subProjectNum = 0;
                foreach($plan->children as $subProject) { //子项目
                    if($subProjectNum > 0) "echo <tr>";//除了第一行 其他换行
                    $subProjectNum ++;
                    ?>
                <td rowspan="<?php echo $subProject->row; ?>" title="<?php echo $subProject->subProjectName;?>"><?php echo $subProject->subProjectName;?></td>
                    <?php
                    $taskNum = 0;
                    if(empty($subProject->tasks[0])){ // 没有任务
                        ?>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        </tr>
                  <?php continue; } //如果有任务
                    $taskNum = 0;
                    foreach($subProject->tasks as $task) {
                        if($taskNum > 0) "echo <tr>";//除了第一行 其他换行
                        $taskNum ++;
                        ?>
                        <td rowspan="<?php echo $task->row; ?>" title ="<?php echo $task->subTaskName;?>"> <?php echo $task->subTaskName;?> </td>

                        <td rowspan="<?php echo $task->row; ?>" title="<?php
                        $vlist = explode(',', $task->subTaskUnit);
                        $arr = [];
                        foreach ($vlist as $itemv){
                            if(empty($itemv)) continue;
                            $arr[] = zget($lang->outsideplan->subProjectUnitList, $itemv,'');
                        }
                        echo implode(',', $arr);

                        ?>"> <?php echo implode(',', $arr); ?> </td>
                        <td rowspan="<?php echo $task->row; ?>" title="<?php

                        $vlist = explode(',', $task->subTaskBearDept);
                        $arr = [];
                        foreach ($vlist as $itemv){
                            if(empty($itemv)) continue;
                            $arr[] = zget($lang->application->teamList, $itemv,'') ;
                        }
                        echo implode(',', $arr);

                        ?>"> <?php  echo implode(',', $arr); ?> </td>
                         <?php
                        if(empty($task->project[0])){ // 任务没有内部项目 ?>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            </tr>
                        <?php continue; } //如果有内部项目

                        foreach($task->project as $projects) {
                            $projectNum = 0;
                            foreach ($projects as $project){
                            if($projectNum > 0) "echo <tr>";//除了第一行 其他换行
                            $projectNum ++;
                            ?>
                             <td title="<?php echo $project->name;?>"> <?php echo $project->name; ?> </td>
                                <td><?php echo $project->planCode; ?> </td>
                             <td> <?php echo zget($lang->projectplan->isImportantList, $project->isImportant,'') ; ?> </td>
                             <td> <?php echo $project->begin; ?> </td>
                             <td> <?php echo $project->end; ?> </td>
                             <td> <?php echo $project->workload; ?> </td>
                             <td> <?php
                                 $bearDepts = isset($project->bearDept) ? explode(',', $project->bearDept) : [];
                                 foreach ($bearDepts as $dept) {
                                     echo zget($depts, $dept, '') . '<br>';
                                 }
                                 ?>
                             </td>
                              <?php
                                $ownerNames = '';
                                 $owners = isset($project->owner) ? explode(',', $project->owner) : [];
                                 foreach ($owners as $owner)
                                 { $ownerNames .= zget($users, $owner, ''). ' ';};
                                 ?>
                             <td title=" <?php echo $ownerNames; ?>"> <?php echo $ownerNames; ?></td>
                             <td> <?php echo $project->phone; ?> </td>
<!--                                财务新增-->
                             <td> <?php echo zget($lang->projectplan->insideStatusList, $project->insideStatus,''); ?> </td>
                                <td><?php echo $project->projectInfo->code; ?> </td>
                                <td><?php echo $project->code;?> </td>
                                <td><?php echo $project->projectInfo->members; ?></td>
                                <td><?php echo $project->projectInfo->deptNames; ?> </td>
                                <td><?php echo $project->projectInfo->estimate; ?> </td>
                                <td><?php echo $project->projectInfo->consumed; ?> </td>
                                <td><?php echo $project->projectInfo->budget; ?> </td>
                                <td><?php echo $project->projectInfo->progress; ?> </td>

                            </tr>
                        <?php }
                            }   //内部项目 ?>
                    <?php } //任务 ?>
                <?php } //(外部)子项/子任务 ?>
            <?php } //外部计划 ?>
            </tbody>
        </table>
    </div>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
