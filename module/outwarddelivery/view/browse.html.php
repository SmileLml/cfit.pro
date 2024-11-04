<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $i=0;
    foreach($lang->outwarddelivery->labelList as $label => $labelName)
    {
      $active = $browseType == $label ? 'btn-active-text' : '';
      echo html::a($this->createLink('outwarddelivery', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
      $i++;
      if($i >= 11) break;
    }
    if($i >= 11)   
    {
      echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
      echo "<ul class='dropdown-menu'>";
      $i = 0;
      foreach($lang->outwarddelivery->labelList as $label => $labelName)
      {
          $i++;
          if($i <= 11) continue;

          $active = $browseType == $label ? 'btn-active-text' : '';
          echo '<li>' . html::a($this->createLink('outwarddelivery', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
        $class = common::hasPriv('outwarddelivery', 'export') ? '' : "class=disabled";
        $misc  = common::hasPriv('outwarddelivery', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
        $link  = common::hasPriv('outwarddelivery', 'export') ? $this->createLink('outwarddelivery', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->outwarddelivery->export, '', $misc) . "</li>";
        ?>
      </ul>
    </div>
    <?php if(common::hasPriv('outwarddelivery', 'create')) echo html::a($this->createLink('outwarddelivery', 'create'), "<i class='icon-plus'></i>", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='outwardDelivery'></div>
    <?php if(empty($outwarddelivery)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' id='outwarddeliveryForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
        <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
        <table class='table table-fixed has-sort-head' id='outwarddelivery'>
          <thead>
          <tr>
            <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->outwarddelivery->code);?></th>
            <th class='w-180px'><?php common::printOrderLink('outwardDeliveryDesc', $orderBy, $vars, $lang->outwarddelivery->outwardDeliveryDesc);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->outwarddelivery->app);?></th>
            <th class='w-120px'><?php echo $lang->outwarddelivery->deliveryType;?></th>
            <th class='w-120px'><?php common::printOrderLink('projectPlanId', $orderBy, $vars, $lang->outwarddelivery->projectPlanId);?></th>
            <th class='w-80px'><?php echo $lang->outwarddelivery->totalReturn;?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->outwarddelivery->createdDepts);?></th>
            <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->outwarddelivery->createdBy);?></th>
            <th class='w-110px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->outwarddelivery->createdDate);?></th>
            <th class='w-60px'><?php common::printOrderLink('currentReview', $orderBy, $vars, $lang->outwarddelivery->currentReview);?></th>
            <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->outwarddelivery->status);?></th>
            <th class='w-80px'> <?php echo $lang->outwarddelivery->dealUser;?></th>
            <th class='text-center w-160px'><?php echo $lang->actions;?></th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($outwarddelivery as $item):?>
            <tr>
              <td title="<?php echo $item->code; ?>"><?php echo common::hasPriv('outwarddelivery', 'view') ? html::a(inlink('view', "outwarddeliveryID=$item->id"), $item->code) : $item->code;?></td>
              <td title="<?php echo $item->outwardDeliveryDesc; ?>"><?php echo $item->outwardDeliveryDesc;?></td>
              <td title="<?php echo $item->app;?>"><?php echo $item->app;?></td>
              <td title="<?php echo $item->childrenCode?>"><?php echo $item->childrenCode;?></td>
              <td title="<?php echo zget($projectList,$item->projectPlanId) ?>"><?php echo zget($projectList,$item->projectPlanId);?></td>
              <td><?php echo $item->totalReturn;?></td>
<!--              <td title="--><?php //echo $item->createdBy?$depts[$dmap[$item->createdBy]->dept]:''?><!--">--><?php //echo $item->createdBy?$depts[$dmap[$item->createdBy]->dept]:'';?><!--</td>-->
              <td title="<?php echo $item->createdDept?$depts[$item->createdDept]:''?>"><?php echo $item->createdDept?$depts[$item->createdDept]:'';?></td>
              <td><?php echo zget($users,$item->createdBy,'');?></td>
              <td><?php echo $item->createdDate;?></td>
              <td><?php echo $item->currentReview;?></td>
              <td title="<?php echo $item->closed == '1' ? $lang->outwarddelivery->labelList['closed']:zget($lang->outwarddelivery->statusList, $item->status)?>"><?php echo $item->closed == '1' ? $lang->outwarddelivery->labelList['closed']:zget($lang->outwarddelivery->statusList, $item->status);?></td>
              <?php
              if($item->status=='waitsubmitted' or $item->status=='reject')
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
              if($item->closed=='1'){
                  $reviewerUsersSubStr = '';
              }
              ?><td title="<?php echo $reviewerUsersSubStr;?>" class='text-ellipsis'><?php echo $reviewerUsersSubStr ?></td>
              <td class='c-actions text-center'>
                <?php
                common::printIcon('outwarddelivery', 'edit', "outwarddeliveryID=$item->id", $item, 'list');
                if ($item->issubmit == 'save'){
                    $disabled = 'disabled';
                    if ($app->user->account == $item->createdBy or $app->user->account == 'admin'){
                        $disabled = '';
                    }
                    echo '<a href="javascript:void(0)" '.$disabled.'  class="btn" onclick="$.zui.messager.danger(\''.$lang->outwarddelivery->submitMsgTip.'\');" title="提交" data-app="second"><i class="icon-modify-submit icon-play"></i></a>';
                }else{
                    common::printIcon('outwarddelivery', 'submit', "outwarddeliveryID=$item->id", $item, 'list', 'play', '', 'iframe', true);
                }
                common::printIcon('outwarddelivery', 'review', "outwarddeliveryID=$item->id&version=$item->version&reviewStage=$item->reviewStage", $item, 'list', 'glasses', '', 'iframe', true);
                common::printIcon('outwarddelivery', 'copy', "outwarddeliveryID=$item->id", $item, 'list');
                common::printIcon('outwarddelivery', 'close', "outwarddeliveryID=$item->id", $item, 'list', 'cancel', '', 'iframe', true);
                common::printIcon('outwarddelivery', 'delete', "outwarddeliveryID=$item->id", $item, 'list', 'trash', '', 'iframe', true);
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
