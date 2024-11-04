<?php
/**
 * Created by Yanqi Tong
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php if(isset($report->id)):?>
    <div  class='main-content' style="height: 52px; padding-top: 9px; margin-top: -9px">
        <table class='table table-form'>
            <tr>
                <td style="width: 25%; overflow: hidden"><span style="margin-left: 27px; font-weight: bold"><?php echo $lang->weeklyreport->reportDate;?></span>： <?php echo $report->reportStartDate;?>~<?php echo $report->reportEndDate;?>
                </td><td style="width: 15%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreport->createUser;?></span>： <?php echo zget($users, $report->createdBy, '');?>
                </td><td style="width: 25%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreport->createTime;?></span>： <?php echo $report->createTime; ?>
                </td><td style="width: 15%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreport->updateUser;?></span>： <?php echo zget($users, $report->editedBy, ''); ?>
                </td><td style="width: 20%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreport->updateTime;?></span>： <?php echo $report->updateTime; ?>
                </td>
            </tr>
        </table>

    </div>

    <div id="mainContent" class="main-row">

        <div class="main-col col-8">

            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->projectReportDetails;?></h4></div>
            <div class="cell">
                <table class='table table-form'>
                    <tr>
                        <td><span style="margin-left: 27px; font-weight: bold"><?php echo $lang->weeklyreport->overallProgress;?></span>： <?php echo $report->projectProgress;?>%</td>

                        <td ><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreport->projectStage;?></span>： <?php  echo $lang->weeklyreport->projectState[$report->projectStage];?></td>

                        <td><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreport->progressStatus;?></span>： <?php
                            echo $report->progressStatus;
                            ?></td>


                    </tr>

                </table>
            </div>
            <div class="cell">

                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->mileDelayNum;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo $report->mileDelayNum;?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->mileDelayMark;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo $report->mileDelayMark;?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->projectProgressMark;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo $report->projectProgressMark;?>
                    </div>
                </div>

                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->productBuilds;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo nl2br($report->productBuilds);?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->projectTransDesc;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo nl2br($report->projectTransDesc);?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->projectAbnormalDesc;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo nl2br($report->projectAbnormalDesc);?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->nextWeekplan;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo nl2br($report->nextWeekplan);?>
                    </div>
                </div>

                <div class="detail">
                    <div class="detail-title"><?php echo $lang->weeklyreport->remark;?></div>
                    <div class="detail-content article-content" style="margin-left: 20px">
                        <?php echo nl2br($report->remark);?>
                    </div>
                </div>
            </div>
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->projectMediumSituation;?></h4></div>
            <div class="cell">

                    <?php foreach ($report->reportMedium as $reportMedium){
?>
                <table class='table table-form' >
                        <tr>
                            <th class="w-150px">
                                <?php echo $lang->weeklyreport->mediumName;?>：
                            </th>
                            <td colspan="3">
                                <?php echo $reportMedium->mediumName;?>
                            </td>
                            <th class="w-150px">
                                <?php echo $lang->weeklyreport->mediumOutsideplanTask;?>：
                            </th>
                            <td colspan="3">
                                <?php echo $mediumOutTask[$reportMedium->mediumOutsideplanTask];?>
                            </td>

                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreport->preMediumPublishDate;?>：</th>
                            <td><?php echo $reportMedium->preMediumPublishDate;?></td>
                            <th><?php echo $lang->weeklyreport->preMediumOnlineDate;?>：</th>
                            <td><?php echo $reportMedium->preMediumOnlineDate;?></td>
                            <th><?php echo $lang->weeklyreport->realMediumPublishDate;?>：</th>
                            <td><?php echo $reportMedium->realMediumPublishDate;?></td>
                            <th><?php echo $lang->weeklyreport->realMediumOnlineDate;?>：</th>
                            <td><?php echo $reportMedium->realMediumOnlineDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreport->mediumRequirement;?>：</th>
                            <td colspan="7"><?php

                                $showMediumRequirementArr = explode(',',$reportMedium->mediumRequirement);
                               foreach ($showMediumRequirementArr as $mediumReuirement){
                                   if(!$mediumReuirement){
                                       continue;
                                   }
                                   echo zget($productRequirement,$mediumReuirement).'<br />';
                               }


                                ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreport->mediumMark; ?>：</th>
                            <td colspan="7">
                                <?php echo $reportMedium->mediumMark;?>
                            </td>
                        </tr>
                </table>
                        <hr />
<?php
                    }?>



            </div>
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->externalMilestones;?></h4></div>
            <div class="cell">

                    <?php foreach ($report->reportOutmile as $reportOutmile){ ?>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th class="w-120px"><?php echo $lang->weeklyreport->outMileStageName;?>：</th>
                        <td><?php echo $reportOutmile->outMileStageName;?></td>
                        <th><?php echo $lang->weeklyreport->outMileName;?>：</th>
                        <td><?php echo $reportOutmile->outMileName;?></td>
                        <th class="w-120px"><?php echo $lang->weeklyreport->outMilePreDate;?>：</th>
                        <td><?php echo $reportOutmile->outMilePreDate;?></td>
                        <th class="w-120px"><?php echo $lang->weeklyreport->outMileRealDate;?>：</th>
                        <td><?php echo $reportOutmile->outMileRealDate;?></td>


                    </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreport->outMileMark;?>：</th>
                            <td colspan="7"><?php echo $reportOutmile->outMileMark;?></td>
                        </tr>
                    </tbody>
                </table>
                        <hr />
                    <?php } ?>


            </div>

            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->internalMilestones;?></h4></div>

            <div class="cell">

                    <?php foreach ($report->reportInsidemile as $reportInsidemile){ ?>
                <table class="table table-form">
                    <tbody>
                        <tr>
                            <th class="w-130px"><?php echo $lang->weeklyreport->insideMileStage;?>：</th>
                            <td><?php echo $reportInsidemile->insideMileStage;?></td>
                            <th><?php echo $lang->weeklyreport->outMileName;?>：</th>
                            <td><?php echo $reportInsidemile->insideMileName;?></td>
                            <th class="w-130px"><?php echo $lang->weeklyreport->outMilePreDate;?>：</th>
                            <td><?php echo $reportInsidemile->insideMilePreDate;?></td>
                            <th class="w-130px"><?php echo $lang->weeklyreport->outMileRealDate;?>：</th>
                            <td><?php echo $reportInsidemile->insideMileRealDate;?></td>


                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreport->outMileMark;?>：</th>
                            <td colspan="7"><?php echo $reportInsidemile->insideMileMark;?></td>
                        </tr>
                    </tbody>
                </table>
                        <hr />
                    <?php } ?>


            </div>

            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->projectRiskSituation;?></h4></div>

            <div class="cell">
                <table class="table ">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->weeklyreport->riskName;?></th>
                        <th><?php echo $lang->weeklyreport->riskStatus;?></th>
                        <th><?php echo $lang->weeklyreport->riskResolution;?></th>

                    </tr>
                    <?php foreach ($report->reportRisk as $reportRisk){ ?>

                        <tr>

                            <td><?php echo $reportRisk->reportRiskMark;?></td>

                            <td><?php echo $lang->risk->statusList[$reportRisk->reportRiskStatus];?></td>
                            <td >
                                <?php echo $lang->weeklyreport->riskCopingStrategies;?>：<?php echo $this->lang->risk->strategyList[$reportRisk->reportRiskStrategy];?><br />
                                <?php echo $lang->weeklyreport->riskPreventiveMeasure;?>：<?php echo $reportRisk->reportRiskPrevention;?><br />
                                <?php echo $lang->weeklyreport->riskEmergencyMeasure;?>：<?php echo $reportRisk->reportRiskRemedy;?><br />
                                <?php echo $lang->weeklyreport->riskSolutionMeasures;?>：<?php echo $reportRisk->reportRiskResolution;?>
                            </td>

                        </tr>


                    <?php } ?>

                    </tbody>
                </table>
            </div>
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->relationOutProjectReport;?></h4></div>
            <div class="cell">


                    <?php foreach ($outreportList as $outreport){ ?>
                <table class="table table-form">
                    <tbody>
                        <tr>
                            <th class="w-150px"><?php echo $lang->weeklyreport->outProjectName;?>：</th>
                            <td><?php echo $outreport->outsideProjectName; ?></td>
                            <th class="w-150px"><?php echo $lang->weeklyreport->outProjectReport;?>：</th>
                            <td><a class="text-primary" href="<?php echo helper::createLink('weeklyreportout','view','outreportID='.$outreport->id,'html#app=platform') ?>">点击查看外部周报</a></td>


                        </tr>
                        <tr>
                            <th ><?php echo $lang->weeklyreportout->outSyncStatus;?>：</th>
                            <td><?php echo $lang->weeklyreportout->outSyncStatusList[$outreport->outSyncStatus]; ?></td>
                            <th><?php echo $lang->weeklyreportout->outSyncDesc;?>：</th>
                            <td><?php echo nl2br($outreport->outSyncDesc); ?></td>


                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreportout->outFeedbackTime;?>：</th>
                            <td ><?php echo $outreport->outFeedbackTime; ?></td>
                            <th><?php echo $lang->weeklyreportout->outFeedbackUser;?>：</th>
                            <td ><?php echo $outreport->outFeedbackUser; ?></td>

                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreportout->outFeedbackView;?>：</th>
                            <td colspan="3"><?php echo nl2br($outreport->outFeedbackView); ?></td>

                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreportout->outFeedbackMark;?>：</th>
                            <td colspan="3"><?php echo nl2br($outreport->outFeedbackMark); ?></td>

                        </tr>
                    </tbody>
                </table>
                        <hr />
                    <?php } ?>


            </div>
            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
            <?php
            if($refershow){
                ?>
                <div class='main-actions'>
                    <div class="btn-toolbar">

                        <?php echo $referhtml; ?>
                        <div class='divider'></div>

                    </div>
                </div>

                <?php
            }
            ?>

        </div>
        <div class="side-col col-4">
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->innerProjectBaseInfo;?></h4></div>
            <div class="cell">
                <div class="detail">
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-140px'><?php echo $lang->weeklyreport->devDept;?></th>
                                <td><?php
                                    $tempdeptDept = explode(',',$report->devDept);
                                    foreach ($tempdeptDept as $deptid){
                                        if($deptid){
                                            echo zget($depts, $deptid, '');
                                        }

                                    }

                                    ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->pm;?></th>
                                <td><?php echo zget($users, $report->pm, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->projectType;?></th>
                                <td><?php echo zget($lang->projectplan->typeList, $report->projectType, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->projectCode;?></th>
                                <td><?php echo $report->projectCode; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->projectName;?></th>
                                <td><a href="<?php echo helper::createLink('projectplan','view','planID='.$projectPlan->id,'html') ?>"><?php echo $report->projectName; ?></a></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->projectAlias;?></th>
                                <td><?php echo $report->projectAlias; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->projectStartDate;?></th>
                                <td><?php echo $report->projectStartDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->projectEndDate;?></th>
                                <td><?php echo $report->projectEndDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->isImportant;?></th>
                                <td><?php echo zget($lang->projectplan->isImportantList, $report->isImportant, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->projectplanYear;?></th>
                                <td><?php echo $report->projectplanYear;?></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->relationOutProject;?></h4></div>
            <div class="cell">
                <div class="detail">
                    <div class='detail-content'>
                        <?php
                        foreach ($outsidePlan as $outplan){
                            ?>

                            <table class='table table-data'>
                                <tbody>

                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->outProjectCode;?></th>
                                    <td><?php echo $outplan->code; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->outProjectName;?></th>
                                    <td><a href="<?php echo helper::createLink('outsideplan','view','planID='.$outplan->id,'html') ?>"><?php echo $outplan->name; ?></a></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->outProjectStatus;?></th>
                                    <td><?php echo zget($lang->outsideplan->statusList,$outplan->status,''); ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->outProjectTask;?></th>
                                    <td>
                                        <?php
                                        foreach ($outplan->subprojectsTask as $subtask){
                                            ?>
                                            <?php echo $subtask->subTaskName;?><br />

                                        <?php } ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <hr />



                            <?php


                        }
                        ?>

                    </div>
                </div>
            </div>
            <!--<div class="page-title" style="text-align: center"><h4>（外部）项目基本信息</h4></div>
            <div class="cell">
                <div class="detail">
                    <div class='detail-content'>
                        <?php /*$arrLen = count($outsidePlan); $i = 0;
                        foreach ($outsidePlan as $plan) {*/?>
                        <table class='table table-data'>
                            <tbody>

                            <tr>
                                <th class='w-140px'>（外部）项目类型</th>
                                <td>(暂不支持)</td>
                            </tr>
                            <tr>
                                <th>（外部）项目编号</th>
                                <td><?php /*echo $plan['code']; */?></td>
                            </tr>
                            <tr>
                                <th>（外部）项目名称</th>
                                <td><?php /*echo $plan['name']; */?></td>
                            </tr>
                            <tr>
                                <th>（外部）子项目名称</th>
                                <td>(暂不支持)</td>
                            </tr>
                            <tr>
                                <th>业务司局</th>
                                <td>(暂不支持)</td>
                            </tr>
                            <tr>
                                <th>（外部）需求方</th>
                                <td>(暂不支持)</td>
                            </tr>
                            <tr>
                                <th>（外部）承建单位</th>
                                <td>(暂不支持)</td>
                            </tr>
                            <tr>
                                <th>（外部）计划开始时间</th>
                                <td><?php /*echo $plan['begin']; */?></td>
                            </tr>
                            <tr>
                                <th>（外部）计划结束时间</th>
                                <td><?php /*echo $plan['end']; */?></td>
                            </tr>

                            <tr>
                                <th>（外部）计划工作量<br>(人月)</th>
                                <td><?php /*echo $plan['workload']; */?></td>
                            </tr>
                            <tr>
                                <th>（外部）项目里程碑</th>
                                <td><?php /*echo html_entity_decode($report->outsideMilestone); */?></td>
                            </tr>

                            <tr>
                                <th>（外部）变化情况</th>
                                <td>(暂不支持)</td>
                            </tr>
                            </tbody>
                        </table>
                        <?php
