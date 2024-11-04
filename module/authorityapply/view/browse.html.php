<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left" style="margin-top: 8px">
        <?php
        $i = 0;
        foreach ($lang->authorityapply->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            $count = $label=='tomedeal'?"<span class='label label-light label-badge red-pending' style='top: -8px'>$toDealCount</span>":'';
            echo html::a($this->createLink('authorityapply', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>'.$count, '', "class='btn btn-link $active'");

            $i++;
            if ($i >= 3) break;
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                    class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>

    </div>
    <div class="btn-toolbar pull-right" style="margin-top: 8px">
        <?php  echo html::a($this->createLink('authorityapply', 'create'), "<i class='icon-plus'></i> {$lang->authorityapply->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='authorityapply'></div>
        <?php if (empty($listData)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='authorityapplyForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='authorityapplys'>
                    <thead>
                    <tr>
                        <th class='w-110px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->authorityapply->code); ?></th>
                        <th class='w-200px'><?php common::printOrderLink('summary', $orderBy, $vars, $lang->authorityapply->summary); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->authorityapply->createdBy); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('applyDepartment', $orderBy, $vars, $lang->authorityapply->applyDepartment); ?></th>
                        <th class='w-110px'><?php common::printOrderLink('createdTime', $orderBy, $vars, $lang->authorityapply->createdTime); ?></th>
                        <th class='w-100px'><?php echo $lang->authorityapply->subSystem; ?></th>
                        <th class='w-70px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->authorityapply->status); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->authorityapply->dealUser); ?></th>
                        <th class='text-center w-100px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($listData as $authorityapply): ?>
                        <tr>
                            <td class='text-ellipsis'><?php echo common::hasPriv('authorityapply', 'deal') ? html::a(inlink('deal', "authorityapplyID=$authorityapply->id"), $authorityapply->code) : $authorityapply->code;?></td>
                            <td title='<?php echo $authorityapply->summary; ?>' class='text-ellipsis'><?php echo $authorityapply->summary; ?></td>

                            <td class='text-ellipsis'><?php echo zget($userList, $authorityapply->createdBy); ?></td>
                            <td class='text-ellipsis'><?php echo zget($deptList, $authorityapply->applyDepartment); ?></td>
                            <td title='<?php echo $authorityapply->createdTime; ?>' class='text-ellipsis'><?php echo $authorityapply->createdTime; ?></td>
                            <?php
                            $content = json_decode($authorityapply->content,true);
                            $subSystem='';
                            if($content){
                                $subSystem = array_column($content,'subSystem');
                                $subSystem = $subSystem?implode(',',$subSystem):'';
                            }

                            ?>
                            <td class='text-ellipsis' title='<?php echo zmget($this->lang->authorityapply->subSystemList, $subSystem); ?>'><?php echo zmget($this->lang->authorityapply->subSystemList, $subSystem); ?></td>

                            <td class='text-ellipsis'><?php echo in_array($authorityapply->status,$lang->authorityapply->approvalStatus)?'审批中':zget($lang->authorityapply->searchStatusList, $authorityapply->status); ?></td>
                            <td class='text-ellipsis'><?php echo zmget($userList, $authorityapply->dealUser); ?></td>
                            <td class='c-actions text-center'>
                                <?php
//                                common::printIcon('authorityapply', 'submit', "authorityapplyID=$authorityapply->id", $authorityapply, 'list', 'checked', 'hiddenwin', '', true, '', $lang->authorityapply->submit);
                                common::printIcon('authorityapply', 'edit', "authorityapplyID=$authorityapply->id", $authorityapply, 'list');
                                common::printIcon('authorityapply', 'deal', "authorityapplyID=$authorityapply->id", $authorityapply, 'list', 'time', '', '', '', '', $lang->authorityapply->deal);
                                common::printIcon('authorityapply', 'delete', "authorityapplyID=$authorityapply->id", $authorityapply, 'list', 'trash', 'hiddenwin');
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
