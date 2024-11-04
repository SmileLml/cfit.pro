<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->closingadvise->adviseFeedback;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->closingadvise->feedbackResult;?></th>
                    <td><?php echo html::select('status', $feedbackResults, '', "class='form-control chosen' required");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->closingadvise->suggest;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control' required");?></td>
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
<?php include '../../common/view/footer.html.php';?>
