<?php
/**
 * The browse view file of testreport module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     testreport
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datatable.fix.html.php'; ?>

<?php if($config->global->flow == 'full'):?>
<div id='mainMenu' class='clearfix'>
  <div class='pull-left btn-toolbar'>
    <span class='btn btn-link btn-active-text'>
      <span class='text'><?php echo $lang->testreport->browse; ?></span>
      <span class="label label-light label-badge"><?php echo $pager->recTotal; ?></span>
    </span>
  </div>
  <div class='pull-right btn-toolbar'>
    <?php common::printLink('testreport', 'create', "applicationID=$applicationID&productID=$productID&objectType=testtask&extra=0", "<i class='icon icon-plus'></i>" . $lang->testreport->create, '', "class='btn btn-primary'"); ?>
  </div>
</div>
<?php endif; ?>

<div id='mainContent' class='main-table'>
  <?php if(empty($reports)):?>
  <?php $useDatatable = ''; ?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->testreport->noReport; ?></span></p>
  </div>
  <?php else:?>
  <?php
  $datatableId   = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');
    ?>
  <form class='main-table table-report' id='reportForm' method='post' <?php if(!$useDatatable) echo "data-ride='table'"; ?>>
    <div class="table-header fixed-right">
      <nav class="btn-toolbar pull-right"></nav>
    </div>
    <?php
    $vars = "applicationID=$applicationID&productID=$productID&objectType=$objectType&extra=$extra&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";

    if($useDatatable) include '../../common/view/datatable.html.php'; 
    if(!$useDatatable) include '../../common/view/tablesorter.html.php'; 

    $setting = $this->datatable->getSetting('testreport');
    $widths = $this->datatable->setFixedFieldWidth($setting);
    $columns = 0;
    ?>
    <?php if(!$useDatatable) echo '<div class="table-responsive">'; ?>
    <table class='table has-sort-head table-fixed <?php if($useDatatable) echo ' datatable'; ?>' id='reportList'>
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
        <?php foreach($reports as $report):?>
        <tr data-id='<?php echo $report->id?>'>
          <?php foreach($setting as $key => $value)
          {
            $this->testreport->printCell($value, $report, $users, $useDatatable ? 'datatable' : 'table', $projects, $products, $tasks);
          }?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if(!$useDatatable)
    {
      echo '</div>';
    }?>
    <div class='table-footer'><?php $pager->show('right', 'pagerjs'); ?></div>
  </form>
  <?php endif; ?>
</div>
<script>
  <?php if($useDatatable):?>
  $(function() {
    $('#reportForm').table();
  })
  <?php endif; ?>
</script>
<?php include '../../common/view/footer.html.php'; ?>
