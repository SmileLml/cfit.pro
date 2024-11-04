<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
    <div id="mainContent" class="main-content fade">
        <div class="center-block">
            <div class="main-header">
                <h2>
                    <span class='label label-id'><?php echo $creditInfo->id;?></span>
                    <span><?php echo $creditInfo->code;?></span>
                    <small><?php echo $lang->arrow . $lang->credit->statusList[$creditInfo->status];?></small>
                </h2>
            </div>
            <?php if(!$checkRes['result']):?>
                <div class="tipMsg red">
                    <span><?php echo $checkRes['message']; ?></span>
                </div>
            <?php else:?>
                <?php include $reviewView;?>
            <?php endif;?>
        </div>
    </div>
<?php include '../../common/view/footer.html.php';?>