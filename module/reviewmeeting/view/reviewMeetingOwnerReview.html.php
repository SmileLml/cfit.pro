<style>
    .info-title > th {text-align: center!important; }
    .disabled-select{disabled;}
    .hidden{display: none;}
    .reviewInfo-title{text-align: left!important; padding-left: 10px!important;}
</style>
<tr class="info-title">
    <th style="width: 15%;"><?php echo $lang->reviewmeeting->reviewTitle;?></th>
    <th style="width: 40%;"><?php echo $lang->reviewmeeting->result;?></th>
    <th style="width: 45%;"><?php echo $lang->reviewmeeting->verifyUsers;?></th>
</tr>

<?php
    if($reviewList):
        foreach ($reviewList as  $key => $reviewInfo):
?>
<tr id="review-info-<?php echo $key?>">
    <th title="<?php echo $reviewInfo->title;?>"><?php echo helper::substr($reviewInfo->title, 20, '...');?></th>
    <td>
        <?php echo html::hidden('reviewIds[]', "$reviewInfo->id");?>
        <?php echo html::select('resultList[]', $lang->reviewmeeting->reviewConclusionList, '', "class='form-control chosen' required onchange='selectReviewResultChange($key, $reviewInfo->id, this.value)'");?>
    </td>

    <td>
        <div class='input-group' id="verify-onlyRead-<?php echo $key?>">
            <?php echo html::select("verifyReviewersTemp[$key][]", $users, '', "class='form-control verifyReviewersSelect' required disabled");?>
        </div>
        <div class='input-group hidden' id="verify-write-<?php echo $key?>">
            <?php echo html::select("verifyReviewers[$key][]", $users, isset($verifyUserList[$reviewInfo->id])? $verifyUserList[$reviewInfo->id]:'', "class='form-control chosen verifyReviewersSelect' required multiple");?>
        </div>
    </td>
</tr>
<tr class="verifyReviewers-<?php echo $key?> hidden">
    <th></th>
    <td>
       <?php echo $lang->review->reviewNote.$preliminaryReviewer[$reviewInfo->id].'<br>'.$lang->review->reviewProblemNote.$questionReviewer[$reviewInfo->id];?>
        <?php
        $tempUnDealIssueUsers = '';
        if(isset($unDealIssueUsers[$reviewInfo->id])):
            $tempUnDealIssueUsers = implode(',', $unDealIssueUsers[$reviewInfo->id]);
        ?>
            <br><?php echo $lang->review->reviewProblemUnDealNote . zmget($users, $tempUnDealIssueUsers);?>
        <?php endif;?>
    </td>
    <td></td>
</tr>
<?php
        endforeach;
    endif;
?>


<tr>
    <th><?php echo $lang->reviewmeeting->editDeadline;?></th>
    <td>
        <?php echo html::input('editDeadline',  $defEditDeadLine, "class='form-control form-date' required");?>
    </td>
    <td>
        <div class='input-group'>
            <span class='input-group-addon'><?php echo  $lang->reviewmeeting->verifyDeadline;?></span>
            <?php echo html::input('verifyDeadline', $defVerifyDeadline, "class='form-control form-date' required");?>
        </div>
    </td>
</tr>

<tr>
   <!-- <th><?php /*echo  $lang->reviewmeeting->consumed;*/?></th>
    <td>
        <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
    </td>-->
   <!-- <td>
        <div class='input-group'>
            <span class='input-group-addon'><?php /*echo $lang->reviewmeeting->mailto;*/?></span>
            <?php /*echo html::select('mailto[]', $users, $meetingInfo->reviewer, "class='form-control chosen' multiple");*/?>
        </div>
    </td>-->
    <th><?php echo $lang->reviewmeeting->mailto;?></th>
    <td colspan="2"> <?php echo html::select('mailto[]', $users, $meetingInfo->reviewer, "class='form-control chosen' multiple");?></td>
</tr>

<tr>
    <th><?php echo $lang->reviewmeeting->comment ;?></th>
    <td colspan='2'>
        <?php echo html::textarea('comment', '', "class='form-control'");?>
    </td>
</tr>

<tr>
    <td class='text-center' colspan='3'>
        <?php echo html::submitButton('', '', 'btn btn-wide btn-primary checkMeetingOwnerReview');?>
    </td>
</tr>