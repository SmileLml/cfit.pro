<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .inoneline{white-space: NOWRAP;}
    .dealine-tr{transition: visibility 0.5s,opacity 0.5s;opacity: 1;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->datamanagement->delay;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->datamanagement->dataStatus;?></th>
                    <td><?php echo zget($lang->datamanagement->statusList, $datamanagement->status, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->delayReason;?></th>
                    <td colspan='2'><?php echo html::input('delayReason', '', "class='form-control' required");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->useDeadline;?></th>
                    <td class='inoneline required' style="display:flex;align-items:center">
                        <span><?php echo html::radio('useDeadline', $lang->datamanagement->useDeadlineChoose,'longterm', "onclick=\"setDeadline(this.value)\"");?></span>
                        <span style="margin-left:40px" class="dealine-tr hidden"><?php echo html::input('deadline', '', "class='form-control form-date' placeholder='请选择日期'");?></span>
                    </td>
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
                        <?php echo html::submitButton('确定延期');?>
                        <?php echo html::backButton('取消');?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    function setDeadline(val){
        console.log(val);
        val == 'custom' ? $('.dealine-tr').removeClass('hidden'):$('.dealine-tr').addClass('hidden');
    }
</script>
<?php include '../../common/view/footer.html.php';?>
