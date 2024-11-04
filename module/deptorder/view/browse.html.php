<?php include '../../common/view/header.html.php';?>
<style>
    .team{
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        word-break: keep-all;
    }
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $i = 0;
    foreach($lang->deptorder->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('deptorder', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

        $i++;
        if($i >= 10) break;
    }
    if($i>=10)
    {
        echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
        echo "<ul class='dropdown-menu'>";
        $i = 0;
        foreach($lang->deptorder->labelList as $label => $labelName)
        {
            $i++;
            if($i <= 10) continue;

            $active = $browseType == $label ? 'btn-active-text' : ''; 
            echo '<li>' . html::a($this->createLink('deptorder', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
        }
        echo '</ul></div>';
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
        <?php $this->app->loadLang('progress'); ?>
        <?php if (common::hasPriv('deptorder', 'importByQA')) echo html::a($this->createLink('deptorder', 'importByQA', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->progress->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('deptorder', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('deptorder', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
      $link  = common::hasPriv('deptorder', 'export') ? $this->createLink('deptorder', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->deptorder->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
    <?php if(common::hasPriv('deptorder', 'create')) echo html::a($this->createLink('deptorder', 'create'), "<i class='icon-plus'></i> {$lang->deptorder->create}", '', "class='btn btn-primary'");?>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='deptorder'></div>
    <?php if(empty($deptorders)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='deptorderForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='deptorders'>
        <thead>
          <tr>
            <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->deptorder->code);?></th>
            <th class='w-160px'><?php common::printOrderLink('summary', $orderBy, $vars, $lang->deptorder->summary);?></th>
            <th class='w-80px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->deptorder->type);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->deptorder->app);?></th>
            <th class='w-60px'><?php common::printOrderLink('source', $orderBy, $vars, $lang->deptorder->source);?></th>
            <th class='w-130px'><?php common::printOrderLink('team', $orderBy, $vars, $lang->deptorder->team);?></th>
            <th class='w-70px'><?php common::printOrderLink('exceptDoneDate', $orderBy, $vars, $lang->deptorder->exceptDoneDate);?></th>
            <th class='w-50px'><?php common::printOrderLink('ifAccept', $orderBy, $vars, $lang->deptorder->ifAccept);?></th>
            <th class='w-50px'><?php common::printOrderLink('acceptDept', $orderBy, $vars, $lang->deptorder->acceptDept);?></th>
            <th class='w-50px'><?php common::printOrderLink('acceptUser', $orderBy, $vars, $lang->deptorder->acceptUser);?></th>
            <th class='w-50px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->deptorder->status);?></th>
            <th class='w-40px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->deptorder->dealUser);?></th>
            <th class='text-center w-150px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($deptorders as $deptorder):?>
          <tr>
            <td title="<?php echo $deptorder->code;?>" class='text-ellipsis'><?php echo common::hasPriv('deptorder', 'view') ? html::a(inlink('view', "deptorderID=$deptorder->id"), $deptorder->code) : $deptorder->code;?></td>
            <td title="<?php echo $deptorder->summary;?>" class='text-ellipsis'><?php echo $deptorder->summary;?></td>
            <td><?php echo zget($lang->deptorder->typeList, $deptorder->type);?></td>
            <td title="<?php echo zget($apps,$deptorder->app);?>" class='text-ellipsis'><?php echo zget($apps,$deptorder->app);?></td>
            <td><?php echo zget($lang->deptorder->sourceList, $deptorder->source);?></td>
            <td  title="<?php $userList = '';foreach(explode(',', trim($deptorder->team, ',')) as $user) $userList .= $users[$user] . ',';$userList = trim($userList, ',');echo $userList; ?>" class='text-ellipsis team'><?php echo $userList; ?></td>
            <td><?php echo $deptorder->exceptDoneDate  != '0000-00-00' ? $deptorder->exceptDoneDate : '';;?></td>
            <td><?php echo zget($lang->deptorder->ifAcceptList, $deptorder->ifAccept, '');?></td>
            <td title="<?php echo zget($depts, $deptorder->acceptDept);?>" class='text-ellipsis'><?php echo zget($depts, $deptorder->acceptDept, '');?></td>
            <td title="<?php echo zget($users, $deptorder->acceptUser);?>" class='text-ellipsis'><?php echo zget($users, $deptorder->acceptUser, '');?></td>
            <td>
                <?php echo zget($lang->deptorder->statusList, $deptorder->status);?>
            </td>
            <td title="<?php echo zget($users, $deptorder->dealUser);?>" class='text-ellipsis'><?php echo zget($users, $deptorder->dealUser, '');?></td>
            <td class='c-actions text-center'>
              <?php
              common::printIcon('deptorder', 'edit', "deptorderID=$deptorder->id", $deptorder, 'list');
              common::printIcon('deptorder', 'deal', "deptorderID=$deptorder->id", $deptorder, 'list', 'time', '', 'iframe', true);
              common::printIcon('deptorder', 'copy', "deptorderID=$deptorder->id", $deptorder, 'list');
              common::printIcon('deptorder', 'close', "deptorderID=$deptorder->id", $deptorder, 'list','off', '', 'iframe', true);
              common::printIcon('deptorder', 'delete', "deptorderID=$deptorder->id", $deptorder, 'list', 'trash', '', 'iframe', true);
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
