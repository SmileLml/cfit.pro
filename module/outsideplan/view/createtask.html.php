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
                        <div class="subBlocks">
                        <div style="float: left; width: 93%; margin-left: 35px;">
                            <table class="table table-form">
                                <tbody style="border-bottom: #dddddd solid thin;">
                                <tr>
                                    <th><?php echo $lang->outsideplan->subTaskName;?></th>
                                    <td colspan='2' class="required"><?php echo html::input('subTaskName', '', "class='form-control required' placeholder='' maxlength='100'");?></td>
                                </tr>
                                <tr>
                                    <th class='w-140px'><?php echo $lang->outsideplan->subTaskDesc;?></th>
                                    <td colspan="2" class="required"><?php echo html::textarea('subTaskDesc',"", "class='form-control' placeholder='若无具体(外部)子项/子任务描述，则(外部)子项/子任务描述等同于(外部)一级子项' maxlength='1000'");?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outsideplan->subTaskBegin;?></th>
                                    <td ><?php echo html::input('subTaskBegin', '', "class='subTaskBegin form-control form-date' readonly=readonly style='background: #FFFFFF;'");?></td>
                                    <td >
                                        <div class='input-group'>
                                            <span class='input-group-addon'><?php echo $lang->outsideplan->subTaskEnd;?></span>
                                            <?php echo html::input('subTaskEnd', '', "class='subTaskEnd form-control form-date' readonly=readonly style='background: #FFFFFF;'");?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outsideplan->subTaskUnit;?></th>
                                    <td ><?php echo html::select('subTaskUnit[]', $lang->outsideplan->subProjectUnitList, '', "class='form-control chosen ' multiple");?></td>
                                    <td >
                                        <div class='input-group'>
                                            <span class='input-group-addon'><?php echo $lang->outsideplan->subTaskBearDept;?></span>
                                            <?php echo html::select('subTaskBearDept[]', $lang->application->teamList,'', "class='form-control chosen ' multiple ");?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outsideplan->subTaskDemandParty;?></th>
                                    <td ><?php echo html::select('subTaskDemandParty[]', $lang->outsideplan->subProjectDemandPartyList,'', "class='form-control chosen ' multiple ");?></td>
                                    <td>
                                        <div class='input-group'>
                                            <span class='input-group-addon'><?php echo $lang->outsideplan->subTaskDemandDeadline;?></span>
                                            <?php echo html::input('subTaskDemandDeadline', '', "class='form-control form-date' readonly=readonly style='background: #FFFFFF;'");?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outsideplan->subTaskDemandContact;?></th>
                                    <td><?php echo html::input('subTaskDemandContact', '', "class='form-control' ");?></td>
                                    <td>
                                    </td>
                                </tr>
                                </tbody>
                            </table></div>

                        </div>
                    </td>
                </tr>

                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::a(inlink('browse'), $lang->goback, '', "class='btn btn-back btn-wide'");?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
