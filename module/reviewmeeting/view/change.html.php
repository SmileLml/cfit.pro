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
                <small><?php echo $lang->arrow . $lang->reviewmeeting->change;?></small>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform meetingReviewForm'>
            <table class='table table-form'>
                <?php
                if($reviewList):
                    foreach ($reviewList as $reviewInfo):
                        ?>
                        <tr>
                            <td colspan="4">
                                <?php echo html::hidden('reviewIds[]', "$reviewInfo->id");?>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                endif;
                ?>
                <tr>
                    <th><?php echo $lang->reviewmeeting->realExport;?></th>
                    <td colspan="4">
                        <?php echo html::select('realExport[]', $users, $meetingInfo->realExport, "class='form-control chosen' multiple='multiple'' required");?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->reviewmeeting->meetingRealTime;?></th>
                    <td colspan="4">
                        <?php echo html::input('meetingRealTime', $meetingInfo->meetingRealTime, "class='form-control form-datetime' required");?>
                    </td>
                </tr>
                <?php
                if($reviewList):
                    foreach ($reviewList as $key => $reviewInfo):
                        ?>
                        <tr>
                            <th title="<?php echo $reviewInfo->title . $lang->reviewmeeting->comment;?>"><?php echo $reviewInfo->title . $lang->reviewmeeting->comment;?></th>
                            <td colspan='4'>
                                <?php echo html::textarea("comment_$key", $commonList[$reviewInfo->id][0]->comment, "class='form-control'");?>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                endif;
                ?>
                <tr>
                    <td class='text-center' colspan='5'>
                        <?php echo html::submitButton();?>
                    </td>
                </tr>
            </table>
        </form>
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
            }else {
                $('#verify-write-'+trKey).addClass('hidden');
                $('#verify-onlyRead-'+trKey).removeClass('hidden');

            }
        }
    </script>
<?php include '../../common/view/footer.html.php';?>