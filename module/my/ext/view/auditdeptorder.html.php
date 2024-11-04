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
        <?php if(empty($reviewList)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <form class='main-table' id='deptorderForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
                <?php $vars = "type=$mode&browseType=$browseType&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
                <table class='table table-fixed has-sort-head' id='deptorders'>
                    <thead>
                    <tr>
                        <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->deptorder->code);?></th>
                        <th class='w-160px'><?php common::printOrderLink('summary', $orderBy, $vars, $lang->deptorder->summary);?></th>
                        <th class='w-80px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->deptorder->type);?></th>
                        <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->deptorder->app);?></th>
                        <th class='w-60px'><?php common::printOrderLink('source', $orderBy, $vars, $lang->deptorder->source);?></th>
                        <th class='w-100px'><?php common::printOrderLink('team', $orderBy, $vars, $lang->deptorder->team);?></th>
                        <th class='w-80px'><?php common::printOrderLink('exceptDoneDate', $orderBy, $vars, $lang->deptorder->exceptDoneDate);?></th>
                        <th class='w-60px'><?php common::printOrderLink('ifAccept', $orderBy, $vars, $lang->deptorder->ifAccept);?></th>
                        <th class='w-50px'><?php common::printOrderLink('acceptDept', $orderBy, $vars, $lang->deptorder->acceptDept);?></th>
                        <th class='w-50px'><?php common::printOrderLink('acceptUser', $orderBy, $vars, $lang->deptorder->acceptUser);?></th>
                        <th class='w-50px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->deptorder->status);?></th>
                        <th class='w-50px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->deptorder->dealUser);?></th>
                        <th class='text-center w-100px'><?php echo $lang->actions;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $deptorder):?>
                        <tr>
                            <td><?php echo common::hasPriv('deptorder', 'view') ? html::a($this->createLink('deptorder','view', "deptorderID=$deptorder->id"), $deptorder->code) : $deptorder->code;?></td>
                            <td title="<?php echo $deptorder->summary;?>" class='text-ellipsis'><?php echo $deptorder->summary;?></td>
                            <td><?php echo zget($lang->deptorder->typeList, $deptorder->type);?></td>
                            <td title="<?php echo zget($apps,$deptorder->app);?>" class='text-ellipsis'><?php echo zget($apps,$deptorder->app);?></td>
                            <td><?php echo zget($lang->deptorder->sourceList, $deptorder->source);?></td>
                            <td  title="<?php $userList = '';foreach(explode(',', trim($deptorder->team, ',')) as $user) $userList .= $users[$user] . ',';$userList = trim($userList, ',');echo $userList; ?>" class='text-ellipsis team'><?php echo $userList; ?></td>
                            <td><?php echo $deptorder->exceptDoneDate;?></td>
                            <td><?php echo zget($lang->deptorder->ifAcceptList, $deptorder->ifAccept, '');?></td>
                            <td title="<?php echo zget($depts, $deptorder->acceptDept);?>" class='text-ellipsis'><?php echo zget($depts, $deptorder->acceptDept, '');?></td>
                            <td title="<?php echo zget($users, $deptorder->acceptUser);?>" class='text-ellipsis'><?php echo zget($users, $deptorder->acceptUser, '');?></td>
                            <td>
                                <?php echo zget($lang->deptorder->statusList, $deptorder->status);?>
                            </td>
                            <td title="<?php echo zget($users, $deptorder->dealUser);?>" class='text-ellipsis'><?php echo zget($users, $deptorder->dealUser, '');?></td>
                            <td class='c-actions text-center'>
                                <?php
                                if(($deptorder->status == 'assigned' or $deptorder->status == 'backed')  and $app->user->account == $deptorder->createdBy) {
                                    common::printIcon('deptorder', 'edit', "deptorderID=$deptorder->id", $deptorder, 'list', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = 'data-app=deptorder' );
                                }
                                if($deptorder->status != 'closed' and $deptorder->status != 'backed') {
                                    common::printIcon('deptorder', 'deal', "deptorderID=$deptorder->id", $deptorder, 'list', 'time', '', 'iframe', true);
                                }
                                common::printIcon('deptorder', 'copy', "deptorderID=$deptorder->id", $deptorder, 'list', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = 'data-app=deptorder');
                                if($deptorder->status != 'closed') {
                                    common::printIcon('deptorder', 'close', "deptorderID=$deptorder->id", $deptorder, 'list','off', '', 'iframe', true);
                                }
                                common::printIcon('deptorder', 'delete', "deptorderID=$deptorder->id", $deptorder, 'list', 'trash', '', 'iframe', true);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </form>
        <?php endif;?>
    </div>
</div>
<script>

</script>
<?php include '../../../common/view/footer.html.php';?>

