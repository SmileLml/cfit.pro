<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
</style>
  <div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->sectransfer->browseStatus as $key => $label):?>
            <?php $active = $status == strtolower($key) ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($status == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
            <?php echo html::a($this->createLink('sectransfer', 'browse', "status=$key&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"),  $label, '',"class='btn btn-link $active'");?>
        <?php endforeach;?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
    <div class="pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('sectransfer', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('sectransfer', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('sectransfer', 'export') ? $this->createLink('sectransfer', 'export', "orderBy=$orderBy&browseType=$key") : '#';
                echo "<li $class>" . html::a($link, $lang->sectransfer->export, '', $misc) . "</li>";
                ?>
            </ul>
        </div>
        <!--        --><?php //echo $lang->pageActions;?>
        <div class="btn-toolbar pull-right">
            <?php
            $params = "secondorderID=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
            ?>
            <?php common::printLink('sectransfer', 'create', $params, "<i class='icon icon-plus'></i>" . $lang->sectransfer->create, '', "class='btn btn-primary'");?>
<!--            --><?php //common::printLink('sectransfer', 'batchCreate', $params, "<i class='icon icon-plus'></i>" . $lang->sectransfer->batchCreate, '', "class='btn btn-secondary'");?>
        </div>
    </div>

  </div>
  <div id="mainContent" class="main-row">
    <div class="main-col">
        <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='sectransfer'></div>
        <?php if(empty($sectransfers)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
        <form class='main-table' method='post' id='reviewForm'>
          <?php
          //include '../../common/view/datatable.feed.html.php';
          include '../../common/view/datatable.html.php';
          $setting = $this->datatable->getSetting('sectransfer');
          $widths  = $this->datatable->setFixedFieldWidth($setting);
          ?>
        <table class='table has-sort-head' id='transferTable' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' >
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
            <?php foreach($sectransfers as $sectransfer):?>
            <tr data-id='<?php echo $sectransfer->id?>'>
            <?php foreach($setting as $value){
                $this->sectransfer->printCell($value, $sectransfer,$users,$depts,$status,$orderBy,$pager);
            } ?>
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
<script>
    $('.viewClick').live('click', function(){
        let transferID = $(this).parent().attr('data-id');
        window.location = createLink('sectransfer', 'view', "transferID="+transferID)
    })
</script>
<?php include '../../common/view/footer.html.php'?>
