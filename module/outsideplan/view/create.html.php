<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('weekend', $config->execution->weekend);?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->outsideplan->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-140px'><?php echo $lang->outsideplan->year;?></th>
                    <td ><?php echo html::input('year', substr(helper::today(), 0, 4), "class='form-control' maxlength='4' onkeyup='value=value.replace(/[^\d]/g,\"\")'");?></td>
                    <td >
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->outsideplan->apptype;?></span>
                            <?php echo html::select('apptype',$lang->outsideplan->apptypeList, '', "class='form-control chosen' ");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-140px'><?php echo $lang->outsideplan->code;?></th>
                    <td><?php echo html::input('code', '', "class='form-control' ");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->outsideplan->historyCode;?></span>
                            <?php echo html::input('historyCode', '', "class='form-control'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->name;?></th>
                    <td colspan='2'><?php echo html::input('name', '', "class='form-control' placeholder='若无{$lang->outsideplan->name}，只有(外部)一级子项，则{$lang->outsideplan->name}等同于(外部)一级子项名称' maxlength='100'");?></td>
                </tr>



                <tr style="background-color: rgba(150,150,150,0.1);">
                    <td colspan='3' id="sub_1">
                        <div class="subBlocks">
                        <div style="float: left; width: 93%; margin-left: 35px;">
                            <table class="table table-form">
                                <tbody>
                                <tr>
                                    <th><?php echo $lang->outsideplan->subProjectName;?></th>
                                    <td colspan='2' class="required"><?php echo html::input('sub[subProjectName][]', '', "class='form-control required' placeholder='若无{$lang->outsideplan->subProjectName}，则{$lang->outsideplan->subProjectName}名称等同于{$lang->outsideplan->name}' maxlength='100'");?></td>
                                </tr>
                                </tbody>
                            </table></div>
                        <div style="float: right;   margin-top: 13px;">
                            <a class="input-group-btn" href="javascript:void(0)" onclick="addSub(this)" class="btn btn-link"><i class="icon-plus"></i></a>
                            <a class="input-group-btn" href="javascript:void(0)" onclick="delSub(this)" class="btn btn-link"><i class="icon-close"></i></a>
                        </div>
                        </div>
                    </td>
                </tr>


                <tr>
                    <th><?php echo $lang->outsideplan->begin;?></th>
                    <td><?php echo html::input('begin', '', "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' onchange='computeWorkDays()'");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->outsideplan->end;?></span>
                            <?php echo html::input('end', '', "class='form-control form-date' readonly=readonly style='background: #FFFFFF;' onchange='computeWorkDays()'");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->workload;?></th>
                    <td><?php echo html::input('workload', '', "class='form-control' onkeyup='value=value.replace(/[^\d]/g,\"\")'");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->outsideplan->duration;?></span>
                            <?php echo html::input('duration', '', "class='form-control' onkeyup='value=value.replace(/[^\d]/g,\"\")' ");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->status;?></th>
                    <td class=""><?php echo html::select('status', $lang->outsideplan->statusList, 'wait', "class='form-control chosen'");?></td>
                    <td class="required">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->outsideplan->maintainers;?></span>
                            <?php echo html::select('maintainers[]', $users,'', "class='form-control chosen' multiple");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->projectisdelay;?></th>
                    <td colspan="2"><?php echo html::select('projectisdelay', $lang->outsideplan->projectisdelayList, 0, "class='form-control chosen'");?></td>
                </tr>
                <tr id="projectisdelaydescWrap" style="display:none;">
                    <th><?php echo $lang->outsideplan->projectisdelaydesc;?></th>
                    <td  colspan='2'><?php echo html::textarea('projectisdelaydesc', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->projectischange;?></th>
                    <td colspan="2"><?php echo html::select('projectischange', $lang->outsideplan->projectischangeList, 0, "class='form-control chosen'");?></td>
                </tr>
                <tr id="projectischangedescWrap" style="display:none;">
                    <th><?php echo $lang->outsideplan->projectischangedesc;?></th>
                    <td  colspan='2'><?php echo html::textarea('projectischangedesc', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->projectinitplan;?></th>
                    <td  colspan='2'><?php echo html::textarea('projectinitplan', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->uatplanfinishtime;?></th>
                    <td  colspan='2'><?php echo html::textarea('uatplanfinishtime', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->materialplanonlinetime;?></th>
                    <td  colspan='2'><?php echo html::textarea('materialplanonlinetime', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->planonlinetime;?></th>
                    <td  colspan='2'><?php echo html::textarea('planonlinetime', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->milestone;?></th>
                    <td  colspan='2'><?php echo html::textarea('milestone', '', "class='form-control' maxlength='1000'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->changestatus;?></th>
                    <td  colspan='2'><?php echo html::textarea('changes', '', "class='form-control' maxlength='1000'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->remark;?></th>
                    <td colspan='2'><?php echo html::textarea('remark', '', "class='form-control' maxlength='1000'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->outsideplan->upfiles; ?></th>
                    <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85'); ?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::a(inlink('browse'), $lang->goback, '', "class='btn btn-back btn-wide'");?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(function(){
        $("#projectisdelay").change(function (){
            let projectisdelayVal = $(this).val();
            if(projectisdelayVal == 2){
                $("#projectisdelaydescWrap").show();
            }else{
                $("#projectisdelaydescWrap").hide();
                $("#projectisdelaydesc").val('');
            }
        })

        $("#projectischange").change(function (){
            let projectischangeVal = $(this).val();
            if(projectischangeVal == 2){
                $("#projectischangedescWrap").show();
            }else{
                $("#projectischangedescWrap").hide();
                $("#projectischangedesc").val('');
            }
        })
    })
</script>
<?php include '../../common/view/footer.html.php';?>
