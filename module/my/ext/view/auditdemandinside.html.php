<?php include '../../../common/view/header.html.php';?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->my->myReviewList as $key => $type):?>
    <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
    <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
    <?php endforeach;?>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <?php if(empty($reviewList) && empty($reviewListIgnore)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='demandForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "type=$mode&browseType=$browseType&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='demands'>
        <thead>
          <tr>
            <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->demandinside->code);?></th>
            <th class='w-180px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->demandinside->title);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->demandinside->app);?></th>
            <th class='w-100px'><?php common::printOrderLink('endDate', $orderBy, $vars, $lang->demandinside->endDate);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->demandinside->createdBy);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->demandinside->createdDept);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->demandinside->createdDate);?></th>
            <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->demandinside->status);?></th>
            <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->demandinside->dealUser);?></th>
            <th class='text-center w-250px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($reviewList as $demand):?>
          <tr>
            <td title='<?php echo $demand->code;?>'><?php echo common::hasPriv('demandinside', 'view') ? html::a($this->createLink('demandinside', 'view', "demandID=$demand->id"), $demand->code) : $demand->code;?></td>
            <td class='text-ellipsis' title='<?php echo $demand->title;?>'><?php echo $demand->title;?></td>
            <?php
            $as = array();
            foreach(explode(',', $demand->app) as $app)
            {
                if(!$app) continue;
                $as[] = zget($apps, $app);
            }
            $app = implode(', ', $as);
            ?>
            <td title="<?php echo $app;?>">
            <?php echo $app;?>
            </td>
            <td><?php echo $demand->endDate;?></td>
            <td><?php echo zget($users, $demand->createdBy, '');?></td>
            <td><?php echo zget($depts, $demand->createdDept, '');?></td>
            <td><?php echo $demand->createdDate;?></td>
            <td><?php echo zget($lang->demandinside->statusList, $demand->status);?></td>
            <td><?php echo zget($users, $demand->dealUser, '');?></td>
            <td class='c-actions'>
                <?php
                common::printIcon('demandinside', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                common::printIcon('demandinside', 'deal', "demandID=$demand->id", $demand, 'list', 'time', '', 'iframe', true);
                common::printIcon('demandinside', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                common::printIcon('demandinside', 'assignment', "demandID=$demand->id", $demand, 'list', 'hand-right', '', 'iframe', true);
                common::printIcon('demandinside', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'list', 'trash', '', 'iframe', true);
                common::printIcon('demandinside', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                //忽略/恢复
                if ($demand->ignoreStatus == 0){
                    common::printIcon('demandinside', 'ignore', "demandID=$demand->id", $demand, 'list', 'ban', '', 'iframe', true);
                }else{
                    common::printIcon('demandinside', 'recoveryed', "demandID=$demand->id", $demand, 'list', 'bell', '', 'iframe', true);
                }
                //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or in_array($this->app->user->account, $executives)) {
                    if ($demand->status == 'suspend') {
                        common::printIcon('demandinside', 'start', "demandID=$demand->id", $demand, 'list', 'magic', '', 'iframe', true);
                    } else {
                        common::printIcon('demandinside', 'suspend', "demandID=$demand->id", $demand, 'list', 'pause', '', 'iframe', true);
                    }
                }
                else if($demand->status == 'suspend'){
                    echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->start . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                }
                ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
        <?php if($reviewListIgnore) { ?>
        <div style="padding: 10px 0 5px 10px">已忽略</div>
        <table class='table table-fixed has-sort-head' id='demands2'>
            <thead>
            <tr>
                <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->demandinside->code);?></th>
                <th class='w-180px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->demandinside->title);?></th>
                <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->demandinside->app);?></th>
                <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->demandinside->type);?></th>
                <th class='w-100px'><?php common::printOrderLink('endDate', $orderBy, $vars, $lang->demandinside->endDate);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->demandinside->createdBy);?></th>
                <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->demandinside->createdDept);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->demandinside->createdDate);?></th>
                <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->demandinside->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->demandinside->dealUser);?></th>
                <th class='text-center w-250px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($reviewListIgnore as $demand):?>
                <tr>
                    <td title='<?php echo $demand->code;?>'><?php echo common::hasPriv('demandinside', 'view') ? html::a($this->createLink('demandinside', 'view', "demandID=$demand->id"), $demand->code) : $demand->code;?></td>
                    <td class='text-ellipsis' title='<?php echo $demand->title;?>'><?php echo $demand->title;?></td>
                    <?php
                    $as = array();
                    foreach(explode(',', $demand->app) as $app)
                    {
                        if(!$app) continue;
                        $as[] = zget($apps, $app);
                    }
                    $app = implode(', ', $as);
                    ?>
                    <td title="<?php echo $app;?>">
                        <?php echo $app;?>
                    </td>
                    <td><?php echo zget($lang->opinion->sourceModeList, $demand->type);?></td>
                    <td><?php echo $demand->endDate;?></td>
                    <td><?php echo zget($users, $demand->createdBy, '');?></td>
                    <td><?php echo zget($depts, $demand->createdDept, '');?></td>
                    <td><?php echo $demand->createdDate;?></td>
                    <td><?php echo zget($lang->demandinside->statusList, $demand->status);?></td>
                    <td><?php echo zget($users, $demand->dealUser, '');?></td>
                    <td class='c-actions'>
                        <?php
                        common::printIcon('demandinside', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                        common::printIcon('demandinside', 'deal', "demandID=$demand->id", $demand, 'list', 'time', '', 'iframe', true);
                        common::printIcon('demandinside', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                        common::printIcon('demandinside', 'assignment', "demandID=$demand->id", $demand, 'list', 'hand-right', '', 'iframe', true);
                        common::printIcon('demandinside', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'list', 'trash', '', 'iframe', true);
                        common::printIcon('demandinside', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                        //忽略/恢复
                        if ($demand->ignoreStatus == 0){
                            common::printIcon('demandinside', 'ignore', "demandID=$demand->id", $demand, 'list', 'ban', '', 'iframe', true);
                        }else{
                            common::printIcon('demandinside', 'recoveryed', "demandID=$demand->id", $demand, 'list', 'bell', '', 'iframe', true);
                        }
                        //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                        if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or in_array($this->app->user->account, $executives)) {
                            if ($demand->status == 'suspend') {
                                common::printIcon('demandinside', 'start', "demandID=$demand->id", $demand, 'list', 'magic', '', 'iframe', true);
                            } else {
                                common::printIcon('demandinside', 'suspend', "demandID=$demand->id", $demand, 'list', 'pause', '', 'iframe', true);
                            }
                        }
                        else if($demand->status == 'suspend'){
                            echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->start . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                        }
                        ?>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <?php } ?>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>