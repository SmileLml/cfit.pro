<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
</style>

<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="btn-group angle-btn">
            <div class="btn-group">
                <?php if(empty($reviewInfo)):?>
                    <button data-toggle="dropdown" type="button" class="btn btn-limit" id="currentItem" title="<?php echo $lang->reviewissueqz->searchReview;?>" style="border-radius: 2px;"><?php echo $lang->reviewissueqz->searchReview;?>
                <?php else:?>
                    <button data-toggle="dropdown" type="button" class="btn btn-limit" id="currentItem" title="<?php echo $reviewInfo->title;?>" style="border-radius: 2px;"><?php echo $reviewInfo->title;?>
                <?php endif;?>
                        <span class="caret"></span>
                    </button>
                    <div id="dropMenu" class="dropdown-menu search-list load-indicator" data-ride="searchList" data-url="<?php echo $this->createLink('reviewqz', 'ajaxGetReviewqz',"status=$status");?>">
                        <div class="input-control search-box has-icon-left has-icon-right search-example">
                            <input type="search" class="form-control search-input empty">
                            <label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
                            <a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a>
                        </div>
                    </div>
            </div>
        </div>
        <?php foreach($lang->reviewissueqz->searchLabelList as $key => $label):?>
            <?php $active = $status == $key ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($status == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
            <?php echo html::a($this->createLink('reviewissueqz', 'issue', "reviewID=$reviewID&status=$key&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"),  $label, '',"class='btn btn-link $active'");?>
        <?php endforeach;?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <div class="pull-right">
        <div class="btn-toolbar pull-right">
            <?php
            $paramsBatch = "reviewID=$reviewID&status=$status&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
            $params = "reviewID=$reviewID&status=$status&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
            ?>
            <?php echo '<a href="javascript:void(0)"  class="btn btn-secondary" onclick="$.zui.messager.danger(\''.$lang->reviewissueqz->issueCreateMsgTip.'\', {html: \'true\'});" title="'.$lang->reviewissueqz->batchCreate.'" data-app="issueCreate"><i class="icon icon-plus"></i>'.$lang->reviewissueqz->batchCreate.'</a>';?>
            <?php echo '<a href="javascript:void(0)"  class="btn btn-primary"   onclick="$.zui.messager.danger(\''.$lang->reviewissueqz->issueCreateMsgTip.'\', {html: \'true\'});" title="'.$lang->reviewissueqz->create.'" data-app="issueCreate"><i class="icon icon-plus"></i>'.$lang->reviewissueqz->create.'</a>';?>

        </div>
    </div>

</div>
<div id="mainContent" class="main-row">
    <div class="main-col">
        <div class="cell<?php if($browseType == 'bySearch') echo ' show';?>" id="queryBox" data-module='reviewissueqz'></div>
        <form class='main-table' method='post' id='reviewForm'>
            <?php
            include '../../common/view/datatable.feed.html.php';
            $setting = $this->datatable->getSetting('reviewissueqz');
            $widths  = $this->datatable->setFixedFieldWidth($setting);
            ?>
            <table class='table has-sort-head datatable' id='issueTable' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' >
                <thead>
                <tr>
                    <?php
                    $vars = "reviewID=$reviewID&status=$status&param=0&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
                    foreach($setting as $value)
                    {
                        if($value->show) {
                            $this->datatable->printHeadFixed($value, $orderBy, $vars, false);
                        }
                    }
                    ?>
                </tr>
                </thead>
                <tbody class="sortable" id="issueTableList">
                <?php foreach($issueList as $issue):?>
                    <tr data-id='<?php echo $issue->id?>'>
                        <?php foreach($setting as $value) $this->reviewissueqz->printCell($value, $issue,$reviewID, $users,$status,$orderBy,$pager);?>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <div class="table-footer">
                <?php $pager->show('right', 'pagerjs');?>
            </div>
        </form>
    </div>
</div>
</div>
<script>
    $(function(){$('#reviewForm').table();})
    $(document).ready(function(){
        $("a").each(function(){
            if($(this).hasClass('disabled btn')){
                $(this).removeAttr('href');
            }
        })
    });

</script>
<?php include '../../common/view/footer.html.php'?>
