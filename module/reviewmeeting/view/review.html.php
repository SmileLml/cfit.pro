<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .input-group-addon{min-width: 150px;} .input-group{margin-bottom: 2px;}
                                          .container{witdh:1200px;}
</style>
<?php
$minHeight = 300;
$maxHeight = 480;
?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height: <?php echo $minHeight;?>px; max-height:  <?php echo $maxHeight;?>px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $meetingInfo->id;?></span>
        <span><?php echo $meetingInfo->meetingCode;?></span>
        <small><?php echo $lang->arrow . $lang->reviewmeeting->reviewOpDescList[$meetingInfo->status];?></small>
      </h2>
    </div>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
            <?php else:?>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform meetingReviewForm'>
              <table class='table table-form'>
                  <?php include $reviewView;?>
              </table>
          </form>

      <?php endif;?>
  </div>
</div>
<?php
js::set('meetingId', $meetingInfo->id);
js::set('status', $meetingInfo->status);
?>
<script>
    function selectReviewResultChange(trKey, reviewId, reviewResult){
        setVerifyReviewersStyle(trKey, reviewId, reviewResult);
    }

    //设置验证人员信息
    function setVerifyReviewersStyle(trKey, reviewId, reviewResult){
        if(reviewResult == 'passNeedEdit'){
            $('#verify-onlyRead-'+trKey).addClass('hidden');
            $('#verify-write-'+trKey).removeClass('hidden');
            $('.verifyReviewers-'+trKey).removeClass('hidden');
        }else {
            $('#verify-write-'+trKey).addClass('hidden');
            $('#verify-onlyRead-'+trKey).removeClass('hidden');
            $('.verifyReviewers-'+trKey).addClass('hidden');
        }
    }
</script>
<?php include '../../common/view/footer.html.php';?>

