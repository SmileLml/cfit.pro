<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->osspchange->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->proposer;?></th>
                    <td colspan='4'><?php echo html::select('proposer', $users, $this->app->user->account, 'class="form-control  chosen" required');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->osspchange->title;?></th>
                    <td colspan='4'><?php echo html::textarea('title', '', 'class="form-control" required style="height:130px"');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->background;?></th>
                    <td colspan='4'><?php echo html::textarea('background', '', 'class="form-control" required');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->content;?></th>
                    <td colspan='4'><?php echo html::textarea('content', '', 'class="form-control" required');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->files;?></th>
                    <td colspan='4' class="required"><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                </tr>
                <tr>
<!--                    <input type="hidden" name="issubmit" value="save">-->
<!--                    <td class='form-actions text-center' colspan='4'>--><?php //echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') . html::commonButton($lang->osspchange->submit, '', 'btn btn-wide btn-primary submitBtn') . html::backButton();?><!--</td>-->
                    <th class="w-120px"></th>
                    <td class='form-actions text-center' colspan='4'>
                        <input type="hidden" name="type" value="save">
<!--                        --><?php //echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') .html::submitButton($lang->osspchange->submit) . html::backButton();?>
                        <button type="button" class="btn btn-wide btn-primary saveBtn">保存</button>
                        <button type="button" class="btn btn-wide btn-primary submitBtn">提交</button>
                        <button type="button" class="btn btn-wide btn-back backBtn">返回</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(".saveBtn").click(function () {
        $("[name='type']").val('save');
        // $(this).addClass("submitBtn")
        // $(".submitBtn").attr("id",'')
        $("#dataform").submit()
    })
    $(".submitBtn").click(function () {
        $("[name='type']").val('submit')
        $("#dataform").submit()
    })
    $(".backBtn").click(function () {
        window.history.go(-1);
    })
</script>
<?php include '../../common/view/footer.html.php'?>
