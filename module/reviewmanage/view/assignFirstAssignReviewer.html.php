<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class='table table-form'>
        <tr>
            <th class='w-120px'><?php echo $lang->review->firstMainReviewer;?></th>
            <td class='w-p45-f'>
                <?php echo html::select('mainReviewer', $users, '', "class='form-control chosen' required");?>
            </td>
            <td></td>
        </tr>

        <tr>
            <th><?php echo $lang->review->firstIncludeReviewer;?></th>
            <td>
                <?php echo html::select('includeReviewers[]', $users, '', "class='form-control chosen' multiple");?>
            </td>
            <td></td>
        </tr>
       <!-- <tr>
            <th><?php /*echo $lang->review->consumed;*/?></th>
            <td>
                <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
            </td>
            <td></td>
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
                <input type="hidden" name = "status" value="<?php echo $review->status; ?>">
                <input type="hidden" name = "reviewedDate" value="<?php echo helper::now(); ?>">
                <?php echo html::submitButton();?>
            </td>
        </tr>
    </table>
</form>
