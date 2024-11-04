<tr>
    <th class='w-120px'><?php echo $lang->review->result;?></th>
    <td class='w-p45-f'>
        <?php echo html::select('result', $lang->review->reviewOnLineConclusionList, '', "class='form-control chosen' required");?>
    </td>
    <td></td>
</tr>


<tr class="verifyReviewers hidden">
    <th><?php echo $lang->review->verifyReviewers;?></th>
    <td>
        <?php echo html::select('verifyReviewers[]', $users, $review->verifyReviewers, "class='form-control chosen' multiple required");?>
    </td>
    <td></td>
</tr>

<tr class="verifyReviewers hidden">
    <th></th>
    <td>
        <?php echo $lang->review->reviewNote.$preliminaryReviewer.'<br>'.$lang->review->reviewProblemNote.$questionReviewer;?>
    </td>
    <td></td>
</tr>

<tr class="verifyReviewers hidden">
    <th><?php echo $lang->review->verifyDeadline;?></th>
    <td>
        <?php echo html::input('verifyDeadline',$review->verifyDeadline != '0000-00-00'? $review->verifyDeadline : '', "class='form-control form-date' required ");?>
    </td>
    <td></td>
</tr>

<tr class="hidden">
    <th><?php echo $lang->review->reviewedDate;?></th>
    <td>
        <?php echo html::input('reviewedDate', helper::now(), "class='form-control form-date' required ");?>
    </td>
    <td></td>
</tr>

<tr class="gradeMeeting hidden">
    <th><?php echo $lang->review->meetingPlanType;?></th>
    <td>
        <?php echo html::radio('meetingPlanType', $lang->review->meetingPlanTypeLabelList, '1');?>
    </td>
    <td></td>
</tr>

<tr class="meetingPlanType-1 hidden">
    <th><?php echo $lang->review->meetingPlanList;?></th>
    <td>
        <?php echo html::select('meetingCode', '', $review->meetingCode, "class='form-control chosen' required");?>
    </td>
    <td></td>
</tr>

<tr class="meetingPlanType-2 hidden">
    <th><?php echo $lang->review->meetingPlanTime;?></th>
    <td>
        <?php echo html::input('meetingPlanTime', $review->meetingPlanTime != '0000-00-00 00:00:00' ? $review->meetingPlanTime :'', "class='form-control form-datetime' required");?>
    </td>
    <td></td>
</tr>


<!--<tr>
    <th><?php /*echo $lang->review->consumed;*/?></th>
    <td>
        <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
    </td>
    <td></td>
</tr>-->

<tr>
    <th class='w-140px'><?php echo $lang->review->mailto;?></th>
    <td colspan="2">
        <?php if($review->status == $lang->review->statusList['waitFormalOwnerReview'] || $review->status == $lang->review->statusList['waitMeetingOwnerReview']):?>
            <?php echo html::select('mailto[]', $users, $mailto, "class='form-control chosen' multiple");?>
        <?php else:?>
            <?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple");?>
        <?php endif;?>
    </td>
</tr>

<tr>
    <th><?php echo $lang->comment ;?></th>
    <td colspan='2'>
        <?php echo html::textarea('comment', '', "rows='6' class='form-control' placeholder=' ".htmlspecialchars($lang->review->commenttip)."'");?>
    </td>
</tr>

<tr>
    <td class='text-center' colspan='3'>
        <?php echo html::submitButton();?>
    </td>
</tr>