/*                            $i++;
                            if($i < $arrLen) echo  "<hr>";//分割线
                        }*/?>

                    </div>
                </div>
            </div>-->
            <!--<div class="page-title" style="text-align: center"><h4>项目需求</h4></div>
            <div class="cell">
                <div class="detail" style="padding-top: 0px;">
                    <div class='detail-content'  style="padding-top: 0px;">
                        <?php /*$i = 0;
                        foreach ($demands as $demand) {
                            $i++; */?>
                            <span style="padding-top: 10px;">
                          <a style="color: #0b5ad3" href="<?php /*echo getWebRoot();*/?>demand-view-<?php /*echo $demand->id; */?>html"><?php /*echo "[$i]".$demand->title;*/?></a>
                        </span><br>
                        <?php /*}*/?>
                    </div>
                </div>
            </div>-->

           <!-- <div class="page-title" style="text-align: center"><h4>需求任务</h4></div>
            <div class="cell">
                <div class="detail"  style="padding-top: 0px;">
                    <div class='detail-content'  style="padding-top: 0px;">
                        <?php /*$i = 0;
                        foreach ($requirements as $requirement) {
                            $i++; */?>
                            <span style="padding-top: 10px;">
                          <a style="color: #0b5ad3" href="<?php /*echo getWebRoot();*/?>requirement-view-<?php /*echo $requirement->id; */?>html"><?php /*echo "[$i]".$requirement->name;*/?></a>
                        </span><br>
                        <?php /*}*/?>
                    </div>
                </div>
            </div>-->

        </div>
    </div>
<?php else:?>
    <div class='main-col'>
        <div class="table-empty-tip">
            <p><?php echo $lang->noData;?></p>
        </div>
    </div>
<?php endif;?>
<script>
    function deleteReport(reportID){
        if(confirm('您是否确定删除')){
            var params = {'reportId': reportID};
            var url = createLink('weeklyreport','delete')
            $.post(url, params, function(data){

                alert(data.message);
                // data = $.parseJSON(data);
                if(data.code == 200){
                    location.reload();
                }

            },'json');
        }else {
            return false;
        }
    }
</script>