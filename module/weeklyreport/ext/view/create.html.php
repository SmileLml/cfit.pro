<?php
/**
 * Created by Yanqi Tong
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>

    <div class='main-header'>
        <h2><?php echo $lang->weeklyreport->create;?></h2>
    </div>
    <table class='table table-form'>
        <tr>
            <th><?php echo $lang->weeklyreport->projectName;?></th>
            <td>
                <?php echo $projectPlan->name;?>
            </td>
            <th><?php echo $lang->weeklyreport->projectCode;?></th>
            <td>
                <?php echo $projectPlan->code;?>
            </td>
            <th><?php echo $lang->weeklyreport->projectAlias;?></th>
            <td>
                <?php echo $projectPlan->mark;?>
            </td>
            <th><?php echo $lang->weeklyreport->projectType;?></th>
            <td>
                <?php echo zget($lang->projectplan->typeList, $projectPlan->type, ''); ?>
            </td>

        </tr>
        <tr>
            <th><?php echo $lang->weeklyreport->devDept;?></th>
            <td>
                <?php echo $devDept;?>
            </td>
            <th><?php echo $lang->weeklyreport->pm;?></th>
            <td>
                <?php echo zget($users, $project->PM);?>
            </td>
            <th><?php echo $lang->weeklyreport->isImportant;?></th>
            <td>
                <?php echo zget($lang->projectplan->isImportantList, $projectPlan->isImportant, ''); ?>
            </td>
            <th><?php echo $lang->weeklyreport->projectplanYear;?></th>
            <td>
                <?php echo $projectPlan->year; ?>
            </td>

        </tr>
        <tr>

            <th><?php echo $lang->weeklyreport->projectStartDate;?></th>
            <td>
                <?php echo $projectPlan->begin;?>
            </td>
            <th><?php echo $lang->weeklyreport->projectEndDate;?></th>
            <td>
                <?php echo $projectPlan->end;?>
            </td>
            <th><?php echo $lang->weeklyreport->relationRequirement;?></th>
            <td>
                <?php echo $requirementStr;?>
            </td>
            <th></th>
            <td>
            </td>
        </tr>
        <?php foreach ($outsidePlan as $outplan){
            ?>
            <tr>

                <th><?php echo $lang->weeklyreport->outProjectName;?></th>
                <td>
                    <?php echo $outplan->name;?>
                </td>
                <th><?php echo $lang->weeklyreport->subprojectsTaskStr;?></th>
                <td>
                    <?php echo nl2br($outplan->subprojectsTaskStr);?>
                </td>
                <th><?php echo $lang->weeklyreport->preWeekOutreport;?></th>
                <td>
                    <?php
                    if($outplan->preWeekOutreport){
                        ?>
                        <a href="<?php echo helper::createLink('weeklyreportout','view','outreportID='.$outplan->preWeekOutreport->id,'html#app=platform') ?>">第<?php echo $outplan->preWeekOutreport->outweeknum; ?>周</a>

                        <?php
                    }
                    ?>
                </td>
                <th><?php echo $lang->weeklyreport->preWeekOutreportOutFeedbackView;?></th>
                <td>
                    <?php
                    if($outplan->preWeekOutreport){
                        echo nl2br($outplan->preWeekOutreport->outFeedbackView);
                    }
                    ?>
                </td>
            </tr>

        <?php
        } ?>
    </table>
    <form class="load-indicator main-form form-ajax" method='post'  action='<?php  echo inlink('create-'. $projectID, '')?>'>

        <table class='table table-form'>
            <tr>
                <th><?php echo $lang->weeklyreport->reportStartDate;?></th>
                <td class="w-160px"><div class='input-group '><span class='input-group-addon fix-border'>第</span><?php echo html::input("weeknum","","class='form-control'  "); ?><span class='input-group-addon fix-border'>周</span></div></td>
                <td  class="">
                    <div class='input-group'>
                        <?php echo html::input('reportStartDate',  $week['week_start'] , "class='form-control form-date'  ");?>
                        <span class='input-group-addon fix-border'>至</span>
                        <?php echo html::input('reportEndDate',  $week['week_end'], "class='form-control form-date' ");?>
                    </div>
                </td>
                <th><?php echo $lang->weeklyreport->projectStage; ?></th>
                <td><?php
                    unset($lang->projectplan->typeList['']);
                    echo html::select('projectStage', $lang->weeklyreport->projectState, '', "class='form-control chosen' ");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->projectProgress;?></th>
                <td colspan="2"><div class='input-group '><?php echo html::input("projectProgress","","class='form-control'  "); ?><span class='input-group-addon fix-border'>%</span></div></td>
                <th><?php echo $lang->weeklyreport->progressStatus;?></th>
                <td><?php
                    echo html::input('progressStatus',  '', "class='form-control' ");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->mediumDetails; ?></th>
                <td colspan='4' class="">
                    <div class='table-row addandremoveflag'>
                        <div class='table-row mediumCol'>
                            <div class='table-col productCol mediumCol' style="width: 400px;">
                                <div class='input-group w-p140'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->mediumName;?></span>
                                    <?php echo html::input('mediumName[0]', '', "class='form-control nousemediumName' ");?>
                                </div>
                            </div>
                            <div class='table-col '>
                                <div class='input-group  w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->mediumOutsideplanTask; ?></span>

                                    <?php echo html::select('mediumOutsideplanTask[0]', $mediumOutTask,"", "class='form-control chosen nousemediumOutsideplanTask' ");?>
                                </div>
                            </div>
                        </div>
                        <div class='table-row '>
                            <div class='table-col productCol mediumCol'>
                                <div class='input-group  w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->preMediumPublishDate; ?></span>
                                    <?php echo html::input('preMediumPublishDate[0]', '', "class='form-control form-date nousepreMediumPublishDate'  ");?>
                                </div>
                            </div>
                            <div class='table-col '>
                                <div class='input-group w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->preMediumOnlineDate; ?></span>
                                    <?php echo html::input('preMediumOnlineDate[0]', '', "class='form-control form-date nousepreMediumOnlineDate'  ");?>
                                </div>
                            </div>

                            <div class='table-col '>
                                <div class='input-group  w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->realMediumPublishDate; ?></span>
                                    <?php echo html::input('realMediumPublishDate[0]', '', "class='form-control form-date nouserealMediumPublishDate' ");?>
                                </div>
                            </div>
                            <div class='table-col '>
                                <div class='input-group w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->realMediumOnlineDate; ?></span>
                                    <?php echo html::input('realMediumOnlineDate[0]', '', "class='form-control form-date nouserealMediumOnlineDate'  ");?>
                                </div>
                            </div>
                        </div>
                        <div class='table-row '>
                            <div class='table-col productCol mediumCol groupmediumCol'>
                                <div class='input-group w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->mediumRequirement; ?></span>
                                    <?php echo html::select('mediumRequirement[0][]', $productRequirement,"", "class='form-control chosen nousemediumRequirement'  multiple ");?>
                                </div>
                            </div>
                        </div>
                        <div class='table-row '>
                            <div class='table-col productCol mediumCol'>
                                <div class='input-group w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->mediumMark; ?></span>
                                    <?php echo html::input('mediumMark[0]', '', "class='form-control nousemediumMark'  ");?>
                                </div>
                            </div>
                        </div>
                        <div class='table-col actionCol text-middle'>
                            <div class='btn-group'>
                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addMediumItem(this)'");?>
                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeMediumItem(this)'");?>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->externalMilestones;?></th>
                <td colspan='4' class="">
                    <div class='table-row addandremoveflag'>
                        <div class='table-row'>
                            <div class='table-col productCol outmileCol' style="width: 400px;">
                                <div class='input-group w-p140'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMileStageName;?></span>
                                    <?php echo html::input('outMileStageName[]', '', "class='form-control' ");?>
                                </div>
                            </div>
                            <div class='table-col '>
                                <div class='input-group mediumCol  w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMileName;?></span>
                                    <?php echo html::select('outMileName[]', $lang->weeklyreport->outmileNameList,'', "class='form-control chosen'  ");?>
                                </div>
                            </div>

                            <div class='table-col productCol outmileCol'>
                                <div class='input-group  w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMilePreDate;?></span>
                                    <?php echo html::input('outMilePreDate[]', '', "class='form-control form-date'  ");?>
                                </div>
                            </div>
                            <div class='table-col '>
                                <div class='input-group w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMileRealDate;?></span>
                                    <?php echo html::input('outMileRealDate[]', '', "class='form-control form-date'  ");?>
                                </div>
                            </div>

                        </div>

                        <div class='table-row '>
                            <div class='table-col productCol outmileCol'>
                                <div class='input-group w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMileMark;?></span>
                                    <?php echo html::input('outMileMark[]', '', "class='form-control'  ");?>
                                </div>
                            </div>
                        </div>
                        <div class='table-col actionCol text-middle'>
                            <div class='btn-group'>
                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addOutMileItem(this)'");?>
                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeOutMileItem(this)'");?>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->internalMilestones;?></th>
                <td colspan='4' class="">
                    <div class='table-row addandremoveflag'>
                        <div class='table-row'>
                            <div class='table-col productCol insidemileCol' style="width: 400px;">
                                <div class='input-group w-p140'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->insideMileStage;?></span>
                                    <?php echo html::input('insideMileStage[]', '', "class='form-control' ");?>
                                </div>
                            </div>
                            <div class='table-col '>
                                <div class='input-group  w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMileName;?></span>
                                    <?php echo html::input('insideMileName[]', '', "class='form-control'  ");?>
                                </div>
                            </div>

                            <div class='table-col productCol insidemileCol'>
                                <div class='input-group  w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMilePreDate;?></span>
                                    <?php echo html::input('insideMilePreDate[]', '', "class='form-control form-date'  ");?>
                                </div>
                            </div>
                            <div class='table-col '>
                                <div class='input-group w-p100 insidemileCol'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMileRealDate;?></span>
                                    <?php echo html::input('insideMileRealDate[]', '', "class='form-control form-date'  ");?>
                                </div>
                            </div>

                        </div>

                        <div class='table-row '>
                            <div class='table-col productCol insidemileCol'>
                                <div class='input-group w-p100'>
                                    <span class='input-group-addon'><?php echo $lang->weeklyreport->outMileMark;?></span>
                                    <?php echo html::input('insideMileMark[]', '', "class='form-control'  ");?>
                                </div>
                            </div>
                        </div>
                        <div class='table-col actionCol text-middle required'>
                            <div class='btn-group'>
                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addInsideMileItem(this)'");?>
                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeInsideMileItem(this)'");?>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <th><?php echo $lang->weeklyreport->mileDelayNum;?></th>
                <td colspan='4' class=""><?php echo html::input('mileDelayNum', '', "class='form-control'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->mileDelayMark;?></th>
                <td colspan='4' class=""><?php echo html::textarea('mileDelayMark', '', "class='form-control'");?></td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;"><?php echo $lang->weeklyreport->projectProgressMark;?></span></th>
                <td colspan='4'><?php echo html::textarea('projectProgressMark', '', "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;"><?php echo $lang->weeklyreport->projectTransDesc;?></span></th>
                <td colspan='4'><?php echo html::textarea('projectTransDesc', '', "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->productBuilds;?></th>
                <td colspan='4'><?php echo html::textarea('productBuilds', '', "rows='3' class='form-control'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->projectAbnormalDesc;?></th>
                <td colspan='4'><?php echo html::textarea('projectAbnormalDesc', '', "rows='3' class='form-control'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->nextWeekplan;?></th>
                <td colspan='4'><?php echo html::textarea('nextWeekplan', '', "rows='3' class='form-control'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreport->remark;?></th>
                <td colspan='4'><?php echo html::textarea('remark', '', "rows='3' class='form-control'");?></td>
            </tr>


            <tr>
                <td colspan='5' class='text-center form-actions'>
                    <input type='hidden' value="<?php echo $projectPlan->code;?>"   name="projectCode" />
                    <input type='hidden' value="<?php echo $projectID;?>"           name="projectId" />
                    <input type='hidden' value="<?php echo $projectPlan->name;;?>"  name="projectName" />
                    <input type='hidden' value="<?php echo $projectPlan->mark;?>"   name="projectAlias" />
                    <input type='hidden' value="<?php echo $projectPlan->year;?>"   name="projectplanYear" />
                    <input type='hidden' value="<?php echo $projectPlan->id;?>"   name="planID" />
                    <input type='hidden' value="<?php echo $requirementIdStr;?>"   name="relationRequirement" />

                    <input type='hidden' value="<?php echo $projectPlan->begin;?>"  name="projectStartDate" />
                    <input type='hidden' value="<?php echo $projectPlan->end;?>"    name="projectEndDate" />
                    <input type='hidden' value="<?php echo $outprojectlanID;?>"   name="outPlanId" />
                    <input type='hidden' value="<?php echo $creation->PM;?>" name="pm">
                    <input type='hidden' value="<?php echo $qa;?>"   name="qa" />
                    <input type='hidden' value="<?php echo $projectPlan->bearDept;?>" name="devDept">
                    <input type='hidden' value="<?php echo $projectPlan->isImportant;?>" name="isImportant">
                    <input type='hidden' value="<?php echo $projectPlan->creation->type;?>" name="projectType">

                    <input type='hidden' value='<?php echo $risks;?>'               name="risks">
<!--                    <input type='hidden' value="--><?php //echo $issues;?><!--"              name="issues">-->
<!--                    <input type='hidden' value="--><?php //echo $outsidePlan->code;?><!--" name="outProjectCode">-->
<!--                    <input type='hidden' value="--><?php //echo $outsidePlan->name;?><!--" name="outProjectName">-->
<!--                    <input type='hidden' value="--><?php //echo '';?><!--" name="outSubProjectName">-->
<!--                    <input type='hidden' value="--><?php //echo '';?><!--" name="govDept">-->
<!--                    <input type='hidden' value="--><?php //echo '';?><!--" name="outDemander">-->
<!--                    <input type='hidden' value="--><?php //echo '';?><!--" name="outBearCompany">-->
<!--                    <input type='hidden' value="--><?php //echo $outsidePlan->begin;?><!--" name="outPlanStartDate">-->
<!--                    <input type='hidden' value="--><?php //echo $outsidePlan->end;?><!--" name="outPlanEndDate">-->
<!--                    <input type='hidden' value="--><?php //echo $outsidePlan->workload;?><!--" name="outPlanWorkload">-->
<!--                    <input type='hidden' value="--><?php //echo "";?><!--" name="outPlanChange">-->
                    <!--<input type='hidden' value="<?php /*echo $outsidePlan;*/?>" name="outsidePlan">
                    <input type='hidden' value="<?php /*echo $builds;*/?>" name="productBuilds">
                    <input type='hidden' value="<?php /*echo $demands;*/?>" name="productDemand">-->
                    <?php echo html::submitButton();?>
                    <?php echo html::linkButton('返回','weeklyreport-index-'. $projectID.'.html#app=project','self','','btn btn-wide');?>
                </td>
            </tr>
        </table>
    </form>
