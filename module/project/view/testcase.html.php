<?php include '../../common/view/header.html.php';?>
<style>
#subHeader #dropMenu .col-left .list-group {margin-bottom: 0px; padding-top: 10px;}
#subHeader #dropMenu .col-left {padding-bottom: 0px;}
</style>
<div id="mainMenu" class="clearfix main-row fade in">
  <div class="btn-toolbar pull-left">
    <?php echo html::a(inlink('testcase', "projectID=$projectID&applicationID=$applicationID&productID=$productID&branchID=$branchID&browseType=$browseType&orderBy=$orderBy"), "<span class='text'>{$lang->testcase->featureBar['browse']['all']}</span> <span class='label label-light label-badge'>{$pager->recTotal}</span>", '', "class='btn btn-link btn-active-text'");?>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button type='button' class='btn btn-link dropdown-toggle' data-toggle='dropdown'>
        <i class="icon icon-export muted"></i> <span class="text"> <?php echo $lang->export;?></span> <span class="caret"></span></button>
      </button>
      <ul class='dropdown-menu' id='exportActionMenu'>
        <?php
        $hasPriv = common::hasPriv('testcase', 'export');
        if($hasPriv)
        {
            $class = $hasPriv ? "" : "class='disabled'";
            $misc  = $hasPriv ? "class='export'" : "class='disabled'";
            $link  = $hasPriv ? $this->createLink('testcase', 'export', "applicationID=$applicationID&productID=$productID&orderBy=$orderBy&taskID=0&browseType=&projectID=$project->id") : '#';
            echo "<li $class>" . html::a($link, $lang->testcase->export, '', $misc) . "</li>";
        }

        $hasPriv = common::hasPriv('testcase', 'exportTemplet');
        if($hasPriv and $productID != 'all')
        {
            $class = $hasPriv ? '' : "class='disabled'";
            $link  = $hasPriv ? $this->createLink('testcase', 'exportTemplet', "applicationID=$applicationID&productID=$productID&projectID=$project->id") : '#';
            $misc  = $hasPriv ? "class='exportTemplet'" : "class='disabled'";
            echo "<li $class>" . html::a($link, $lang->testcase->exportTemplet, '', $misc) . '</li>';
        }
        ?>
      </ul>
    </div>

    <?php
    if(common::hasPriv('testcase', 'import') and $productID != 'all')
    {
        echo html::a($this->createLink('testcase', 'import', "applicationID=$applicationID&productID=$productID&branch=0"), '<i class="icon-import muted"></i> ' . $lang->testcase->import, '', "class='btn btn-link import'");
    }
    ?>
    <?php
    if($applicationID and common::hasPriv('testcase', 'create'))
    {
        echo html::a(helper::createLink('testcase', 'create', "applicationID=$applicationID&productID=$productID", '', '', '', true), "<i class='icon icon-plus'></i> " . $lang->testcase->create, '', "class='btn btn-primary' data-app='{$this->app->openApp}'");
    }
    else
    {
        echo html::a($this->createLink('project', 'ajaxSelectProductToBug', "projectID=$project->id&object=testcase"), "<i class='icon icon-plus'></i> " . $lang->testcase->create, '', "class='btn btn-primary iframe'");
    }
    ?>
  </div>
