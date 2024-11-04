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
            <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->demand->code);?></th>
            <th class='w-180px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->demand->title);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->demand->app);?></th>
<!--            <th class='w-100px'>--><?php //common::printOrderLink('endDate', $orderBy, $vars, $lang->demand->endDate);?><!--</th>-->
            <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->demand->createdBy);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->demand->createdDept);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->demand->createdDate);?></th>
            <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->demand->status);?></th>
            <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->demand->dealUser);?></th>
            <th class='text-center w-250px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($reviewList as $demand):?>
          <tr>
            <td title='<?php echo $demand->code;?>'><?php echo common::hasPriv('demand', 'view') ? html::a($this->createLink('demand', 'view', "demandID=$demand->id"), $demand->code) : $demand->code;?></td>
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
<!--            <td>--><?php //echo $demand->endDate;?><!--</td>-->
            <td><?php echo zget($users, $demand->createdBy, '');?></td>
            <td><?php echo zget($depts, $demand->createdDept, '');?></td>
            <td><?php echo $demand->createdDate;?></td>
            <td><?php echo zget($lang->demand->statusList, $demand->status);?></td>
            <td class='text-ellipsis' title='<?php echo zmget($users, $demand->dealUser, '');?>'><?php echo zmget($users, $demand->dealUser, '');?></td>
            <td class='c-actions' style="overflow:visible">
                <?php if(common::hasPriv('demand', 'reviewdelay')): ?>
                    <?php if(in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title="<?php echo $lang->demand->review ?>"><i class="icon icon-glasses"></i></button>
                            <ul class="dropdown-menu">
                                <?php if(in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                                    <li><?php echo html::a($this->createLink('demand', 'reviewdelay', 'demandID=' . $demand->id , '', true), $lang->demand->reviewdelay , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                <?php endif;?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle disabled" data-toggle="dropdown" title="<?php echo $lang->demand->review ?>"><i class="icon icon-glasses disabled"></i></button>
                        </div>
                    <?php endif; ?>
                <?php endif;?>
              <?php
                common::printIcon('demand', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                common::printIcon('demand', 'deal', "demandID=$demand->id", $demand, 'list', 'time', '', 'iframe', true);
                common::printIcon('demand', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                common::printIcon('demand', 'assignment', "demandID=$demand->id", $demand, 'list', 'hand-right', '', 'iframe', true);
                common::printIcon('demand', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'list', 'trash', '', 'iframe', true);
//                common::printIcon('demand', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or in_array($this->app->user->account, $executives)) {
                    common::printIcon('demand', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                }
                //忽略/恢复
                if ($demand->ignoreStatus == 0){
                    common::printIcon('demand', 'ignore', "demandID=$demand->id", $demand, 'list', 'ban', '', 'iframe', true);
                }else{
                    common::printIcon('demand', 'recoveryed', "demandID=$demand->id", $demand, 'list', 'bell', '', 'iframe', true);
                }
                //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or in_array($this->app->user->account, $executives)) {
                    if ($demand->status == 'suspend') {
                        common::printIcon('demand', 'start', "demandID=$demand->id", $demand, 'list', 'magic', '', 'iframe', true);
                    } else {
                        common::printIcon('demand', 'suspend', "demandID=$demand->id", $demand, 'list', 'pause', '', 'iframe', true);
                    }
                }
                else if($demand->status == 'suspend'){
                    echo '<button type="button" class="disabled btn" title="' . $lang->demand->start . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->demand->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
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
                <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->demand->code);?></th>
                <th class='w-180px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->demand->title);?></th>
                <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->demand->app);?></th>
                <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->demand->type);?></th>
                <th class='w-100px'><?php common::printOrderLink('endDate', $orderBy, $vars, $lang->demand->endDate);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->demand->createdBy);?></th>
                <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->demand->createdDept);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->demand->createdDate);?></th>
                <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->demand->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->demand->dealUser);?></th>
                <th class='text-center w-250px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($reviewListIgnore as $demand):?>
                <tr>
                    <td title='<?php echo $demand->code;?>'><?php echo common::hasPriv('demand', 'view') ? html::a($this->createLink('demand', 'view', "demandID=$demand->id"), $demand->code) : $demand->code;?></td>
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
                    <td><?php echo zget($lang->demand->statusList, $demand->status);?></td>
                    <td><?php echo zget($users, $demand->dealUser, '');?></td>
                    <td class='c-actions'>
                        <?php if(common::hasPriv('demand', 'reviewdelay')): ?>
                            <?php if(in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                                <div class="btn-group">
                                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title="<?php echo $lang->datamanagement->readmessage ?>"><i class="icon icon-glasses"></i></button>
                                    <ul class="dropdown-menu">
                                        <?php if(in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                                            <li><?php echo html::a($this->createLink('demand', 'reviewdelay', 'demandID=' . $demand->id , '', true), $lang->demand->reviewdelay , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                        <?php endif;?>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <div class="btn-group">
                                    <button class="btn btn-primary dropdown-toggle disabled" data-toggle="dropdown" title="<?php echo $lang->demand->review ?>"><i class="icon icon-glasses disabled"></i></button>
                                </div>
                            <?php endif; ?>
                        <?php endif;?>
                        <?php
                        common::printIcon('demand', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                        common::printIcon('demand', 'deal', "demandID=$demand->id", $demand, 'list', 'time', '', 'iframe', true);
                        common::printIcon('demand', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                        common::printIcon('demand', 'assignment', "demandID=$demand->id", $demand, 'list', 'hand-right', '', 'iframe', true);
                        common::printIcon('demand', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'list', 'trash', '', 'iframe', true);
                        common::printIcon('demand', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                        //忽略/恢复
                        if ($demand->ignoreStatus == 0){
                            common::printIcon('demand', 'ignore', "demandID=$demand->id", $demand, 'list', 'ban', '', 'iframe', true);
                        }else{
                            common::printIcon('demand', 'recoveryed', "demandID=$demand->id", $demand, 'list', 'bell', '', 'iframe', true);
                        }
                        //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                        if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or in_array($this->app->user->account, $executives)) {
                            if ($demand->status == 'suspend') {
                                common::printIcon('demand', 'start', "demandID=$demand->id", $demand, 'list', 'magic', '', 'iframe', true);
                            } else {
                                common::printIcon('demand', 'suspend', "demandID=$demand->id", $demand, 'list', 'pause', '', 'iframe', true);
                            }
                        }
                        else if($demand->status == 'suspend'){
                            echo '<button type="button" class="disabled btn" title="' . $lang->demand->start . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->demand->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                        }
                        ?>
                    </td>
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
