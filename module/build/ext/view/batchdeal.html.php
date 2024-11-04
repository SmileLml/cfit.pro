<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
    .task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
    .task-toggle .icon{display: inline-block; transform: rotate(90deg);}
    .more-tips{display: none;}
    .close-tips{display: none}
</style>
<?php $urlParams = "projectId=$build->project&productId=$build->product&productVersion=$build->version&buildId=$build->id"; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->build->deal;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">

                <?php if($build->status == 'waittest'):?>
                    <tr>
                        <th class='w-140px'></th>
                        <td colspan="10" style="color:red;"><?php echo $lang->build->notice;;?></td>
                    </tr>
                <?php endif;?>
                <!-- <tr>
                        <th class='w-140px'><?php /*echo $lang->build->name;*/?></th>
                        <td colspan="10"><?php /*echo html::input('name', $build->name, "class='form-control ' ");*/?></td>
                </tr>-->

                <?php if($build->status == 'waittest' && $isQualityGate):?>
                    <tr>
                        <th class='w-140px'><?php echo $lang->qualitygate->severityTest;?></th>
                        <td colspan="10">
                            <?php echo $this->qualitygate->diffColorStatus($severityTestResult); ?>
                        </td>
                    </tr>

                    <?php if($severityTestResult == $lang->qualitygate->statusArray['finish']):?>
                        <tr>
                            <th class='w-140px'><?php echo $lang->qualitygate->qualitygate;?></th>
                            <td colspan="10">
                                <?php echo $this->qualitygate->diffSeverityGateResult($severityGateResult);?>
                                <span style="margin-left: 40px;">
                            <?php echo html::a($this->createLink('report', 'qualityGateCheckResult', $urlParams, '', true).'#app=project', '点击查看详情',  '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'");?>
                        </span>
                            </td>
                        </tr>
                    <?php endif;?>
                <?php endif;?>

                <?php if($build->status == 'waitdeptmanager'):?>
                    <tr>
                        <th class='w-140px'><?php echo $lang->qualitygate->qualitygate;?></th>
                        <td colspan="10">

                            <?php echo $this->qualitygate->diffSeverityGateResult($severityGateResult);?>
                            <span style="margin-left: 40px;">
                            <?php echo html::a($this->createLink('report', 'qualityGateCheckResult', $urlParams, '', true).'#app=project', '点击查看详情',  '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'");?>
                        </span>
                        </td>
                    </tr>

                    <tr>
                        <th class='w-140px'><?php echo $lang->build->specialPassReason;?></th>
                        <td colspan="10">
                            <?php echo $build->specialPassReason;?>
                        </td>
                    </tr>
                <?php endif;?>

                <tr class="hidden dev">
                    <th class='w-140px'><?php echo $lang->build->result;?></th>
                    <td colspan="10"><?php echo html::select('status', $statusList, '',"class='form-control chosen ' required");?></td>
                    <input type="hidden" value="<?php echo $build->status;?>" name='oldstatus' id = 'oldstatus'/>
                </tr>
                <tr class="hidden dev2">
                    <th class='w-140px'><?php echo $lang->build->releaseName;?></th>
                    <td colspan="10"><?php echo html::input('releaseName',  $build->name,"class='form-control  ' ");?></td>
                </tr>
                <tr class="hidden dev2">
                    <th class='w-140px'><?php echo $lang->build->releasePath;?></th>
                    <td colspan="10"><?php echo html::input('releasePath', '',"class='form-control  ' ");?></td>
                </tr>
                <tr>
                    <th class='w-140px'><?php echo $lang->build->filePath;?></th>
                    <td colspan="10"><?php echo html::input('filePath', '', "class='form-control '  placeholder='{$lang->build->placeholder->filePathTip}'");?></td>
                </tr>
                <!--<tr>
                     <th class='w-140px'><?php /*echo $lang->build->consumed;*/?></th>
                     <td colspan="10"><?php /*echo html::input('consumed', '', "class='form-control ' required");*/?>
                         <span style="color: lightslategray" colspan='10'><?php /*echo $this->lang->build->dealTip */?></span>
                     </td>
                 </tr>-->

                <?php if ($build->status != "waitdeptmanager"):?>
                    <tr id='relevantDept1'>
                        <th class='w-140px'><?php echo $lang->build->relevantDept;?></th>
                        <td colspan="10">
                            <div class='table-row'>
                                <div class='table-col'>
                                    <?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen'");?>
                                </div>
                                <!-- <div class='table-col'>
                                     <div class='input-group'>
                                         <span class='input-group-addon fix-border'><?php /*echo $lang->build->workload;*/?></span>
                                         <?php /*echo html::input('workload[]', '', "class='form-control'");*/?>
                                     </div>
                                 </div>-->
                            </div>
                        </td>
                        <td class="c-actions">
                            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>
                        </td>
                    </tr>
                <?php endif;?>
                <tr class="hidden dev2">
                    <th class='w-140px'><?php echo $lang->build->plateName;?></th>
                    <td colspan='10'><?php echo html::textarea('plateName', '', "rows='10' class='form-control kindeditor' hidefocus='true'  placeholder='{$lang->build->placeholder->plateTip}'");?></td>
                </tr>
                <tr class="hidden dev2">
                    <th><?php echo $lang->files;?></th>
                    <td colspan='10'><?php echo $this->fetch('file', 'buildform');?></td>
                </tr>
                <tr>
                    <th class='w-140px'><?php echo $lang->build->desc;?></th>
                    <td colspan='10' class="required"><?php echo html::textarea('comment', '', "rows='10' class='form-control kindeditor' hidefocus='true'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='12'><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<table class='hidden'>
    <tbody id="relevantDeptTable">
    <tr id='relevantDept0'>
        <th class='w-140px'><?php echo $lang->build->relevantDept;?></th>
        <td colspan='10'>
            <div class='table-row'>
                <div class='table-col'>
                    <?php echo html::select('relevantUser[]', $users, '', "class='form-control' id='relevantUser0'");?>
                </div>
                <!--<div class='table-col'>
                    <div class='input-group'>
                        <span class='input-group-addon fix-border'><?php /*echo $lang->build->workload;*/?></span>
                        <?php /*echo html::input('workload[]', '', "class='form-control'");*/?>
                    </div>
                </div>-->
            </div>
        </td>
        <td class="c-actions">
            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>
<?php echo js::set('status', $build->status);?>

<?php include '../../../common/view/footer.html.php';?>
