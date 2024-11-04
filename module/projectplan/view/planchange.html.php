<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('weekend', $config->execution->weekend);?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <span class="btn btn-link btn-active-text"><span class="text">变更年度计划</span></span>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform' onsubmit="return checkTaskDate()">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-150px'><?php echo $lang->projectplan->year;?></th>
                    <td class="required"><?php echo html::input('year', $plan->year, "class='form-control' maxlength='4'");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->type;?></span>
                            <?php
                            unset($lang->projectplan->typeList['']);
                            echo html::select('type', $lang->projectplan->typeList, $plan->type, "class='form-control chosen' onchange='changeTypeToHide()'");?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->projectplan->name;?></th>
                    <td colspan='2' class="required"><?php echo html::input('name', $plan->name, "class='form-control' maxlength='100'");?></td>
                </tr>

                <!--<tr>
                    <th><?php /*echo $lang->projectplan->outsideProject;*/?></th>
                    <td colspan='2'><?php /*echo html::select('outsideProject[]', $outsideProject, $plan->outsideProject, "class='form-control chosen' multiple onchange='setSubProjectField(this)'");*/?></td>
                </tr>
                <tr>
                    <th><?php /*echo $lang->projectplan->outsideSubProject;*/?></th>
                    <td colspan='2'><?php /*echo html::select('outsideSubProject[]', $outsideSubProject, $plan->outsideSubProject, "class='form-control chosen' multiple");*/?></td>
                </tr>-->
                <tr>
                    <th><?php echo $lang->projectplan->outsideTask;?></th>
                    <td colspan='2'><?php echo html::select('outsideTask[]', $outsideTask, $plan->outsideTask, "class='form-control chosen'  multiple onchange='setNewSubProjectField(this)'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->outsideProject;?></th>
                    <td colspan='2'>

                        <div id="outsideProjectShow" style="background-color:#f5f5f5;border: 1px solid #dcdcdc;border-radius: 2px;padding: 5px 8px;line-height:1.6em;min-height:33px;"><?php echo $outsideNames; ?></div>
                        <?php echo html::input('outsideProject', $outsideIDs, "class='form-control' style='display:none;' readonly='readonly' ");?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->outsideSubProject;?></th>
                    <td colspan='2'>

                        <div id="outsideSubProjectShow" style="background-color:#f5f5f5;border: 1px solid #dcdcdc;border-radius: 2px;padding: 5px 8px;line-height:1.6em;min-height:33px;"><?php echo $subOutsideNames; ?></div>
                        <?php echo html::input('outsideSubProject', $subOutsideIDs,  "class='form-control ' style='display:none;' readonly='readonly' ");?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->projectplan->basis;?></th>
                    <td class="required"><?php echo html::select('basis[]', $lang->projectplan->basisList, $plan->basis, "class='form-control chosen' multiple");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->opinion->category;?></span>
                            <?php echo html::select('category', $lang->opinion->categoryList, $plan->category, "class='form-control chosen'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->app;?></th>
                    <td class="required">  <?php echo html::select('app[]', $apps, $plan->app, "class='form-control chosen' multiple");?></td>
                    <td id="platformownerTD">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->platformowner;?></span>
                            <?php echo html::select('platformowner[]', $lang->projectplan->platformownerList, $plan->platformowner, "class='form-control chosen' multiple");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->isImportant;?></th>
                    <td class="required"><?php echo html::select('isImportant', $lang->projectplan->isImportantList, $plan->isImportant, "class='form-control chosen'");?>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->bearDept;?></span>
                            <?php echo html::select('bearDept', $depts, $plan->bearDept, "class='form-control chosen' ");?>
                        </div>
                    </td>
                </tr>

                <tr class="hidden">
                    <th><?php echo $lang->projectplan->secondLine;?></th>
                    <td colspan="2"><?php echo html::radio('secondLine', $lang->projectplan->secondLineList, "0");?></td>
                </tr>
                <tr class="tohide hidden">
                    <th><?php echo $lang->projectplan->storyStatus;?></th>
                    <td class="required"><?php echo html::select('storyStatus', $lang->projectplan->storyStatusList, $plan->storyStatus, "class='form-control chosen'");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->dataEnterLake;?></span>
                            <?php  echo html::select('dataEnterLake', ['' => ''] + $lang->projectplan->dataEnterLakeList,$plan->dataEnterLake, "class='form-control chosen'"); ?>
                        </div>
                    </td>
                </tr>
                <tr class="tohide hidden">
                    <th><?php echo $lang->projectplan->basicUpgrade;?></th>
                    <td ><?php echo html::select('basicUpgrade', ['' => ''] + $lang->projectplan->basicUpgradeList, $plan->basicUpgrade, "class='form-control chosen'");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->systemAssemble;?></span>
                            <?php echo html::select('systemAssemble', $lang->projectplan->systemAssembleList, $plan->systemAssemble, "class='form-control chosen'");?>
                        </div>
                    </td>
                </tr>
                <tr class="tohide hidden">
                    <th><?php echo $lang->projectplan->cloudComputing;?></th>
                    <td ><?php echo html::select('cloudComputing', $lang->projectplan->cloudComputingList, $plan->cloudComputing, "class='form-control chosen'");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->passwordChange;?></span>
                            <?php echo html::select('passwordChange', $lang->projectplan->passwordChangeList, $plan->passwordChange, "class='form-control chosen'");?>
                        </div>
                    </td>
                </tr>
                <tr class="tohide hidden">
                    <th><?php echo $lang->projectplan->structure;?></th>
                    <td ><?php  echo html::input('structure', $plan->structure, "class='form-control' maxlength='100'"); ?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->localize;?></span>
                            <?php echo html::select('localize', $lang->projectplan->localizeList, $plan->localize, "class='form-control chosen'");?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->projectplan->owner;?></th>
                    <td class="required"><?php echo html::select('owner', $users, $plan->owner, "class='form-control chosen'");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->phone;?></span>
                            <?php echo html::input('phone', $plan->phone, "class='form-control' maxlength='100'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->begin;?></th>
                    <td class="required"><?php echo html::input('begin', $plan->begin, "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' onchange='computeWorkDays()'");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->end;?></span>
                            <?php echo html::input('end', $plan->end, "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' onchange='computeWorkDays()'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->workloadBase;?></th>
                    <td>
                        <div class='input-group  required'>
                            <?php echo html::input('workloadBase', $plan->workloadBase, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10'");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->workloadChengdu;?></span>
                            <?php echo html::input('workloadChengdu', $plan->workloadChengdu, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10'");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->nextYearWorkloadBase;?></th>
                    <td>
                        <div class='input-group  required'>
                            <?php echo html::input('nextYearWorkloadBase', $plan->nextYearWorkloadBase, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10'");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->nextYearWorkloadChengdu;?></span>
                            <?php echo html::input('nextYearWorkloadChengdu', $plan->nextYearWorkloadChengdu, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10'");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->workload;?></th>
                    <td>
                        <div class='input-group required'>
                            <?php echo html::input('workload', $plan->workload, "placeholder = '自动计算' class='form-control' readonly onkeyup='value=value.replace(/[^\d]/g,\"\")' maxlength='10'");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->projectplan->duration;?></span>
                            <?php echo html::input('duration', $plan->duration, "class='form-control' onkeyup='value=value.replace(/[^\d]/g,\"\")' maxlength='10' readonly");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->day;?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->content;?></th>

                    <td colspan='2' id="contentTd" class="required">
                        <?php echo $this->fetch('user', 'ajaxPrintTemplates', 'type=projectplan&link=content');?>
                        <?php echo html::textarea('content', $plan->content, "class='form-control' maxlength='1000'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->projectStages;?></th>

                    <td colspan='2'>
                        <?php $i = 0; foreach ($planStages as $planStage) { $i++; ?>
                            <div class='table-row'>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <span class='input-group-addon stageName'>第一阶段 </span>
                                        <span class='input-group-addon'>计划开始时间</span>
                                        <?php echo html::input('stageBegin[]', $planStage['stageBegin'], "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' ");?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束时间</span>
                                        <?php echo html::input('stageEnd[]', $planStage['stageEnd'], "class='form-control form-date'readonly=readonly style='background: #FFFFFF;'  ");?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>
                                        <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addStage(this)'");?>
                                        <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeStage(this)'");?>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        if($i == 0 ){
                            ?>
                            <div class='table-row'>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <span class='input-group-addon stageName'>第一阶段 </span>
                                        <span class='input-group-addon'>计划开始时间</span>
                                        <?php echo html::input('stageBegin[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;'");?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束时间</span>
                                        <?php echo html::input('stageEnd[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' ");?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>
                                        <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addStage(this)'");?>
                                        <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeStage(this)'");?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </td>

                </tr>
                <!--<tr>
                    <th><?php /*echo $lang->projectplan->productsPlanPeriod;*/?></th>

                    <td colspan='2'>
                        <?php /*$i = 0; foreach ($productsRelated as $productRelated) { $i++; */?>
                            <div class='table-row'>

                                <div class='table-col' style="width: 475px;">
                                    <div class='input-group productCol w-p100'>
                                        <span class='input-group-addon'>产品名称</span>
                                        <?php /*echo html::select('productIds[]', $products, $productRelated['productId'], "class='form-control chosen'");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <span class='input-group-addon'>计划开始</span>
                                        <?php /*echo html::input('realRelease[]', $productRelated['realRelease'], "class='form-control form-date' maxlength='1000'");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束</span>
                                        <?php /*echo html::input('realOnline[]', $productRelated['realOnline'], "class='form-control form-date'  maxlength='1000'");*/?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>
                                        <?php /*echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");*/?>
                                        <?php /*echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this)'");*/?>
                                    </div>
                                </div>
                            </div>
                        <?php /*}
                        if($i == 0 ){
                            */?>
                            <div class='table-row'>

                                <div class='table-col' style="width: 475px;">
                                    <div class='input-group productCol w-p100'>
                                        <span class='input-group-addon'>产品名称</span>
                                        <?php /*echo html::select('productIds[]', $products, "", "class='form-control chosen' ");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <span class='input-group-addon'>计划开始</span>
                                        <?php /*echo html::input('realRelease[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;'");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束</span>
                                        <?php /*echo html::input('realOnline[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' ");*/?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>
                                        <?php /*echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");*/?>
                                        <?php /*echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this)'");*/?>
                                    </div>
                                </div>
                            </div>
                            <?php
/*                        }
                        */?>
                    </td>

                </tr>-->
                <tr>
                    <th></th>
                    <td class="text-red"><?php echo $lang->projectplan->planRemarkDesc?> </td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->planRemarkChange;?></th>
                    <td colspan='2'><?php echo html::textarea('planRemark', '',   "class='form-control' maxlength='1000'  placeholder='" . htmlspecialchars($lang->projectplan->specTemplate)."'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', $plan->comment ?? '', "class='form-control' maxlength='1000'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($lang->submit) . html::a(inlink('browse'), $lang->goback, '', "class='btn btn-back btn-wide'");?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
    function changeTypeToHide()
    {
        let type = $("#type").val();
        if(type == 1 || type == 2){
            $(".tohide").removeClass('hidden');
            $("#platformownerTD").addClass('required');
        } else {
            $(".tohide").addClass('hidden');
            $("#platformownerTD").removeClass('required');
        }


        var content = editor['content'].html();
        if(content == '' || content== '<p></p>' || content == '<p><br /></p>'){
            var projectPlanContentTemptlate = <?php echo json_encode($lang->projectplan->projectPlanContentTemptlate) ?>;
            var cmd     = editor['content'].edit.cmd;
            editor['content'].html('');
            cmd.inserthtml(projectPlanContentTemptlate[type]);
            editor['content'].templateHtml = editor['content'].html();
        }

        return true;
    }
    $(function(){
        $(window).load(function(){
            changeTypeToHide();
        });
    });
</script>