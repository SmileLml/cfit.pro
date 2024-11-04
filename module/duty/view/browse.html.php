<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a($this->createLink('duty', 'calendar'), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-primary'") . '<div class="divider"></div>';?>
    <?php
    foreach($lang->duty->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : '';
        echo html::a($this->createLink('duty', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('duty', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('duty', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('duty', 'export') ? $this->createLink('duty', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->duty->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
    <?php //if(common::hasPriv('duty', 'batchCreate')) echo html::a($this->createLink('duty', 'batchCreate'), "<i class='icon-plus'></i> {$lang->duty->batchCreate}", '', "class='btn btn-secondary'");?>
    <?php if(common::hasPriv('duty', 'create')) echo html::a($this->createLink('duty', 'create'), "<i class='icon-plus'></i> {$lang->duty->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id='mainContent' class='main-row fade'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='duty'></div>
    <?php if(empty($dutys)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='dutyForm' method='post' data-ride='table'>
      <table class='table has-sort-head' id='dutys'>
        <thead>
          <tr>
            <?php $vars = "browseType=$browseType&param=0&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
            <th class="c-id w-100px"><?php common::printOrderLink('id', $orderBy, $vars, $lang->duty->id);?></th>
            <th class='w-200px'><?php common::printOrderLink('application', $orderBy, $vars, $lang->duty->application);?></th>
            <th class='w-150px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->duty->type);?></th>
            <th class='w-150px'><?php common::printOrderLink('importantTime', $orderBy, $vars, $lang->duty->importantTime);?></th>
            <th class='w-150px'><?php common::printOrderLink('user', $orderBy, $vars, $lang->duty->user);?></th>
            <th class='w-150px'><?php common::printOrderLink('planDate', $orderBy, $vars, $lang->duty->planDate);?></th>
            <th class='w-150px'><?php common::printOrderLink('actualUser', $orderBy, $vars, $lang->duty->actualUser);?></th>
            <th class='w-150px'><?php common::printOrderLink('actualDate', $orderBy, $vars, $lang->duty->actualDate);?></th>
            <th class='c-actions-2'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($dutys as $duty):?>
          <tr>
            <td><?php echo html::a(inlink('view', "dutyID=$duty->id"), sprintf('%03d', $duty->id));?></td>
            <td><?php echo zget($appList, $duty->application, '');?></td>
            <td><?php echo zget($lang->duty->typeList, $duty->type, '');?></td>
            <td><?php echo zget($lang->duty->importantTimeList, $duty->importantTime, '');?></td>
            <?php $userName = ''; foreach(explode(',', $duty->user) as $account) $userName .= zget($users, $account, '') . ' ';?>
            <td title='<?php echo $userName;?>'><?php echo $userName;?></td>
            <td><?php echo $duty->planDate;?></td>
            <?php $userName = ''; foreach(explode(',', $duty->actualUser) as $account) $userName .= zget($users, $account, '') . ' ';?>
            <td title='<?php echo $userName;?>'><?php echo $userName;?></td>
            <td><?php echo $duty->actualDate;?></td>
            <td class='c-actions'>
            <?php
              if(common::hasPriv('duty', 'edit')) common::printIcon('duty', 'edit', "dutyID=$duty->id", $duty, 'list');
              if(common::hasPriv('duty', 'delete')) echo html::a($this->createLink("duty", "delete", "dutyID=$duty->id"), "<i class='icon-trash'></i> ", 'hiddenwin', "class='btn btn-action'");
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
