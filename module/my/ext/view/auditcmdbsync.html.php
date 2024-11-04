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
    <?php if(empty($data)):?>
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
              <th class='w-70px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->cmdbsync->id);?></th>
              <th class='w-260px'><?php echo $lang->cmdbsync->app;?></th>
              <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->cmdbsync->type);?></th>
              <th class='w-120px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->cmdbsync->status);?></th>
              <th class='w-160px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->cmdbsync->createdDate);?></th>
              <th class='w-160px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->cmdbsync->dealUser);?></th>
              <th class='text-center w-80px'><?php echo $lang->actions;?></th>
          </tr>
          </thead>
          <tbody>
          <?php
          foreach ($data as $item):
              $typeInfo = zget($lang->cmdbsync->typeList, $item->type, '');
              $statusInfo = zmget($lang->cmdbsync->statusList, $item->status, '');
              $appInfo = zmget($appList, $item->app, '');
              $dealUserInfo = zmget($users, $item->dealUser, '');
              ?>
              <tr data-val='<?php echo $item->id?>'>
                  <td title="<?php echo $item->id; ?>">
                      <?php echo common::hasPriv('cmdbsync', 'view') ? html::a($this->createLink('cmdbsync','view', "id=$item->id"), $item->id) : $item->id;?>
                  </td>
                  <td class='text-ellipsis viewClick' title="<?php echo $appInfo; ?>"><?php echo $appInfo;?></td>
                  <td class='text-ellipsis viewClick' title="<?php echo  $typeInfo;?>"><?php echo $typeInfo;?></td>
                  <td class='text-ellipsis viewClick' title="<?php echo  $statusInfo;?>"><?php echo $statusInfo;?></td>
                  <td class='text-ellipsis viewClick' title="<?php echo  $item->createdDate;?>"><?php echo $item->createdDate;?></td>
                  <td class='text-ellipsis viewClick' title="<?php echo $dealUserInfo; ?>"><?php echo $dealUserInfo;?></td>
                  <td class='c-actions text-center'>
                      <?php
                      common::printIcon('cmdbsync', 'deal', "id=$item->id", $item, 'list', 'time', '', 'iframe', true);
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
