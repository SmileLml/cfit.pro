<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .inoneline{white-space: NOWRAP;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->copyrightqz->reviewNodeList[$copyrightqz->reviewStage]; ?></h2>
        </div>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->copyrightqz->result; ?></th>
                        <td class="required"><?php echo html::select('result', $lang->copyrightqz->confirmList, '', "class='form-control chosen' onchange='rejectShow(this.value)'"); ?></td>
                    </tr>
                    <tr id='rejectReasonTr' class="hidden">
                        <th><?php echo $lang->copyrightqz->rejectReason; ?></th>
                        <td colspan='2' class="required"><?php echo html::input('rejectReason', '', "class='form-control'"); ?></td>
                    </tr>
                   <!-- <tr>
                        <th class='inoneline'><?php /*echo $lang->copyrightqz->consumed;*/?></th>
                        <td><?php /*echo html::input('consumed', '', "class='form-control' required placeholder='小数点后保留1位小数'");*/?></td>
                    </tr>-->
                    <tr>
                        <th><?php echo $lang->copyrightqz->comment; ?></th>
                        <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'>
                            <!--保存初始审核节点-->
                            <input type="hidden" name = "changeVersion" value="<?php echo $copyrightqz->changeVersion; ?>">
                            <input type="hidden" name = "reviewStage" value="<?php echo $copyrightqz->reviewStage; ?>">
                            <?php echo html::submitButton($lang->copyrightqz->save) . html::backButton(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
    </div>
</div>
<script>
    function rejectShow(isReject) {
        if (isReject == 'reject') {
            $('#rejectReasonTr').removeClass('hidden');
        } else {
            $('#rejectReasonTr').addClass('hidden');
        }
    }

</script>
<?php include '../../common/view/footer.html.php'; ?>
