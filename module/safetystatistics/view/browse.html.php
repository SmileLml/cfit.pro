<?php include '../../common/view/header.html.php'; ?>
<style>
    table{
        border-collapse: collapse !important;
    }
    table,th,td{
        border: 1px solid #b0bac1 !important;
        text-align: center;
    }
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text"><?php echo $lang->safetystatisstics->title; ?></span>
        </div>
    </div>
    <div class="btn-toolbar pull-right">
        <?php echo html::a($this->createLink('safetystatistics', 'createScore', '', '', true), "<i class='icon-plus'></i> {$lang->safetystatisstics->create}", '', "class='btn btn-primary iframe'"); ?>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='side-col' id='sidebar'><?php include 'blockreportlist.html.php'; ?></div>
    <div class='main-col'>
        <?php if (empty($list)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='secondorderForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
                <?php $vars = "orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
                <table class='table table-fixed has-sort-head table-bordered'>
                    <thead>
                    <tr>
                        <th class='w-120px'><?php echo  $lang->safetystatisstics->num; ?></th>
                        <th class='w-120px'><?php common::printOrderLink('appId', $orderBy, $vars, $lang->safetystatisstics->appId); ?></th>
                        <th class='w-120px'><?php echo $lang->safetystatisstics->targetTwo; ?></th>
                        <th class='w-120px'><?php echo $lang->safetystatisstics->targetThree; ?></th>
                        <th class='w-120px'><?php echo $lang->safetystatisstics->count; ?></th>
                        <th class='w-120px'><?php echo $lang->safetystatisstics->riskValue; ?></th>
                        <th class='w-120px'><?php echo $lang->safetystatisstics->scoreValue; ?></th>
                        <th class='w-120px'><?php common::printOrderLink('riskValue', $orderBy, $vars, $lang->safetystatisstics->risk); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('score', $orderBy, $vars, $lang->safetystatisstics->score); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $key => $item): ?>
                        <?php $flag = 0; ?>
                        <?php $details = json_decode($item->details, true);
                        $row = count($details) * 4;?>
                        <?php foreach ($details as $k => $value): ?>
                            <?php foreach ($value['child'] as $kk => $v): ?>
                                <tr>
                                    <?php if($flag % $row == 0): ?>
                                    <td rowspan="<?php echo $row; ?>"><?php echo ($pager->pageID - 1) * $pager->recPerPage + $key + 1; ?></td>
                                    <td rowspan="<?php echo $row; ?>" title="<?php echo zget($apps, $item->appId, ''); ?>"><?php echo zget($apps, $item->appId, ''); ?></td>
                                    <?php endif;?>
                                    <?php if($flag % 4 == 0): ?>
                                    <td rowspan="4"><?php echo $lang->safetystatistics->targetTwoList[$k] ?? '其他'; ?></td>
                                    <?php endif;?>
                                    <td><?php echo $lang->safetystatistics->targetThreeList[$kk]; ?></td>
                                    <td><?php echo $v; ?></td>
                                    <?php if($flag % 4 == 0): ?>
                                    <td rowspan="4"><?php echo $value['riskValue'] !== '' ? $value['riskValue'] : '数据待确认'; ?></td>
                                    <td rowspan="4"><?php echo $value['score'] !== '' ? $value['score'] : '数据待确认'; ?></td>
                                    <?php endif;?>
                                    <?php if($flag % $row == 0): ?>
                                    <td rowspan="<?php echo $row; ?>"><?php echo $item->riskValue;?></td>
                                    <td rowspan="<?php echo $row; ?>"><?php echo $item->score;?></td>
                                    <?php endif;?>
                                </tr>
                                <?php $flag++; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs');?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
