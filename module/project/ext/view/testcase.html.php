<?php
$config->project->datatable = $config->testcase->datatable; 

$config->project->datatable->defaultField = $config->project->datatableTestcase->defaultField;
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datatable.fix.html.php';?>
<?php include $this->app->getModuleRoot() . 'testcase/view/caseheader.html.php';?>
<?php
js::set('moduleID',       $moduleID);
js::set('browseType',     $browseType);
js::set('caseBrowseType', ($browseType == 'bymodule' and $this->session->caseBrowseType == 'bysearch') ? 'all' : $this->session->caseBrowseType);
js::set('confirmDelete',  $lang->testcase->confirmDelete);
js::set('batchDelete',    $lang->testcase->confirmBatchDelete);
js::set('productID',      $productID);
js::set('branch',         $branch);
js::set('suiteID',        $suiteID);
?>
<style>
body {margin-bottom: 25px;}
#subHeader #dropMenu .col-left .list-group {margin-bottom: 0px; padding-top: 10px;}
#subHeader #dropMenu .col-left {padding-bottom: 0px;}
</style>

<div id="mainContent" class='main-row split-row fade'>
  <div class='side-col' id='sidebar'>
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class='cell'>
      <?php if(!$moduleTree):?>
      <hr class="space">
      <div class="text-center text-muted"><?php echo $lang->testcase->noModule;?></div>
      <hr class="space">
      <?php endif;?>
      <?php echo $moduleTree;?>
      <div class='text-center'>
        <?php if(is_numeric($productID) && $productID != 0) common::printLink('tree', 'browse', "productID=$productID&view=case&currentModuleID=0&branch=0&from={$this->lang->navGroup->testcase}", $lang->tree->manage, '', "class='btn btn-info btn-wide'");?>
        <hr class="space-sm" />
      </div>
    </div>
  </div>
  <div class='main-col' data-min-width='400'>
    <div id='queryBox' data-module='projectTestcase' class='cell<?php if($browseType == 'bysearch') echo ' show';?>'></div>
    <?php if(empty($cases)):?>
    <?php $useDatatable = '';?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->testcase->noCase;?></span>
        <?php if($applicationID and common::hasPriv('testcase', 'create') and $browseType != 'bysuite'):?>
        <?php $initModule = isset($moduleID) ? (int)$moduleID : 0;?>
        <?php echo html::a(helper::createLink('testcase', 'create', "applicationID=$applicationID&productID=$productID&branch=$branch&moduleID=$initModule", '', '', '', true), "<i class='icon icon-plus'></i> " . $lang->testcase->create, '', "class='btn btn-info' data-app='{$this->app->openApp}'");?>
        <?php endif;?>

        <?php if(common::hasPriv('testsuite', 'linkCase') and $browseType == 'bysuite'):?>
        <?php echo html::a($this->createLink('testsuite', 'linkCase', "suiteID=$param"), "<i class='icon icon-plus'></i> " . $lang->testsuite->linkCase, '', "class='btn btn-info' data-app='{$this->app->openApp}'");?>
        <?php endif;?>
      </p>
    </div>
    <?php else:?>
    <?php
    $datatableId  = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');
    ?>
    <form class='main-table' method='post' id='projectTestcaseForm' <?php if(!$useDatatable) echo "data-ride='table'";?>>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php 
      $vars = "projectID=$projectID&applicationID=$applicationID&productID=$productID&branchID=$branchID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";

      if($useDatatable) include '../../../common/view/datatable.html.php';
      if(!$useDatatable) include '../../../common/view/tablesorter.html.php';

      if($config->testcase->needReview or !empty($config->testcase->forceReview)) $config->testcase->datatable->fieldList['actions']['width'] = '180';
      $setting = $this->datatable->getSetting('project', 'testcase');
      $widths  = $this->datatable->setFixedFieldWidth($setting);
      $columns = 0;

      $canBatchRun                = common::hasPriv('testtask', 'batchRun');
      $canBatchEdit               = common::hasPriv('testcase', 'batchEdit');
      $canBatchDelete             = common::hasPriv('testcase', 'batchDelete');
      $canBatchCaseTypeChange     = common::hasPriv('testcase', 'batchCaseTypeChange');
      $canBatchConfirmStoryChange = common::hasPriv('testcase', 'batchConfirmStoryChange');
      $canConfirmLibcaseChange       = common::hasPriv('testcase', 'confirmLibcaseChange');
      $canBatchChangeModule       = common::hasPriv('testcase', 'batchChangeModule');
      $canBatchAction             = ($canBatchRun or $canBatchEdit or $canBatchDelete or $canBatchCaseTypeChange or $canBatchConfirmStoryChange or $canConfirmLibcaseChange or $canBatchChangeModule);

      $canView    = common::hasPriv('testcase', 'view');
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
          <?php foreach($cases as $case):?>
          <tr data-id='<?php echo $case->id?>'>
            <?php foreach($setting as $key => $value) $this->testcase->printCell($value, $case, $users, $branches, $modulePairs, $browseType, $useDatatable ? 'datatable' : 'table', $projects, $products);?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <?php if(!$useDatatable) echo '</div>';?>
      <div class="table-footer">
      <?php if($canBatchAction and (!empty($productID) or $productID == 0)):?>
        <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
        <div class='table-actions btn-toolbar'>
          <div class='btn-group dropup'>
            <?php
            $actionLink = $this->createLink('testtask', 'batchRun', "applicationID=$applicationID&productID=$productID&orderBy=$orderBy");
            $misc       = $canBatchRun ? "onclick=\"setFormAction('$actionLink')\"" : "disabled='disabled' data-app='project'";
            $misc      .= " data-app='project'";
            echo html::commonButton($lang->testtask->runCase, $misc);
            
            $actionLink = $this->createLink('testcase', 'batchEdit', "applicationID=$applicationID&productID=$productID&branch=$branch");
            $misc       = $canBatchEdit ? "onclick=\"setFormAction('$actionLink')\"" : "disabled='disabled'";
            $misc      .= " data-app='project'";
            echo html::commonButton($lang->edit, $misc);
            ?>
            <button type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
            <ul class='dropdown-menu' id='moreActionMenu'>
              <?php
              if(common::hasPriv('testcase', 'batchReview') and ($config->testcase->needReview or !empty($config->testcase->forceReview)))
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

              if($canBatchDelete)
              {
                  $actionLink = $this->createLink('testcase', 'batchDelete', "productID=$productID");
                  $misc       = "onclick=\"confirmBatchDelete('$actionLink')\"";
                  echo "<li>" . html::a('#', $lang->delete, '', $misc) . "</li>";
              }

              if($canBatchCaseTypeChange)
              {
                  echo "<li class='dropdown-submenu'>";
                  echo html::a('javascript:;', $lang->testcase->type, '', "id='typeChangeItem'");
                  echo "<ul class='dropdown-menu'>";
                  unset($lang->testcase->typeList['']);
                  foreach($lang->testcase->typeList as $key => $result)
                  {
                      $actionLink = $this->createLink('testcase', 'batchCaseTypeChange', "result=$key");
                      echo '<li>' . html::a('#', $result, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"") . '</li>';
                  }
                  echo '</ul></li>';
              }

              if($canBatchConfirmStoryChange)
              {
                  $actionLink = $this->createLink('testcase', 'batchConfirmStoryChange', "productID=$productID");
                  $misc       = "onclick=\"setFormAction('$actionLink')\"";
                  echo "<li>" . html::a('#', $lang->testcase->confirmStoryChange, '', $misc) . "</li>";
              }

              if($canConfirmLibcaseChange)
              {
                  $actionLink = $this->createLink('testcase', 'batchConfirmLibcaseChange');
                  $misc       = "onclick=\"setFormAction('$actionLink')\"";
                  echo "<li>" . html::a('#', $lang->testcase->confirmLibcaseChange, '', $misc) . "</li>";
              }
              ?>
            </ul>
          </div>
          <?php if($canBatchChangeModule and (is_numeric($productID) && $productID != 0)):?>
          <div class="btn-group dropup">
            <button data-toggle="dropdown" type="button" class="btn"><?php echo $lang->story->moduleAB;?> <span class="caret"></span></button>
            <?php $withSearch = count($modules) > 10;?>
            <?php if($withSearch):?>
            <div class="dropdown-menu search-list search-box-sink" data-ride="searchList">
              <div class="input-control search-box has-icon-left has-icon-right search-example">
                <input id="userSearchBox" type="search" autocomplete="off" class="form-control search-input">
                <label for="userSearchBox" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>
                <a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a>
              </div>
              <?php $modulesPinYin = common::convert2Pinyin($modules);?>
            <?php else:?>
            <div class="dropdown-menu search-list">
            <?php endif;?>
              <div class="list-group">
                <?php
                foreach($modules as $moduleId => $module)
                {
                    $searchKey = $withSearch ? ('data-key="' . zget($modulesPinYin, $module, '') . '"') : '';
                    $actionLink = $this->createLink('testcase', 'batchChangeModule', "moduleID=$moduleId");
                    echo html::a('#', $module, '', "title='$module' $searchKey onclick=\"setFormAction('$actionLink', 'hiddenwin')\"");
                }
                ?>
              </div>
            </div>
          </div>
          <?php endif;?>
        </div>
        <?php endif;?>
        <div class="table-statistic"><?php echo $summary;?></div>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<script>
$('#module' + moduleID).closest('li').addClass('active');
$('#' + caseBrowseType + 'Tab').addClass('btn-active-text').find('.text').after(" <span class='label label-light label-badge'><?php echo $pager->recTotal;?></span>");
$(".exportTemplet").modalTrigger({width:650, type:'iframe'});
$(".import").modalTrigger({width:650, type:'iframe'});
<?php if($useDatatable):?>
$(function(){$('#projectTestcaseForm').table();})
<?php endif;?>
</script>
<?php include '../../../common/view/footer.html.php';?>
