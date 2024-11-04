<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<script>
    $(document).ready(function () {
        var screenHeight = window.screen.height;
        var mainContentMaxHeight = (screenHeight - 250);
        if(mainContentMaxHeight > 780){
            mainContentMaxHeight = 780;
        }
        $("#mainContent").css({'max-height':mainContentMaxHeight+'px'});
    });
</script>
<div id='mainContent' class='main-content fade in  scrollbar-hover ' style="min-height:300px; ">
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
        <?php else:?>
            <?php include $view;?>
           <!--
            <hr class='small' />
            <div class='main'><?php include '../../common/view/action.html.php';?></div>
            -->
        <?php endif;?>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<?php
    js::set('status', $review->status);
    js::set('deptId', $review->createdDept);
    js::set('reviewer', $review->reviewer);
    js::set('reviewId', $review->id);
    js::set('owner', $review->owner);
    js::set('meetingCode', $review->meetingCode);
    js::set('meetingOwnerList', $meetingOwnerList);
    js::set('bearDept', $bearDept);
?>

