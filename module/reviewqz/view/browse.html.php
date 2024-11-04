<?php
/**
 * The project view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     my
 * @version     $Id
 * @link        http://www.zentao.net
 */
?>

<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
    #queryBox .table td{overflow: unset}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
            foreach($browseStatusList as $key => $label):?>
            <?php if($key == 'finalPass' || $key == 'finalReject') continue;?>
            <?php $active = $browseType == $key ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($browseType == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
            <?php echo html::a(inlink('browse', "browseType=$key"), $label, '', "class='btn btn-link $active'");?>
        <?php endforeach;?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
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
<?php include '../../common/view/footer.html.php';?>