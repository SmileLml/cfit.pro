<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
    .input-group-addon{min-width: 150px;} .input-group{margin-bottom: 2px;}
                                          .container{witdh:1200px;}
</style>
<?php
    $minHeight = 300;
    $maxHeight = 480;
    if($review->status == 'baseline'){
        $minHeight = 400;
        $maxHeight = 400;
    }
?>

<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height: <?php echo $minHeight;?>px; max-height:  <?php echo $maxHeight;?>px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $review->id;?></span>
        <span><?php echo $review->title;?></span>

        <small><?php echo $lang->arrow . $lang->review->statusLabelList[$review->status];?></small>
      </h2>
    </div>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
          <?php elseif($checkRes['result'] && $review->status == 'baseline'):?>
          <!--
            <hr class='small' />
            <div class='main'><?php include '../../../common/view/action.html.php';?></div>
            -->
                <?php include 'reviewBaseLine.html.php';?>
            <?php else:
          ?>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class='table table-form'>
                  <!--评审主席确定线上评审结论 -->
                  <?php if($review->status == $lang->review->statusList['waitFormalOwnerReview']):?>
                  <?php include 'reviewFormalOwnerReview.html.php';?>
                  <?php elseif(in_array($review->status, $lang->review->allowMeetingReviewStatusList)):?>
                      <?php include 'reviewMeeting.html.php';?>
                  <?php elseif(in_array($review->status, $lang->review->allowVerifyReviewStatusList)):?>
                      <?php include 'reviewVerify.html.php';?>
                  <?php elseif($review->status == $lang->review->statusList['archive']):?> <!--待归档-->
                      <?php include 'reviewArchive.html.php';?>
                  <?php else:?>
                      <tr>
                          <th class='w-120px'><?php echo $lang->review->result;?></th>
                          <td class='w-p45-f'>
                              <?php if(in_array($review->status, $lang->review->allowFormalFirstReviewStatusList)):?>
                                  <?php echo html::select('result', $lang->review->reviewPassConclusionList, '', "class='form-control chosen' required");?>
                              <?php elseif($review->status == $lang->review->statusList['waitFormalOwnerReview']):?>
                                  <?php echo html::select('result', $lang->review->reviewOnLineConclusionList, '', "class='form-control chosen' required");?>
                              <?php else:?>
                                  <?php echo html::select('result', $lang->review->reviewConclusionList, '', "class='form-control chosen' required");?>
                              <?php endif;?>
                          </td>
                          <td></td>
                      </tr>

                      <?php if($isSetAdviceGrade):?>
                          <tr>
                              <th><?php echo $lang->review->adviceGrade;?></th>
                              <td class='w-p45-f'>
                                  <?php echo html::select('grade', $adviceGradeList, $review->adviceGrade, "class='form-control chosen' required");?>
                              </td>
                              <td></td>
                          </tr>
                      <?php endif;?>

                          <?php if(in_array($review->status, $lang->review->allowAssignVerifyStatusList)):?>

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
                                    <?php echo $lang->review->reviewNote.$preliminaryReviewer.$preliminaryReviewer.'<br>'.$lang->review->reviewProblemNote.$questionReviewer;?>
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

                      <?php endif;?>

                      <tr class="hidden">
                          <th><?php echo $lang->review->reviewedDate;?></th>
                          <td>
                              <?php echo html::input('reviewedDate', helper::now(), "class='form-control form-date' required ");?>
                          </td>
                          <td></td>
                      </tr>

                  <?php if($review->status == $lang->review->statusList['waitFormalOwnerReview']):?>
                      <tr class="gradeMeeting hidden">
                          <th><?php echo $lang->review->meetingPlanTime;?></th>
                          <td>
                              <?php echo html::input('meetingPlanTime', '', "class='form-control form-datetime'");?>
                          </td>
                          <td></td>
                      </tr>
                  <?php endif;?>

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

                  <?php endif;?>

              </table>
          </form>
      <?php endif;?>
  </div>
</div>
<?php if($review->status == 'baseline'):?>
    <?php include 'reviewBaseLineHidden.html.php';?>
<?php elseif($review->status == 'archive'):?>
    <?php include 'reviewArchiveHidden.html.php';?>
<?php endif;?>

<?php include '../../../common/view/footer.html.php';?>

<?php
js::set('reviewId', $review->id);
js::set('status', $review->status);
if(!empty($review->verifyReviewers)){
    js::set('verifyReviewers',$review->verifyReviewers);
}
js::set('allowAssignVerifyersStatusList', $lang->review->allowAssignVerifyersStatusList);
js::set('type', $review->type);
js::set('meetingCode', $review->meetingCode);
?>
<script>
</script>
