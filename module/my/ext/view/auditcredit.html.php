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
                <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->credit->code);?></th>
                <th class='w-180px'><?php common::printOrderLink('summary', $orderBy, $vars, $lang->credit->summary);?></th>
                <th class='w-150px'><?php common::printOrderLink('appIds', $orderBy, $vars, $lang->credit->appIds);?></th>
                <th class='w-80px'><?php common::printOrderLink('projectPlanId', $orderBy, $vars, $lang->credit->projectPlanId);?></th>
                <th class='w-80px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->credit->level);?></th>
                <th class='w-100px'><?php common::printOrderLink('emergencyType', $orderBy, $vars, $lang->credit->emergencyType);?></th>
                <th class='w-120px'><?php common::printOrderLink('planBeginTime', $orderBy, $vars, $lang->credit->planBeginTime);?></th>
                <th class='w-120px'><?php common::printOrderLink('planEndTime', $orderBy, $vars, $lang->credit->planEndTime);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->credit->createdBy);?></th>
                <th class='w-120px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->credit->createdDate);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->credit->createdDept);?></th>
                <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->credit->status);?></th>
                <th class='w-100px'><?php common::printOrderLink('dealUsers', $orderBy, $vars, $lang->credit->dealUsers);?> </th>
                <th class='text-center w-160px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php
                foreach($reviewList as $item):
                    $levelInfo    = zget($lang->credit->levelList, $item->level, '');
                    $appInfo      = zmget($apps, $item->appIds, '');
                    $dealUserInfo = zmget($users, $item->dealUsers, '');
                    $projectInfo  = zget($projectList, $item->projectPlanId, '');
                ?>
            <tr>
                <td title="<?php echo $item->code; ?>">
                    <?php echo common::hasPriv('credit', 'view') ? html::a($this->createLink('credit','view', "creditID=$item->id"), $item->code) : $item->code;?>
                </td>
                <td class='text-ellipsis viewClick' title="<?php echo strip_tags($item->summary); ?>"><?php echo strip_tags($item->summary);?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $appInfo;?>"><?php echo $appInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo $projectInfo; ?>"><?php echo $projectInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $levelInfo;?>"><?php echo $levelInfo;?></td>
                <td class='viewClick'><?php echo zget($lang->credit->emergencyTypeList, $item->emergencyType,'');?></td>
                <td class='viewClick'><?php echo $item->planBeginTime;?></td>
                <td class='viewClick'><?php echo $item->planEndTime;?></td>
                <td class='viewClick'><?php echo zget($users, $item->createdBy,'');?></td>
                <td class='viewClick'><?php echo $item->createdDate;?></td>
                <td class='viewClick'  title="<?php echo zget($deptList, $item->createdDept,''); ?>"><?php echo zget($depts, $item->createdDept,'');?></td>
                <td class='viewClick' title="<?php echo zget($lang->credit->statusList, $item->status,'');?>"><?php echo zget($lang->credit->statusList, $item->status,'');?></td>
                <td class='viewClick' title="<?php echo $dealUserInfo;?>" class='text-ellipsis'><?php echo $dealUserInfo; ?></td>
                <td class='c-actions text-center'>
                    <?php
                    common::printIcon('credit', 'edit', "creditId=$item->id", $item, 'list');
                    common::printIcon('credit', 'submit', "creditId=$item->id", $item, 'list', 'play', '', 'iframe', true);
                    common::printIcon('credit', 'review', "creditId=$item->id", $item, 'list', 'glasses', '', 'iframe', true);
                    common::printIcon('credit', 'copy', "creditId=$item->id", $item, 'list');
                    common::printIcon('credit', 'cancel', "creditId=$item->id", $item, 'list', 'cancel', '', 'iframe', true);
                    common::printIcon('credit', 'delete', "creditId=$item->id", $item, 'list', 'trash', '', 'iframe', true);
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
<script>
    $('.viewClick').live('click', function(){
        var id = $(this).parent().attr('data-val');
        window.location = createLink('credit', 'view', "creditId="+id)
    })
</script>

<?php include '../../../common/view/footer.html.php'?>
