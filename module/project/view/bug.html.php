<?php
/**
 * The bug view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: bug.html.php 4894 2013-06-25 01:28:39Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php 
  $config->project->datatable = $config->bug->datatable;

  $config->project->datatable->defaultField = $config->project->datatableBug->defaultField;
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<?php
js::set('moduleID', $moduleID);
?>
<style>
body {margin-bottom: 25px;}
#subHeader #dropMenu .col-left .list-group {margin-bottom: 0px; padding-top: 10px;}
#subHeader #dropMenu .col-left {padding-bottom: 0px;}
#mainMenu .btn-toolbar .btn-group .dropdown-menu .btn-active-text:hover .text {color: #fff;}
#mainMenu .btn-toolbar .btn-group .dropdown-menu .btn-active-text:hover .text:after {border-bottom: unset;}
</style>
<div id="mainMenu" class="clearfix main-row fade in">
   <div id="sidebarHeader">
    <div class="title">
      <?php
      echo !empty($moduleID) ? $moduleName : $this->lang->tree->all;
      if(!empty($moduleID))
      {
          $removeLink = $browseType == 'bymodule' ? $this->createLink('project', 'bug', "projectID={$this->session->project}&" . "applicationID=$applicationID&productID=$productID&browseType=$browseType&branch=$branch&build=$build&orderBy=$orderBy&param=0&recTotal=0&recPerPage={$pager->recPerPage}") : 'javascript:removeCookieByKey("bugModule")';
          echo html::a($removeLink, "<i class='icon icon-sm icon-close'></i>", '', "class='text-muted' data-app='{$this->app->openApp}'");
      }
      ?>
    </div>
  </div>
  <div class="btn-toolbar pull-left">
    <?php
    $menus = customModel::getFeatureMenu('bug', 'browse');
    foreach($menus as $menuItem)
    {
        if(isset($menuItem->hidden)) continue;
        $menuBrowseType = strpos($menuItem->name, 'QUERY') === 0 ? 'bySearch' : $menuItem->name;
        $label  = "<span class='text'>{$menuItem->text}</span>";
        $label .= $menuBrowseType == $this->session->projectBugBrowseType ? " <span class='label label-light label-badge'>{$pager->recTotal}</span>" : '';
        $active = $menuBrowseType == $this->session->projectBugBrowseType ? 'btn-active-text' : '';

        if($menuItem->name == 'my')
        {
            echo "<li id='statusTab' class='dropdown " . (!empty($currentBrowseType) ? 'active' : '') . "'>";
            echo html::a('javascript:;', $menuItem->text . " <span class='caret'></span>", '', "data-toggle='dropdown' class='btn btn-link'");
            echo "<ul class='dropdown-menu'>";
            foreach($lang->bug->mySelects as $key => $value)
            {
                echo '<li' . ($key == $currentBrowseType ? " class='active'" : '') . '>';
                echo html::a($this->createLink('project', 'bug', "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=$key&branch=$branch"), $value);
            }
            echo '</ul></li>';
        }
        elseif($menuItem->name == 'QUERY')
        {
            $searchBrowseLink = inlink('bug', "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=bySearch&branch=$branch&build=0&orderBy=status,id_desc&param=%s");
            $isBySearch       = $browseType == 'bysearch';
            include '../../common/view/querymenu.html.php';
        }
        elseif($menuItem->name == 'more')
        {
            if(!empty($lang->bug->moreSelects))
            {
                $moreLabel       = $lang->more;
                $moreLabelActive = '';
                if(isset($lang->bug->moreSelects[$browseType]))
                {
                    $moreLabel       = "<span class='text'>{$lang->bug->moreSelects[$browseType]}</span> <span class='label label-light label-badge'>{$pager->recTotal}</span>";
                    $moreLabelActive = 'btn-active-text';
                }
                echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link {$moreLabelActive}'>{$moreLabel} <span class='caret'></span></a>";
                echo "<ul class='dropdown-menu'>";
                foreach($lang->bug->moreSelects as $menuBrowseType => $label)
                {
                    $active = $menuBrowseType == $browseType ? 'btn-active-text' : '';
                    echo '<li>' . html::a($this->createLink('project', 'bug', "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=$menuBrowseType&branch=$branch"), "<span class='text'>{$label}</span>", '', "class='btn btn-link $active'") . '</li>';
                }
                echo '</ul></div>';
            }
        }
        else
        {
            echo html::a($this->createLink('project', 'bug', "projectID=$projectID&applicationID=$applicationID&productID=$productID&browseType=$menuBrowseType&branch=$branch"), $label, '', "class='btn btn-link $active'");
        }
    }
    ?>
    <a class="btn btn-link querybox-toggle" id="bysearchTab"><i class="icon icon-search muted"></i> <?php echo $lang->bug->search;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button type='button' class='btn btn-link dropdown-toggle' data-toggle='dropdown'>
        <i class="icon icon-export muted"></i> <span class="text"> <?php echo $lang->export;?></span> <span class="caret"></span></button>
      </button>
      <ul class='dropdown-menu' id='exportActionMenu'>
        <?php
        $hasPriv = common::hasPriv('bug', 'export');
        if($hasPriv)
        {
            $class   = $hasPriv ? "" : "class='disabled'";
            $misc    = $hasPriv ? "class='export'" : "class='disabled'";
            $link    = $hasPriv ? $this->createLink('bug', 'export', "applicationID=$applicationID&productID=$productID&orderBy=$orderBy&browseType=&projectID=$project->id") : '#';
            echo "<li $class>" . html::a($link, $lang->bug->export, '', $misc) . "</li>";
        }

        $hasPriv = common::hasPriv('bug', 'exportTemplet');
        if($hasPriv and $productID != 'all')
        {
            $class   = $hasPriv ? '' : "class='disabled'";
            $link    = $hasPriv ? $this->createLink('bug', 'exportTemplet', "applicationID=$applicationID&productID=$productID&branch=$branch&projectID=$project->id") : '#';
            $misc    = $hasPriv ? "class='exportTemplet'" : "class='disabled'";
            echo "<li $class>" . html::a($link, $lang->bug->exportTemplet, '', $misc) . '</li>';
        }
        ?>
      </ul>
    </div>

    <?php
    if(common::hasPriv('bug', 'import') and $productID != 'all')
    {
        echo html::a($this->createLink('bug', 'import', "applicationID=$applicationID&productID=$productID&branch=$branch"), '<i class="icon-import muted"></i> ' . $lang->bug->import, '', "class='btn btn-link import'");
    }
    ?>
    <div class='btn-group dropdown-hover'>
      <?php
      $btnOpenClass = '';
      if($applicationID)
      {
        $createProjectLink = $this->createLink('bug', 'create', "applicationID=$applicationID&productID=$productID&branch=$branch&extras=projectID=$project->id");
        $batchCreateLink   = $this->createLink('bug', 'batchCreate', "applicationID=$applicationID&productID=$productID&branch=$branch&executionID=0&moduleID=$moduleID");
      }
      else
      {
        $btnOpenClass      = 'iframe';
        $createProjectLink = $this->createLink('project', 'ajaxSelectProductToBug', "projectID=$projectID&object=bug");
        $batchCreateLink   = $this->createLink('project', 'ajaxSelectProductToBug', "projectID=$projectID&object=bugBatchCreate");
      }
      $buttonLink  = '';
      $buttonTitle = '';
      if(common::hasPriv('testcase', 'batchCreate'))
      {
        $buttonLink  = !empty($productID) ? $batchCreateLink : '';
        $buttonTitle = $lang->bug->batchCreate;
      }
      if(common::hasPriv('testcase', 'create'))
      {
        $buttonLink  = $createProjectLink;
        $buttonTitle = $lang->bug->create;
      }
      $disabled = '';
      if(!common::hasPriv('bug', 'create'))
      {
          $link     = '###';
          $disabled = "disabled";
      }
      echo html::a($buttonLink, "<i class='icon icon-plus'></i> {$lang->bug->create} </span><span class='caret'>", '', "class='btn btn-primary $disabled $btnOpenClass' data-app='{$this->app->openApp}'");
      ?>
      <ul class='dropdown-menu'>
        <?php $disabled = common::hasPriv('bug', 'batchCreate') ? '' : "class='disabled'";?>
        <li <?php echo $disabled?>>
        <?php echo html::a($batchCreateLink, $lang->bug->batchCreate, '', "class='$btnOpenClass' data-app='{$this->app->openApp}'");?>
        </li>
      </ul>
    </div>
  </div>
</div>
<div id="mainContent" class='main-row split-row fade'>
  <div class='side-col' id='sidebar'>
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class='cell'>
      <?php if(!$moduleTree):?>
      <hr class="space">
      <div class="text-center text-muted"><?php echo $lang->bug->noModule;?></div>
      <hr class="space">
      <?php endif;?>
      <?php echo $moduleTree;?>
      <div class='text-center'>
        <?php if(is_numeric($productID) && $productID > 0) common::printLink('tree', 'browse', "productID=$productID&view=bug&currentModuleID=0&branch=0&from={$this->lang->navGroup->bug}", $lang->tree->manage, '', "class='btn btn-info btn-wide'");?>
        <hr class="space-sm" />
      </div>
    </div>
  </div>
  <div class='main-col' data-min-width='400'>
    <div class="cell <?php if($browseType == 'bysearch') echo 'show';?>" id="queryBox" data-module='projectBug'></div>
    <?php if(empty($bugs)):?>
    <?php $useDatatable = '';?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->bug->noBug;?></span>
        <?php if($applicationID and common::hasPriv('bug', 'create')):?>
        <?php echo html::a($this->createLink('bug', 'create', "applicationID=$applicationID&productID=$productID&branch=$branch&extras=projectID=$project->id"), "<i class='icon icon-plus'></i> " . $lang->bug->create, '', "class='btn btn-info' data-app='project'");?>
        <?php endif;?>
      </p>
    </div>
    <?php else:?>
    <?php
    $datatableId  = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');
    ?>
    <form class='main-table' method='post' id='projectBugForm' <?php if(!$useDatatable) echo "data-ride='table'";?>>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php $canBatchAssignTo = common::hasPriv('bug', 'batchAssignTo');?>
      <?php 
      $vars = "projectID={$project->id}&applicationID={$applicationID}&productID=$productID&browseType=$browseType&branch=$branch&build=$buildID&orderBy=%s&param=$param&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; 

      if($useDatatable)  include '../../common/view/datatable.html.php';
      if(!$useDatatable) include '../../common/view/tablesorter.html.php';

      $setting = $this->datatable->getSetting('project');
      $widths  = $this->datatable->setFixedFieldWidth($setting);
      $columns = 0;

      $canBeChanged         = true;
      $canBatchEdit         = ($canBeChanged and common::hasPriv('bug', 'batchEdit'));
      $canBatchConfirm      = ($canBeChanged and common::hasPriv('bug', 'batchConfirm'));
      $canBatchAssignTo     = ($canBeChanged and common::hasPriv('bug', 'batchAssignTo'));
      $canBatchChangeModule = ($canBeChanged and common::hasPriv('bug', 'batchChangeModule'));

      $canBatchClose        = false;
      $canBatchActivate     = false;
      $canBatchResolve      = false;
      $canBatchAction       = ($canBatchEdit or $canBatchConfirm or $canBatchClose or $canBatchActivate or $canBatchChangeModule or $canBatchResolve or $canBatchAssignTo);
      ?>
      <?php if(!$useDatatable) echo '<div class="table-responsive">';?>
      <table class='table has-sort-head<?php if($useDatatable) echo ' datatable';?>' id='projectBugList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' data-checkbox-name='caseIDList[]'>
      <thead>
          <tr>
          <?php
          foreach($setting as $key => $value)
          {
              if($value->show)
              {
                  if(common::checkNotCN() and $value->id == 'severity')  $value->name = $lang->bug->severity;
                  if(common::checkNotCN() and $value->id == 'pri')       $value->name = $lang->bug->pri;
                  if(common::checkNotCN() and $value->id == 'confirmed') $value->name = $lang->bug->confirmed;
                  $this->datatable->printHead($value, $orderBy, $vars);
                  $columns ++;
              }
          }
          ?>
          </tr>
      </thead>
      <?php
        $hasCustomSeverity = false;
        foreach($lang->bug->severityList as $severityKey => $severityValue)
        {
            if(!empty($severityKey) and (string)$severityKey != (string)$severityValue)
            {
                $hasCustomSeverity = true;
                break;
            }
        }
        ?>
      <tbody>
        <?php foreach($bugs as $bug):?>
        <tr data-id='<?php echo $bug->id?>'>
        <?php foreach($setting as $value) $this->bug->printCell($value, $bug, $users, $builds, $branches, $modulePairs, $projects, $plans, $stories, $tasks, $typeTileList, $productsPairs, $useDatatable ? 'datatable' : 'table',$testtasks,$planInfo);?>
        </tr>
        <?php endforeach;?>
      </tbody>
      </table>
      <?php if(!$useDatatable) echo '</div>';?>
      <div class='table-footer'>
        <?php if($canBatchAssignTo):?>
        <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
        <div class="table-actions btn-toolbar">
          <div class='btn-group dropup'>
            <?php
            $actionLink = $this->createLink('bug', 'batchEdit', "applicationID=$applicationID&productID=$productID&branch=$branch");
            $misc       = $canBatchEdit ? "onclick=\"setFormAction('$actionLink')\"" : "disabled='disabled' data-app='project'";
            echo html::commonButton($lang->edit, $misc);
            ?>
            <button type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
            <ul class='dropdown-menu'>
              <?php
              $class      = $canBatchConfirm ? '' : "class='disabled'";
              $actionLink = $this->createLink('bug', 'batchConfirm');
              $misc       = $canBatchConfirm ? "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"" : '';
              echo "<li $class>" . html::a('javascript:;', $lang->bug->confirmBug, '', $misc) . "</li>";

              if($canBatchClose)
              {
                  $class      = $canBatchClose ? '' : "class='disabled'";
                  $actionLink = $this->createLink('bug', 'batchClose');
                  $misc       = $canBatchClose ? "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"" : '';
                  echo "<li $class>" . html::a('javascript:;', $lang->bug->close, '', $misc) . "</li>";
              }

              if($canBatchActivate)
              {
                  $class      = $canBatchActivate ? '' : "class='disabled'";
                  $actionLink = $this->createLink('bug', 'batchActivate', "productID=$productID&branch=$branch");
                  $misc       = $canBatchActivate ? "onclick=\"setFormAction('$actionLink')\"" : '';
                  echo "<li $class>" . html::a('javascript:;', $lang->bug->activate, '', $misc) . "</li>";
              }

              $misc = $canBatchResolve ? "id='resolveItem'" : '';
              if($misc)
              {
                  echo "<li class='dropdown-submenu'>" . html::a('javascript:;', $lang->bug->resolve,  '', $misc);
                  echo "<ul class='dropdown-menu'>";
                  unset($lang->bug->resolutionList['']);
                  unset($lang->bug->resolutionList['duplicate']);
                  unset($lang->bug->resolutionList['tostory']);
                  foreach($lang->bug->resolutionList as $key => $resolution)
                  {
                      $actionLink = $this->createLink('bug', 'batchResolve', "resolution=$key");
                      if($key == 'fixed')
                      {
                          $withSearch = count($builds) > 4;
                          echo "<li class='dropdown-submenu'>";
                          echo html::a('javascript:;', $resolution, '', "id='fixedItem'");
                          echo "<div class='dropdown-menu" . ($withSearch ? ' with-search':'') . "'>";
                          echo '<ul class="dropdown-list">';
                          unset($builds['']);
                          foreach($builds as $key => $build)
                          {
                              $actionLink = $this->createLink('bug', 'batchResolve', "resolution=fixed&resolvedBuild=$key");
                              echo "<li class='option' data-key='$key'>";
                              echo html::a('javascript:;', $build, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"");
                              echo "</li>";
                          }
                          echo "</ul>";
                          if($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                          echo '</div></li>';
                      }
                      else
                      {
                          echo '<li>' . html::a('javascript:;', $resolution, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin')\"") . '</li>';
                      }
                  }
                  echo '</ul></li>';
              }
              ?>
            </ul>
          </div>
          <?php if($canBatchChangeModule and is_numeric($productID) and $productID > 0):?>
          <div class="btn-group dropup">
            <button data-toggle="dropdown" type="button" class="btn"><?php echo $lang->bug->moduleAB;?> <span class="caret"></span></button>
            <?php $withSearch = count($modules) > 8;?>
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
                    $actionLink = $this->createLink('bug', 'batchChangeModule', "moduleID=$moduleId");
                    echo html::a('#', $module, '', "$searchKey onclick=\"setFormAction('$actionLink', 'hiddenwin')\" data-key='$moduleID'");
                }
                ?>
              </div>
            </div>
          </div>
          <?php endif;?>
          <div class="btn-group dropup">
            <button data-toggle="dropdown" type="button" class="btn"><?php echo $lang->bug->assignedTo?> <span class="caret"></span></button>
            <?php
            $withSearch = count($memberPairs) > 10;
            $actionLink = $this->createLink('bug', 'batchAssignTo', "projectID={$project->id}&type=project&applicationID=$applicationID&productID=$productID");
            echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');

            if($withSearch)
            {
                echo "<div class='dropdown-menu search-list search-box-sink' data-ride='searchList'>";
                echo '<div class="input-control search-box has-icon-left has-icon-right search-example">';
                echo '<input id="userSearchBox" type="search" class="form-control search-input" autocomplete="off" />';
                echo '<label for="userSearchBox" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label>';
                echo '<a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a>';
                echo '</div>';
                $membersPinYin = common::convert2Pinyin($memberPairs);
            }
            else
            {
                echo "<div class='dropdown-menu search-list'>";
            }
            echo '<div class="list-group">';
            foreach($memberPairs as $key => $value)
            {
                if(empty($key)) continue;
                $searchKey = $withSearch ? ('data-key="' . zget($membersPinYin, $value, '') . " @$key\"") : "data-key='@$key'";
                echo html::a("javascript:$(\".table-actions #assignedTo\").val(\"$key\");setFormAction(\"$actionLink\")", $value, '', $searchKey);
            }
            echo "</div>";
            echo "</div>";
            ?>
          </div>
        </div>
        <?php endif;?>
        <div class="table-statistic"><?php echo $summary;?></div>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php js::set('replaceID', 'bugList');?>
<?php js::set('browseType', $browseType);?>
<script>
$('#module' + moduleID).closest('li').addClass('active');
$(".exportTemplet").modalTrigger({width:650, type:'iframe'});
$(".import").modalTrigger({width:650, type:'iframe'});
if($('#projectBugList thead th.c-title').width() < 150) $('#projectBugList thead th.c-title').width(150);
<?php if($useDatatable):?>
$(function(){$('#projectBugForm').table();})
<?php endif;?>
</script>
<?php include '../../common/view/footer.html.php';?>
