<?php
/**
 * The browse view file of testsuite module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testsuite
 * @version     $Id: browse.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datatable.fix.html.php'; ?>
<?php js::set('confirmDelete', $lang->testsuite->confirmDelete)?>
<?php js::set('flow', $config->global->flow); ?>
<div id="mainMenu" class='clearfix'>
  <div class="btn-toolbar pull-left">
    <a href class='btn btn-link btn-active-text'>
      <span class='text'><?php echo $lang->testsuite->browse?></span>
      <span class='label label-light label-badge'><?php echo $pager->recTotal; ?></span>
    </a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('testsuite', 'create', "applicationID=$applicationID&product=$productID", "<i class='icon icon-plus'></i> " . $lang->testsuite->create, '', "class='btn btn-primary'"); ?>
  </div>
</div>
<div id='mainContent' class='main-table' data-ride='table'>
  <?php if(empty($suites)):?>
  <?php $useDatatable = ''; ?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->testsuite->noTestsuite; ?></span>
      <?php echo html::a($this->createLink('testsuite', 'create', "applicationID=$applicationID&product=$productID"), "<i class='icon icon-plus'></i> " . $lang->testsuite->create, '', "class='btn btn-info'"); ?>
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
    $vars = "applicationID=$applicationID&productID=$productID&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";
    if($useDatatable) include '../../common/view/datatable.html.php'; 
    if(!$useDatatable) include '../../common/view/tablesorter.html.php'; 

    $setting = $this->datatable->getSetting('testsuite');
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
          <?php foreach($setting as $key => $value) $this->testsuite->printCell($value, $suite, $users, $products); ?>
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
<?php include '../../common/view/footer.html.php'; ?>
