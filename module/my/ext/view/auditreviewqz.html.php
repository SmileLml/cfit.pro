<?php include '../../../common/view/header.html.php';?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->my->myReviewList as $key => $type):?>
            <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
            <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
        <?php endforeach;?>
    </div>
</div>

<div id="mainContent" class="main-row fade">
    <div class='main-col'>
        <div class="cell<?php
        if($browseType == 'bysearch' || $browseType == 'bySearch') echo ' show';?>" id="queryBox" data-module='reviewqz'></div>
        <?php if(empty($reviewList)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <form class='main-table' method='post' id='reviewQzForm'>
                <div class="table-header fixed-right">
                    <nav class="btn-toolbar pull-right"></nav>
                </div>
                <?php
                $vars = "browseType=$browseType&queryID=$queryID&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                include '../../common/view/datatable.html.php';
                $setting = $this->datatable->getSetting('reviewqz');
                $widths  = $this->datatable->setFixedFieldWidth($setting);
                $widths['rightWidth'] = '50';
                ?>
                <table class='table has-sort-head datatable' id='reviewQzList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
                    <thead>
                    <tr>
                        <?php
                        foreach($setting as $value)
                        {
                            if($value->show)
                            {
                                $this->datatable->printHead($value, $orderBy, $vars, false);
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $review):?>
                        <tr data-id='<?php echo $review->id?>'>
                            <?php foreach($setting as $value) $this->reviewqz->printCell($value, $review, $users);?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
            </form>
        <?php endif;?>
    </div>
</div>
<script>
    $(function(){$('#reviewQzForm').table();})
</script>
<?php include '../../../common/view/footer.html.php';?>
