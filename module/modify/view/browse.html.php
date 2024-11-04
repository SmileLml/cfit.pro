<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $i=0;
    foreach($lang->modify->labelList as $label => $labelName)
    {
      $active = $browseType == $label ? 'btn-active-text' : '';
      echo html::a($this->createLink('modify', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
      $i++;
      if($i >= 11) break;
    }
    if($i >= 11)
    {
      echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
      echo "<ul class='dropdown-menu'>";
      $i = 0;
      foreach($lang->modify->labelList as $label => $labelName)
      {
          $i++;
          if($i <= 11) continue;

          $active = $browseType == $label ? 'btn-active-text' : '';
          echo '<li>' . html::a($this->createLink('modify', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
        $class = common::hasPriv('modify', 'export') ? '' : "class=disabled";
        $misc  = common::hasPriv('modify', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
        $link  = common::hasPriv('modify', 'export') ? $this->createLink('modify', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->modify->export, '', $misc) . "</li>";
        ?>
      </ul>
    </div>
    <?php if(common::hasPriv('modify', 'create')) echo html::a($this->createLink('modify', 'create'), "<i class='icon-plus'></i>", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='modify'></div>
    <?php if(empty($modify)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' id='modifyForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
        <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
        <table class='table table-fixed has-sort-head' id='modify'>
          <thead>
          <tr>

            <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->modify->code);?></th>
            <th class='w-180px'><?php echo $lang->modify->desc;?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->modify->app);?></th>
            <th class='w-120px'><?php common::printOrderLink('projectPlanId', $orderBy, $vars, $lang->modify->projectPlanId);?></th>
            <th class='w-60px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->modify->level);?></th>
            <th class='w-60px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->modify->type);?></th>
            <th class='w-60px'><?php common::printOrderLink('planBegin', $orderBy, $vars, $lang->modify->planBegin);?></th>
            <th class='w-60px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->modify->planEnd);?></th>
            <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->modify->createdBy);?></th>
            <th class='w-110px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->modify->createdDate);?></th>
            <th class='w-70px'><?php common::printOrderLink('returnTime', $orderBy, $vars, $lang->modify->returnTime);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->modify->createdDepts);?></th>
            <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->modify->status);?></th>
            <th class='w-80px'> <?php echo $lang->modify->dealUser;?></th>
            <th class='text-center w-160px'><?php echo $lang->actions;?></th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($modify as $item):?>
            <tr>
              <td title="<?php echo $item->code; ?>"><?php echo common::hasPriv('modify', 'view') ? html::a(inlink('view', "modifyID=$item->id"), $item->code) : $item->code;?></td>
              <td class='text-ellipsis' title="<?php echo strip_tags($item->desc); ?>"><?php echo strip_tags($item->desc);?></td>
              <td class='text-ellipsis' title="<?php echo $item->app;?>"><?php echo $item->app;?></td>
              <td class='text-ellipsis' title="<?php echo zget($projectList,$item->projectPlanId) ?>"><?php echo zget($projectList,$item->projectPlanId);?></td>
              <td title="<?php echo zget( $lang->modify->levelList,$item->level);?>"><?php echo zget( $lang->modify->levelList,$item->level);?></td>
              <td title="<?php echo zget( $lang->modify->typeList,$item->type);?>"><?php echo zget( $lang->modify->typeList,$item->type);?></td>
              <td title="<?php echo $item->planBegin;?>"><?php echo $item->planBegin;?></td>
              <td title="<?php echo $item->planEnd;?>"><?php echo $item->planEnd;?></td>
              <td><?php echo zget($users,$item->createdBy,'');?></td>
              <td><?php echo $item->createdDate;?></td>
              <td><?php echo $item->returnTime;?></td>
              <td class='text-ellipsis' title="<?php echo zget($depts, $item->createdDept, ''); ?>"><?php echo zget($depts, $item->createdDept, '');?></td>
              <td title="<?php echo $item->closed == '1' ? $lang->modify->labelList['closed']: zget($lang->modify->statusList, $item->status)?>"><?php echo $item->closed == '1' ? $lang->modify->labelList['closed']:zget($lang->modify->statusList, $item->status);?></td>
              <?php
              if($item->status=='waitsubmitted')
              {
                $item->dealUser = $item->createdBy;
              }
              elseif($item->status=='withexternalapproval')
              {
                  $item->dealUser = 'guestjx';
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
                if ($item->issubmit == 'save'){
                    $disabled = 'disabled';
                    if ($app->user->account == $item->createdBy or $app->user->account == 'admin'){
                        $disabled = '';
                    }
                  echo '<a href="javascript:void(0)" '.$disabled.'  class="btn" onclick="$.zui.messager.danger(\''.$lang->modify->submitMsgTip.'\');" title="提交" data-app="second"><i class="icon-modify-submit icon-play"></i></a>';
                }else{
                    common::printIcon('modify', 'submit', "modifyID=$item->id", $item, 'list', 'play', '', 'iframe', true);
                }
                common::printIcon('modify', 'review', "modifyID=$item->id&version=$item->version&reviewStage=$item->reviewStage", $item, 'list', 'glasses', '', 'iframe', true);
                common::printIcon('modify', 'copy', "modifyID=$item->id", $item, 'list');
                common::printIcon('modify', 'close', "modifyID=$item->id", $item, 'list', 'cancel', '', 'iframe', true);
                common::printIcon('modify', 'delete', "modifyID=$item->id", $item, 'list', 'trash', '', 'iframe', true);
                // common::printIcon('modify', 'cancel', "modifyID=$item->id", $item, 'list', 'restart', '', 'iframe', true);
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
