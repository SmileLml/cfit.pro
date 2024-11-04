<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->putproduction->statusList[$putproduction->status];?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->putproduction->dealResult;?></th>
                    <td><?php echo html::select('repushResult', $lang->putproduction->repushResultList , '', "class='form-control chosen' required");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->putproduction->dealMessage;?></th>
                    <td colspan='2' id="suggestTd"><?php echo html::textarea('dealMessage', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                </tr>
                <tr>

                </tr>
                <tr>

                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>