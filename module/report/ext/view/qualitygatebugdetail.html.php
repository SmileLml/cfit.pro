<?php include '../../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 300px;">
    <div class="center-block">
        <div class="main-header">
            <h2>
                <small><?php echo  $title;?></small>
            </h2>
        </div>
        <?php if(!$data):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <form class='main-table' id='bugForm'  method='post' data-ride='table' data-nested='true' data-checkable='false'>
                <?php $vars = "projectId=$projectID&productId=$productId&productVersion=$productVersion&childType=$childType&sourceType=$sourceType&severity=$severity&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
                <table class='table table-fixed has-sort-head' id='bug'>
                    <thead>
                    <tr>
                        <th class='w-80px'><?php common::printOrderLink('id', $orderBy, $vars,  $lang->bug->id); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('severity', $orderBy, $vars, $lang->bug->severity);?></th>
                        <th class='w-180px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->bug->title);?></th>
                        <th class='w-80px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->bug->type);?></th>
                        <th class='w-100px'><?php common::printOrderLink('childType', $orderBy, $vars, $lang->bug->childType);?></th>
                        <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->bug->status);?></th>
                        <th class='w-100px'><?php common::printOrderLink('openedBy', $orderBy, $vars, $lang->bug->openedByAB);?></th>
                        <th class='w-120px'><?php common::printOrderLink('openedDate', $orderBy, $vars, $lang->bug->openedDate);?></th>
                        <th class='w-100px'><?php common::printOrderLink('assignedTo', $orderBy, $vars, $lang->bug->assignedTo);?></th>
                        <th class='w-100px'><?php common::printOrderLink('resolution', $orderBy, $vars, $lang->bug->resolutionAB);?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($data as $item):?>
                        <tr>
                            <td><?php echo html::a(helper::createLink('bug', 'view', "bugID=$item->id"), sprintf('%03d', $item->id), null, "data-app='{$this->app->openApp}'"); ?></td>
                            <td>
                                <?php
                                $severityValue = zget($this->lang->bug->severityList, $item->severity);
                                $hasCustomSeverity = !is_numeric($severityValue);
                                if($hasCustomSeverity)
                                {
                                    echo "<span class='label-severity-custom' data-severity='{$item->severity}' title='" . $severityValue . "'>" . $severityValue . "</span>";
                                }
                                else
                                {
                                    echo "<span class='label-severity' data-severity='{$item->severity}' title='" . $severityValue . "'></span>";
                                }
                                ?>
                            </td>
                            <td><?php echo html::a(helper::createLink('bug', 'view', "bugID=$item->id"), $item->title, null, "data-app='{$this->app->openApp}'"); ?></td>
                            <td><?php echo zget($this->lang->bug->typeList, $item->type); ?></td>
                            <td><?php echo zget($childTypeList[$item->type], $item->childType); ?></td>
                            <td>
                                <span class="status-bug status-<?php echo $item->status;?>">
                                <?php echo zget($this->lang->bug->statusList, $item->status); ?>
                                </span>
                            </td>
                            <td><?php echo zget($users, $item->openedBy); ?></td>
                            <td><?php echo $item->openedDate; ?></td>
                            <td><?php echo zget($users, $item->assignedTo); ?></td>
                            <td><?php echo zget($this->lang->bug->resolutionList, $item->resolution); ?></td>
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

<?php include '../../../common/view/footer.html.php';?>
<script>
</script>

