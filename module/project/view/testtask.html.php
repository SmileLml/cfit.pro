<?php
/**
 * The browse view file of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: browse.html.php 1914 2011-06-24 10:11:25Z yidong@cnezsoft.com $
 * @link        http://www.zentao.net
 */
?>
<?php $config->project->datatable = $config->project->datatableTesttask; ?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
body {margin-bottom: 25px;}
</style>
<?php
$scope  = $this->session->testTaskVersionScope;
$status = $this->session->testTaskVersionStatus;
?>
<?php js::set('confirmDelete', $lang->testtask->confirmDelete)?>
<?php js::set('status', $status); ?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->testtask->featureBar['browse'] as $key => $label):?>
    <?php echo html::a(inlink('testtask', "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=$scope,$key"), "<span class='text'>$label</span>", '', "id='{$key}Tab' class='btn btn-link'"); ?>
    <?php endforeach; ?>
    <?php $condition = "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=$browseType&param=0&orderBy=$orderBy&recTotal=0&recPerPage={$pager->recPerPage}&pageID=1"?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->testtask->bySearch;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if($productID != 'all')
    {
        common::printLink('testtask', 'create', "applicationID=$applicationID&productID=$productID&build=0&projectID=$projectID", "<i class='icon icon-plus'></i> " . $lang->testtask->create, '', "class='btn btn-primary'");
    }
    else
    {
        echo html::a($this->createLink('project', 'ajaxSelectProductToBug', "projectID=$project->id&object=testtask"), "<i class='icon icon-plus'></i> " . $lang->testtask->create, '', "class='btn btn-primary iframe'");
    }
    ?>
  </div>
</div>
<div id="mainContent">
  <div class="cell<?php if($browseType == $scope.',bySearch') echo ' show';?>" id="queryBox" data-module='projecttesttask'></div>
  <?php if(empty($tasks)):?>
  <?php $useDatatable = '';?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->testtask->noTesttask;?></span>
      <?php if(common::hasPriv('testtask', 'create') and $productID != 'all'):?>
      <?php echo html::a($this->createLink('testtask', 'create', "applicationID=$applicationID&productID=$productID&build=0&projectID=$projectID"), "<i class='icon icon-plus'></i> " . $lang->testtask->create, '', "class='btn btn-info' data-app={$this->app->openApp}");?>
      <?php endif;?>
    </p>
  </div>
  <?php else:?>
  <?php
  $datatableId  = $this->moduleName . ucfirst($this->methodName);
  $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');
  ?>
  <form class="main-table table-testtask" <?php if(!$useDatatable) echo "data-ride='table'";?> data-group="true" method="post" target='hiddenwin' id='testtaskForm'>
    <div class="table-header fixed-right">
      <nav class="btn-toolbar pull-right"></nav>
    </div>
    <?php 
    $vars = "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
    
    if($useDatatable) include '../../common/view/datatable.html.php';
    if(!$useDatatable) include '../../common/view/tablesorter.html.php';

    $setting = $this->datatable->getSetting('project');
    $widths  = $this->datatable->setFixedFieldWidth($setting);
    $columns = 0;
    ?>
    <?php if(!$useDatatable) echo '<div class="table-responsive">';?>
      <table class='table has-sort-head<?php if($useDatatable) echo ' datatable';?>' id='caseList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' data-checkbox-name='caseIDList[]'>
        <thead>
          <tr>
          <?php
          foreach($setting as $key => $value)
          {
              if($value->show)
              {
                  $this->datatable->printHead($value, $orderBy, $vars, false);
                  $columns ++;
              }
          }
          ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($tasks as $task):?>
          <tr data-id='<?php echo $task->id?>'>
            <?php foreach($setting as $key => $value) $this->project->printCellTesttask($value, $task, $users, $useDatatable ? 'datatable' : 'table', $products);?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <?php if(!$useDatatable) echo '</div>';?>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs');?>
      </div>
  </form>
  <?php endif;?>
</div>
<script>
<?php if($useDatatable):?>
$(function(){$('#testtaskForm').table();})
<?php endif;?>
$(function() {
  $("#" + status + "Tab").addClass('btn-active-text').append(" <span class='label label-light label-badge'><?php echo $pager->recTotal; ?></span>")
})
</script>
<?php include '../../common/view/footer.html.php';?>
