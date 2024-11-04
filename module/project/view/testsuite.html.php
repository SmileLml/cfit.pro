<?php $config->project->datatable = $config->project->datatableTestsuite; ?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
body {margin-bottom: 25px;}
#subHeader #dropMenu .col-left .list-group {margin-bottom: 0px; padding-top: 10px;}
#subHeader #dropMenu .col-left {padding-bottom: 0px;}
</style>
<div id="mainMenu" class="clearfix main-row fade in">
  <div class="btn-toolbar pull-left">
    <?php echo html::a(inlink('testsuite', "projectID=$projectID&applicationID=$applicationID&productID=$productID&branchID=$branchID&browseType=$browseType&orderBy=$orderBy"), "<span class='text'>{$lang->testsuite->featureBar['browse']['all']}</span> <span class='label label-light label-badge'>{$pager->recTotal}</span>", '', "class='btn btn-link btn-active-text'");?>
  </div>
  <div class="btn-toolbar pull-right">
    
    <?php
    if($applicationID and common::hasPriv('testsuite', 'create'))
    {
        echo html::a(helper::createLink('testsuite', 'create', "applicationID=$applicationID&productID=$productID", '', '', '', true), "<i class='icon icon-plus'></i> " . $lang->testsuite->create, '', "class='btn btn-primary' data-app='{$this->app->openApp}'");
    }
    else
    {
        echo html::a($this->createLink('project', 'ajaxSelectProductToBug', "projectID=$project->id&object=testsuite"), "<i class='icon icon-plus'></i> " . $lang->testsuite->create, '', "class='btn btn-primary iframe'");
    }
    ?>
  </div>
</div>
<div id="mainContent" class='main-row split-row fade'>
<?php if(empty($suites)):?>
  <?php $useDatatable = ''; ?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->testsuite->noTestsuite; ?></span>
    </p>
  </div>
  <?php else:?>
  <?php
    $datatableId = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');
    ?>
  <form class='main-table table-case' id='testsuiteForm' method='post' <?php if(!$useDatatable) echo "data-ride='table'"; ?>>
    <div class="table-header fixed-right">
      <nav class="btn-toolbar pull-right"></nav>
    </div>
    <?php
    $vars = "projectID=$projectID&applicationID=$applicationID&productID=$productID&branchID=$branchID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
    if($useDatatable) include '../../common/view/datatable.html.php'; 
    if(!$useDatatable) include '../../common/view/tablesorter.html.php'; 

    $setting = $this->datatable->getSetting('project');
    $widths = $this->datatable->setFixedFieldWidth($setting);
    $columns = 0;

    ?>
    <?php if(!$useDatatable) echo '<div class="table-responsive">'; ?>
    <table class='table has-sort-head<?php if($useDatatable) echo ' datatable'; ?>' id='caseList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
      <thead>
        <tr>
          <?php
            foreach($setting as $key => $value)
            {
                if($value->show)
                {
                    $this->datatable->printHead($value, $orderBy, $vars, false);
                    $columns++;
                }
            }
            ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($suites as $suite):?>
        <tr data-id='<?php echo $suite->id?>'>
          <?php foreach($setting as $key => $value) $this->project->printCellTestsuite($value, $suite, $users, $products); ?>
        </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
    <?php if(!$useDatatable) echo '</div>'; ?>
    <div class='table-footer'><?php $pager->show('right', 'pagerjs'); ?></div>
  </form>
  <?php endif; ?>
</div>
<script>
  <?php if($useDatatable):?>
  $(function() {
    $('#testsuiteForm').table();
  })
  <?php endif; ?>
</script>
<?php include '../../common/view/footer.html.php';?>