</div>
<div id="mainContent" class='main-row split-row fade'>
  <div class='main-col' data-min-width='400'>
    <?php if(empty($cases)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->testcase->noCase;?></span>
        <?php if($applicationID and common::hasPriv('testcase', 'create')):?>
        <?php echo html::a(helper::createLink('testcase', 'create', "applicationID=$applicationID&productID=$productID", '', '', '', true), "<i class='icon icon-plus'></i> " . $lang->testcase->create, '', "class='btn btn-info' data-app='{$this->app->openApp}'");?>
        <?php endif;?>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='projectBugForm' data-ride="table">
      <table class='table has-sort-head' id='testcaseList'>
      <?php $vars = "projectID=$projectID&applicationID=$applicationID&productID=$productID&branchID=$branchID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
        <thead>
          <tr>
            <th class='w-id'>    <?php common::printOrderLink('id',            $orderBy, $vars, $lang->idAB);?></th>
            <th>                 <?php common::printOrderLink('title',         $orderBy, $vars, $lang->testcase->title);?></th>
            <th class='w-200px'> <?php common::printOrderLink('product',       $orderBy, $vars, $lang->testcase->product);?></th>
            <th class='c-pri'>   <?php common::printOrderLink('pri',           $orderBy, $vars, $lang->priAB);?></th>
            <th class='c-type'>  <?php common::printOrderLink('type',          $orderBy, $vars, $lang->testcase->type);?></th>
            <th class='c-status'><?php common::printOrderLink('status',        $orderBy, $vars, $lang->statusAB);?></th>
            <th class='c-user'>  <?php common::printOrderLink('openedBy',      $orderBy, $vars, $lang->testcase->openedBy);?></th>
            <th class='c-user'>  <?php common::printOrderLink('lastRunner',    $orderBy, $vars, $lang->testtask->lastRunAccount);?></th>
            <th class='c-date'>  <?php common::printOrderLink('lastRunDate',   $orderBy, $vars, $lang->testtask->lastRunTime);?></th>
            <th class='c-result'><?php common::printOrderLink('lastRunResult', $orderBy, $vars, $lang->testtask->lastRunResult);?></th>
            <th class='c-actions-6 text-center'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cases as $case):?>
          <?php
          $caseID = $case->id;
          $runID  = 0;
          ?>
          <tr>
            <td class="c-id">
              <?php echo sprintf('%03d', $case->id); ?>
            </td>
            <?php $params = "testcaseID=$caseID&version=$case->version";?>
            <td class='c-title text-left' title="<?php echo $case->title?>"><?php echo html::a($this->createLink('testcase', 'view', $params, '', '', $case->project), $case->title, null, "style='color: $case->color' data-app='{$this->app->openApp}'");?></td>
            <?php
            $productName = zget($products, $case->applicationID . '-' . $case->product, '');
            ?>
            <td class='text-left c-name' title='<?php echo $productName;?>'>
            <?php echo $productName;?>
            </td>
            <td><span class='label-pri <?php echo 'label-pri-' . $case->pri?>' title='<?php echo zget($lang->testcase->priList, $case->pri, $case->pri);?>'><?php echo zget($lang->testcase->priList, $case->pri, $case->pri)?></span></td>
            <td><?php echo zget($lang->testcase->typeList, $case->type);?></td>
            <td>
            <?php
            if($case->needconfirm)
            {
                echo "<span class='status-story status-changed' title='{$this->lang->story->changed}'>{$this->lang->story->changed}</span>";
            }
            elseif(isset($case->fromCaseVersion) and $case->fromCaseVersion > $case->version and !$case->needconfirm)
            {
                echo "<span class='status-story status-changed' title='{$this->lang->testcase->changed}'>{$this->lang->testcase->changed}</span>";
            }
            else
            {
                echo "<span class='status-testcase status-{$case->status}'>" . $this->processStatus('testcase', $case) . "</span>";
            }
            ?>
            </td>
            <td><?php echo zget($users, $case->openedBy);?></td>
            <td><?php echo zget($users, $case->lastRunner);?></td>
            <td><?php if(!helper::isZeroDate($case->lastRunDate)) echo substr($case->lastRunDate, 5, 11);?></td>
            <td class='result-testcase <?php echo $case->lastRunResult;?>'><?php echo $case->lastRunResult ? $lang->testcase->resultList[$case->lastRunResult] : $lang->testcase->unexecuted;?></td>
            <td class='c-actions'>
            <?php
            if($case->needconfirm or $browseType == 'needconfirm')
            {
                common::printIcon('testcase', 'confirmstorychange',  "caseID=$case->id", $case, 'list', 'confirm', 'hiddenwin', '', '', '', $this->lang->confirm);
            }
            else
            {
                common::printIcon('testtask', 'results', "runID=0&caseID=$case->id", $case, 'list', '', '', 'iframe', true, "data-width='95%'");
                common::printIcon('testtask', 'runCase', "runID=0&caseID=$case->id&version=$case->version", $case, 'list', 'play', '', 'runCase iframe', false, "data-width='95%'");
                common::printIcon('testcase', 'edit',    "caseID=$case->id", $case, 'list');
                if($this->config->testcase->needReview or !empty($this->config->testcase->forceReview)) common::printIcon('testcase', 'review',  "caseID=$case->id", $case, 'list', 'glasses', '', 'iframe');
                common::printIcon('testcase', 'createBug', "applicationID=$case->applicationID&product=$case->product&branch=$case->branch&extra=caseID=$case->id,version=$case->version,runID=", $case, 'list', 'bug', '', 'iframe', '', "data-width='90%'");
                common::printIcon('testcase', 'create',  "applicationID=$case->applicationID&productID=$case->product&branch=$case->branch&moduleID=$case->module&from=testcase&param=$case->id", $case, 'list', 'copy');
            }
            ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class="table-footer"><?php $pager->show('right', 'pagerjs');?></div>
    </form>
    <?php endif;?>
  </div>
</div>
<script>
$(".exportTemplet").modalTrigger({width:650, type:'iframe'});
$(".import").modalTrigger({width:650, type:'iframe'});
</script>
<?php include '../../common/view/footer.html.php';?>
