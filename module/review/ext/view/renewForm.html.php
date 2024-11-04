<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px; max-height: 500px;">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $review->id;?></span>
                <span><?php echo $review->title;?></span>

                <small><?php echo $lang->arrow . $lang->review->renew;?></small>
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
                        <th class='w-120px'><?php echo $lang->review->renewReason;?></th>
                        <td colspan='2'>
                            <?php echo html::input('renewReason', '', "class='form-control' required");?>
                        </td>
                    </tr>
                    <tr>
                        <th class='w-120px'><?php echo $lang->review->nextStage;?></th>
                        <td colspan='2'>
                            <?php echo html::select('nextStage', $historyReviewStageList,'', "class='form-control chosen' required");?>
                        </td>
                    </tr>
                    <tr class="gradeMeeting hidden">
                        <th ><?php echo $lang->review->meetingPlanType;?></th>
                        <td colspan='2'>
                            <?php echo html::radio('meetingPlanType', $lang->review->meetingPlanTypeLabelListRenew, '1');?>
                        </td>
                    </tr>

                    <tr class="meetingPlanType-1 hidden">
                        <th><?php echo $lang->review->meetingPlanList;?></th>
                        <td colspan='2'>
                            <?php echo html::select('meetingCode', '', $review->meetingCode, "class='form-control chosen' required");?>
                        </td>
                    </tr>

                    <tr class="meetingPlanType-2 hidden">
                        <th><?php echo $lang->review->meetingPlanTime;?></th>
                        <td colspan='2'>
                            <?php echo html::input('meetingPlanTime', '', "class='form-control form-datetime' required");?>
                        </td>
                    </tr>

                    <!--<tr>
                        <th><?php /*echo $lang->review->consumed;*/?></th>
                        <td colspan='2'>
                            <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
                        </td>
                    </tr>-->
                    <tr>
                        <th class='w-120px'><?php echo $lang->review->suspendTime;?></th>
                        <td colspan='2'>
                            <?php
                            echo html::input('renewTime', $review->suspendTime, "class=' form-control form-datetime' disabled");?>
                        </td>
                    </tr>
                    <tr>
                        <th class='w-120px'><?php echo $lang->review->renewTime;?></th>
                        <td colspan='2'>
                            <?php
                            $currentTime = helper::now();
                            echo html::input('renewTime', $currentTime, "class=' form-control form-datetime' required");?>
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