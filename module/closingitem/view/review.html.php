<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $closingitem->status == $lang->closingitem->statusList['waitPreReview'] ? $lang->closingitem->itemQA : $lang->closingitem->itemFB;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $closingitem->status == $lang->closingitem->statusList['waitPreReview'] ? $lang->closingitem->qaReview : $lang->closingitem->fbkResult;?></th>
                    <?php $selected = $closingitem->status == $lang->closingitem->statusList['waitPreReview'] ? $lang->closingitem->reviewList : $lang->closingitem->feedbackResultList;?>
                    <td><?php echo html::select('result', $selected, '', "class='form-control chosen' required");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->closingitem->suggest;?></th>
                    <td colspan='2'><?php echo html::textarea('suggest', '', "class='form-control' required");?></td>
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
