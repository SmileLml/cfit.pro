<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('begin', $plan->begin);?>
<?php js::set('end',  $plan->end);?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->outsideplan->createTask;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform' onsubmit="return checkDate()">
            <table class="table table-form">
                <tbody>

                <tr>
                    <th><?php echo $lang->outsideplan->subProjectName;?></th>
                    <td colspan='2'><?php echo $outsideProject->subProjectName;?></td>
                </tr>
                <tr style="background-color: rgba(150,150,150,0.1);">
                    <td colspan='3' id="sub_1">
                        <?php

                        foreach ($tasks as $sub) { ?>
                            <div class="subBlocks">
                                <div style="float: left; width: 93%; margin-left: 35px;">
                                    <table class="table table-form">
                                        <tbody style="border-bottom: #dddddd solid thin; ">
                                        <tr>
                                            <th><?php echo $lang->outsideplan->subTaskName;?></th>
                                            <td colspan='2' class="required"><?php echo html::input('subTaskName', $sub->subTaskName, "placeholder='' class='form-control' maxlength='100'");?></td>
                                        </tr>
                                        <tr>
                                            <th class='w-140px'><?php echo $lang->outsideplan->subProjectDesc;?></th>
                                            <td colspan="2" class="required subProjectDesc"><?php echo html::textarea('subTaskDesc',$sub->subTaskDesc, "placeholder='若无(外部)子项/子任务描述，则(外部)子项/子任务名称和(外部)子项/子任务描述等同于{$lang->outsideplan->subProjectName}' class='form-control required' maxlength='1000'");?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $lang->outsideplan->subProjectBegin;?></th>
                                            <td ><?php echo html::input('subTaskBegin', $sub->subTaskBegin, "class='subTaskBegin form-control form-date' readonly=readonly style='background: #FFFFFF;' ");?></td>
                                            <td >
                                                <div class='input-group'>
                                                    <span class='input-group-addon'><?php echo $lang->outsideplan->subProjectEnd;?></span>
                                                    <?php echo html::input('subTaskEnd', $sub->subTaskEnd, "class='subTaskEnd form-control form-date' readonly=readonly style='background: #FFFFFF;' ");?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $lang->outsideplan->subProjectUnit;?></th>
                                            <td ><?php echo html::select('subTaskUnit[]', $lang->outsideplan->subProjectUnitList, $sub->subTaskUnit, "class='form-control chosen ' multiple");?></td>
                                            <td >
                                                <div class='input-group'>
                                                    <span class='input-group-addon'><?php echo $lang->outsideplan->subProjectBearDept;?></span>
                                                    <?php echo html::select('subTaskBearDept[]', $lang->application->teamList, $sub->subTaskBearDept, "class='form-control chosen ' multiple");?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $lang->outsideplan->subProjectDemandParty;?></th>
                                            <td ><?php echo html::select('subTaskDemandParty[]', $lang->outsideplan->subProjectDemandPartyList, $sub->subTaskDemandParty, "class='form-control chosen ' multiple");?></td>
                                            <td>
                                                <div class='input-group'>
                                                    <span class='input-group-addon'><?php echo $lang->outsideplan->subProjectDemandDeadline;?></span>
                                                <?php echo html::input('subTaskDemandDeadline', $sub->subTaskDemandDeadline, "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' ");?></td>
                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $lang->outsideplan->subProjectDemandContact;?></th>
                                            <td><?php echo html::input('subTaskDemandContact', $sub->subTaskDemandContact, "class='form-control' ");?>
                                            <td>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table></div>

                            </div>
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton('', '') . html::a(inlink('browse'), $lang->goback, '', "class='btn btn-back btn-wide'");?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
