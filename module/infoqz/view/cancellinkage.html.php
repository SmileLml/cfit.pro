<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
                <span class="label label-id"><?php echo $info->code?></span>
                <strong style="font-size: 14px;padding-left: 15px"><?php echo $lang->infoqz->cancelLinkage;?></strong>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->infoqz->cancelLinkage;?></th>
                    <td><?php echo html::select('cancelLinkage', $lang->infoqz->cancelLinkageList, $info->$type, "class='form-control chosen' required");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='2'>
                        <?php echo html::submitButton('提交') . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
