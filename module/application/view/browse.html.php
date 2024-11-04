<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    foreach($lang->application->labelList as $label => $labelName)
    {   
        $active = $browseType == $label ? 'btn-active-text' : '';
        echo html::a($this->createLink('application', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
    }   
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('application', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('application', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('application', 'export') ? $this->createLink('application', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->application->export, '', $misc) . "</li>";

      $class = common::hasPriv('application', 'exportTemplate') ? '' : "class=disabled";
      $misc  = common::hasPriv('application', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' class='exportTemplate'" : "class=disabled";    
      $link  = common::hasPriv('application', 'exportTemplate') ? $this->createLink('application', 'exportTemplate', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->application->exportTemplate, '', $misc) . "</li>";

      ?>
      </ul>
      <?php if(common::hasPriv('application', 'import')) echo html::a($this->createLink('application', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->application->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
    </div>
    <?php if(common::hasPriv('application', 'create')) echo html::a($this->createLink('application', 'create'), "<i class='icon-plus'></i> {$lang->application->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='application'></div>
    <?php if(empty($applications)):?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' data-ride='table' data-nested='true' data-checkable='false' method='post' id='applicationForm'>
      <?php $vars = "browseType=$browseType&param=0&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
      <table class='table has-sort-head' id='applicationList'>
        <thead>
          <tr>
            <th class='c-id w-40px'></th>
            <th class='c-id w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->application->code);?></th>
            <th><?php common::printOrderLink('name', $orderBy, $vars, $lang->application->name);?></th>
            <th class='w-100px'><?php common::printOrderLink('isBasicLine', $orderBy, $vars, $lang->application->isBasicLine);?></th>
            <th class='w-100px'><?php common::printOrderLink('isSyncJinx', $orderBy, $vars, $lang->application->isSyncJinx);?></th>
            <th class='w-100px'><?php common::printOrderLink('isSyncQz', $orderBy, $vars, $lang->application->isSyncQz);?></th>
            <th class='c-date'><?php  common::printOrderLink('createdDate', $orderBy, $vars, $lang->application->createdDate);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->application->createdBy);?></th>
            <th class='w-100px'><?php common::printOrderLink('isPayment', $orderBy, $vars, $lang->application->isPayment);?></th>
            <th class='w-150'><?php common::printOrderLink('team', $orderBy, $vars, $lang->application->team);?></th>
            <th class='w-150'><?php common::printOrderLink('belongOrganization', $orderBy, $vars, $lang->application->belongOrganization);?></th>
            <th class='c-actions-2'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($applications as $app):?>
          <tr>
            <td><?php echo html::checkbox('appIDList', array($app->id => ''));?></td>
            <td><?php echo $app->code;?></td>
            <td title=<?php echo $app->name;?>>
             <?php echo common::hasPriv('application', 'view') ? html::a(inlink('view', "appID=$app->id"), $app->name) : $app->name; ?>
            </td>
            <td><?php echo zget($lang->application->boolList, $app->isBasicLine);?></td>
            <td><?php echo zget($lang->application->boolList, $app->isSyncJinx);?></td>
            <td><?php echo zget($lang->application->boolList, $app->isSyncQz);?></td>
            <td><?php echo $app->createdDate;?></td>
            <td><?php echo zget($users, $app->createdBy, $app->createdBy);?></td>
            <td><?php echo zget($lang->application->isPaymentList, $app->isPayment, '');?></td>
            <td title=<?php echo $app->team;?>><?php echo zget($lang->application->teamList, $app->team, '');?></td>
            <td title=<?php echo zget($lang->application->belongOrganizationList, $app->belongOrganization, '');?>><?php echo zget($lang->application->belongOrganizationList, $app->belongOrganization, '');?></td>
            <td class='c-actions'>
            <?php
              common::printIcon('application', 'edit', "appID=$app->id", $app, 'list');
              if(common::hasPriv('application', 'delete'))  echo html::a($this->createLink("application", "delete", "appID=$app->id"), "<i class='icon-trash'></i> ", 'hiddenwin', "class='btn btn-action'");
              // common::printIcon('application', 'activate', "appID=$app->id", $app, 'list', 'magic', '', 'hiddenwin');
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