</div>
<script>

    var outmileNewRow;
    var outmileNRowNum = 0;
    function addOutMileItem(obj)
    {
        // if(outmileNRowNum >= 50) { alert("最多加50条外部里程信息"); return; }
        outmileNRowNum++;
        /*if(outmileNewRow){
            $row.after(outmileNewRow.clone());
        } else {
            outmileNewRow = $row = $(obj).closest('.addandremoveflag');
            $row.after($row.clone());
        }*/

        $row = $(obj).closest('.addandremoveflag');
        $row.after($row.clone());
        $next = $row.next();

        $next.find("#outMileName_chosen").remove();
        $next.find('.mediumCol select').val('').chosen();

        $next.find('.form-date').datepicker();
    }
    function removeOutMileItem(obj)
    {
        if($(obj).closest('td').find('.addandremoveflag').size() == 1) return false;
        $(obj).closest('.addandremoveflag').remove();
        outmileNRowNum--;
    }


    var insidemileNewRow;
    var insidemileNRowNum = 0;
    function addInsideMileItem(obj)
    {
        // if(insidemileNRowNum >= 50) { alert("最多加50条内部里程信息"); return; }
        insidemileNRowNum++;
        /*if(insidemileNewRow){
            $row.after(insidemileNewRow.clone());
        } else {
            insidemileNewRow = $row = $(obj).closest('.addandremoveflag');
            $row.after($row.clone());
        }*/
        $row = $(obj).closest('.addandremoveflag');
        $row.after($row.clone());
        $next = $row.next();

        $next.find('.form-date').datepicker();
    }
    function removeInsideMileItem(obj)
    {
        if($(obj).closest('td').find('.addandremoveflag').size() == 1) return false;
        $(obj).closest('.addandremoveflag').remove();
        insidemileNRowNum--;
    }

    var mediumNewRow;
    var mediumNRowNum = 0;
    var mediumNRowNumCount = 0;
    function addMediumItem(obj)
    {
        // if(mediumNRowNum >= 200) { alert("最多加200条介质信息"); return; }
        mediumNRowNum++;
        mediumNRowNumCount++;
       /* if(mediumNewRow){
            $row.after(mediumNewRow.clone());
        } else {
            mediumNewRow = $row = $(obj).closest('.addandremoveflag');
            $row.after($row.clone());
        }*/
        mediumNewRow = $row = $(obj).closest('.addandremoveflag');
        $row.after($row.clone());

        $next = $row.next();


        //处理其他表单
        //介质名称
        let mediumNameId = "mediumName["+mediumNRowNumCount+"]";
        $next.find('.nousemediumName').attr("id",mediumNameId).attr("name",mediumNameId);

        //拟发布时间
        let preMediumPublishDateId = "preMediumPublishDate["+mediumNRowNumCount+"]";
        $next.find('.nousepreMediumPublishDate').attr("id",preMediumPublishDateId).attr("name",preMediumPublishDateId);

        //拟上线时间
        let preMediumOnlineDateId = "preMediumOnlineDate["+mediumNRowNumCount+"]";
        $next.find('.nousepreMediumOnlineDate').attr("id",preMediumOnlineDateId).attr("name",preMediumOnlineDateId);

        //实际发布时间
        let realMediumPublishDateId = "realMediumPublishDate["+mediumNRowNumCount+"]";
        $next.find('.nouserealMediumPublishDate').attr("id",realMediumPublishDateId).attr("name",realMediumPublishDateId);

        //实际上线时间
        let realMediumOnlineDateId = "realMediumOnlineDate["+mediumNRowNumCount+"]";
        $next.find('.nouserealMediumOnlineDate').attr("id",realMediumOnlineDateId).attr("name",realMediumOnlineDateId);

        //产品实现需求补充
        let mediumMarkId = "mediumMark["+mediumNRowNumCount+"]";
        $next.find('.nousemediumMark').attr("id",mediumMarkId).attr("name",mediumMarkId);

        //所属外部任务
        let premediumOutsideplanTaskID = $row.find(".nousemediumOutsideplanTask").attr("id");
        let premediumOutsideplanTask_chosenID = "#"+premediumOutsideplanTaskID+"_chosen";
        $next.find(premediumOutsideplanTask_chosenID).remove();

        let tempmediumOutsideplanTaskID = "mediumOutsideplanTask"+mediumNRowNumCount;
        let tempmediumOutsideplanTaskName = "mediumOutsideplanTask["+mediumNRowNumCount+"]";

        $next.find('.nousemediumOutsideplanTask').attr("id",tempmediumOutsideplanTaskID);
        $next.find('.nousemediumOutsideplanTask').attr("name",tempmediumOutsideplanTaskName);
        //所属外部任务 结束

        /*let tempmediumOutsideplanTaskChosenID = "#mediumOutsideplanTask"+mediumNRowNumCount+"_chosen";
        let tempfindoutsideplan = '.mediumCol ' + tempmediumOutsideplanTaskChosenID;
        $next.find(tempfindoutsideplan).remove();*/


        //子任务
        let premediumRequirementID = $row.find(".nousemediumRequirement").attr("id");
        let premediumRequirement_chosenID = "#"+premediumRequirementID+"_chosen";

        let temtid = "mediumRequirement"+mediumNRowNumCount;
        let temtname = "mediumRequirement["+mediumNRowNumCount+"][]";
        $next.find('.nousemediumRequirement').attr("id",temtid);
        $next.find('.nousemediumRequirement').attr("name",temtname);


        $next.find(premediumRequirement_chosenID).remove();
        //子任务 结束


        // $next.find('.mediumCol select').val('0').chosen();

        $next.find('.mediumCol select').val('').chosen();
        $next.find('.form-date').datepicker();

    }
    function removeMediumItem(obj)
    {
        if($(obj).closest('td').find('.addandremoveflag').size() == 1) return false;
        $(obj).closest('.addandremoveflag').remove();
        mediumNRowNum--;

    }


</script>
<?php include '../../../common/view/footer.modal.html.php';?>
