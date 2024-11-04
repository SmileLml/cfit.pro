<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="height:420px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->closingadvise->assignUser;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->closingadvise->dealUsers;?></th>
                    <td colspan='2'><?php echo $dealuserStr;?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->closingadvise->dealUser;?></th>
                    <td colspan='2'><?php echo html::select('dealusers[]', $users, $reviewers, 'class="form-control chosen" multiple required');?></td>
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
</div>
<script>
    $('#dealusers').change(function(){
        var exp = $('#dealusers').val();
        if(exp == 'null' || exp == null){
            $('#dealusers').val('');
        }
    });
</script>
<?php include '../../common/view/footer.html.php';?>

