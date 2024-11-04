<?php include '../../../common/view/header.html.php';?>
<?php if(common::checkNotCN()):?>
    <style>#conditions .col-xs {width: 126px;}</style>
<?php endif;?>
<div id='mainContent' class='main-row'>
    <div class='side-col col-lg' id='sidebar'>
        <?php include './blockreportlist.html.php';?>
    </div>
    <div class='main-col'>
        <div class='cell'>
            <div class="with-padding">
                <form method='post'>
                    <div class="table-row" id='conditions'>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->report->begin;?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='form-control form-date' onchange='changeDate(this.value, \"$end\")'");?></div>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->report->end;?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control form-date' onchange='changeDate(\"$begin\", this.value)'");?></div>
                            </div>
                        </div>
                        <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?></div>
                    </div>
                </form>
            </div>
        </div>
        <?php if(empty($reviewList)):?>
            <div class="cell">
                <div class='panel'>
                    <?php if(common::hasPriv('report', 'refreshReport')) echo html::a(inLink('refreshReport', array('method' => 'reviewFlowWorkload', 'projectID' => $projectID)), $lang->report->refreshReport, '', 'class="btn btn-primary btn-sm"');?>
                </div>
                <div class="table-empty-tip">
                    <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
                </div>
            </div>
        <?php else:?>
            <div class='cell'>
                <div class='panel'>
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="table-row" id='conditions'>
                                <div class="col-xs"><?php echo $title;?></div>
                            </div>
                        </div>
                        <nav class="panel-actions btn-toolbar">
                            <?php if(common::hasPriv('report', 'refreshReport')) echo html::a(inLink('refreshReport', array('method' => 'reviewFlowWorkload', 'projectID' => $projectID)), $lang->report->refreshReport, '', 'class="btn btn-primary btn-sm"');?>
                            <?php if(common::hasPriv('report', 'exportFlowWorkload')) echo html::a(inLink('exportFlowWorkload', array('projectID' => $projectID, 'param' => $param)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
                        </nav>
                    </div>
                    <div data-ride='table'  style="width:100%; height:500px; overflow:scroll;">
                        <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
                            <thead  style="position:sticky;top:0;background:white;box-shadow:0 0 3px 3px rgba(64,169,255,0.2);">
                            <tr class='text-center'>
                                <th class='w-150px text-left'><?php echo $lang->project->name;?></th>
                                <th class='w-80px text-left'><?php echo $lang->project->code;?></th>
                                <th class='w-80px text-left'><?php echo $lang->project->projectId;?></th>
                                <th class='w-60px text-left'><?php echo $lang->reviewmeet->createdBy;?></th>
                                <th class='w-80px text-left'><?php echo $lang->reviewmeet->createdDept;?></th>
                                <th class='w-60px text-left'><?php echo $lang->review->reviewID;?></th>
                                <th class='w-150px text-left'><?php echo $lang->review->title;?></th>
                                <th class='w-100px text-left'><?php echo $lang->review->reviewStatus;?></th>
                                <th class='w-100px text-left'><?php echo $lang->review->type;?></th>
                                <th class='w-80px text-left'><?php echo $lang->review->trialDept;?></th>
                                <th class='w-110px text-left'><?php echo $lang->review->trialDeptLiasisonOfficer;?></th>
                                <th class='w-100px text-left'><?php echo $lang->review->trialAdjudicatingOfficer;?></th>
                                <th class='w-100px text-left'><?php echo $lang->review->trialJoinOfficer;?></th>
                                <th class='w-80px text-left'><?php echo $lang->review->reviewOwner;?></th>
                                <th class='w-80px text-left'><?php echo $lang->review->qa;?></th>
                                <th class='w-80px text-left'><?php echo $lang->review->cm;?></th>
                                <th class='w-120px text-left'><?php echo $lang->review->onLineExpert;?></th>
                                <th class='w-120px text-left'><?php echo $lang->review->realExpert;?></th>
                                <th class='w-100px text-left'><?php echo $lang->review->verifier;?></th>
                                <th class='w-140px text-left'><?php echo $lang->review->createdDate;?></th>
                                <th class='w-140px text-left'><?php echo $lang->review->firstPreReviewDate;?></th>
                                <th class='w-140px text-left'><?php echo $lang->review->closeTime;?></th>
                                <th class='w-140px text-left'><?php echo $lang->review->baselineDate;?></th>
                                <th class='w-140px text-left'><?php echo $lang->review->suspendTime;?></th>
                                <th class='w-140px text-left'><?php echo $lang->review->renewTime;?></th>
                                <?php foreach($stageList as $key => $name):?>
                                    <th class="w-120px"><?php echo $name;?></th>
                                <?php endforeach;?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($reviewList as $reviewID => $list):?>
                                <tr class="text-center">
                                    <td class='text-left'><?php echo $list->projectName;?></td>
                                    <td class='text-left'><?php echo $list->projectMark;?></td>
                                    <td class='text-left'><?php echo $list->projectCode;?></td>
                                    <td class='text-left'><?php echo $accounts[$list->createdBy];?></td>
                                    <td class='text-left'><?php echo $depts[$list->createdDept];?></td>
                                    <td class='text-left'><?php echo $list->reviewID;?></td>
                                    <td class='text-left'><?php echo $list->reviewName;?></td>
                                    <td class='text-left'><?php echo $statusList[$list->status];?></td>
                                    <td class='text-left'><?php echo $typeList[$list->type];?></td>
                                    <td class='text-left'><?php echo $list->trialDept;?></td>
                                    <td class='text-left'><?php echo $list->trialDeptLiasisonOfficer;?></td>
                                    <td class='text-left'><?php echo $list->trialAdjudicatingOfficer;?></td>
                                    <td class='text-left'><?php echo $list->trialJoinOfficer;?></td>
                                    <td class='text-left'><?php echo $accounts[$list->owner];?></td>
                                    <td class='text-left'><?php echo $accounts[$list->qa];?></td>
                                    <td class='text-left'><?php echo $accounts[$list->qualityCm];?></td>
                                    <td class='text-left'><?php echo $list->onLineExpert;?></td>
                                    <td class='text-left'><?php echo $list->realExpert;?></td>
                                    <td class='text-left'><?php echo $list->verifier;?></td>
                                    <td class='text-left'><?php echo $list->createdDate != '0000-00-00 00:00:00' ? $list->createdDate : '';?></td>
                                    <td class='text-left'><?php echo $list->firstPreReviewDate != '0000-00-00 00:00:00' ? $list->firstPreReviewDate : '';?></td>
                                    <td class='text-left'><?php echo $list->closeTime != '0000-00-00 00:00:00' ? $list->closeTime : '';?></td>
                                    <td class='text-left'><?php echo $list->baselineDate != '0000-00-00 00:00:00' ? $list->baselineDate: '';?></td>
                                    <td class='text-left'><?php echo $list->suspendTime != '0000-00-00 00:00:00' ? $list->suspendTime : '';?></td>
                                    <td class='text-left'><?php echo $list->renewTime != '0000-00-00 00:00:00' ? $list->renewTime : '';?></td>
                                    <?php foreach($stageList as $stage => $name):?>
                                        <td class='text-left'><?php echo $list->$stage;?></td>
                                    <?php endforeach;?>
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
<?php include '../../../common/view/footer.html.php';?>
