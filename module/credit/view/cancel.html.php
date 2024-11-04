<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2>    <span class='label label-id'><?php echo $info->id;?></span>
                <span><?php echo $info->code;?></span>
                <small><?php echo $lang->arrow . $lang->credit->cancel;?></small></h2>
        </div>
        <?php if(!$checkRes['result']):?>
            <div class="tipMsg red">
                <span><?php echo $checkRes['message']; ?></span>
            </div>
        <?php else:?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th></th>
                        <td style="color:#FF0000" colspan='3'><?php echo $lang->credit->cancelNotice?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->credit->cancelReason;?></th>
                        <td colspan='3' class="required"><?php echo html::textarea('cancelReason', '', "class='form-control' rows='5'");?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='4'><?php echo html::submitButton() . html::backButton();?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        <?php endif;?>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
