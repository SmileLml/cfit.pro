<?php
/**
 * The browse view file of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: browse.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php $config->testtask->datatable = $config->testtask->datatableMainBrowse; ?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datatable.fix.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php js::set('confirmDelete', $lang->testtask->confirmDelete)?>
<?php js::set('flow', $config->global->flow); ?>
<?php
$scope = $this->session->testTaskVersionScope;
$status = $this->session->testTaskVersionStatus;
?>
<?php js::set('status', $status); ?>
<style>
body {margin-bottom: 25px;}
#action-divider{display: inline-block; line-height: 0px; border-right: 2px solid #ddd}
</style>
<div id="mainMenu" class='clearfix'>
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->testtask->featureBar['browse'] as $key => $label):?>
    <?php echo html::a(inlink('browse', "applicationID=$applicationID&productID=$productID&branch=$branch&type=$scope,$key"), "<span class='text'>$label</span>", '', "id='{$key}Tab' class='btn btn-link'"); ?>
    <?php endforeach; ?>
    <?php $condition = "applicationID=$applicationID&productID=$productID&branch=$branch&type=$scope,$status&orderBy=$orderBy&recTotal=0&recPerPage={$pager->recPerPage}&pageID=1"?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->testtask->bySearch;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('testtask', 'create', "applicationID=$applicationID&product=$productID", "<i class='icon icon-plus'></i> " . $lang->testtask->create, '', "class='btn btn-primary'"); ?>
  </div>
</div>
<div id='mainContent' >
  <div class="cell<?php if($browseType == 'local,bySearch') echo ' show';?>" id="queryBox" data-module='testtask'></div>
  <?php if(empty($tasks)):?>
  <?php $useDatatable = ''; ?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->testtask->noTesttask; ?></span>
      <?php if(common::hasPriv('testtask', 'create')):?>
      <?php echo html::a($this->createLink('testtask', 'create', "applicationID=$applicationID&product=$productID"), "<i class='icon icon-plus'></i> " . $lang->testtask->create, '', "class='btn btn-info'"); ?>
      <?php endif; ?>
    </p>
  </div>
  <?php else:?>
  <?php
    $datatableId   = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');
    ?>
  <form class='main-table table-testtask' id='taskForm' method='post' <?php if(!$useDatatable) echo "data-ride='table'"; ?>>
    <div class="table-header fixed-right">
      <nav class="btn-toolbar pull-right"></nav>
    </div>
    <?php
    $vars = "applicationID=$applicationID&productID=$productID&branch=$branch&type=$scope,$status&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";

    if($useDatatable) include '../../common/view/datatable.html.php'; 
    if(!$useDatatable) include '../../common/view/tablesorter.html.php'; 

    $setting = $this->datatable->getSetting('testtask');
    $widths = $this->datatable->setFixedFieldWidth($setting);
    $columns = 0;
    ?>
    <?php if(!$useDatatable) echo '<div class="table-responsive">'; ?>
    <table class='table has-sort-head<?php if($useDatatable) echo ' datatable'; ?>' id='caseList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' data-checkbox-name='caseIDList[]'>
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
        <?php foreach($tasks as $task):?>
        <tr data-id='<?php echo $task->id?>'>
          <?php foreach($setting as $key => $value)
          {
            $this->testtask->printMainBrowseCell($value, $task, $applicationID, $productID, $users, $useDatatable ? 'datatable' : 'table', $projects, $products);
          }?>
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
  $(function() {
    $("#" + status + "Tab").addClass('btn-active-text').append(" <span class='label label-light label-badge'><?php echo $pager->recTotal; ?></span>")
  })
  <?php if($useDatatable):?>
  $(function() {
    $('#taskForm').table();
  })
  <?php endif; ?>
</script>
<?php include '../../common/view/footer.html.php'; ?>
