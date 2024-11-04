<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->putproduction->assignment;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->putproduction->assignTo;?></th>
                    <td colspan='3'><?php echo html::select('dealUser', $users,'', "class='form-control chosen'");?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->putproduction->remark;?></th>
                    <td colspan='3'><?php echo html::textarea('remark', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($this->lang->submit) . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
