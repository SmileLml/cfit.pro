<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach ($lang->qualitygate->labelList as $label => $labelName) {
            $active = $browseType == strtolower($label) ? 'btn-active-text' : '';
            echo html::a($this->createLink('qualitygate', 'browse', "projectId=$projectId&browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
            $i++;
            if ($i >= 13) break;
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <?php if (common::hasPriv('qualitygate', 'create')) echo html::a($this->createLink('qualitygate', 'create', "projectId=$projectId"), "<i class='icon-plus'></i> {$lang->qualitygate->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='qualitygate'>
        </div>
        <?php if (empty($data)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='qualitygateForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "projectId=$projectId&browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='qualitygates'>
                    <thead>
                    <tr>
                        <th class='w-80px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->qualitygate->code); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('productId', $orderBy, $vars, $lang->qualitygate->productName); ?></th>
                        <th class='w-50px'><?php common::printOrderLink('productCode', $orderBy, $vars, $lang->qualitygate->productCode); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('productVersion', $orderBy, $vars, $lang->qualitygate->version); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('buildName', $orderBy, $vars, $lang->qualitygate->buildName); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('buildStatus', $orderBy, $vars, $lang->qualitygate->buildStatus); ?></th>
                        <th class='w-80px'><?php echo $lang->qualitygate->severityGate; ?></th>
                        <th class='w-50px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->qualitygate->status); ?></th>
                        <th class='w-50px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->qualitygate->dealUser); ?></th>
                        <th class='text-center w-50px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $item): ?>
                        <tr>
                            <td class='text-ellipsis'><?php echo common::hasPriv('qualitygate', 'view') ?
                                    html::a(inlink('view', "qualityGateId=$item->id"), $item->code) : $item->code;?>
                            </td>
                            <td title='<?php echo zget($productList, $item->productId); ?>' class='text-ellipsis'><?php echo zget($productList, $item->productId);?>
                            </td>
                            <td class='text-ellipsis'><?php echo $item->productCode; ?></td>
                            <td class='text-ellipsis'><?php echo zget($productVersionList, $item->productVersion); ?></td>
                            <td title='<?php echo $item->buildName; ?>' class='text-ellipsis'><?php echo empty($item->buildName) ? '' :
                                    html::a($this->createLink('build', 'view', "buildID=$item->buildId", '', true),
                                        $item->buildName, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'")
                                ;?>
                            </td>
                            <td title='<?php echo $item->buildStatus; ?>' class='text-ellipsis'><?php echo zget($buildstatusList, $item->buildStatus); ?></td>
                            <td class='text-ellipsis'>
                                <?php echo common::hasPriv('report', 'qualityGateCheckResult') ?
                                    html::a($this->createLink('report', 'qualityGateCheckResult', "projectId=$item->projectId&productId=$item->productId&productVersion=$item->productVersion&buildId=$item->buildId", '', true).'#app=project',
                                    $lang->qualitygate->check, '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'") : $lang->qualitygate->check;?>
                            </td>
                            <td class='text-ellipsis'><?php echo $this->qualitygate->diffColorStatus($item->status); ?></td>
                            <td title='<?php echo $item->dealUser; ?>' class='text-ellipsis'><?php echo $this->qualitygate->printAssignedHtml($item, $users);?>
                            </td>
                            <td class='c-actions text-ellipsis text-center'>
                                <?php

                                //common::printIcon('qualitygate', 'edit', "qualitygateID=$item->id", $item, 'list','edit', '', 'iframe', true);
                                common::printIcon('qualitygate', 'deal', "qualitygateId=$item->id", $item, 'list', 'time', '', 'iframe', true, '', $lang->qualitygate->todeal);
                                common::printIcon('qualitygate', 'delete', "qualitygateId=$item->id", $item, 'list', 'trash', '', 'iframe', true);
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
