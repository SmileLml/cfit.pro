<?php
/**
 * Created by Yanqi Tong
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>

    <div class='main-header'>
        <h2>编辑周报</h2>
    </div>
    <table class='table table-form'>
        <tr>
            <th>项目编号</th>
            <td>
                <?php echo $report->projectCode;?>
            </td>
            <th>项目代码</th>
            <td>
                <?php echo $report->projectAlias;?>
            </td>
            <th>整体进度</th>
            <td>
                <?php echo $report->projectProgress;?>%
            </td>
        </tr>
        <tr>
            <th>计划开始时间</th>
            <td>
                <?php echo $report->projectStartDate;?>
            </td>
            <th>计划结束时间</th>
            <td>
                <?php echo $report->projectEndDate;?>
            </td>
            <th></th>
            <td>
            </td>
        </tr>
    </table>
    <form class="load-indicator main-form form-ajax" method='post'   action='<?php  echo inlink('edit-'. $reportId, '')?>'>
        <input type='hidden' value="<?php echo $reportId;?>" name="reportId">

        <table class='table table-form'>
            <tr>
                <th>周报时间</th>
                <td colspan='3' class="required">
                    <div class='input-group'>
                        <?php echo html::input('reportStartDate',  $report->reportStartDate , "class='form-control form-date'  ");?>
                        <span class='input-group-addon fix-border'>至</span>
                        <?php echo html::input('reportEndDate',  $report->reportEndDate, "class='form-control form-date' ");?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>介质明细</th>
                <td colspan='3' class="required">
                    <?php
                    foreach ($relations as $relation) {
                    ?>
                    <div class='table-row'>
                        <div class='table-col productCol' style="width: 400px;">
                            <div class='input-group' >
                                <span class='input-group-addon'>制品名称</span>
                                <?php echo html::select('productPlanCode[]', $projectPlanRelation, $relation->productPlanCode, "class='form-control chosen' onchange='loadRelease(this)'");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group  w-p100'>
                                <span class='input-group-addon'>拟发布</span>
                                <?php echo html::input('preRelease[]', $relation->preRelease, "class='form-control form-date'  ");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group w-p100'>
                                <span class='input-group-addon'>拟上线</span>
                                <?php echo html::input('preOnline[]',  $relation->preOnline, "class='form-control form-date'  ");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group  w-p100'>
                                <span class='input-group-addon'>实际发布</span>
                                <?php echo html::input('realRelease[]', $relation->realRelease, "class='form-control form-date' ");?>
                            </div>
                        </div>
                        <div class='table-col '>
                            <div class='input-group w-p100'>
                                <span class='input-group-addon'>实际上线</span>
                                <?php echo html::input('realOnline[]', $relation->realOnline, "class='form-control form-date'  ");?>
                            </div>
                        </div>
                        <div class='table-col actionCol'>
                            <div class='btn-group'>
                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");?>
                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this)'");?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th class='w-100px'>项目处于阶段</th>
                <td class='w-p30 required' colspan="3">
                    <?php
                    echo html::select('progressStatus[]', $stages,  $report->progressStatus, "class='form-control chosen' multiple");?>
            </td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;">项目状态(对内)</span></th>
                <td class="required">
                    <?php echo html::select('insideStatus', $statusSelects['insideReportStatusList'], $report->insideStatus, "class='form-control '");?>
                </td>
                <th>项目状态(对外)</th>
                <td class="required">
                    <?php echo html::select('outsideStatus', $statusSelects['outsideReportStatusList'], $report->outsideStatus, "class='form-control '");?>
                </td>

            </tr>
            <tr>
                <th>项目进展描述</th>
                <td colspan='3' class="required"><?php echo html::textarea('reportDesc', $report->reportDesc, "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;">(内部)项目里程碑</span></th>
                <td colspan='3'><?php echo html::textarea('insideMilestone', $report->insideMilestone, "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th><span style="margin-left:-18px;">(外部)项目里程碑</span></th>
                <td colspan='3'><?php echo html::textarea('outsideMilestone', $report->outsideMilestone, "rows='5' class='form-control'");?></td>
            </tr>
            <tr>
                <th>项目移交状况</th>
                <td colspan='3'><?php echo html::textarea('transDesc', $report->transDesc, "rows='3' class='form-control'");?></td>
            </tr>
            <tr>
                <th>备注</th>
                <td colspan='3'><?php echo html::textarea('remark', $report->remark, "rows='3' class='form-control'");?></td>
            </tr>


            <tr>
                <td colspan='3' class='text-center form-actions'>
                    <?php echo html::submitButton();?>
                    <?php echo html::linkButton('返回','weeklyreport-index-'. $report->projectId.'.html#app=project','self','','btn btn-wide');?>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php include '../../common/view/footer.modal.html.php';?>

