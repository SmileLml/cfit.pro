<?php include '../../common/view/header.html.php'; ?>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php
            $i = 0;
            foreach ($lang->environmentorder->labelList as $label => $labelName) {
                $active = $browseType == $label ? 'btn-active-text' : '';
                echo html::a($this->createLink('environmentorder', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

                $i++;
                if ($i >= 13) break;
            }
            ?>
            <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                        class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
        </div>
        <div class="btn-toolbar pull-right">
            <?php if (in_array($app->user->account, array_keys($lang->environmentorder->createByList))) echo html::a($this->createLink('environmentorder', 'create'), "<i class='icon-plus'></i> {$lang->environmentorder->create}", '', "class='btn btn-primary'"); ?>
        </div>
    </div>
    <div id='mainContent' class='main-row'>
        <div class='main-col'>
            <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
                 data-module='environmentorder'></div>
            <?php if (empty($environmentorders)): ?>
                <div class="table-empty-tip">
                    <p>
                        <span class="text-muted"><?php echo $lang->noData; ?></span>
                    </p>
                </div>
            <?php else: ?>
                <form class='main-table' id='environmentorderForm' method='post' data-ride='table' data-nested='true'
                      data-checkable='false'>
                    <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                    <table class='table table-fixed has-sort-head' id='environmentorders'>
                        <thead>
                        <tr>
                            <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->environmentorder->code); ?></th>
                            <th class='w-100px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->environmentorder->title); ?></th>
                            <th class='w-50px'><?php common::printOrderLink('priority', $orderBy, $vars, $lang->environmentorder->priority); ?></th>
                            <th class='w-60px'><?php common::printOrderLink('origin', $orderBy, $vars, $lang->environmentorder->origin); ?></th>
                            <th class='w-150px'><?php common::printOrderLink('content', $orderBy, $vars, $lang->environmentorder->content); ?></th>
                            <th class='w-120px'><?php common::printOrderLink('finallytime', $orderBy, $vars, $lang->environmentorder->finallytime); ?></th>
<!--                            <th class='w-80px'>--><?php //common::printOrderLink('description', $orderBy, $vars, $lang->environmentorder->description); ?><!--</th>-->
                            <th class='w-50px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->environmentorder->createdBy); ?></th>
                            <th class='w-50px'><?php common::printOrderLink('reviewer', $orderBy, $vars, $lang->environmentorder->reviewer); ?></th>
                            <th class='w-150px'><?php common::printOrderLink('executor', $orderBy, $vars, $lang->environmentorder->executor); ?></th>
                            <th class='w-150px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->environmentorder->dealUser); ?></th>
                            <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->environmentorder->status); ?></th>
                            <th class='w-120px'><?php common::printOrderLink('createdTime', $orderBy, $vars, $lang->environmentorder->createdTime); ?></th>
                            <th class='text-center w-150px'><?php echo $lang->actions; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($environmentorders as $environmentorder): ?>
                            <tr>
                                <td class='text-ellipsis'><?php echo common::hasPriv('environmentorder', 'view') ? html::a(inlink('view', "environmentorderID=$environmentorder->id"), $environmentorder->code) : $environmentorder->code;?></td>
                                <td title='<?php echo $environmentorder->title; ?>' class='text-ellipsis'><?php echo $environmentorder->title; ?></td>
                                <td class='text-ellipsis'>
                                    <?php if($environmentorder->priority):?>
                                    <span class="label <?php echo $this->environmentorder->diffColorPriority($environmentorder->priority); ?>">
                                        <?php echo zget($lang->environmentorder->priorityList, $environmentorder->priority); ?>
                                    </span>
                                    <?php endif;?>
                                </td>
                                <td class='text-ellipsis'>
                                    <?php if($environmentorder->origin):?>
                                    <?php echo zget($lang->environmentorder->originList, $environmentorder->origin); ?>
                                    <?php endif;?>

                                </td>
                                <td title='<?php echo $environmentorder->content; ?>' class='text-ellipsis'><?php echo $environmentorder->content; ?></td>
                                <td title='<?php echo $environmentorder->finallytime; ?>' class='text-ellipsis'>
        <?php if($environmentorder->finallytime):?>
                                    <?php echo $environmentorder->finallytime; ?>
        <?php endif;?>
            </td>
<!--                                <td title='--><?php //echo $environmentorder->description; ?><!--' class='text-ellipsis'>--><?php //echo $environmentorder->description; ?><!--</td>-->
<!--                                <td class='text-ellipsis'>--><?php //echo $environmentorder->list; ?><!--</td>-->
                                <td class='text-ellipsis'><?php echo zget($users, $environmentorder->createdBy); ?></td>
                                <td class='text-ellipsis'><?php echo zget($users, $environmentorder->reviewer); ?></td>
                                <td title='<?php echo zmget($users, $environmentorder->executor); ?>' class='text-ellipsis'><?php echo zmget($users, $environmentorder->executor); ?>
                                </td>
                                <td class='text-ellipsis' title="<?php echo zmget($users, $environmentorder->dealUser); ?>"><?php echo zmget($users, $environmentorder->dealUser); ?></td>
                                <td class='text-ellipsis'><?php echo zget($lang->environmentorder->statusList, $environmentorder->status); ?></td>
                                <td title='<?php echo $environmentorder->createdTime; ?>' class='text-ellipsis'><?php echo $environmentorder->createdTime; ?></td>
                                <td class='c-actions text-center'>
                                    <?php
                                    common::printIcon('environmentorder', 'submit', "environmentorderID=$environmentorder->id", $environmentorder, 'list', 'checked', 'hiddenwin', '', true, '', $lang->environmentorder->submit);
                                    common::printIcon('environmentorder', 'edit', "environmentorderID=$environmentorder->id", $environmentorder, 'list');
                                    common::printIcon('environmentorder', 'deal', "environmentorderID=$environmentorder->id", $environmentorder, 'list', 'time', '', 'iframe', true, '', $lang->environmentorder->deal);
//                                    common::printIcon('environmentorder', 'copy', "environmentorderID=$environmentorder->id", $environmentorder, 'list', 'copy', 'hiddenwin', '', true, '', $lang->environmentorder->copy);
                                    common::printIcon('environmentorder', 'delete', "environmentorderID=$environmentorder->id", $environmentorder, 'list', 'trash', 'hiddenwin');
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
<?php include '../../common/view/footer.html.php'; ?>