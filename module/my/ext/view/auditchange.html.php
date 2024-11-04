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
        <table class='table has-sort-head' id='reviewList'>
          <thead>
            <tr>
              <th class='w-160px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->change->code);?></th>
              <th class='w-80px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->change->level);?></th>
              <th class='w-80px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->change->type);?></th>
              <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->change->createdBy);?></th>
              <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->change->createdDept);?></th>
              <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->change->createdDate);?></th>
              <th class='w-100px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->change->status);?></th>
                <th class='w-80px'><?php echo $lang->change->pending;?></th>
              <th class='text-left w-40px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($reviewList as $change):?>
            <tr>
              <td><?php echo common::hasPriv('change', 'view') ? html::a($this->createLink('change', 'view', "changeID=$change->id"), $change->code, '', 'data-app="project"') : $change->code;?></td>
              <td><?php echo zget($lang->change->levelList, $change->level);?></td>
              <td><?php echo zget($lang->change->typeList, $change->type);?></td>
              <td><?php echo $change->realname;?></td>
              <td><?php echo zget($depts, $change->createdDept, '');?></td>
              <td><?php echo substr($change->createdDate, 0, 11);?></td>
              <td><?php echo zget($lang->change->statusList, $change->status);?></td>
             <?php
                $reviewers = $change->reviewers;
                $reviewersArray = explode(',', $reviewers);
                $appiontUsers = $change->appiontUsers;
                $appiontUsersArray = explode(',', $appiontUsers);
                //所有审核人
                $reviewersArray = array_filter(array_merge($reviewersArray, $appiontUsersArray));
                $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                $reviewerUsersStr = implode(',', $reviewerUsers);
                $subCount = 3;
                $reviewerUsersSubStr = getArraySubValuesStr($reviewerUsers, $subCount);
             ?>
                <td title="<?php echo $reviewerUsersStr; ?>">
                    <?php echo $reviewerUsersSubStr ?>
                </td>

              <td class='c-actions'>
                <?php
                common::printIcon('change', 'appoint', "changeID=$change->id", $change, 'list', 'hand-right', '', 'iframe', true);
                common::printIcon('change', 'review', "changeID=$change->id&version=$change->version&reviewStage=$change->reviewStage", $change, 'list', 'glasses', '', 'iframe', true,'data-width="1200px"');
                ?>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'></div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
