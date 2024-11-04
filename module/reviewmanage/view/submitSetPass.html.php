<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class='table table-form'>
        <!--
        <tr>
            <th class='w-120px'><?php echo $lang->review->reviewer;?></th>
            <td class='w-p45-f'><?php echo html::select('reviewer', $users, $review->reviewer, "class='form-control chosen'");?></td><td></td>
        </tr>
        -->

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
                <input type="hidden" name = "version" value="<?php echo $review->version; ?>">
                <input type="hidden" name = "rejectStage" value="<?php echo $review->rejectStage; ?>">
                <?php echo html::submitButton();?>
            </td>
        </tr>
    </table>
</form>
