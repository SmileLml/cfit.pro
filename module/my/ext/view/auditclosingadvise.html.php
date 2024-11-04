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
    <div id="mainContent" class="main-row fade">
        <div class='main-col'>
            <?php if(empty($reviewList)):?>
                <div class="table-empty-tip">
                    <p>
                        <span class="text-muted"><?php echo $lang->noData;?></span>
                    </p>
                </div>
            <?php else:?>
                <form class='main-table' id='closingadviseForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
                    <?php $vars = "projectId=$projectID&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
                    <table class='table table-fixed has-sort-head' id='closingadvises'>
                        <thead>
                        <tr>
                            <th class='w-50px'><?php echo $lang->closingadvise->id;?></th>
                            <th class='w-100px'><?php echo $lang->closingitem->projectName;?></th>
                            <th class='w-100px'><?php echo $lang->closingadvise->type;?></th>
                            <th class='w-200px'><?php echo $lang->closingadvise->advise;?></th>
                            <th class='w-100px'><?php echo $lang->closingadvise->createdDate;?></th>
                            <th class='w-80px'><?php echo $lang->closingadvise->status;?></th>
                            <th class='w-120px'><?php echo $lang->closingadvise->dealUser;?></th>
                            <th class='text-center w-60px'><?php echo $lang->actions;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($reviewList as $item):?>
                            <tr>
                                <td title="<?php echo $item->id;?>" class='text-ellipsis'><?php echo common::hasPriv('closingadvise', 'view') ? html::a($this->createLink('closingadvise', 'view', "projectID=$item->projectId&closingitemID=$item->id"), $item->id): $item->id;?></td>
                                <td title="<?php echo $projects[$item->projectId];?>" class='text-ellipsis'><?php echo common::hasPriv('closingadvise', 'view') ? html::a($this->createLink('closingadvise', 'view', "projectID=$item->projectId&closingitemID=$item->id"), $projects[$item->projectId]) : $projects[$item->projectId];?></td>
                                <td title="<?php echo $this->lang->closingadvise->sourceList[$item->source];?>" class='text-ellipsis'><?php echo $this->lang->closingadvise->sourceList[$item->source];?></td>
                                <td title="<?php echo $item->advise;?>" class='text-ellipsis'><?php echo $item->advise;?></td>
                                <td><?php echo $item->createdDate  != '0000-00-00' ? $item->createdDate : '';;?></td>
                                <td><?php echo zget($lang->closingadvise->browseStatus + $feedbackResults, $item->status);?></td>
                                <td  title="<?php $userList = '';foreach(explode(',', trim($item->dealuser, ',')) as $user) $userList .= $users[$user] . ',';$userList = trim($userList, ',');echo $userList; ?>" class='text-ellipsis team'><?php echo $userList; ?></td>
                                <td class='c-actions text-center'>
                                    <?php
                                    common::printIcon('closingadvise', 'review', "closingadviseID=$item->id", $item, 'list', 'checked', '', 'iframe', true);
                                    common::printIcon('closingadvise', 'assignUser', "closingadviseID=$item->id", $item, 'list', 'hand-right', '', 'iframe', true);
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
        $(function(){$('#closingadviseForm').table();})
    </script>
<?php include '../../common/view/footer.html.php';?>