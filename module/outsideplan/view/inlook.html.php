<?php include '../../common/view/header.html.php';?>
<style>
    #outlookTable { cursor: pointer;}
    #outlookTable td{ width: 100% !important; white-space: normal !important; word-wrap: break-word !important; word-break: break-all !important;}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        if(common::hasPriv('outsideplan', 'chart')) echo html::a($this->createLink('outsideplan', 'chart', ""), '<span class="text">统计信息</span>', '', "class='btn btn-link '");
        //    echo html::a($this->createLink('outsideplan', 'outlook', ""), '<span class="text">'.$listType.'</span>', '', "class='btn btn-link active'");
        ?>

        <div class="btn-group"><a href="javascript:;" data-toggle="dropdown" class="btn btn-link active" style="border-radius: 4px;">一览表【内部视角】<span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="outsideplan-outlook.html" class="btn btn-link "><span class="text">一览表【外部视角】</span></a></li>
                <li><a href="outsideplan-inlook.html" class="btn btn-link "><span class="text">一览表【内部视角】</span></a></li>

            </ul></div>

        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <?php if(common::hasPriv('outsideplan', 'exportInlook')) { ?>
        <div class="btn-toolbar pull-right">
            <div class='btn-group'>
                <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
                <ul class="dropdown-menu" id='exportActionMenu'>
                    <?php
                    $class = common::hasPriv('outsideplan', 'exportInlook') ? '' : "class=disabled";
                    $misc  = common::hasPriv('outsideplan', 'exportInlook') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                    $link  = common::hasPriv('outsideplan', 'exportInlook') ? $this->createLink('outsideplan', 'exportInlook', "browseType=$browseType") : '#';
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
            <div style="overflow-x: scroll;height: calc(100vh - 105px);">
                <table id='outlookTable' class="table table-datatable table-bordered table-condensed table-striped table-fixed table-hover  " >
                    <thead style="position:sticky;top:0px;background-color: #DDDDDD !important;">
                    <tr class=" text-center">
                        <th class='w-200px'><?php echo $lang->projectplan->name;?></th>
                        <th class='w-140px'><?php echo $lang->projectplan->code;?></th>
                        <th class='w-140px'><?php echo $lang->projectplan->planCode;?></th>
                        <th class='w-140px'><?php echo $lang->projectplan->mark;?></th>
                        <th class='w-140px'>项目承担部门</th>
                        <th class='w-100px'>内部项目计划<br>开始时间</th>
                        <th class='w-100px'>内部项目计划<br>完成时间</th>
                        <th class='w-160px'>内部项目<br>计划状态</th>
                        <th class='w-120px'>内部项目状态</th>
                        <th class='w-100px'>项目计划<br>工作量（人/月）</th>
                        <th class='w-100px'>项目计划<br>工作量（小时）</th>
                        <th class='w-100px'>工作量<br>（小时）</th>
                        <th class='w-100px'>项目预算收入</th>
                        <th class='w-100px'>进度</th>
                        <th class='w-500px th-blue'><?php echo $lang->outsideplan->name;?></th>
                        <th class='w-150px th-blue'>业务司局</th>
                        <th class='w-150px th-blue'>承建单位</th>
<!--                        <th class='w-180px'>(外部)子项/子任务</th>-->
<!--                        <th class='w-100px'>(外部)项目/任务名称</th>-->
                        <th class='w-140px th-blue'>(外部)项目/任务编码</th>
                        <th class='w-100px th-blue'>(外部)项目/任务<br>开始时间</th>
                        <th class='w-100px th-blue'>(外部)项目/任务<br>完成时间</th>
                        <th class='w-100px th-blue'>(外部)项目/任务状态</th>
                        <th class='w-100px th-blue'>(外部)子项/子任务<br>计划开始时间</th>
                        <th class='w-100px th-blue'>(外部)子项/子任务<br>计划结束时间</th>

                    </tr>
                    </thead>
                    <tbody style="background-color: #FFFFFF !important;">
                    <?php foreach($plans as $plan) { ?>
                    <tr>
                        <td rowspan="<?php echo $plan->row; ?>" title="<?php echo $plan->name;?>"><?php echo  $plan->name;?></td>
                        <td rowspan="<?php echo $plan->row; ?>" title="<?php echo $plan->code;?>"><?php echo  $plan->code;?></td>
                        <td rowspan="<?php echo $plan->row; ?>" title="<?php echo $plan->planCode;?>"><?php echo  $plan->planCode;?></td>
                        <td rowspan="<?php echo $plan->row; ?>" title="<?php echo $plan->mark;?>"><?php echo  $plan->mark;?></td>
                        <?php
                        $bearDeptStr = '';
                        if(stripos($plan->bearDept,',') !== false){
                            $bearDeptArr = explode(',',$plan->bearDept);
                            foreach ($bearDeptArr as $dept){
                                $bearDeptStr .= zget($depts,$dept)."<br/>";
                            }
                        }else{
                            $bearDeptStr = zget($depts,$plan->bearDept);
                        }

                        ?>
                        <td rowspan="<?php echo $plan->row; ?>" title="<?php echo $bearDeptStr;?>"><?php echo  $bearDeptStr;?></td>
                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->begin;?></td>
                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->end;?></td>
                        <?php
                        $projectplanstatusstr = '';
                        if($plan->status==$lang->projectplan->statusEnglishList['yearpass'] && $plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['pending']){
                            $projectplanstatusstr = $lang->projectplan->changeing;
                        }else{
                            $projectplanstatusstr = zget($lang->projectplan->statusList, $plan->status, '');
                            if($plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['pass']){
                                $projectplanstatusstr .= $lang->projectplan->changePass;
                            }else if($plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['reject']){
                                $projectplanstatusstr .= $lang->projectplan->changeReject;
                            }
                        }


                        ?>
                        <td rowspan="<?php echo $plan->row;?>" class='text-ellipsis' title="<?php echo $projectplanstatusstr;?>">
                            <?php echo $projectplanstatusstr;?>
                        </td>
<!--                        <td rowspan="--><?php //echo $plan->row; ?><!--">--><?php //echo  zget($lang->projectplan->statusList, $plan->status,'');?><!--</td>-->

                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  zget($lang->projectplan->insideStatusList, $plan->insideStatus,'');?></td>
                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->workload;?></td>
                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->estimate;?></td>
                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->consumed;?></td>
                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->budget;?></td>
                        <td rowspan="<?php echo $plan->row; ?>"><?php echo  $plan->progress;?></td>

                        <?php
                        $taskNum = 0;
                        foreach ($plan->outTasks as $task) {
                            if($taskNum != 0) { echo "<tr>";}
                            echo "<td>{$task->outsideProjectPlanName}/{$task->subProjectName}/{$task->subTaskName} </td>
                            <td>$task->subTaskUnit </td>
                            <td>$task->subTaskBearDept </td>  
                            <td>$task->outsideProjectPlanCode </td>
                            <td>$task->outsideProjectPlanBegin </td>
                            <td>$task->outsideProjectPlanEnd </td>
                            <td>$task->outsideProjectPlanStatus </td>
                            <td>$task->subTaskBegin </td>
                            <td>$task->subTaskEnd </td>
                            
                            </tr>";
                            $taskNum ++;
                        }
                        if(empty($plan->outTasks)) {
                            echo "<td> </td>
                            <td> </td>
                            <td> </td> 
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            </tr>";
                        }
                        ?>

                        <?php } //内部计划 ?>
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
