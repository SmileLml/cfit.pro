<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    /*.detail-title-bold{font-size:14px;lisne-height:20px;font-weight:bold;}*/
    .desc>div{float:left}
    .detail-content{margin-top: 0px !important;}
    .detail-title{width:130px;text-align: left}
    .main-change>div{margin-bottom: 10px}
</style>
</div>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px; max-height: 500px;">
    <div class="main-header">
        <div class="page-title">
            <span class="label label-id"><?php echo $requirement->code ?></span>
            <span class="text" title='<?php echo htmlspecialchars_decode($requirement->name); ?>'><?php echo htmlspecialchars_decode($requirement->name); ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="main-col col-8 main-change">
            <div class="desc clearfix">
                <div class="detail-title"><?php echo $lang->requirementchange->QzRequirementCode; ?>：</div>
                <div class="detail-content article-content"><?php echo $requirement->entriesCode;?></div>
            </div>
            <div>
                <div class="detail-title"><?php echo $lang->requirementchange->line; ?>：</div>
                <div class="detail-content article-content" style="text-indent: 2em"><?php echo $requirement->lineStr;?></div>
            </div>
            <div>
                <div class="detail-title"><?php echo $lang->requirementchange->product; ?>：</div>
                <div class="detail-content article-content" style="text-indent: 2em"><?php echo $requirement->productStr;?></div>
            </div>
            <div>
                <div class="detail-title"><?php echo $lang->requirementchange->desc; ?>：</div>
                <div class="detail-content article-content" style="text-indent: 2em"><?php echo $requirement->desc;?></div>
            </div>
        </div>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
