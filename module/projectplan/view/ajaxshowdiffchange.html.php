<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('weekend', $config->execution->weekend);?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->projectplan->planChangeInfo;?><span class="text-red">(<?php echo $lang->projectplan->planChangePromptInfo;?>)</span></h2>
        </div>
        <form class="load-indicator main-form form-ajax"  id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class="w-150px <?php if(in_array('year',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->year;?></th>
                    <td class="required"><?php echo html::input('year', $plan->year, "class='form-control' maxlength='4' disabled");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('type',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->type;?></span>
                            <?php
                            unset($lang->projectplan->typeList['']);
                            echo html::select('type', $lang->projectplan->typeList, $plan->type, "class='form-control chosen'  disabled");?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th class="<?php if(in_array('name',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->name;?></th>
                    <td colspan='2' class="required"><?php echo html::input('name', $plan->name, "class='form-control' maxlength='100' disabled");?></td>
                </tr>

                <tr>
                    <th class="<?php if(in_array('outsideProject',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->outsideProject;?></th>
                    <td colspan='2'><?php echo html::select('outsideProject[]', $outsideProject, $plan->outsideProject, "class='form-control chosen' multiple onchange='setSubProjectField(this)' disabled");?></td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('outsideSubProject',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->outsideSubProject;?></th>
                    <td colspan='2'><?php echo html::select('outsideSubProject[]', $outsideSubProject, $plan->outsideSubProject, "class='form-control chosen' multiple disabled");?></td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('outsideTask',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->outsideTask;?></th>
                    <td colspan='2'><?php echo html::select('outsideTask[]', $outsideTask, $plan->outsideTask, "class='form-control chosen' multiple disabled");?></td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('basis',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->basis;?></th>
                    <td class="required"><?php echo html::select('basis[]', $lang->projectplan->basisList, $plan->basis, "class='form-control chosen' multiple disabled");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('category',$changefield)){ echo 'red';}  ?>"><?php echo $lang->opinion->category;?></span>
                            <?php echo html::select('category', $lang->opinion->categoryList, $plan->category, "class='form-control chosen' disabled");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('app',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->app;?></th>
                    <td class="required">  <?php echo html::select('app[]', $apps, $plan->app, "class='form-control chosen' multiple disabled");?></td>
                    <td>
                        <div class='input-group'>
                            <span class="input-group-addon <?php if(in_array('platformowner',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->platformowner;?></span>
                            <?php echo html::select('platformowner[]', $lang->projectplan->platformownerList, $plan->platformowner, "class='form-control chosen' multiple disabled");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('isImportant',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->isImportant;?></th>
                    <td class="required"><?php echo html::select('isImportant', $lang->projectplan->isImportantList, $plan->isImportant, "class='form-control chosen' disabled");?>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('bearDept',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->bearDept;?></span>
                            <?php echo html::select('bearDept[]', $depts, $plan->bearDept, "class='form-control chosen' multiple disabled");?>
                        </div>
                    </td>
                </tr>

                <tr <?php if($plan->secondLine != 1) echo 'class="hidden"' ?> >
                    <th class="<?php if(in_array('secondLine',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->secondLine;?></th>
                    <td colspan="2"><?php echo html::radio('secondLine', $lang->projectplan->secondLineList, $plan->secondLine);?></td>

                </tr>
                <tr class="tohide hidden">
                    <th class="<?php if(in_array('storyStatus',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->storyStatus;?></th>
                    <td><?php echo html::select('storyStatus', $lang->projectplan->storyStatusList, $plan->storyStatus, "class='form-control chosen' disabled");?></td>
                    <td>
                        <div class='input-group'>
                            <span class="input-group-addon <?php if(in_array('dataEnterLake',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->dataEnterLake;?></span>
                            <?php  echo html::select('dataEnterLake', ['' => ''] + $lang->projectplan->dataEnterLakeList,$plan->dataEnterLake, "class='form-control chosen' disabled"); ?>
                        </div>
                    </td>
                </tr>
                <tr class="tohide hidden">
                    <th class="<?php if(in_array('basicUpgrade',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->basicUpgrade;?></th>
                    <td ><?php echo html::select('basicUpgrade', ['' => ''] + $lang->projectplan->basicUpgradeList, $plan->basicUpgrade, "class='form-control chosen' disabled");?></td>
                    <td>
                        <div class='input-group'>
                            <span class="input-group-addon <?php if(in_array('systemAssemble',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->systemAssemble;?></span>
                            <?php echo html::select('systemAssemble', $lang->projectplan->systemAssembleList, $plan->systemAssemble, "class='form-control chosen' disabled");?>
                        </div>
                    </td>
                </tr>
                <tr class="tohide hidden">
                    <th class="<?php if(in_array('cloudComputing',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->cloudComputing;?></th>
                    <td ><?php echo html::select('cloudComputing', $lang->projectplan->cloudComputingList, $plan->cloudComputing, "class='form-control chosen' disabled");?></td>
                    <td>
                        <div class='input-group'>
                            <span class="input-group-addon <?php if(in_array('passwordChange',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->passwordChange;?></span>
                            <?php echo html::select('passwordChange', $lang->projectplan->passwordChangeList, $plan->passwordChange, "class='form-control chosen' disabled");?>
                        </div>
                    </td>
                </tr>
                <tr class="tohide hidden">
                    <th <?php if(in_array('structure',$changefield)){ echo 'red';}  ?>><?php echo $lang->projectplan->structure;?></th>
                    <td ><?php  echo html::input('structure', $plan->structure, "class='form-control' maxlength='100' disabled"); ?></td>
                    <td>
                        <div class='input-group'>
                            <span class="input-group-addon <?php if(in_array('localize',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->localize;?></span>
                            <?php echo html::select('localize', $lang->projectplan->localizeList, $plan->localize, "class='form-control chosen' disabled");?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th class="<?php if(in_array('owner',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->owner;?></th>
                    <td class="required"><?php echo html::select('owner[]', $users, $plan->owner, "class='form-control chosen' multiple disabled");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('phone',$changefield)){ echo 'red';}  ?>" > <?php echo $lang->projectplan->phone;?></span>
                            <?php echo html::input('phone', $plan->phone, "class='form-control' maxlength='100' disabled");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('begin',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->begin;?></th>
                    <td class="required"><?php echo html::input('begin', $plan->begin, "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' onchange='computeWorkDays()' disabled");?></td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('end',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->end;?></span>
                            <?php echo html::input('end', $plan->end, "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' onchange='computeWorkDays()' disabled");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('workloadBase',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->workloadBase;?></th>
                    <td>
                        <div class='input-group  required'>
                            <?php echo html::input('workloadBase', $plan->workloadBase, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10' disabled");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('workloadChengdu',$changefield)){ echo 'red';}  ?>" ><?php echo $lang->projectplan->workloadChengdu;?></span>
                            <?php echo html::input('workloadChengdu', $plan->workloadChengdu, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10' disabled");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('nextYearWorkloadBase',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->nextYearWorkloadBase;?></th>
                    <td>
                        <div class='input-group  required'>
                            <?php echo html::input('nextYearWorkloadBase', $plan->nextYearWorkloadBase, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10' disabled");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('nextYearWorkloadChengdu',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->nextYearWorkloadChengdu;?></span>
                            <?php echo html::input('nextYearWorkloadChengdu', $plan->nextYearWorkloadChengdu, "class='form-control' placeholder = '若不涉及填写“0”' onblur='totalWorkload(this)' maxlength='10' disabled");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('workload',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->workload;?></th>
                    <td>
                        <div class='input-group required'>
                            <?php echo html::input('workload', $plan->workload, "placeholder = '自动计算' class='form-control' onblur='totalWorkload(this)' maxlength='10' disabled");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
                        </div>
                    </td>
                    <td>
                        <div class='input-group required'>
                            <span class="input-group-addon <?php if(in_array('duration',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->duration;?></span>
                            <?php echo html::input('duration', $plan->duration, "class='form-control' onkeyup='value=value.replace(/[^\d]/g,\"\")' maxlength='10' readonly");?>
                            <span class='input-group-addon'><?php echo $lang->projectplan->day;?></span>
                        </div>
                    </td>
                </tr>


                <tr>
                    <th class="<?php if(in_array('content',$changefield)){ echo 'red';}  ?>" ><?php echo $lang->projectplan->content;?></th>

                    <td colspan='2' id="contentTd" class="required" style="border:solid #838a9d 1px">
<?php echo $plan->content; ?>
<!--                        --><?php //echo html::textarea('content', $plan->content, "class='form-control' maxlength='1000' disabled");?>
                    </td>
                </tr>
                <tr>
                    <th class="<?php if(in_array('planStages',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->projectStages;?></th>

                    <td colspan='2'>
                        <?php $i = 0; foreach ($planStages as $planStage) { $i++; ?>
                            <div class='table-row'>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <span class='input-group-addon stageName'>第一阶段 </span>
                                        <span class='input-group-addon'>计划开始时间</span>
                                        <?php echo html::input('stageBegin[]', $planStage['stageBegin'], "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' disabled");?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束时间</span>
                                        <?php echo html::input('stageEnd[]', $planStage['stageEnd'], "class='form-control form-date'readonly=readonly style='background: #FFFFFF;'  disabled");?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>

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
                                        <?php echo html::input('stageBegin[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' disabled");?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束时间</span>
                                        <?php echo html::input('stageEnd[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' disabled");?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>

                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </td>

                </tr>
                <!--<tr>
                    <th class="<?php /*if(in_array('productsRelated',$changefield)){ echo 'red';}  */?>"><?php /*echo $lang->projectplan->productsPlanPeriod;*/?></th>

                    <td colspan='2'>
                        <?php /*$i = 0; foreach ($productsRelated as $productRelated) { $i++; */?>
                            <div class='table-row'>

                                <div class='table-col' style="width: 475px;">
                                    <div class='input-group productCol w-p100'>
                                        <span class='input-group-addon'>产品名称</span>
                                        <?php /*echo html::select('productIds[]', $products, $productRelated['productId'], "class='form-control chosen' disabled");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <span class='input-group-addon'>计划开始</span>
                                        <?php /*echo html::input('realRelease[]', $productRelated['realRelease'], "class='form-control form-date' maxlength='1000' disabled");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束</span>
                                        <?php /*echo html::input('realOnline[]', $productRelated['realOnline'], "class='form-control form-date'  maxlength='1000' disabled");*/?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>

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
                                        <?php /*echo html::select('productIds[]', $products, "", "class='form-control chosen' disabled");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <span class='input-group-addon'>计划开始</span>
                                        <?php /*echo html::input('realRelease[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' disabled");*/?>
                                    </div>
                                </div>
                                <div class='table-col '  >
                                    <div class='input-group  '>
                                        <span class='input-group-addon'>计划结束</span>
                                        <?php /*echo html::input('realOnline[]', "", "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' disabled");*/?>
                                    </div>
                                </div>
                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>

                                    </div>
                                </div>
                            </div>
                            <?php
/*                        }
                        */?>
                    </td>

                </tr>-->
                <tr>
                    <th class="<?php if(in_array('planRemark',$changefield)){ echo 'red';}  ?>"><?php echo $lang->projectplan->planRemarkChange;?></th>
                    <td colspan='2' style="border:solid #838a9d 1px">
                        <?php echo $plan->planRemark ?? ''; ?>

                    </td>
                </tr>

                <tr>

                    <td colspan='3' class="text-center"><a id="guanbiiframe" class="btn"><?php echo $lang->projectplan->close;?></a></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<?php include '../../common/view/footer.html.php';?>
<script>
    $(document).ready(function(){
        $("#guanbiiframe").click(function(){
            // $.zui.modalTrigger.close();
            window.parent.closeajaxdiff();
        })
        window.editor['content'].readonly(true);
        window.editor['planRemark'].readonly(true);
        $("#outsideSubProject").attr("disabled","disabled");
    })
</script>
<!--还原-->
