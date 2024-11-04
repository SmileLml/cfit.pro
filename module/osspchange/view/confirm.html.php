<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->osspchange->confirm;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->proposer;?></th>
                    <td colspan='4'><?php echo html::select('proposer', $users, $osspchange->proposer, 'class="form-control  chosen" required disabled');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->osspchange->title;?></th>
                    <td colspan='4'><?php echo html::textarea('title', $osspchange->title, 'class="form-control" required readonly style="height:100px"');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->background;?></th>
                    <td colspan='4'><?php echo html::textarea('background', $osspchange->background, 'class="form-control" required readonly style="height:100px"');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->content;?></th>
                    <td colspan='4'><?php echo html::textarea('content', $osspchange->content, 'class="form-control" required readonly style="height:100px"');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->osspchange->filelist;?></th>
                    <td colspan='4'>
                        <div class='detail'>
                            <div class='detail-content article-content'>
                                <?php
                                if($osspchange->files){
                                    echo $this->fetch('file', 'printFiles', array('files' => $osspchange->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => true));
                                }else{
                                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->systemProcess;?></th>
                    <td colspan='2'><?php echo html::select('systemProcess', $this->lang->osspchange->systemProcessList,$osspchange->systemProcess, 'class="form-control  chosen" required');?></td>
                    <td colspan='2'>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->osspchange->systemVersion;?></span>
                            <?php echo html::select('systemVersion', $this->lang->osspchange->systemVersionList,$osspchange->systemVersion, 'class="form-control chosen" required');?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->advise;?></th>
                    <td colspan='4'><?php echo html::textarea('advise', $osspchange->status == $lang->osspchange->statusList['rejectToConfirm'] ? $osspchange->advise : '', 'class="form-control" required');?></td>
                </tr>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->reviewResult;?></th>
                    <td colspan='4'><?php echo html::select('result', $this->lang->osspchange->resultList, $osspchange->status == $lang->osspchange->statusList['rejectToConfirm'] ? $osspchange->result : '', 'class="form-control  chosen" required');?></td>
                </tr>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->changeNotice;?></th>
                    <td colspan='4'><?php echo html::select('changeNotice', $this->lang->osspchange->changeNoticeList, $osspchange->changeNotice, 'class="form-control  chosen" required');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->systemDept;?></th>
                    <td colspan='2'><?php echo html::select('systemDept', $depts,$osspchange->systemDept, 'class="form-control  chosen" required');?></td>
                    <td colspan='2'>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->osspchange->systemManager;?></span>
                            <?php echo html::select('systemManager', $users,$osspchange->systemManager, 'class="form-control chosen" required');?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->QMDmanager;?></th>
                    <td colspan='4'><?php echo html::select('QMDmanager', $users, $osspchange->QMDmanager ? $osspchange->QMDmanager : $QMDmanager, 'class="form-control  chosen" required');?></td>
                </tr>
                <td class='form-actions text-center' colspan='5'>
                    <input type="hidden" name="type" value="save">
                    <!--                        --><?php //echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') .html::submitButton($lang->osspchange->submit) . html::backButton();?>
                    <button type="button" class="btn btn-wide btn-primary saveBtn">保存</button>
                    <button type="button" class="btn btn-wide btn-primary submitBtn">提交</button>
                </td>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(".saveBtn").click(function () {
        $("[name='type']").val('save');
        $("#dataform").submit()
    })
    $(".submitBtn").click(function () {
        $("[name='type']").val('submit')
        $("#dataform").submit()
    })
    $('#submit').click(function()
    {
        $('#proposer').removeAttr('disabled');
    });
    $(document).ready(function(){
        window.editor['background'].readonly(true);
    })
    $(document).ready(function(){
        window.editor['content'].readonly(true);
    })
    $('#systemDept').change(function(){
        var dept = $(this).val();
        $.get(createLink('osspchange', 'ajaxGetSystemManager', 'dept=' + dept), function(data)
        {
            $('#systemManager_chosen').remove();
            $('#systemManager').replaceWith(data);
            $('#systemManager').chosen();
        });
    })
</script>
<?php include '../../common/view/footer.html.php'?>
