<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="main-header">
        <h2><?php echo $lang->cmdbsync->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
        <table class="table table-form">
            <tbody>
            <tr>
                <th><?php echo $lang->cmdbsync->result;?></th>
                <td class='required'><?php echo html::select('result', $lang->cmdbsync->resultList, '', "class='form-control chosen' onchange=selectResult(this.value)");?></td>
            </tr>
            <tr id="isAutoTr">
                <th><?php echo $lang->cmdbsync->isAuto;?></th>
                <td class='required'>
                    <?php echo html::radio('isAuto', $lang->cmdbsync->isAutoList, '');?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->cmdbsync->comment;?></th>
                <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
            </tr>
            <tr>
                <td class='form-actions text-center' colspan='3'>
                    <?php echo html::submitButton() . html::backButton();?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    $("#dataform").submit(function(event){
        var result = $('#result').val();
        var isAuto = $('input[name="isAuto"]:checked').val();

        if(
            result != ''&& isAuto != '' && result == 'pass'
        ){
            if(isAuto == 'auto'){
                if(confirm('提交之后会自动更新系统数据，请确认')){
                    return true;
                }else{
                    return false;
                }
            }else if(isAuto == 'head'){
                if(confirm('请确认手动更新是否完成，请确认')){
                    return true;
                }else{
                    return false;
                }
            }

        }
    })

    function selectResult(result){
        if(result == 'pass'){
            $('#isAutoTr').removeClass('hidden');
        }else {
            $('#isAutoTr').addClass('hidden');
        }
    }
</script>

<?php include '../../common/view/footer.html.php';?>
