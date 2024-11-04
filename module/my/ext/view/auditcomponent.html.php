<?php include '../../../common/view/header.html.php'?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->my->myReviewList as $key => $type):?>
    <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
    <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
    <?php endforeach;?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='component'></div>
    <?php if(empty($reviewList)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' method='post' id='myReviewForm'>
          <div class="table-header fixed-right">
              <nav class="btn-toolbar pull-right"></nav>
          </div>
          <?php
          $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
          ?>
        <table class='table has-sort-head table-fixed' id='component'>
          <thead>
          <tr>
              <th class='w-60px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->component->id); ?></th>
              <th class='w-120px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->component->name); ?></th>
              <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->component->componentType); ?></th>
              <th class='w-80px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->component->level); ?></th>
              <th class='w-80px'><?php common::printOrderLink('applicationMethod', $orderBy, $vars, $lang->component->application); ?></th>
              <th class='w-100px'><?php common::printOrderLink('version', $orderBy, $vars, $lang->component->version); ?></th>
              <th class='w-260px'><?php common::printOrderLink('projectId', $orderBy, $vars, $lang->component->project); ?></th>
              <th class='w-120px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->component->status); ?></th>
              <th class='w-100px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->component->dealUser); ?></th>
              <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->component->createdBy); ?></th>
              <th class='w-180px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->component->createdDept); ?></th>
              <th class='w-150px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->component->createdDate); ?></th>
              <th class='text-center c-actions-1 w-120px'><?php echo $lang->actions; ?></th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($reviewList as $component):?>
              <tr>
                  <td><?php echo $component->id;?></td>
                  <td class='text-ellipsis' title="<?php echo $item->name; ?>"><?php echo common::hasPriv('component', 'view') ? html::a($this->createLink('component', 'view', "componentID=$component->id"), $component->name) : $component->name;?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->component->type,$component->type) ?>"><?php echo zget($lang->component->type,$component->type);?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->component->levelList,$component->level) ?>"><?php echo zget($lang->component->levelList,$component->level);?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->component->applicationMethod,$component->applicationMethod) ?>"><?php echo zget($lang->component->applicationMethod,$component->applicationMethod);?></td>
                  <td class='text-ellipsis' title="<?php echo $component->version; ?>"><?php echo $component->version; ?></td>
                  <td class='text-ellipsis' title="<?php echo zget($projectPlanList,$component->projectId) ?>"><?php echo zget($projectPlanList,$component->projectId);?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->component->statusList,$component->status) ?>"><?php echo zget($lang->component->statusList,$component->status);?></td>
                  <?php
                  $dealUserTitle = '';
                  $dealUsersTitles = '';
                  if (!empty($component->dealUser)) {
                      foreach (explode(',', $component->dealUser) as $dealUser) {
                          if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                      }
                  }
                  $dealUsersTitles = trim($dealUserTitle, ',');
                  ?>
                  <td title='<?php echo $dealUsersTitles; ?>' class='text-ellipsis'>
                      <?php echo $dealUsersTitles; ?>
                  </td>
                  <td class='text-ellipsis' title="<?php echo zget($users,$component->createdBy) ?>"><?php echo zget($users,$component->createdBy);?></td>
                  <td class='text-ellipsis' title="<?php echo zget($depts,$component->createdDept) ?>"><?php echo zget($depts,$component->createdDept);?></td>
                  <td class='text-ellipsis' title="<?php echo $component->createdDate; ?>"><?php echo $component->createdDate; ?></td>
                  <td class='c-actions text-center'>
                      <?php
                      common::printIcon('component', 'edit', "componentID=$component->id", $component, 'list', 'edit');
                      common::printIcon('component', 'submit', "componentID=$component->id", $component, 'list', 'play','','iframe', true);
                      common::printIcon('component', 'review', "componentID=$component->id&changeVersion=$component->changeVersion&reviewStage=$component->reviewStage", $component, 'list', 'glasses', '', 'iframe', true);
                      common::printIcon('component', 'publish', "componentID=$component->id", $component, 'list', 'folder-open','','iframe', true);
                      ?>
                  </td>
              </tr>
          <?php endforeach;?>
          </tbody>
        </table>
        <div class="table-footer">
        </div>
      </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
