<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:450px; max-height: 700px;">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $review->id;?></span>
                <span><?php echo $review->title;?></span>

                <small><?php echo $lang->arrow . $lang->review->suspend;?></small>
            </h2>
        </div>
        <?php if(!$checkRes['result']):?>
            <div class="tipMsg">
                <span><?php echo $checkRes['message']; ?></span>
            </div>
        <?php else:?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class='table table-form'>
                    <tr>
                        <th class='w-120px'><?php echo $lang->review->suspendReason;?></th>
                        <td colspan='2'>
                            <?php echo html::input('suspendReason', '', "class='form-control' required");?>
                        </td>
                    </tr>

                   <!-- <tr>
                        <th><?php /*echo $lang->review->consumed;*/?></th>
                        <td colspan='2'>
                            <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
                        </td>
                    </tr>-->
                    <tr>
                        <th class='w-120px'><?php echo $lang->review->suspendTime;?></th>
                        <td colspan='2'>
                            <?php
                            $currentTime = helper::now();
                            echo html::input('suspendTime', $currentTime, "class=' form-control form-datetime' required");?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->review->currentComment;?></th>
                        <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control' required");?></td>
                    </tr>
                    <tr>
                        <td class='text-center' colspan='3'>
                            <input type="hidden" name = "version" value="<?php echo $review->version; ?>">
                            <?php echo html::submitButton();?>
                        </td>
                    </tr>
                </table>

            </form>

        <?php endif;?>
    </div>
</div>
