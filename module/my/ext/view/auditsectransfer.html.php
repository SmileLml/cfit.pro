<?php include '../../../common/view/header.html.php'?>
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
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='component'></div>
    <?php if(empty($reviewList)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' method='post' id='myReviewForm'>
          <?php
          //include '../../common/view/datatable.feed.html.php';
          $setting = $this->datatable->getSetting('sectransfer');
          $widths  = $this->datatable->setFixedFieldWidth($setting);
          ?>
          <div class="table-header fixed-right">
              <nav class="btn-toolbar pull-right"></nav>
          </div>
          <?php
          $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
          ?>
          <table class='table has-sort-head datatable' id='transferTable' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' >
              <thead>
              <tr>
                  <?php
                  $vars = "&param=0&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
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
              <?php foreach($reviewList as $sectransfer):?>
                  <tr data-id='<?php echo $sectransfer->id?>'>
                      <?php foreach($setting as $value) $this->sectransfer->printCell($value, $sectransfer,$users,$depts,$sectransfer->status,$orderBy,$pager);?>
                  </tr>
              <?php endforeach;?>
              </tbody>
          </table>
        <div class="table-footer">
        </div>
      </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
