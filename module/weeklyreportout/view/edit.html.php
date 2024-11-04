<?php
/**
 * Created by Yanqi Tong
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>

    <div class='main-header'>
        <h2><?php echo $lang->weeklyreportout->edit;?></h2>
    </div>
    <table class='table table-form'>
        <tr>
            <th><?php echo $lang->weeklyreportout->cbpname;?></th>
            <td>
                <?php echo $outsideplan->name;?>
            </td>
            <th><?php echo $lang->weeklyreportout->cbpcode;?></th>
            <td>
                <?php echo $outsideplan->code;?>
            </td>
            <th><?php echo $lang->weeklyreportout->outsideProjectSubProject;?></th>
            <td>
                <?php
                $tempSubProject = explode(',',$outreport->outsideProjectSubProject);
                foreach ($tempSubProject as $subID){
                    echo $outSubProjectList[$subID]->subProjectName.'<br />';
                }



                ?>
            </td>
            <th><?php echo $lang->weeklyreportout->outweeknum;?></th>
            <td>
                <?php echo $outreport->outweeknum; ?>
            </td>

        </tr>
        <tr>
            <th><?php echo $lang->weeklyreportout->outreportDate;?></th>
            <td>
                <?php echo $outreport->outreportStartDate;?>~<?php echo $outreport->outreportEndDate;?>
            </td>
            <th><?php echo $lang->weeklyreportout->relationInsideProject;?></th>
            <td>

                <?php

                //部门、项目经理、QA、项目名称、项目代号、项目阶段、（内部）计划开始时间、（内部）计划结束时间、项目编号
                foreach ($outreport->innerReportBaseInfo as $plan){

                    $temppm = zget($users, $plan->pm);

                    $plan->devDept = trim($plan->devDept);
                    $tempbearDeptArr = explode(',',$plan->devDept);
                    $tempdept = '';
                    $qaArr = explode(',',$plan->qa);
                    $qastr = '';
                    foreach ($tempbearDeptArr as $tdept){
                        $tempdept .= zget($depts,$tdept ).',';


                    }
                    foreach ($qaArr as $qa){
                        $qastr .= zget($users, $qa).',';
                    }
                    $qastr = rtrim($qastr,',');
                    $tempdept = rtrim($tempdept,',');


                    echo $tempdept.':'.$temppm.':'.$qastr.':'.$plan->projectName.':'.$plan->projectCode.':'.$plan->projectStage.':'.$plan->projectStartDate.':'.$plan->projectEndDate.'<br />';

                }



                ?>

            </td>
            <th></th>
            <td>

            </td>
            <th></th>
            <td>

            </td>

        </tr>


    </table>
    <form class="load-indicator main-form form-ajax" method='post'   action='<?php  echo inlink('edit-'. $outreport->id, '')?>'>
        <input type='hidden' value="<?php echo $outreport->id;?>" name="reportId">

        <table class='table table-form'>
            <tr>
                <th><?php echo $lang->weeklyreportout->outprojectStatus;?></th>
                <td ><div class='input-group '><?php echo html::select("outprojectStatus",$lang->weeklyreport->outProjectStatusList,$outreport->outprojectStatus,"class='form-control'  "); ?></div></td>
                <th><?php echo $lang->weeklyreportout->outFeedbackUser;?></th>
                <td><?php
                    echo html::input('outFeedbackUser',$outreport->outFeedbackUser, "class='form-control' ");?></td>
                <th><?php echo $lang->weeklyreportout->outFeedbackTime;?></th>
                <td><?php
                    echo html::input('outFeedbackTime',$outreport->outFeedbackTime, "class='form-control' ");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreportout->outFeedbackView;?></th>
                <td colspan="5"><div class='input-group '><?php echo html::textarea("outFeedbackView",$outreport->outFeedbackView,"class='form-control'  "); ?></div></td>

            </tr>


            <tr>
                <th><?php echo $lang->weeklyreportout->mediaDetails;?></th>
                <td colspan='5' class="">
                    <?php
                    if($outreport->outmediuListInfo){
                    foreach ($outreport->outmediuListInfo as $key=>$reportMedium) {

                        ?>
                        <div class='table-row addandremoveflag'>
                            <div class='table-row mediumCol'>
                                <div class='table-col productCol mediumCol' style="width: 400px;">
                                    <div class='input-group w-p140'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreport->mediumName;?></span>
                                        <?php echo html::input('outMediumName[]', $reportMedium->outMediumName, "class='form-control nousemediumName' ");?>
                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group  mediumCol' >
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMediumOutsideplanSub;?></span>

                                        <?php echo html::select('outMediumOutsideplanSub[]',$outPlanTaskList, $reportMedium->outMediumOutsideplanSub, "class='form-control chosen  nousemediumOutsideplanTask' ");?>
                                    </div>
                                </div>
                            </div>
                            <div class='table-row '>
                                <div class='table-col productCol mediumCol'>
                                    <div class='input-group  w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outPreMediumPublishDate;?></span>
                                        <?php echo html::input('outPreMediumPublishDate[]', $reportMedium->outPreMediumPublishDate, "class='form-control form-date nousepreMediumPublishDate'  ");?>
                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outPreMediumOnlineDate;?></span>
                                        <?php echo html::input('outPreMediumOnlineDate[]', $reportMedium->outPreMediumOnlineDate, "class='form-control form-date nousepreMediumOnlineDate'  ");?>
                                    </div>
                                </div>

                                <div class='table-col '>
                                    <div class='input-group  w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outRealMediumPublishDate;?></span>
                                        <?php echo html::input('outRealMediumPublishDate[]', $reportMedium->outRealMediumPublishDate, "class='form-control form-date nouserealMediumPublishDate' ");?>
                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outRealMediumOnlineDate;?></span>
                                        <?php echo html::input('outRealMediumOnlineDate[]', $reportMedium->outRealMediumOnlineDate, "class='form-control form-date nouserealMediumOnlineDate'  ");?>
                                    </div>
                                </div>
                            </div>

                            <div class='table-row '>
                                <div class='table-col productCol mediumCol'>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMediumRequirement;?></span>
                                        <?php echo html::textarea('outMediumRequirement[]', $reportMedium->outMediumRequirement, "class='form-control nousemediumMark'  ");?>
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

                    <?php
                    }
                    }else{
                        ?>
                        <div class='table-row addandremoveflag'>
                            <div class='table-row mediumCol'>
                                <div class='table-col productCol mediumCol' style="width: 400px;">
                                    <div class='input-group w-p140'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreport->mediumName;?></span>
                                        <?php echo html::input('outMediumName[]', '', "class='form-control nousemediumName' ");?>
                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group  mediumCol' >
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMediumOutsideplanSub;?></span>

                                        <?php echo html::select('outMediumOutsideplanSub[]',$outPlanTaskList, '', "class='form-control chosen nousemediumOutsideplanTask' ");?>
                                    </div>
                                </div>
                            </div>
                            <div class='table-row '>
                                <div class='table-col productCol mediumCol'>
                                    <div class='input-group  w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outPreMediumPublishDate;?></span>
                                        <?php echo html::input('outPreMediumPublishDate[]', '', "class='form-control form-date nousepreMediumPublishDate'  ");?>
                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outPreMediumOnlineDate;?></span>
                                        <?php echo html::input('outPreMediumOnlineDate[]', '', "class='form-control form-date nousepreMediumOnlineDate'  ");?>
                                    </div>
                                </div>

                                <div class='table-col '>
                                    <div class='input-group  w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outRealMediumPublishDate;?></span>
                                        <?php echo html::input('outRealMediumPublishDate[]', '', "class='form-control form-date nouserealMediumPublishDate' ");?>
                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outRealMediumOnlineDate;?></span>
                                        <?php echo html::input('outRealMediumOnlineDate[]', '', "class='form-control form-date nouserealMediumOnlineDate'  ");?>
                                    </div>
                                </div>
                            </div>

                            <div class='table-row '>
                                <div class='table-col productCol mediumCol'>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMediumRequirement;?></span>
                                        <?php echo html::textarea('outMediumRequirement[]', '', "class='form-control nousemediumMark'  ");?>
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

                    <?php
                    }
                    ?>

                </td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreportout->outmileListInfo;?></th>
                <td colspan='5' class="">
                    <?php
                    if($outreport->outmileListInfo){

                    foreach ($outreport->outmileListInfo as $reportOutmile){
                        ?>
                        <div class='table-row addandremoveflag'>
                            <div class='table-row'>
                                <div class='table-col productCol outmileCol' style="width: 400px;">
                                    <div class='input-group w-p140'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileStageName;?></span>
                                        <?php echo html::input('outMileStageName[]', $reportOutmile->outMileStageName, "class='form-control' ");?>
                                    </div>
                                </div>


                                <div class='table-col productCol outmileCol'>
                                    <div class='input-group  w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileProductManual;?></span>
                                        <?php
                                        if(isset($reportOutmile->outMileProductManual)){
                                            echo html::input('outMileProductManual[]', $reportOutmile->outMileProductManual, "class='form-control '  ");

                                        }else{
                                            echo html::input('outMileProductManual[]', '不涉及', "class='form-control '  ");

                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileTechnicalProposal;?></span>
                                        <?php
                                        if(isset($reportOutmile->outMileTechnicalProposal)){
                                            echo html::input('outMileTechnicalProposal[]', $reportOutmile->outMileTechnicalProposal, "class='form-control '  ");

                                        }else{
                                            echo html::input('outMileTechnicalProposal[]', '不涉及', "class='form-control '  ");

                                        }
                                        ?>

                                    </div>
                                </div>


                            </div>

                            <div class='table-row'>
                                <div class='table-col productCol'>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileDeploymentPlan;?></span>
                                        <?php
                                        if(isset($reportOutmile->outMileDeploymentPlan)){
                                            echo html::input('outMileDeploymentPlan[]', $reportOutmile->outMileDeploymentPlan, "class='form-control '  ");

                                        }else{
                                            echo html::input('outMileDeploymentPlan[]', '不涉及', "class='form-control '  ");

                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileUATTest;?></span>
                                        <?php
                                        if(isset($reportOutmile->outMileUATTest)){
                                            echo html::input('outMileUATTest[]', $reportOutmile->outMileUATTest, "class='form-control '  ");

                                        }else{
                                            echo html::input('outMileUATTest[]', '不涉及', "class='form-control '  ");

                                        }
                                        ?>

                                    </div>
                                </div>


                            </div>

                            <div class="table-row ">
                                <div class='table-col productCol'>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileProductReg;?></span>
                                        <?php
                                        if(isset($reportOutmile->outMileProductReg)){
                                            echo html::input('outMileProductReg[]', $reportOutmile->outMileProductReg, "class='form-control '  ");

                                        }else{
                                            echo html::input('outMileProductReg[]', '不涉及', "class='form-control '  ");

                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileAutoScript;?></span>
                                        <?php
                                        if(isset($reportOutmile->outMileAutoScript)){
                                            echo html::input('outMileAutoScript[]', $reportOutmile->outMileAutoScript, "class='form-control '  ");

                                        }else{
                                            echo html::input('outMileAutoScript[]', '不涉及', "class='form-control '  ");

                                        }
                                        ?>

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
                        <?php
                    }
                    }else{
                        ?>
                        <div class='table-row addandremoveflag'>
                            <div class='table-row'>
                                <div class='table-col productCol outmileCol' style="width: 400px;">
                                    <div class='input-group w-p140'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileStageName;?></span>
                                        <?php echo html::input('outMileStageName[]', '', "class='form-control' ");?>
                                    </div>
                                </div>


                                <div class='table-col productCol outmileCol'>
                                    <div class='input-group  w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileProductManual;?></span>
                                        <?php
                                        echo html::input('outMileProductManual[]', '', "class='form-control '  ");
                                        ?>

                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileTechnicalProposal;?></span>
                                        <?php
                                        echo html::input('outMileTechnicalProposal[]', '', "class='form-control '  ");
                                        ?>

                                    </div>
                                </div>


                            </div>

                            <div class='table-row'>
                                <div class='table-col productCol'>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileDeploymentPlan;?></span>
                                        <?php
                                        echo html::input('outMileDeploymentPlan[]', '', "class='form-control '  ");
                                        ?>

                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileUATTest;?></span>
                                        <?php
                                        echo html::input('outMileUATTest[]', '', "class='form-control '  ");
                                        ?>

                                    </div>
                                </div>


                            </div>

                            <div class="table-row ">
                                <div class='table-col productCol'>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileProductReg;?></span>
                                        <?php
                                        echo html::input('outMileProductReg[]', '', "class='form-control '  ");
                                        ?>

                                    </div>
                                </div>
                                <div class='table-col '>
                                    <div class='input-group w-p100'>
                                        <span class='input-group-addon'><?php echo $lang->weeklyreportout->outMileAutoScript;?></span>
                                        <?php
                                        echo html::input('outMileAutoScript[]', '', "class='form-control '  ");
                                        ?>

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

                        <?php
                    }
                    ?>

                </td>
            </tr>



            <tr>
                <th><?php echo $lang->weeklyreport->projectTransDesc;?></th>
                <td colspan='5' class=""><?php echo html::textarea('outProjectTransferMark', $outreport->outProjectTransferMark, "class='form-control'");?></td>
            </tr>

            <tr>
                <th><span style="margin-left:-18px;"><?php echo $lang->weeklyreport->projectProgressMark;?></span></th>
                <td colspan='5'><?php echo html::textarea('outOverallProgress', $outreport->outOverallProgress, "rows='5' class='form-control'");?></td>
            </tr>


            <tr>
                <th><?php echo $lang->weeklyreportout->outProjectAbnormal;?></th>
                <td colspan='5'><?php echo html::textarea('outProjectAbnormal', $outreport->outProjectAbnormal, "rows='3' class='form-control'");?></td>
            </tr>
            <tr>
                <th><?php echo $lang->weeklyreportout->outNextWeekplan;?></th>
                <td colspan='5'><?php echo html::textarea('outNextWeekplan', $outreport->outNextWeekplan, "rows='3' class='form-control'");?></td>
            </tr>

            <tr>
                <th><?php echo $lang->weeklyreportout->outOperatingRemarks;?></th>
                <td colspan='5'><?php echo html::textarea('outOperatingRemarks', $outreport->outOperatingRemarks, "rows='3' class='form-control'");?></td>
            </tr>
            <tr>
                <td colspan='6' class='text-center form-actions'>
                    <input type='hidden' value="<?php echo $outsideplan->id;?>"   name="outProjectID" />

                    <input type='hidden' value="<?php echo $outsideplan->name;?>"  name="outsideProjectName" />
                    <input type='hidden' value="<?php echo $outsideplan->code;?>"  name="outsideProjectCode" />

                    <input type='hidden' value="<?php echo $outreport->outsideProjectSubProject;?>"   name="outsideProjectSubProject" />
                    <input type='hidden' value="<?php echo $outreport->relationInsideProject;?>"   name="relationInsideProject" />
                    <input type='hidden' value="<?php echo $outreport->outweeknum;?>"   name="outweeknum" />


                    <input type='hidden' value='<?php echo $outreport->outriskListInfo;?>'               name="outriskListInfo">

                    <?php echo html::submitButton();?>
                    <?php echo html::linkButton('返回','weeklyreportout-browse.html#app=platform','self','','btn btn-wide');?>
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
        if(outmileNewRow){
            $row.after(outmileNewRow.clone());
        } else {
            outmileNewRow = $row = $(obj).closest('.table-row');
            $row.after($row.clone());
        }
        $next = $row.next();

        $next.find('.form-date').datepicker();
    }
    function removeOutMileItem(obj)
    {
        if($(obj).closest('td').find('.addandremoveflag').size() == 1) return false;
        $(obj).closest('.addandremoveflag').remove();
        outmileNRowNum--;
    }




    var mediumNewRow;

    var mediumNRowNum = 0;
    var mediumNRowNumCount = 0;

    function addMediumItem(obj)
    {
        // if(mediumNRowNum >= 50) { alert("最多加50条介质信息"); return; }
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

        $next.find('#outMediumOutsideplanSub_chosen').remove();
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
<?php include '../../common/view/footer.modal.html.php';?>
