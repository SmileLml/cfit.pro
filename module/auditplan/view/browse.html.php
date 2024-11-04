<?php include '../../common/view/header.html.php';?>
<style>
td.status-checked{color: #43a047}
td.status-delay{color: #f00}
.table td{white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
</style>
<div id="mainMenu" class="clearfix">
  <div id="sidebarHeader">
    <div class="title">
      <?php echo empty($process) ? $lang->auditcl->object : $process->name;?>
      <?php if($processID) echo html::a(inLink('browse', "projectID=$projectID&processID=0"), "<i class='icon icon-sm icon-close'></i>", '', 'class="text-muted"');?>
    </div>
  </div>
  <div class="btn-toolbar pull-left">
    <?php
    foreach($lang->auditplan->statusList as $key => $label)
    {
      $active = $key == $browseType ? ' btn-active-text' : ''; 
      echo html::a(inLink('browse', "projectID=$projectID&processID=$processID&browseType=$key"), "<span class='text'>{$label}</span>", '', "class='btn btn-link $active'");
    }

    echo "<div class='btn-group' id='more'>";
    $current = $lang->more;
    $active  = '';
    if(isset($lang->auditplan->moreStatusList[$browseType]))
    {
      $current = "<span class='text'>{$lang->auditplan->moreStatusList[$browseType]}</span> <span class='label label-light label-badge'>{$pager->recTotal}</span>";
      $active  = 'btn-active-text';
    }
    echo html::a('javascript:;', $current . " <span class='caret'></span>", '', "data-toggle='dropdown' class='btn btn-link $active'");
    echo "<ul class='dropdown-menu'>";
    foreach($lang->auditplan->moreStatusList as $key => $value)
    {
      if($key == '') continue;
      echo '<li' . ($key == $browseType ? " class='active'" : '') . '>';
      echo html::a(inLink('browse', "projectID=$projectID&processID=$processID&browseType=$key"), $value);
    }
    echo '</ul></div>';
    ?>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('auditplan', 'batchCreate', "projectID=$projectID", "<i class='icon icon-plus'></i>" . $lang->auditplan->batchCreate, '', "class='btn btn-secondary'", '');?>
    <?php common::printLink('auditplan', 'create', "projectID=$projectID", "<i class='icon icon-plus'></i>" . $lang->auditplan->create, '', "class='btn btn-primary'", '');?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div id="sidebar" class="side-col">
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class="cell">
      <ul class="tree" data-ride="tree" id="projectTree" data-name="tree-project" data-idx="0">
      <?php
      foreach($processList as $id => $processName)
      {
          $activate = '';
          if($processID == $id)  $activate = ' active';
          echo '<li>' . html::a(inLink('browse', "projectID=$projectID&processID=" . $id), $processName, '', "class='$activate'") . '</li>';
      }
      ?>
      </ul>
    </div>
  </div>
  <?php if(empty($auditplans)):?>
  <div class="main-col">
    <div class="table-empty-tip">
      <p> 
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
  </div>
  <?php else:?>
  <div class='main-col'>
    <form class='main-table table-bug' method='post' id='auditplanForm' data-ride='table'>
      <table class='table'>
        <thead>
          <tr>
            <th class='w-80px'><?php echo "<div class='checkbox-primary check-all' title='{$this->lang->selectAll}'><label></label></div>" . $lang->idAB;?></th>
            <th class='w-70px'><?php echo $lang->auditplan->process;?></th>
            <th class='w-90px'><?php echo $lang->auditplan->processType;?></th>
            <th><?php echo $lang->auditplan->objectID;?></th>
            <th class='w-80px'><?php echo $lang->auditplan->objectType;?></th>
            <th class='w-80px'><?php echo $lang->auditplan->status;?></th>
            <th class='w-100px'><?php echo $lang->auditplan->createdBy;?></th>
            <th class='w-100px'><?php echo $lang->auditplan->assignedTo;?></th>
            <th class='w-100px'><?php echo $lang->auditplan->checkDate;?></th>
            <th class='w-100px'><?php echo $lang->auditplan->realCheckDate;?></th>
            <th class='w-70px'><?php echo $lang->auditplan->nc;?></th>
            <th class='c-actions-6 w-150px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($auditplans as $auditplan):?>
        <?php $auditplan->ncs = $this->loadModel('auditplan')->getNcCount($auditplan->id);?>
          <tr>
            <td><?php echo html::checkbox('auditIDList', array($auditplan->id => '')) . sprintf('%03d', $auditplan->id)?></td>
            <td><?php echo zget($processes, $auditplan->process);?></td>
            <td><?php echo zget($processTypeList, $auditplan->processType);?></td>
            <td><?php echo $auditplan->objectType == 'activity' ? zget($activities, $auditplan->objectID) : zget($outputs, $auditplan->objectID);?></td>
            <td><?php echo zget($lang->auditplan, $auditplan->objectType, '');?></td>
            <td class='status-<?php echo $auditplan->status;?>'><?php echo zget($lang->auditplan->statusList, $auditplan->status);?></td>
            <td><?php echo zget($users, $auditplan->createdBy);?></td>
            <td><?php echo zget($users, $auditplan->assignedTo);?></td>
            <?php $delayed = ''; if((helper::diffDate(helper::today(), $auditplan->checkDate) > -1) and $auditplan->checkDate) $delayed = 'bg-important';?>
            <td class="<?php echo $delayed;?>"><?php echo $auditplan->checkDate;?></td>
            <td><?php echo $auditplan->realCheckDate;?></td>
            <td class='text-center'><?php echo $auditplan->ncs == 0 ? 0 : html::a($this->createLink('auditplan', 'nc', "id=$auditplan->id", '', true), $auditplan->ncs, '', "class='iframe'");?></td>
            <td class='c-actions'>
              <?php
                if($auditplan->status == 'wait' || $auditplan->status == 'checking')
                {
                    common::printIcon('auditplan', 'check', "auditplanID=$auditplan->id&projectID=$projectID", $auditplan, 'list', 'confirm', '', 'iframe', true, '', $lang->auditplan->check);
                }
                else
                {
                    common::printIcon('auditplan', 'check', "auditplanID=$auditplan->id&projectID=$projectID", $auditplan, 'list', 'confirm', '', 'disabled');
                }

                common::printIcon('auditplan', 'result', "auditplanID=$auditplan->id", $auditplan, 'list', 'list-alt', '', 'iframe', true);
                if($auditplan->ncs)
                {
                    common::printIcon('auditplan', 'nc', "auditplanID=$auditplan->id", $auditplan, 'list', 'bug', '', 'iframe', true);
                }
                else
                {
                    common::printIcon('auditplan', 'nc', "auditplanID=$auditplan->id", $auditplan, 'list', 'bug', '', 'disabled', true);
                }

                common::printIcon('auditplan', 'edit', "auditplanID=$auditplan->id", $auditplan, 'list');
                common::printIcon('auditplan', 'delete', "auditplanID=$auditplan->id", $auditplan, 'list', 'trash', 'hiddenwin');
              ?>
            </td>
          </tr>
        <?php endforeach;?>
        </tbody>
      </table>
      <div class='table-footer'>
        <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
        <div class="table-actions btn-toolbar">
          <div class='btn-group dropup'>
            <?php
            $actionLink = $this->createLink('auditplan', 'batchCheck', "projectID=$projectID");
            $misc       = common::hasPriv('auditplan', 'batchCheck') ? "onclick=\"setFormAction('$actionLink')\"" : "disabled='disabled'";
            echo html::commonButton($lang->auditplan->batchCheck, $misc);
            ?>
          </div>
        </div>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
  </div>
  <?php endif;?>
</div>
<script>
$(".tree .active").parent('li').addClass('active');
</script>
<?php include '../../common/view/footer.html.php';?>
