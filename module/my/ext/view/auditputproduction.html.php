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
                <th class='w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->putproduction->code);?></th>
                <th class='w-110px'><?php echo $lang->putproduction->desc;?></th>
                <th class='w-80px'><?php common::printOrderLink('outsidePlanId', $orderBy, $vars, $lang->putproduction->outsidePlanId);?></th>
                <th class='w-80px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->putproduction->app);?></th>
                <th class='w-60px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->putproduction->level);?></th>
                <th class='w-80px'><?php common::printOrderLink('stage', $orderBy, $vars, $lang->putproduction->stage);?></th>
                <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->putproduction->createdBy);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->putproduction->createdDate);?></th>
                <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->putproduction->status);?></th>
                <th class='w-100px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->putproduction->dealUser);?> </th>
                <th class='text-center w-160px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php
                foreach($reviewList as $item):
                    $levelInfo = zget($lang->putproduction->levelList, $item->level, '');
                    $stageInfo = zmget($lang->putproduction->stageList, $item->stage, '');
                    $outsidePlanInfo = zmget($outsideProjectList, $item->outsidePlanId, '');
                    $appInfo = zmget($apps, $item->app, '');
                    $dealUserInfo = zmget($users, $item->dealUser, '');
                ?>
            <tr>
                <td title="<?php echo $item->code; ?>"><?php echo common::hasPriv('putproduction', 'view') ? html::a($this->createLink('putproduction','view', "putproductionID=$item->id"), $item->code) : $item->code;?></td>
                <td class='text-ellipsis' title="<?php echo strip_tags($item->desc); ?>"><?php echo strip_tags($item->desc);?></td>
                <td class='text-ellipsis' title="<?php echo  $outsidePlanInfo;?>"><?php echo $outsidePlanInfo;?></td>
                <td class='text-ellipsis' title="<?php echo  $appInfo;?>"><?php echo $appInfo;?></td>
                <td class='text-ellipsis' title="<?php echo  $levelInfo;?>"><?php echo $levelInfo;?></td>
                <td class='text-ellipsis' title="<?php echo $stageInfo; ?>"><?php echo $stageInfo;?></td>
                <td><?php echo zget($users, $item->createdBy,'');?></td>
                <td><?php echo $item->createdDate;?></td>
                <td><?php echo zget($lang->putproduction->statusList, $item->status,'');?></td>
                <td title="<?php echo $dealUserInfo;?>" class='text-ellipsis'><?php echo $dealUserInfo; ?></td>
                <td class='c-actions text-center'>
                <?php
                    common::printIcon('putproduction', 'edit', "putproductionID=$item->id", $item, 'list');
                    if ($item->issubmit == 'save'){
                        $disabled = 'disabled';
                        if((common::hasPriv('putproduction', 'submit') && common::isDealUser($item->dealUser, $app->user->account))|| $app->user->account == 'admin'){
                            $disabled = '';
                        }
                        echo '<a href="javascript:void(0)" '.$disabled.'  class="btn" onclick="$.zui.messager.danger(\''.$lang->putproduction->submitMsgTip.'\');" title="提交" data-app="second"><i class="icon-putproduction-submit icon-play"></i></a>';
                    }else{
                        common::printIcon('putproduction', 'submit', "putproductionID=$item->id", $item, 'list', 'play', '', 'iframe', true);
                    }
                    common::printIcon('putproduction', 'assignment', "putproductionID=$item->id", $item, 'list', 'hand-right', '', 'iframe', true);
                    common::printIcon('putproduction', 'review', "putproductionID=$item->id", $item, 'list', 'glasses', '', 'iframe', true);
                    common::printIcon('putproduction', 'copy', "putproductionID=$item->id", $item, 'list');
                    common::printIcon('putproduction', 'delete', "putproductionID=$item->id", $item, 'list', 'trash', '', 'iframe', true);
                    if (common::hasPriv('putproduction', 'cancel')) common::printIcon('putproduction', 'cancel', "putproductionID=$item->id", $item, 'list', 'cancel', '', 'iframe', true);
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
