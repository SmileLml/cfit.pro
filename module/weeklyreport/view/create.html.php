<?php
/**
 * Created by Yanqi Tong
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>

    <div class='main-header'>
        <h2>新建周报</h2>
    </div>
    <table class='table table-form'>
        <tr>
            <th>项目编号</th>
            <td>
                <?php echo $projectPlan->code;?>
            </td>
            <th>项目代码</th>
            <td>
                <?php echo $projectPlan->mark;?>
            </td>
            <th>整体进度</th>
            <td>
                <?php echo $project->progress - 0;?>%
            </td>
        </tr>
        <tr>
            <th>计划开始时间</th>
            <td>
                <?php echo $projectPlan->begin;?>
            </td>
            <th>计划结束时间</th>
            <td>
                <?php echo $projectPlan->end;?>
            </td>
            <th></th>
            <td>
            </td>
        </tr>
    </table>
    <form class="load-indicator main-form form-ajax" method='post'  action='<?php  echo inlink('create-'. $projectID, '')?>'>

        <table class='table table-form'>
            <tr>
                <th>周报时间</th>
                <td colspan='3' class="required">
                    <div class='input-group'>
                        <?php echo html::input('reportStartDate',  $week['week_start'] , "class='form-control form-date'  ");?>
                        <span class='input-group-addon fix-border'>至</span>
                        <?php echo html::input('reportEndDate',  $week['week_end'], "class='form-control form-date' ");?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>介质明细</th>
                <td colspan='3' class="required">
                    <div class='table-row'>
                        <div class='table-col productCol' style="width: 400px;">
                            <div class='input-group w-p140'>
                                <span class='input-group-addon'>制品名称</span>
                                <?php echo html::select('productPlanCode[]', $projectPlanRelation, '', "class='form-control chosen' onchange='loadRelease(this)'");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group  w-p100'>
                                <span class='input-group-addon'>拟发布</span>
                                <?php echo html::input('preRelease[]', '', "class='form-control form-date'  ");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group w-p100'>
                                <span class='input-group-addon'>拟上线</span>
                                <?php echo html::input('preOnline[]', '', "class='form-control form-date'  ");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group  w-p100'>
                                <span class='input-group-addon'>实际发布</span>
                                <?php echo html::input('realRelease[]', '', "class='form-control form-date' ");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group w-p100'>
                                <span class='input-group-addon'>实际上线</span>
                                <?php echo html::input('realOnline[]', '', "class='form-control form-date'  ");?>
                            </div>
                        </div>
                        <div class='table-col actionCol'>
                            <div class='btn-group'>
                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");?>
                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this)'");?>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class='w-100px'>项目处于阶段</th>
                <td class='w-p30 required' colspan="3" id="prosatus">
                    <?php echo html::select('progressStatus[]', $stages, '', "class='form-control chosen'  multiple");?>
                </td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;">项目状态(对内)</span></th>
                <td class="required">
                    <?php echo html::select('insideStatus',  $statusSelects['insideReportStatusList'], 0, "class='form-control '");?>
                </td>
                <th>项目状态(对外)</th>
                <td class="required">
                    <?php echo html::select('outsideStatus',  $statusSelects['outsideReportStatusList'], 0, "class='form-control '");?>
                </td>

            </tr>
            <tr>
                <th>项目进展描述</th>
                <td colspan='3' class="required"><?php echo html::textarea('reportDesc', '<span style="font-size:14px;"><strong>本周计划：</strong></span></br>1、</br> </br> <span style="font-size:14px;"><strong>下周计划：</strong></span></br>1、</br> </br> <strong>遇到问题：</strong></span></br>1、</br> </br> ', "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;">(内部)项目里程碑</span></th>
                <td colspan='3'><?php echo html::textarea('insideMilestone', '', "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;">(外部)项目里程碑</span></th>
                <td colspan='3'><?php echo html::textarea('outsideMilestone', '', "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th>项目移交状况</th>
                <td colspan='3'><?php echo html::textarea('transDesc', '', "rows='3' class='form-control'");?></td>
            </tr>
            <tr>
                <th>备注</th>
                <td colspan='3'><?php echo html::textarea('remark', '', "rows='3' class='form-control'");?></td>
            </tr>


            <tr>
                <td colspan='3' class='text-center form-actions'>
                    <input type='hidden' value="<?php echo $projectPlan->code;?>"   name="projectCode">
                    <input type='hidden' value="<?php echo $projectID;?>"           name="projectId">
                    <input type='hidden' value="<?php echo $projectPlan->name;;?>"  name="projectName">
                    <input type='hidden' value="<?php echo $projectPlan->mark;?>"   name="projectAlias">
                    <input type='hidden' value="<?php echo $project->progress - 0;?>" name="projectProgress">
                    <input type='hidden' value="<?php echo $projectPlan->begin;?>"  name="projectStartDate">
                    <input type='hidden' value="<?php echo $projectPlan->end;?>"    name="projectEndDate">
                    <input type='hidden' value="<?php echo $project->progress - 0;?>" name="projectProgress">
                    <input type='hidden' value="<?php echo zget($users, $creation->PM, '')?>" name="pm">
                    <input type='hidden' value="<?php echo zget($depts, $projectPlan->bearDept);?>" name="devDept">
                    <input type='hidden' value="<?php echo zget($lang->projectplan->typeList, $projectPlan->creation->type, '');?>" name="projectType">
                    <input type='hidden' value="<?php echo $risks;?>"               name="risks">
                    <input type='hidden' value="<?php echo $issues;?>"              name="issues">
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
                    <input type='hidden' value="<?php echo $outsidePlan;?>" name="outsidePlan">
                    <input type='hidden' value="<?php echo $builds;?>" name="productBuilds">
                    <input type='hidden' value="<?php echo $demands;?>" name="productDemand">
                    <?php echo html::submitButton();?>
                    <?php echo html::linkButton('返回','weeklyreport-index-'. $projectID.'.html#app=project','self','','btn btn-wide');?>
                </td>
            </tr>
        </table>
    </form>
</div>
<?php include '../../common/view/footer.modal.html.php';?>
