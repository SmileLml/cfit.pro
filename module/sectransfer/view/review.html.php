<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->sectransfer->statusListName[$transfer->status];?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $examine.$lang->sectransfer->result;?></th>
                    <td><?php echo html::select('result', $lang->sectransfer->reviewList, '', "class='form-control chosen' required");?></td>
                </tr>
                <?php if($transfer->status == $lang->sectransfer->statusList['waitCMApprove']): ?>
                    <tr id = 'sftpPathTr'>
                        <th><?php echo $lang->sectransfer->sftpPath;?></th>
                        <td colspan='2'><?php echo html::input('sftpPath', $transfer->sftpPath, "class='form-control' required");?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th><?php echo $examine.$lang->sectransfer->suggest;?></th>
                    <td colspan='2' id="suggestTd"><?php echo html::textarea('suggest', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <!--保存初始审核节点-->
                        <input type="hidden" name = "version" value="<?php echo $transfer->version; ?>">
                        <input type="hidden" name = "reviewStage" value="<?php echo $transfer->reviewStage; ?>">

                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
