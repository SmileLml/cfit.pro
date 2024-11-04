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
        <table class='table has-sort-head table-fixed' id='reviewList'>
          <thead>
            <tr>
              <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->info->code);?></th>
              <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->info->type);?></th>
              <th class='w-100px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->info->app);?></th>
              <th class='w-100px'><?php common::printOrderLink('planBegin', $orderBy, $vars, $lang->info->planBegin);?></th>
              <th class='w-100px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->info->planEnd);?></th>
              <th class='w-100px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->info->createdBy);?></th>
              <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->info->createdDept);?></th>
              <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->info->createdDate);?></th>
              <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->info->status);?></th>
              <th class='text-left w-80px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($reviewList as $info):?>
            <tr>
              <td><?php echo common::hasPriv('info', 'view') ? html::a($this->createLink('info', 'view', "infoID=$info->id"), $info->code) : $info->code;?></td>
              <td><?php echo zget($lang->info->typeList, $info->type);?></td>
              <?php
              $as = array();
              foreach(explode(',', $info->app) as $app)
              {
                  if(!$app) continue;
                  $as[] = zget($apps, $app);
              }
              $app = implode(', ', $as);
              ?>
              <td title="<?php echo $app;?>"><?php echo $app;?></td>
              <td><?php echo $info->planBegin;?></td>
              <td><?php echo $info->planEnd;?></td>
              <td><?php echo $info->realname;?></td>
              <td><?php echo zget($depts, $info->createdDept, '');?></td>
              <td><?php echo substr($info->createdDate, 0, 11);?></td>
              <td><?php echo zget($lang->info->statusList, $info->status);?></td>
              <td class='c-actions'>
                <?php
                common::printIcon('info', 'edit', "infoID=$info->id", $info, 'list');
                common::printIcon('info', 'link', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'list', 'link', '', 'iframe', true);
                if($info->status == 'closing'){
                   echo '<button type="button" class="disabled btn" style="pointer-events: unset;"><i class="icon-modify-review disabled icon-glasses" title="审批" data-app="second"></i></button>';
                } else {
                    common::printIcon('info', 'review', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'list', 'glasses', '', 'iframe', true);
                }
                common::printIcon('info', 'run', "infoID=$info->id", $info, 'list', 'play', '', 'iframe', true);
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
