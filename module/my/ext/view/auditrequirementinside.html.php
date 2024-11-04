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
        <?php if(empty($reviewList) && empty($reviewListIgnore)):?>
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
                        <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->requirementinside->code);?></th>
                        <th class='w-250px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->requirementinside->name);?></th>
                        <th class='w-250px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->requirementinside->project);?></th>
                        <th class='w-100px'><?php common::printOrderLink('dept', $orderBy, $vars, $lang->requirementinside->dept);?></th>
                        <th class='w-90px'><?php common::printOrderLink('owner', $orderBy, $vars, $lang->requirementinside->owner);?></th>
                        <th class='w-100px'><?php common::printOrderLink('deadLine', $orderBy, $vars, $lang->requirementinside->deadLine);?></th>
                        <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->requirementinside->end);?></th>
                        <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->requirementinside->createdDate);?></th>
                        <th class='w-90px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->requirementinside->status);?></th>
                        <th class='w-90px'><?php echo $lang->requirementinside->pending;?></th>
                        <th class='text-center w-250px'><?php echo $lang->actions;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $requirement):?>
                        <tr>
                            <td><?php echo $requirement->code;?></td>
                            <td class='text-ellipsis' title="<?php echo $requirement->name;?>"><?php echo common::hasPriv('requirementinside', 'view') ? html::a($this->createLink('requirementinside', 'view', "requirementID=$requirement->id"), $requirement->name) : $requirement->name;?></td>
                            <td class="text-ellipsis" title="<?php echo zmget($projects, $requirement->project, '');?>"><?php echo zmget($projects, $requirement->project, '');?></td>
                            <td title="<?php echo zmget($depts, $requirement->dept);?>"><?php echo zmget($depts, $requirement->dept);?></td>
                            <td title="<?php echo zmget($users, $requirement->owner);?>"><?php echo zmget($users, $requirement->owner, '');?></td>
                            <td><?php echo $requirement->deadLine;?></td>
                            <td><?php if(!helper::isZeroDate($requirement->end)) echo $requirement->end;?></td>
                            <td class="text-ellipsis" title="<?php echo $requirement->createdDate;?>"><?php echo $requirement->createdDate;?></td>
                            <td><?php echo zget($lang->requirementinside->statusList, $requirement->status);?></td>

                    <?php
                    $reviewersTitle = '';
                    if(!empty($requirement->reviewer))
                    {
                        foreach(explode(',', $requirement->reviewer) as $reviewers)
                        {
                            if(!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                        }
                    }
                    $reviewersTitle = trim($reviewersTitle, ',');
                    ?>
                    <td title='<?php echo $reviewersTitle;?>' class='text-ellipsis'>
                        <?php echo $reviewersTitle;?>
                    </td>
                    <td class='c-actions'>
                        <?php
                        common::printIcon('requirementinside', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                        common::printIcon('requirementinside', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                        common::printIcon('requirementinside', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');
                        if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy) {
                            if ($requirement->status == 'closed') {
                                common::printIcon('requirementinside', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                            } else {
                                common::printIcon('requirementinside', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                            }
                        }else if($requirement->status == 'closed'){
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                        }

                        if ($requirement->ignoreStatus) {
                            common::printIcon('requirementinside', 'recover', "requirementID=$requirement->id", $requirement, 'list', 'bell', '', 'iframe', true);
                        } else {
                            common::printIcon('requirementinside', 'ignore', "requirementID=$requirement->id", $requirement, 'list', 'ban', '', 'iframe', true);
                        }
                        common::printIcon('requirementinside', 'delete', "requirementID=$requirement->id", $requirement, 'list', 'trash', '', 'iframe', true);

                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'></div>
        <?php if($reviewListIgnore) { ?>
        <div style="padding: 10px 0 5px 10px">已忽略</div>
        <table class='table has-sort-head table-fixed' id='reviewList2'>
            <thead>
            <tr>
                <th class='w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->requirementinside->code);?></th>
                <th><?php common::printOrderLink('name', $orderBy, $vars, $lang->requirementinside->name);?></th>
                <th class='w-250px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->requirementinside->project);?></th>
                <th class='w-100px'><?php common::printOrderLink('dept', $orderBy, $vars, $lang->requirementinside->dept);?></th>
                <th class='w-90px'><?php common::printOrderLink('owner', $orderBy, $vars, $lang->requirementinside->owner);?></th>
                <th class='w-100px'><?php common::printOrderLink('deadLine', $orderBy, $vars, $lang->requirementinside->deadLine);?></th>
                <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->requirementinside->end);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->requirementinside->createdDate);?></th>
                <th class='w-90px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->requirementinside->status);?></th>
                <th class='w-90px'><?php echo $lang->requirementinside->pending;?></th>
                <th class='text-center w-250px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($reviewListIgnore as $requirement):?>
                <tr>
                    <td><?php echo $requirement->code;?></td>
                    <td class='text-ellipsis' title="<?php echo $requirement->name;?>"><?php echo common::hasPriv('requirementinside', 'view') ? html::a($this->createLink('requirementinside', 'view', "requirementID=$requirement->id"), $requirement->name) : $requirement->name;?></td>
                    <td class="text-ellipsis" title="<?php echo zget($projects, $requirement->project, '');?>"><?php echo zget($projects, $requirement->project, '');?></td>
                    <td><?php echo zget($depts, $requirement->dept);?></td>
                    <td><?php echo zget($users, $requirement->owner, '');?></td>
                    <td><?php echo $requirement->deadLine;?></td>
                    <td><?php if(!helper::isZeroDate($requirement->end)) echo $requirement->end;?></td>
                    <td class="text-ellipsis" title="<?php echo $requirement->createdDate;?>"><?php echo $requirement->createdDate;?></td>
                    <td><?php echo zget($lang->requirementinside->statusList, $requirement->status);?></td>

                    <?php
                    $reviewersTitle = '';
                    if(!empty($requirement->reviewer))
                    {
                        foreach(explode(',', $requirement->reviewer) as $reviewers)
                        {
                            if(!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                        }
                    }
                    $reviewersTitle = trim($reviewersTitle, ',');
                    ?>
                    <td title='<?php echo $reviewersTitle;?>' class='text-ellipsis'>
                        <?php echo $reviewersTitle;?>
                    </td>
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
        <div class='table-footer'></div>
        <?php } ?>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
