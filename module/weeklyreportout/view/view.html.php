<?php
/**
 * Created by Yanqi Tong
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php if(isset($outreport->id)):?>
    <div  class='main-content' style="height: 52px; padding-top: 9px; margin-top: -9px">
        <table class='table table-form'>
            <tr>
                <td style="width: 25%; overflow: hidden"><span style="margin-left: 27px; font-weight: bold"><?php echo $lang->weeklyreportout->outreportDate;?></span>： <?php echo $outreport->outreportStartDate;?>~<?php echo $outreport->outreportEndDate;?>
                </td><td style="width: 15%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreportout->createdBy;?></span>： <?php echo zget($users, $outreport->createdBy, '');?>
                </td><td style="width: 25%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreportout->createTime;?></span>： <?php echo $outreport->createTime; ?>
                </td><td style="width: 15%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreportout->editedBy;?></span>： <?php echo zget($users, $outreport->editedBy, ''); ?>
                </td><td style="width: 20%; overflow: hidden"><span style="margin-left: 20px; font-weight: bold"><?php echo $lang->weeklyreportout->updateTime;?></span>： <?php echo $outreport->updateTime; ?>
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
                        <th class="w-150px"><?php echo $lang->weeklyreportout->outOverallProgress;?>：</th>
                        <td style=""><?php echo nl2br($outreport->outOverallProgress);?></td>
                    </tr>
                    <tr>
                        <th ><?php echo $lang->weeklyreportout->outProjectTransferMark;?>：</th>
                        <td ><?php  echo nl2br($outreport->outProjectTransferMark);?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->weeklyreportout->outProjectAbnormal;?>：</th>
                        <td><?php
                            echo nl2br($outreport->outProjectAbnormal);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->weeklyreportout->outNextWeekplan;?>：</th>
                        <td><?php
                            echo nl2br($outreport->outNextWeekplan);
                            ?>
                        </td>
                    </tr>




                </table>
            </div>

            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->projectMediumSituation;?></h4></div>
            <div class="cell">

                    <?php foreach ($outreport->outmediuListInfo as $reportMedium){
?>
                <table class='table table-form'>
                        <tr>
                            <th class="w-150px">
                                <?php echo $lang->weeklyreport->mediumName;?>：
                            </th>
                            <td colspan="3">
                                <?php echo $reportMedium->outMediumName;?>
                            </td>
                            <th class="w-150px">
                                <?php echo $lang->weeklyreportout->outMediumOutsideplanSub;?>：
                            </th>
                            <td colspan="3">
                                <?php echo zget($outPlanTaskList,$reportMedium->outMediumOutsideplanSub);?>
                            </td>

                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreportout->outPreMediumPublishDate;?>：</th>
                            <td><?php echo $reportMedium->outPreMediumPublishDate;?></td>
                            <th><?php echo $lang->weeklyreportout->outPreMediumOnlineDate;?>：</th>
                            <td><?php echo $reportMedium->outPreMediumOnlineDate;?></td>
                            <th><?php echo $lang->weeklyreportout->outRealMediumPublishDate;?>：</th>
                            <td><?php echo $reportMedium->outRealMediumPublishDate;?></td>
                            <th><?php echo $lang->weeklyreportout->outRealMediumOnlineDate;?>：</th>
                            <td><?php echo $reportMedium->outRealMediumOnlineDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->weeklyreportout->outMediumRequirement;?>：</th>
                            <td colspan="7"><?php

                                echo nl2br($reportMedium->outMediumRequirement);

                                ?></td>
                        </tr>
                </table>
                        <hr />
<?php
                    }?>



            </div>
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreport->externalMilestones;?></h4></div>
            <div class="cell">
                <?php foreach ($outreport->outmileListInfo as $reportOutmile){ ?>
                <table class="table table-form">
                    <tbody>

                    <tr>
                        <th class="w-150px"><?php echo $lang->weeklyreportout->outMileStageName;?>：</th>
                        <td><?php echo $reportOutmile->outMileStageName;?></td>
                        <th class="w-150px"><?php echo $lang->weeklyreportout->outMileProductManual;?>：</th>
                        <td><?php echo $reportOutmile->outMileProductManual;?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->weeklyreportout->outMileTechnicalProposal;?>：</th>
                        <td><?php echo $reportOutmile->outMileTechnicalProposal;?></td>
                        <th><?php echo $lang->weeklyreportout->outMileDeploymentPlan;?>：</th>
                        <td><?php echo $reportOutmile->outMileDeploymentPlan;?></td>


                    </tr>
                    <tr>
                        <th><?php echo $lang->weeklyreportout->outMileUATTest;?>：</th>
                        <td><?php echo $reportOutmile->outMileUATTest;?></td>
                        <th><?php echo $lang->weeklyreportout->outMileProductReg;?>：</th>
                        <td><?php echo $reportOutmile->outMileProductReg;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->weeklyreportout->outMileAutoScript;?>：</th>
                        <td><?php echo $reportOutmile->outMileAutoScript;?></td>
                        <td></td>
                        <td></td>
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
                        <th><?php echo $lang->weeklyreportout->ownerProject;?></th>
                        <th><?php echo $lang->weeklyreportout->riskDescribe;?></th>
                        <th><?php echo $lang->weeklyreportout->stateOfRisk;?></th>
                        <th><?php echo $lang->weeklyreportout->riskResponseMeasure;?></th>

                    </tr>
                    <?php foreach ($outreport->outriskListInfo as $reportRisk){ ?>

                        <tr>

                            <td><?php echo $reportRisk->projectName;?></td>
                            <td><?php echo $reportRisk->riskDescribe;?></td>

                            <td><?php echo $reportRisk->stateOfRisk;?></td>
                            <td >
                                <?php echo nl2br($reportRisk->riskResponseMeasure);?>

                            </td>

                        </tr>


                    <?php } ?>

                    </tbody>
                </table>
            </div>
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreportout->outFeedback;?></h4></div>
            <div class="cell">
                <table class="table table-form">
                    <tbody>

                    <tr>
                        <th class="w-150px"><?php echo $lang->weeklyreportout->outSyncStatus;?>：</th>
                        <td><?php echo $lang->weeklyreportout->outSyncStatusList[$outreport->outSyncStatus]; ?></td>
                        <th class="w-150px"><?php echo $lang->weeklyreportout->outSyncDesc;?>：</th>
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
            </div>
            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php $browseLink = $app->session->weeklyreportoutList != false ? $app->session->weeklyreportoutList : inlink('browse'); ?>
                    <?php common::printBack($browseLink); ?>
                    <div class='divider'></div>

                </div>
            </div>
        </div>
        <div class="side-col col-4">
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreportout->baseInfo;?></h4></div>
            <div class="cell">
                <div class="detail">
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-140px'><?php echo $lang->weeklyreportout->cbpcode;?></th>
                                <td><?php echo $outreport->outsideProjectCode; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreportout->cbpname;?></th>
                                <td><a href="<?php echo helper::createLink('outsideplan','view','planID='.$outreport->outProjectID); ?>"><?php echo $outreport->outsideProjectName; ?></a></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreportout->outprojectStatus;?></th>
                                <td><?php echo zget($lang->weeklyreport->outProjectStatusList,$outreport->outprojectStatus); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreportout->outOutsideplanSub;?></th>
                                <td>
                                    <?php
                                        foreach ($outsidetaskList as $task){
                                            echo $task->subTaskName."<br/>";
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->preWeekOutreport;?></th>
                                <td><?php

                                    if($preOutreprot){

                                        ?>
                                        <a href="<?php echo helper::createLink('weeklyreportout','view','outreportID='.$preOutreprot->id,'html#app=platform') ?>"><?php echo $preOutreprot->outreportStartDate.'~'.$preOutreprot->outreportEndDate;?></a>
                                        <?php
                                    }else{
                                        echo '暂无';
                                    }
                                     ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->weeklyreport->preWeekOutreportOutFeedbackView;?></th>
                                <td>
                                    <?php
                                    if($preOutreprot){
                                        echo $preOutreprot->outFeedbackView;
                                    }else{
                                        echo '暂无';
                                    }
                                    ?>
                                </td>
                            </tr>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="page-title" style="text-align: center"><h4><?php echo $lang->weeklyreportout->relationInsideProject;?></h4></div>
            <div class="cell">
                <div class="detail">
                    <div class='detail-content'>
                        <?php
                        foreach ($outreport->innerReportBaseInfo as $plan){
                            ?>

                            <table class='table table-data' >
                                <tbody>

                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreportout->inProjectName;?></th>
                                    <td><a href="<?php echo helper::createLink('projectplan','view','planID='.$plan->planid); ?>"><?php echo $plan->projectName; ?></a></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreportout->inProjectpm;?></th>
                                    <td><?php echo zget($users,$plan->pm); ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->projectCode;?></th>
                                    <td><?php echo $plan->projectCode; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->projectAlias;?></th>
                                    <td><?php echo $plan->projectAlias; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreportout->outPlanStartDate;?></th>
                                    <td><?php echo $plan->projectStartDate; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreportout->outPlanEndDate;?></th>
                                    <td><?php echo $plan->projectEndDate; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->progressStatus;?></th>
                                    <td><?php echo $plan->progressStatus; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreport->devDept;?></th>
                                    <td>
                                        <?php
                                        $tempdeptArr = explode(',',$plan->devDept);
                                        foreach ($tempdeptArr as $tempDept){
                                            ?>
                                            <?php echo zget($depts,$tempDept);?><br />

                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->weeklyreportout->inWeeklyreport;?></th>
                                    <td><?php echo html::a(helper::createLink("weeklyreport",'index',"projectID={$plan->projectId}&reportId={$plan->id}"),$outreport->outreportStartDate.'~'.$outreport->outreportEndDate,'_self',"data-app='project'"); ?></td>
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
