<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>#tableCustomBtn+.dropdown-menu > li:last-child{display: none}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    foreach($lang->review->browseTypeList as $type => $label) 
    {
        $active = $type == $browseType ? 'btn-active-text' : '';
        echo html::a($this->createLink('review', 'browse', "project=$projectID&browseType=$type"), "<span class='text'>" . $label . '</span>', '', "class='btn btn-link $active'");
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('review', 'create', "project=$projectID", "<i class='icon icon-plus'></i>" . $lang->review->create, '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='review'></div>
    <?php if(empty($reviewList)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='reviewForm'>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php 
      $vars = "project=$projectID&browseType=$browseType&queryID=$queryID&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
      include '../../common/view/datatable.html.php';

      $setting = $this->datatable->getSetting('review');
      $widths  = $this->datatable->setFixedFieldWidth($setting);
      ?>
        <table class='table has-sort-head datatable' id='reviewList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
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
            <?php foreach($setting as $value) $this->review->printCell($value, $review, $users, $products);?>
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
$(function(){$('#reviewForm').table();})
</script>
<?php include '../../common/view/footer.html.php';?>
