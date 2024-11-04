<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->osspchange->browseStatus as $key => $label):?>
            <?php $active = $status == strtolower($key) ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php echo html::a($this->createLink('osspchange', 'browse', "status=$key&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"),  $label, '',"class='btn btn-link $active'");?>
        <?php endforeach;?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <div class="pull-right">
        <!--        --><?php //echo $lang->pageActions;?>
        <div class="btn-toolbar pull-right">
            <?php $params = "orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"; ?>
            <?php common::printLink('osspchange', 'create', $params, "<i class='icon icon-plus'></i>" . $lang->osspchange->create, '', "class='btn btn-primary'");?>
        </div>
    </div>

</div>
<div id="mainContent" class="main-row">
    <div class="main-col">
        <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='osspchange'></div>
        <?php if(empty($osspchanges)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <form class='main-table' method='post' id='osspchangeForm'>
                <?php
                $setting = $this->datatable->getSetting('osspchange');
                $widths  = $this->datatable->setFixedFieldWidth($setting);
                ?>
                <table class='table has-sort-head datatable' id='transferTable' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' >
                    <thead>
                    <tr>
                        <?php
                        $vars = "status=$status&param=0&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
                        foreach($setting as $value)
                        {
                            if($value->show)
                            {
                                $this->datatable->printHeadFixed($value, $orderBy, $vars, false);
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody class="sortable" id="transferTableList">
                    <?php foreach($osspchanges as $osspchange):?>
                        <tr data-id='<?php echo $osspchange->id?>'>
                            <?php foreach($setting as $value) $this->osspchange->printCell($value, $osspchange,$users,$status,$orderBy,$pager);?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs');?>
                </div>
            </form>
        <?php endif;?>
    </div>
</div>
</div>
<?php include '../../common/view/footer.html.php'?>
