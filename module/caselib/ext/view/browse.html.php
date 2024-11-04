<?php
/**
 * The library view file of caselib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     caselib
 * @version     $Id: library.html.php 5108 2013-07-12 01:59:04Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../../common/view/header.html.php';
include '../../../common/view/datatable.fix.html.php';
js::set('browseType',    $browseType);
js::set('moduleID',      $moduleID);
js::set('confirmDelete', $lang->testcase->confirmDelete);
js::set('batchDelete',   $lang->testcase->confirmBatchDelete);
js::set('flow',          $config->global->flow);
?>
<style>
body {margin-bottom: 25px;}
.btn-group a i.icon-plus {font-size: 16px;}
.btn-group a.btn-primary {border-right: 1px solid rgba(255,255,255,0.2);}
.btn-group button.dropdown-toggle.btn-primary {padding:6px;}
</style>
<div id='mainMenu' class='clearfix'>
  <div id="sidebarHeader">
    <div class="title">
      <?php
      $this->app->loadLang('tree');
      echo isset($moduleID) ? $moduleName : $this->lang->tree->all;
      if(!empty($moduleID))
      {
          $removeLink = $browseType == 'bymodule' ? inlink('browse', "libID=$libID&browseType=$browseType&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}") : 'javascript:removeCookieByKey("libCaseModule")';
          echo html::a($removeLink, "<i class='icon icon-sm icon-close'></i>", '', "class='text-muted'");
      }
      ?>
    </div>
  </div>
  <div class='btn-toolbar pull-left'>
    <?php
    if(common::hasPriv('caselib', 'browse'))
    {
        echo html::a($this->inlink('browse', "libID=$libID&browseType=all"), "<span class='text'>{$lang->testcase->allCases}</span>", '', "id='allTab' class='btn btn-link'");
        if($config->testcase->needReview or !empty($config->testcase->forceReview)) echo html::a($this->inlink('browse', "libID=$libID&browseType=wait"), "<span class='text'>" . $lang->testcase->statusList['wait'] . "</span>", '', "id='waitTab' class='btn btn-link'");
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->testcase->bySearch;?></a>
    <?php
    if(common::hasPriv('caselib', 'view'))
    {
        $link = helper::createLink('caselib', 'view', "libID=$libID");
        echo html::a($link, "<i class='icon icon-list-alt muted'> </i>" . $this->lang->caselib->view, '', "class='btn btn-link'");
    }
    ?>
  </div>
  <div class='btn-toolbar pull-right'>
    <div class='btn-group'>
      <button type='button' class='btn btn-link dropdown-toggle' data-toggle='dropdown'>
        <i class='icon icon-export muted'></i> <?php echo $lang->export ?>
        <span class='caret'></span>
      </button>
      <ul class='dropdown-menu pull-right' id='exportActionMenu'>
        <?php
        $class = common::hasPriv('caselib', 'export') ? '' : 'class=disabled';
        $misc  = common::hasPriv('caselib', 'export') ? "class='export'" : 'class=disabled';
        $link  = common::hasPriv('caselib', 'export') ? $this->createLink('caselib', 'export', "libID=$libID&browseType=$browseType&param=$param&orderBy=$orderBy") : '#';
        echo "<li $class>" . html::a($link, $lang->caselib->export, '', $misc . "data-app={$this->app->openApp}") . '</li>';

        $class = common::hasPriv('caselib', 'exportTemplet') ? '' : 'class=disabled';
        $misc = common::hasPriv('caselib', 'exportTemplet') ? "class='export'" : 'class=disabled';
        $link = common::hasPriv('caselib', 'exportTemplet') ? $this->createLink('caselib', 'exportTemplet', "libID=$libID") : '#';
        echo "<li $class>" . html::a($link, $lang->caselib->exportTemplet, '', $misc . "data-app={$this->app->openApp} data-width='50%'") . '</li>';
        ?>
      </ul>
    </div>
    <div class='btn-group'>
      <?php common::printLink('caselib', 'import', "libID=$libID", "<i class='icon muted icon-import'> </i>" . $lang->testcase->fileImport, '', "class='btn btn-link export'"); ?>
    </div>
    <?php echo html::a($this->createLink('caselib', 'create'), "<i class='icon icon-plus'> </i>" . $lang->caselib->create, '', 'class="btn btn-secondary"'); ?>
    <div class='btn-group dropdown'>
      <?php
        $params   = "libID=$libID&moduleID=" . (isset($moduleID) ? $moduleID : 0);
        $actionLink = $this->createLink('caselib', 'createCase', $params);
        echo html::a($actionLink, "<i class='icon icon-plus'></i> {$lang->testcase->create}", '', "class='btn btn-primary'");
        ?>
      <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
      <ul class='dropdown-menu'>
        <li><?php echo html::a($actionLink, $lang->testcase->create); ?></li>
        <li><?php echo html::a($this->createLink('caselib', 'batchCreateCase', $params), $lang->testcase->batchCreate); ?></li>
      </ul>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="side-col" id="sidebar">
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class="cell">
      <?php if(!$moduleTree):?>
      <hr class="space">
      <div class="text-center text-muted">
        <?php echo $lang->caselib->noModule; ?>
      </div>
      <hr class="space">
      <?php endif; ?>
      <?php echo $moduleTree; ?>
      <div class="text-center">
        <?php common::printLink('tree', 'browse', "libID=$libID&view=caselib&currentModuleID=0&branch=0&from={$this->lang->navGroup->caselib}", $lang->tree->manage, '', "class='btn btn-info btn-wide'"); ?>
        <hr class="space-sm" />
      </div>
    </div>
  </div>
  <div class="main-col">
    <div id='queryBox' data-module='caselib' class='cell <?php if($browseType == 'bysearch') {echo 'show';}?>'></div>
    <?php $useDatatable = ''; ?>
    <?php if(empty($cases)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->testcase->noCase; ?></span>
        <?php if(common::hasPriv('caselib', 'createCase')):?>
        <?php echo html::a($this->createLink('caselib', 'createCase', "libID=$libID&moduleID=" . (isset($moduleID) ? $moduleID : 0)), "<i class='icon icon-plus'></i> " . $lang->caselib->createCase, '', "class='btn btn-info'"); ?>
        <?php endif; ?>
      </p>
    </div>
    <?php else:?>
    <?php
      $datatableId = $this->moduleName . ucfirst($this->methodName);
      $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable'); ?>
    <form class="main-table table-case" data-ride="table" method="post" id='caselibForm' <?php if(!$useDatatable) echo "data-ride='table'";?>>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php $canBatchEdit     = common::hasPriv('caselib', 'batchEditCase'); ?>
      <?php $canBatchDelete    = common::hasPriv('testcase', 'batchDelete'); ?>
      <?php $canBatchReview    = common::hasPriv('testcase', 'batchReview') and ($config->testcase->needReview or !empty($config->testcase->forceReview)); ?>
      <?php $canBatchChangeModule = common::hasPriv('testcase', 'batchChangeModule'); ?>
      <?php $canBatchAction    = ($canBatchEdit or $canBatchDelete or $canBatchReview or $canBatchChangeModule); ?>
      <?php

      $vars = "libID=$libID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";
      if($useDatatable) include '../../../common/view/datatable.html.php'; 
      if(!$useDatatable) include '../../../common/view/tablesorter.html.php'; 

      $setting = $this->datatable->getSetting('caselib');
      $widths = $this->datatable->setFixedFieldWidth($setting);

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
                $this->datatable->printHead($value, $orderBy, $vars, $canBatchAction);
                $columns++;
              }
            }
            ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cases as $case):?>
          <tr data-id='<?php echo $case->id?>'>
            <?php foreach($setting as $key => $value)
            {
              $this->caselib->printCell($value, $case, $users, $modulePairs, $browseType, $useDatatable ? 'datatable' : 'table');
            }?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php if(!$useDatatable)
      {
        echo '</div>';
      }?>
      <div class='table-footer'>
        <?php if($canBatchAction):?>
        <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
        <?php endif; ?>
        <div class="table-actions btn-toolbar">
          <div class='btn-group dropup'>
            <?php $actionLink = $this->createLink('caselib', 'batchEditCase', "libID=$libID&branch=0&type=lib"); ?>
            <?php $misc    = $canBatchEdit ? "onclick=\"setFormAction('$actionLink')\"" : "disabled='disabled'"; ?>
            <?php echo html::commonButton($lang->edit, $misc); ?>
            <?php if($canBatchDelete or $canBatchReview or $canBatchChangeModule):?>
            <button type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
            <ul class='dropdown-menu' id='moreActionMenu'>
              <?php
           if($canBatchDelete)
           {
             $actionLink = $this->createLink('testcase', 'batchDelete', "libID=$libID");
             $misc    = "onclick=\"confirmBatchDelete('$actionLink')\"";
             echo '<li>' . html::a('#', $lang->delete, '', $misc) . '</li>';
           }

           if($canBatchReview)
           {
             echo "<li class='dropdown-submenu'>";
             echo html::a('javascript:;', $lang->testcase->review, '', "id='reviewItem'");
             echo "<ul class='dropdown-menu'>";
             unset($lang->testcase->reviewResultList['']);
             foreach($lang->testcase->reviewResultList as $key => $result)
             {
               $actionLink = $this->createLink('testcase', 'batchReview', "result=$key");
               echo '<li>' . html::a('#', $result, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"") . '</li>';
             }
             echo '</ul></li>';
           }

           if($canBatchChangeModule)
           {
             $withSearch = count($modules) > 8;
             echo "<li class='dropdown-submenu'>";
             echo html::a('javascript:;', $lang->testcase->moduleAB, '', "id='moduleItem'");
             echo "<div class='dropdown-menu" . ($withSearch ? ' with-search' : '') . "'>";
             echo '<ul class="dropdown-list">';
             foreach($modules as $moduleId => $module)
             {
               $actionLink = $this->createLink('testcase', 'batchChangeModule', "moduleID=$moduleId");
               echo "<li class='option' data-key='$moduleID'>" . html::a('#', $module, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"") . '</li>';
             }
             echo '</ul>';
             if($withSearch)
             {
               echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
             }
             echo '</div></li>';
           }
              ?>
            </ul>
            <?php endif; ?>
          </div>
        </div>
        <div class='table-statistic'></div>
        <?php $pager->show('right', 'pagerjs'); ?>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>
<script>
  $('#module' + moduleID).addClass('active');
  $('#<?php echo $this->session->libBrowseType?>Tab').addClass('btn-active-text').append(" <span class='label label-light label-badge'><?php echo $pager->recTotal; ?></span>");
  <?php if($useDatatable):?>
  $(function() {
    $('#caselibForm').table();
  })
  <?php endif; ?>
</script>
<?php include '../../../common/view/footer.html.php'; ?>
