<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2>    <span class='label label-id'><?php echo $info->id;?></span>
                <span><?php echo $info->code;?></span>
                <small><?php echo $lang->arrow . $lang->credit->editSecondorderCancelLinkage;?></small></h2>
        </div>

            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->credit->modifyStatus;?></th>
                        <td><?php echo html::select('secondorderCancelLinkage',  $lang->credit->secondorderCancelLinkageList , $info->secondorderCancelLinkage, "class='form-control chosen' required");?></td>
                        <td></td>
                    </tr>


                    <tr>
                        <th><?php echo $lang->credit->comment;?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('comment', '', "class='form-control' rows='5'");?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
                    </tr>
                    </tbody>
                </table>
            </form>

    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
