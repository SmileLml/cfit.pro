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
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='modify'></div>
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
        <table class='table has-sort-head table-fixed' id='modify'>
          <thead>
          <tr>
            <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->modify->code);?></th>
            <th class='w-180px'><?php common::printOrderLink('desc', $orderBy, $vars, $lang->modify->desc);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->modify->app);?></th>
            <th class='w-120px'><?php common::printOrderLink('projectPlanId', $orderBy, $vars, $lang->modify->projectPlanId);?></th>
            <th class='w-60px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->modify->level);?></th>
            <th class='w-60px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->modify->type);?></th>
            <th class='w-60px'><?php common::printOrderLink('planBegin', $orderBy, $vars, $lang->modify->planBegin);?></th>
            <th class='w-60px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->modify->planEnd);?></th>
            <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->modify->createdBy);?></th>
            <th class='w-110px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->modify->createdDate);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDepts', $orderBy, $vars, $lang->modify->createdDepts);?></th>
            <th class='w-80px'><?php common::printOrderLink('cardStatus', $orderBy, $vars, $lang->modify->status);?></th>
            <th class='w-80px'> <?php echo $lang->modify->dealUser;?></th>
            <th class='text-center w-160px'><?php echo $lang->actions;?></th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($reviewList as $item):?>
            <tr>
              <td title="<?php echo $item->code; ?>"><?php echo common::hasPriv('modify', 'view') ? html::a($this->createLink('modify', 'view', "modifyID=$item->id"), $item->code) : $item->code;?></td>
              <td class='text-ellipsis' title="<?php echo strip_tags($item->desc); ?>"><?php echo strip_tags($item->desc);?></td> 
              <td class='text-ellipsis' title="<?php echo $item->app;?>"><?php echo $item->app;?></td>
              <td class='text-ellipsis' title="<?php echo zget($projectList,$item->projectPlanId) ?>"><?php echo zget($projectList,$item->projectPlanId);?></td>
              <td title="<?php echo zget( $lang->modify->levelList,$item->level);?>"><?php echo zget( $lang->modify->levelList,$item->level);?></td>
              <td title="<?php echo zget( $lang->modify->typeList,$item->type);?>"><?php echo zget( $lang->modify->typeList,$item->type);?></td>
              <td title="<?php echo $item->planBegin;?>"><?php echo $item->planBegin;?></td>
              <td title="<?php echo $item->planEnd;?>"><?php echo $item->planEnd;?></td>
              <td><?php echo zget($users,$item->createdBy,'');?></td>
              <td><?php echo $item->createdDate;?></td>
              <td title="<?php echo $item->createdBy?$depts[$item->createdDept]:''?>"><?php echo $item->createdBy?$depts[$item->createdDept]:'';?></td>
              <td title="<?php echo  zget($lang->modify->statusList, $item->status)?>"><?php echo zget($lang->modify->statusList, $item->status);?></td>
              <?php
              if($item->status=='waitsubmitted')
              {
                $item->dealUser = $item->createdBy;
              }
              elseif($item->status=='withexternalapproval')
              {
                  $item->dealUser = 'guestcn';
              }
              ?>
              <?php
              $reviewers = $item->dealUser;
              $reviewersArray = explode(',', $reviewers);
              //所有审核人
              $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
              $reviewerUsersStr = implode(',', $reviewerUsers);
              $subCount = 3;
              $reviewerUsersSubStr = getArraySubValuesStr($reviewerUsers, $subCount);
              ?><td title="<?php echo $reviewerUsersStr;?>" class='text-ellipsis'><?php echo $reviewerUsersSubStr ?></td>
              <td class='c-actions text-center'>
              <?php
                common::printIcon('modify', 'edit', "modifyID=$item->id", $item, 'list');
                common::printIcon('modify', 'submit', "modifyID=$item->id", $item, 'list', 'play', '', 'iframe', true);
                common::printIcon('modify', 'review', "modifyID=$item->id&version=$item->version&reviewStage=$item->reviewStage", $item, 'list', 'glasses', '', 'iframe', true);
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
