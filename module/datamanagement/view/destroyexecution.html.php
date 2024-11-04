<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->datamanagement->destroyexecution;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-120px'><?php echo $lang->datamanagement->dataStatus;?></th>
                    <td><?php echo zget($lang->datamanagement->statusList, $datamanagement->status, '');?></td>
                </tr>
               <!-- <tr>
                    <th><?php /*echo $lang->datamanagement->consumedInput;*/?></th>
                    <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
                </tr>-->
                <tr>
                    <th><?php echo $lang->datamanagement->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton($datamanagement->status == 'destroying'? $lang->datamanagement->destroyed : $lang->datamanagement->reviewed) . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
