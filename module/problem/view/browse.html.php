<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $i = 0;
    foreach($lang->problem->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('problem', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

        $i++;
        if($i >= 10) break;
    }
    if($i>=10)
    {
        echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
        echo "<ul class='dropdown-menu'>";
        $i = 0;
        foreach($lang->problem->labelList as $label => $labelName)
        {
            $i++;
            if($i <= 10) continue;

            $active = $browseType == $label ? 'btn-active-text' : ''; 
            echo '<li>' . html::a($this->createLink('problem', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
        }
        echo '</ul></div>';
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('problem', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('problem', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('problem', 'export') ? $this->createLink('problem', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->problem->export, '', $misc) . "</li>";

      //$class = common::hasPriv('problem', 'exportTemplate') ? '' : "class='disabled'";
      //$link  = common::hasPriv('problem', 'exportTemplate') ? $this->createLink('problem', 'exportTemplate') : '#';
      //$misc  = common::hasPriv('problem', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
      //echo "<li $class>" . html::a($link, $lang->problem->exportTemplate, '', $misc) . '</li>';
      ?>
      </ul>
      <?php if(common::hasPriv('problem', 'import')) echo html::a($this->createLink('problem', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->problem->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
    </div>
    <?php if(common::hasPriv('problem', 'create')) echo html::a($this->createLink('problem', 'create'), "<i class='icon-plus'></i> {$lang->problem->create}", '', "class='btn btn-primary'");?>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='problem'></div>
    <?php if(empty($problems)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='problemForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='problems'>
        <thead>
          <tr>
            <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->problem->code);?></th>
            <th class='w-160px'><?php common::printOrderLink('abstract', $orderBy, $vars, $lang->problem->abstract);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->problem->app);?></th>
            <th class='w-60px'><?php common::printOrderLink('severity', $orderBy, $vars, $lang->problem->severity);?></th>
            <th class='w-60px'><?php common::printOrderLink('pri', $orderBy, $vars, $lang->problem->pri);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->problem->createdBy);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->problem->createdDept);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->problem->createdDate);?></th>
            <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->problem->status);?></th>
            <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->problem->dealUser);?></th>
            <th class='text-center w-100px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($problems as $problem):?>
          <tr>
            <td><?php echo common::hasPriv('problem', 'view') ? html::a(inlink('view', "problemID=$problem->id"), $problem->code) : $problem->code;?></td>
            <?php
            $as = [];
            foreach(explode(',', $problem->app) as $app)
            {
                if(!$app) continue;
                $as[] = zget($apps, $app);
            }
            $app = implode(', ', $as);
            ?>
            <td title="<?php echo $problem->abstract;?>" class='text-ellipsis'><?php echo $problem->abstract;?></td>
            <td title="<?php echo $app;?>"><?php echo $app;?></td>
            <td><?php echo zget($lang->problem->severityList, $problem->severity);?></td>
            <td><?php echo zget($lang->problem->priList, $problem->pri);?></td>
            <td><?php echo zget($users, $problem->createdBy, '');?></td>
            <td><?php echo zget($depts, $problem->createdDept, '');?></td>
            <td><?php echo $problem->createdDate;?></td>
            <td>
                <?php echo zget($lang->problem->statusList, $problem->status);?>
            </td>
            <td><?php echo zget($users, $problem->dealUser, '');?></td>
            <td class='c-actions text-center'>
              <?php
              common::printIcon('problem', 'edit', "problemID=$problem->id", $problem, 'list');
              if($this->app->user->admin or  $this->app->user->account == $problem->dealUser) //非当前处理人，图标置灰不能操作
              {
                  common::printIcon('problem', 'deal', "problemID=$problem->id", $problem, 'list', 'time', '', 'iframe', true);
              }
              else
              {
                  echo '<button type="button" class="disabled btn" title="' . $lang->problem->deal . '"><i class="icon-common-suspend disabled icon-time"></i></button>';
              }
              common::printIcon('problem', 'copy', "problemID=$problem->id", $problem, 'list');
              common::printIcon('problem', 'delete', "problemID=$problem->id", $problem, 'list', 'trash', '', 'iframe', true);
              if($this->app->user->admin or in_array($this->app->user->account, $executives))
              {
                  if($problem->status == 'suspend')
                  {
                      common::printIcon('problem', 'start', "problemID=$problem->id", $problem, 'list', 'start', '', 'iframe', true);
                  }
                  else
                  {
                      common::printIcon('problem', 'suspend', "problemID=$problem->id", $problem, 'list', 'pause', '', 'iframe', true);
                  }
              }
              else
              {
                  echo '<button type="button" class="disabled btn" title="' . $lang->problem->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
              }
              ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
