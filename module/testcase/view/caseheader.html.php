<?php js::set('flow', $config->global->flow);?>
<?php $isProjectApp  = $this->app->openApp == 'project'?>
<?php $currentModule = $isProjectApp ? 'project'  : 'testcase';?>
<?php $currentMethod = $isProjectApp ? 'testcase' : 'browse';?>
<?php $projectParam  = $isProjectApp ? "projectID={$this->session->project}&" : '';?>

<?php $projectParamAtEnd = substr($projectParam, 0, -1) ?>

<?php if(!isset($branch)) $branch = 0;?>
<?php if($config->global->flow == 'full'):?>
<style>
body {margin-bottom: 25px;}
.btn-group a i.icon-plus {font-size: 16px;}
.btn-group a.btn-primary {border-right: 1px solid rgba(255,255,255,0.2);}
.btn-group button.dropdown-toggle.btn-primary {padding:6px;}
.body-modal #mainMenu>.btn-toolbar {width: auto;}
</style>
<div id='mainMenu' class='clearfix'>
  <div id="sidebarHeader">
    <div class="title">
      <?php
      if($this->app->rawMethod == 'browseunits')
      {
          echo $lang->testtask->unitTag[$browseType];
      }
      else
      {
          echo !empty($moduleID) ? $moduleName : $this->lang->tree->all;
          if(!empty($moduleID))
          {
              $removeLink = $browseType == 'bymodule' ? $this->createLink($currentModule, $currentMethod, $projectParam . "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=$browseType&param=0&orderBy=$orderBy&recTotal=0&recPerPage={$pager->recPerPage}") : 'javascript:removeCookieByKey("caseModule")';
              echo html::a($removeLink, "<i class='icon icon-sm icon-close'></i>", '', "class='text-muted' data-app='{$this->app->openApp}'");
          }
      }
      ?>
    </div>
  </div>
  <div class='btn-toolbar pull-left'>
    <?php
    $hasBrowsePriv = common::hasPriv('testcase', 'browse');
    $hasGroupPriv  = common::hasPriv('testcase', 'groupcase');
    $hasZeroPriv   = common::hasPriv('testcase', 'zerocase');
    $hasUnitPriv   = common::hasPriv('testtask', 'browseunits');
    ?>
    <?php foreach(customModel::getFeatureMenu('testcase', 'browse') as $menuItem):?>
    <?php
    if(isset($menuItem->hidden)) continue;
    $menuType = $menuItem->name;

    if(!$config->testcase->needReview and empty($config->testcase->forceReview) and $menuType == 'wait') continue;
    if($hasBrowsePriv and $menuType == 'QUERY' and $this->app->rawMethod != 'groupcase' and $this->app->rawMethod != 'browseunits' and $this->app->rawMethod != 'zerocase')
    {
        $searchBrowseLink = $this->createLink($currentModule, $currentMethod, $projectParam . "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=bySearch&param=%s");
        $isBySearch       = $browseType == 'bysearch';
        include $this->app->getModuleRoot() . 'common/view/querymenu.html.php';
    }
    elseif($hasBrowsePriv and ($menuType == 'all' or $menuType == 'needconfirm' or $menuType == 'wait'))
    {
        echo html::a($this->createLink($currentModule, $currentMethod, $projectParam . "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=$menuType"), "<span class='text'>{$menuItem->text}</span>", '', "class='btn btn-link' id='{$menuType}Tab' data-app='{$this->app->openApp}'");
    }
    elseif($hasBrowsePriv and $menuType == 'suite' and ($this->app->openApp == 'qa' or $isProjectApp))
    {
        $currentSuiteID = isset($suiteID) ? (int)$suiteID : 0;
        $currentSuite   = zget($suiteList, $currentSuiteID, '');
        $currentLable   = empty($currentSuite) ? $lang->testsuite->common : $currentSuite->name;

        echo "<div id='bysuiteTab' class='btn-group'>";
        echo html::a('javascript:;', "<span class='text'>{$currentLable}</span>" . " <span class='caret'></span>", '', "class='btn btn-link' data-toggle='dropdown'");

        if(!empty($productID) || $productID == 0)
        {
            echo "<ul class='dropdown-menu' style='max-height:240px; overflow-y:auto'>";

            if(empty($suiteList))
            {
                $testsuiteOpenClass = '';
                if($applicationID)
                {
                    $createTestsuiteLink = $this->createLink('testsuite', 'create', "applicationID=$applicationID&productID=$productID");
                }
                else
                {
                    $testsuiteOpenClass = 'iframe';
                    $createTestsuiteLink = $this->createLink('project', 'ajaxSelectProductToBug', "projectID=$projectID&object=testsuite");
                }

                echo '<li>';
                echo html::a($createTestsuiteLink, "<i class='icon-plus'></i>" . $lang->testsuite->create,'',"class='$testsuiteOpenClass' data-app='{$this->app->openApp}'");
                echo '</li>';
            }
        }

        foreach($suiteList as $suiteID => $suite)
        {
            $suiteName = $suite->name;
            echo '<li' . ($suiteID == (int)$currentSuiteID ? " class='active'" : '') . '>';
            echo html::a($this->createLink($currentModule, $currentMethod, $projectParam . "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=bySuite&param=$suiteID"), $suiteName);
            echo "</li>";
        }

        echo '</ul></div>';
    }
    elseif($hasGroupPriv and $menuType == 'group' and $applicationID > 0)
    {
        $groupBy = isset($groupBy)  ? $groupBy : '';
        $active  = !empty($groupBy) ? 'btn-active-text' : '';

        echo "<div id='groupTab' class='btn-group'>";
        echo html::a($this->createLink('testcase', 'groupCase', "applicationID=$applicationID&productID=$productID&branch=$branch&groupBy=story"), "<span class='text'>{$lang->testcase->groupByStories}</span>", '', "class='btn btn-link $active' data-app='{$this->app->openApp}'");
        echo '</div>';
    }
    elseif($hasZeroPriv and $menuType == 'zerocase' and (is_numeric($productID) and $productID != 0))
    {
        echo html::a($this->createLink('testcase', 'zeroCase', "applicationID=$applicationID&productID=$productID&branch=$branch&orderBy=id_desc"), "<span class='text'>{$lang->story->zeroCase}</span>", '', "class='btn btn-link' id='zerocaseTab' data-app='{$this->app->openApp}'");
    }
    elseif($hasUnitPriv and $menuType == 'browseunits' and !$isProjectApp)
    {
        echo html::a($this->createLink('testtask', 'browseUnits', "applicationID=$applicationID&productID=$productID"), "<span class='text'>{$lang->testcase->browseUnits}</span>", '', "class='btn btn-link' id='browseunitsTab' data-app='{$this->app->openApp}'");
    }
    ?>
    <?php endforeach;?>
    <?php
    if($this->methodName == 'browse' || $this->methodName == 'testcase') echo "<a id='bysearchTab' class='btn btn-link querybox-toggle'><i class='icon-search icon'></i> {$lang->testcase->bySearch}</a>";
    ?>
  </div>
  <?php if(!isonlybody()):?>
  <div class='btn-toolbar pull-right'>
    <div class='btn-group'>
      <button type='button' class='btn btn-link dropdown-toggle' data-toggle='dropdown'>
        <i class='icon icon-export muted'></i> <?php echo $lang->export ?>
        <span class='caret'></span>
      </button>
      <ul class='dropdown-menu pull-right' id='exportActionMenu'>
      <?php
      $class = common::hasPriv('testcase', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('testcase', 'export') ? "class='export'" : "class=disabled";
      $link  = common::hasPriv('testcase', 'export') ?  $this->createLink('testcase', 'export', "applicationID=$applicationID&productID=$productID&orderBy=$orderBy&taskID=0&browseType=$browseType&$projectParamAtEnd") : '#';
      echo "<li $class>" . html::a($link, $lang->testcase->export, '', $misc . "data-app={$this->app->openApp}") . "</li>";

      if($productID != 'all' and $productID != 'na' and $productID != '0')
      {
        $class = common::hasPriv('testcase', 'exportTemplet') ? '' : "class=disabled";
        $misc  = common::hasPriv('testcase', 'exportTemplet') ? "class='export'" : "class=disabled";
        $link  = common::hasPriv('testcase', 'exportTemplet') ?  $this->createLink('testcase', 'exportTemplet', "applicationID=$applicationID&productID=$productID&$projectParamAtEnd") : '#';
        echo "<li $class>" . html::a($link, $lang->testcase->exportTemplet, '', $misc . "data-app={$this->app->openApp} data-width='50%'") . "</li>";

        $class = common::hasPriv('testcase', 'exportFreemind') ? '' : "class=disabled";
        $misc  = common::hasPriv('testcase', 'exportFreemind') ? "class='export'" : "class=disabled";
        $link  = common::hasPriv('testcase', 'exportFreemind') ?  $this->createLink('testcase', 'exportFreemind', "applicationID=$applicationID&productID=$productID&moduleID=$moduleID") : '#';
        echo "<li $class>" . html::a($link, $lang->testcase->exportFreemind, '', $misc . "data-app={$this->app->openApp} data-width='45%'") . "</li>";

        $class = common::hasPriv('testcase', 'exportXmind') ? '' : "class=disabled";
        $misc  = common::hasPriv('testcase', 'exportXmind') ? "class='export'" : "class=disabled";
        $link  = common::hasPriv('testcase', 'exportXmind') ?  $this->createLink('testcase', 'exportXmind', "applicationID=$applicationID&productID=$productID&moduleID=$moduleID") : '#';
        echo "<li $class>" . html::a($link, $lang->testcase->exportXmind, '', $misc . "data-app={$this->app->openApp} data-width='45%'") . "</li>";
      }
      ?>
      </ul>
    </div>
    <div class='btn-group'>
      <button type='button' class='btn btn-link dropdown-toggle' data-toggle='dropdown' id='importAction'><i class='icon icon-import muted'></i> <?php echo $lang->import ?><span class='caret'></span></button>
      <ul class='dropdown-menu pull-right' id='importActionMenu'>
      <?php
      if($productID != 'all')
      {
        $class = common::hasPriv('testcase', 'import') ? '' : "class=disabled";
        $misc  = common::hasPriv('testcase', 'import') ? "class='export'" : "class=disabled";
        $link  = common::hasPriv('testcase', 'import') ?  $this->createLink('testcase', 'import', "applicationID=$applicationID&productID=$productID&branch=$branch") : '#';
        echo "<li $class>" . html::a($link, $lang->testcase->fileImport, '', $misc . "data-app={$this->app->openApp}") . "</li>";

        $class = common::hasPriv('testcase', 'importXmind') ? '' : "class=disabled";
        $misc  = common::hasPriv('testcase', 'importXmind') ? "class='export'" : "class=disabled";
        $link  = common::hasPriv('testcase', 'importXmind') ?  $this->createLink('testcase', 'importXmind', "applicationID=$applicationID&productID=$productID&branch=$branch") : '#';
        echo "<li $class>" . html::a($link, $lang->testcase->xmindImport, '', $misc . "data-app={$this->app->openApp}") . "</li>";
      }

      if($isProjectApp)
      {
        $class = common::hasPriv('project', 'importFromLib') ? '' : "class=disabled";
        $misc  = common::hasPriv('project', 'importFromLib') ? "data-app='{$this->app->openApp}'" : "class=disabled";
        $link  = common::hasPriv('project', 'importFromLib') ?  $this->createLink('project', 'ajaxImportFromLib', "projectID=$projectID&applicationID=$applicationID&productID=$productID") : '#';
        echo "<li $class>" . html::a($link, $lang->testcase->importFromLib, '', $misc . "data-app={$this->app->openApp} class='iframe'") . "</li>";
      }
      else
      {
        $class = common::hasPriv('testcase', 'importFromLib') ? '' : "class=disabled";
        $misc  = common::hasPriv('testcase', 'importFromLib') ? "data-app='{$this->app->openApp}'" : "class=disabled";
        $link  = common::hasPriv('testcase', 'importFromLib') ? $this->createLink('testcase', 'ajaxImportFromLib', "applicationID=$applicationID&productID=$productID&branch=$branch&moduleID=$moduleID") : '#';
        echo "<li $class>" . html::a($link, $lang->testcase->importFromLib, '', $misc . "data-app={$this->app->openApp} class='iframe'") . "</li>";
      }
      ?>
      </ul>
    </div>

    <?php if(!empty($productID) || $productID == 0):?>
    <?php $initModule = isset($moduleID) ? (int)$moduleID : 0;?>
    <?php if(!common::checkNotCN()):?>
    <div class='btn-group dropdown create-btn-group'>
      <?php
      $btnOpenClass = '';
      if($applicationID)
      {
        $createTestcaseLink = $this->createLink('testcase', 'create', "applicationID=$applicationID&productID=$productID&branch=$branch&moduleID=$initModule");
        $batchCreateLink    = $this->createLink('testcase', 'batchCreate', "applicationID=$applicationID&productID=$productID&branch=$branch&moduleID=$initModule");
      }
      else
      {
        $btnOpenClass = 'iframe';
        $createTestcaseLink = $this->createLink('project', 'ajaxSelectProductToBug', "projectID=$projectID&object=testcase");
        $batchCreateLink    = $this->createLink('project', 'ajaxSelectProductToBug', "projectID=$projectID&object=testcaseBatchCreate");
      }

      $buttonLink  = '';
      $buttonTitle = '';
      if(common::hasPriv('testcase', 'batchCreate'))
      {
        $buttonLink  = (!empty($productID)  || $productID == 0) ? $batchCreateLink : '';
        $buttonTitle = $lang->testcase->batchCreate;
      }
      if(common::hasPriv('testcase', 'create'))
      {
        $buttonLink  = $createTestcaseLink;
        $buttonTitle = $lang->testcase->create;
      }

      $hidden = empty($buttonLink) ? 'hidden' : '';
      echo html::a($buttonLink, "<i class='icon-plus'></i> " . $buttonTitle, '', "class='btn btn-primary $hidden $btnOpenClass' data-app='{$this->app->openApp}'");
      ?>
      <?php if((!empty($productID) || $productID == 0) and common::hasPriv('testcase', 'batchCreate') and common::hasPriv('testcase', 'create')):?>
      <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
      <ul class='dropdown-menu'>
        <li><?php echo html::a($createTestcaseLink, $lang->testcase->create,'',"class='$btnOpenClass' data-app='{$this->app->openApp}'");?></li>
        <li><?php echo html::a($batchCreateLink, $lang->testcase->batchCreate, '', "class='$btnOpenClass' data-app='{$this->app->openApp}'");?></li>
      </ul>
      <?php endif;?>
    </div>
    <?php if($this->app->rawMethod == 'browseunits'):?>
      <?php common::printLink('testtask', 'importUnitResult', "applicationID=$applicationID&product=$productID", "<i class='icon icon-import'></i> " . $lang->testtask->importUnitResult, '', "class='btn btn-primary' data-app='{$this->app->openApp}'");?>
    <?php endif;?>
    <?php else:?>
    <?php
    $btnOpenClass = '';
    if($applicationID)
    {
      $createTestcaseLink = $this->createLink('testcase', 'create', "applicationID=$applicationID&productID=$productID&branch=$branch&moduleID=$initModule");
      $batchCreateLink    = $this->createLink('testcase', 'batchCreate', "applicationID=$applicationID&productID=$productID&branch=$branch&moduleID=$initModule");
    }
    else
    {
      $btnOpenClass = 'iframe';
      $createTestcaseLink = $this->createLink('project', 'ajaxSelectProductToBug', "projectID=$project->id&object=testcase");
      $batchCreateLink    = $this->createLink('project', 'ajaxSelectProductToBug', "projectID=$project->id&object=testcaseBatchCreate");
    }
    ?>
    <div class='btn-group dropdown-hover create-btn-group'>
      <?php
      $disabled = common::hasPriv('testcase', 'create') ? '' : "disabled";
      echo html::a($createTestcaseLink, "<i class='icon icon-plus'></i> {$lang->testcase->create} </span><span class='caret'>", '', "class='btn btn-primary $disabled $btnOpenClass' data-app='project'");
      ?>
      <ul class='dropdown-menu'>
        <?php $disabled = common::hasPriv('testcase', 'batchCreate') ? '' : "class='disabled'";?>
        <li <?php echo $disabled?>>
        <?php
        echo "<li>" . html::a($batchCreateLink, "<i class='icon icon-plus'></i>" . $lang->testcase->batchCreate, '', "class='$btnOpenClass' data-app='{$this->app->openApp}'") . "</li>";
        ?>
        </li>
      </ul>
    </div>
    <?php endif;?>
    <?php endif;?>
  </div>
  <?php endif;?>
</div>
<?php endif;?>

<?php
$headerHooks = glob(dirname(dirname(__FILE__)) . "/ext/view/featurebar.*.html.hook.php");
if(!empty($headerHooks))
{
    foreach($headerHooks as $fileName) include($fileName);
}
?>
