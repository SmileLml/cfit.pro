<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>#tableCustomBtn+.dropdown-menu > li:last-child{display: none}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    foreach($lang->nc->featureBar as $type => $label) 
    {   
        $active = $type == $browseType ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('nc', 'browse', "project=$projectID&browseType=$type"), "<span class='text'>" . $label . '</span>', '', "class='btn btn-link $active'");
    }   
    ?>  
  </div>
  <!-- div class="btn-toolbar pull-right">
    <?php common::printLink('nc', 'create', "project=$projectID", "<i class='icon icon-plus'></i>" . $lang->nc->create, '', "class='btn btn-primary'", '');?>
  </div -->
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <?php if(empty($ncs)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='ncForm'>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php 
      $vars = "project=$projectID&browseType=$browseType&param=$param&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
      include '../../common/view/datatable.html.php';

      $setting = $this->datatable->getSetting('nc');
      $widths  = $this->datatable->setFixedFieldWidth($setting);
      ?>
        <table class='table has-sort-head datatable' id='bugList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
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
          <?php foreach($ncs as $nc):?>
          <tr data-id='<?php echo $nc->id?>'>
            <?php foreach($setting as $value) $this->nc->printCell($value, $nc, $users, $activities, $outputs, $projectID);?>
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
$(function(){$('#ncForm').table();})
</script>
<?php include '../../common/view/footer.html.php';?>
