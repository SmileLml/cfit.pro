<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="height: 360px;">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $review->id;?></span>
                <span><?php echo $review->title;?></span>

                <small><?php echo $lang->arrow . $reviewNode->statusStageName;?></small>
            </h2>
        </div>
        <?php if(!$checkRes['result']):?>
            <div class="tipMsg">
                <span><?php echo $checkRes['message']; ?></span>
            </div>
        <?php else:?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class='table table-form'>
                    <?php
                    $multiple = '';
                    if(in_array($reviewNode->nodeCode, $lang->review->multipleUserStageList)){
                        $multiple = 'multiple';
                    }
                    $allowEditReviewers = implode(',', $allowEditReviewers);
                    ?>
                    <tr>
                        <th  class='w-150px'><?php echo $lang->review->currentNodeDealUsers;?></th>
                        <td colspan='2'><?php echo html::select('reviewers[]', $users, $allowEditReviewers, "class='form-control chosen' $multiple");?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class='text-center' colspan='3'>
                            <input type="hidden" name = "nodeId" value="<?php echo $reviewNode->id; ?>">
                            <?php echo html::submitButton();?>
                        </td>
                    </tr>
                </table>
            </form>

        <?php endif;?>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>