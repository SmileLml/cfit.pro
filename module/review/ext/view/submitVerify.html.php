<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class='table table-form'>
        <?php if($review->status != 'outPassButEdit'): ?>
        <tr>
            <th class='w-120px'><?php echo $lang->review->verifyReviewers;?></th>
            <td colspan="2" ><?php echo html::select('verifyReviewers[]', $users, $review->verifyReviewers, "class='form-control chosen' multiple required");?></td>
        </tr>
        <tr <?php if(empty($unDealReviewIssueUsers)):?> class="hidden" <?php endif;?>>
            <th class='w-120px'><?php echo $lang->review->unDealIssueRaiseByUsers;?></th>
            <td colspan="2" ><?php echo html::select('unDealIssueRaiseByUsers[]', $users, $unDealReviewIssueUsers, "class='form-control chosen' multiple");?></td>
        </tr>

        <?php endif ?>
        <tr>
            <th><?php echo $lang->review->deadline;?></th>
            <td colspan="2">
                <?php echo html::input('deadline', $review->deadline != '0000-00-00' ? $review->deadline:'', "class='form-date form-control' ");?>
            </td>
        </tr>
      <!--  <tr>
            <th><?php /*echo $lang->review->consumed;*/?></th>
            <td td colspan="2">
                <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
            </td>
        </tr>-->
        <tr>
            <th class='w-140px'><?php echo $lang->review->mailto;?></th>
            <td colspan="2"><?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple");?></td>
        </tr>
        <tr>
            <th><?php echo $lang->review->currentComment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
        </tr>
        <tr>
            <td class='text-center' colspan='3'>
                <input type="hidden" name = "version" value="<?php echo $review->version; ?>">
                <input type="hidden" name = "rejectStage" value="<?php echo $review->rejectStage; ?>">
                <?php echo html::submitButton();?>
            </td>
        </tr>
    </table>

</form>
<?php js::set('verifyReviewers',$review->verifyReviewers);?>
<script>
    // $(document).ready(function(){
    //     $.get(createLink('review', 'ajaxGetVerifyReviewer','reviewer='+verifyReviewers), function(data) {
    //         $('#verifyReviewers_chosen').remove();
    //         $('#verifyReviewers').replaceWith(data);
    //         $('#verifyReviewers').chosen();
    //     })
    // })
</script>