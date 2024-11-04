<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class='pull-right'>
        <?php common::printLink('cm', 'export', "project=$projectID", "<i class='icon icon-export'></i> " . $lang->cm->export, '', "class='btn btn-link export'", '', 1); ?>
        <?php common::printLink('cm', 'create', "project=$projectID", "<i class='icon icon-plus'></i>" . $lang->cm->create, '', "class='btn btn-primary'"); ?>
    </div>
</div>
<div id="mainContent" class="main-row fade in">
    <div class="main-col">
        <?php if (empty($baselines)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->cm->noData; ?></span>
                    <?php if (common::hasPriv('cm', 'create')): ?>
                        <?php echo html::a($this->createLink('cm', 'create', "progrm=$projectID"), "<i class='icon icon-plus'></i> " . $lang->cm->create, '', "class='btn btn-info'"); ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' method='post' data-ride="table">
                <?php $vars = "projectID=$projectID&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
                <table class="table has-sort-head table-fixed">
                    <thead>
                    <tr>
                        <th class='c-id w-40px'>
                            <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll ?>">
                                <label></label>
                            </div>
                            <?php echo common::printOrderLink('id', $orderBy, $vars, $lang->idAB); ?>
                        </th>
                        <th class='w-50px'><?php echo common::printOrderLink('type', $orderBy, $vars, $lang->cm->type); ?></th>
                        <th class='w-120px'><?php echo common::printOrderLink('title', $orderBy, $vars, $lang->cm->title); ?></th>
                        <th class='w-230px'><?php echo $lang->cm->path; ?></th>
                        <th class='w-60px'><?php echo common::printOrderLink('createdBy', $orderBy, $vars, $lang->cm->createdBy); ?></th>
                        <th class='w-60px'><?php echo common::printOrderLink('cmDate', $orderBy, $vars, $lang->cm->cmDate); ?></th>
                        <th class='w-60px'><?php echo common::printOrderLink('createdDate', $orderBy, $vars, $lang->cm->createdDate); ?></th>
                        <th class='w-30px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($baselines as $baseline): ?>
                        <tr>
                            <td>
                                <?php echo html::checkbox('cmIDList', array($baseline->id => '')); ?>
                                <?php echo $baseline->id; ?>
                            </td>
                            <td title='<?php echo zget($lang->cm->typeList, $baseline->type);?>'><?php echo zget($lang->cm->typeList, $baseline->type); ?></td>
                            <td title='<?php echo $baseline->title;?>'><?php echo html::a(helper::createLink('cm', 'view', "baselineID=$baseline->id"), $baseline->title); ?></td>
                            <td title='<?php echo isset($baseline->items->path) ? $baseline->items->path : '';?>'><?php echo isset($baseline->items->path) ? $baseline->items->path : ''; ?></td>
                            <td><?php echo zget($users, $baseline->createdBy); ?></td>
                            <td><?php echo $baseline->cmDate; ?></td>
                            <td><?php echo $baseline->createdDate; ?></td>
                            <td class='c-actions'>
                                <?php common::printIcon('cm', 'edit', "id=$baseline->id", $baseline, 'edit'); ?>
                                <?php common::printIcon('cm', 'delete', "id=$baseline->id", $baseline, 'closed', '', 'hiddenwin'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class='table-footer'> <?php $pager->show('right', 'pagerjs'); ?> </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
