<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .inoneline{white-space: NOWRAP;}
    .dealine-tr{transition: visibility 0.5s,opacity 0.5s;opacity: 1;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->datamanagement->destroy;?></h2>
        </div>
        <?php if ($datamanagement->reviewStage == '1'):?>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform' onsubmit="return destroyConfirm()" >
        <?php else:?>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform' >
        <?php endif;?>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->datamanagement->dataStatus;?></th>
                    <td><?php echo zget($lang->datamanagement->statusList, $datamanagement->status, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->destroyReason;?></th>
                    <td colspan='2'><?php echo html::input('destroyReason', '', "class='form-control' required");?></td>
                </tr>
               <!-- <tr>
                    <th class='inoneline'><?php /*echo $lang->datamanagement->consumedInput;*/?></th>
                    <td><?php /*echo html::input('consumed', '', "class='form-control' required placeholder='小数点后保留1位小数'");*/?></td>
                </tr>-->
                <tr>
                    <th><?php echo $lang->datamanagement->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton('申请销毁');?>
                        <?php echo html::backButton('取消');?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    function destroyConfirm(){
        var wf = confirm("您当前有数据使用延期尚在申请中或使用期限未到期，是否继续申请销毁，销毁申请提交后，将无法再进行延期!");
        if (wf==true){
            return true;
        }else {
            return false;
        }
    }
</script>
<?php include '../../common/view/footer.html.php';?>
