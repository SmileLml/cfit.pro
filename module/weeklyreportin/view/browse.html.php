<?php include '../../common/view/header.html.php'; ?>
<?php include '../../weeklyreport/lang/zh-cn.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        $active = $browseType == 'all' ? 'btn-active-text' : '';
        echo html::a($this->createLink(
            'weeklyreportin',
            'browse',
            "browseType=all&param=0&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"),
            '<span class="text">全部</span>',
            '',
            "class='btn btn-link {$active}'"
        );
        foreach ($lang->weeklyreport->projectState as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a($this->createLink(
                    'weeklyreportin',
                    'browse',
                    "browseType={$label}&param=0&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"),
                '<span class="text">' . $labelName . '</span>',
                '',
                "class='btn btn-link {$active}'"
            );

            ++$i;
            if ($i >= 11) {
                break;
            }
        }
        if ($i >= 12) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->weeklyreport->projectState as $label => $labelName) {
                ++$i;
                if ($i <= 11) {
                    continue;
                }
                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink(
                        'secondorder',
                        'browse',
                        "browseType={$label}&param=0&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"),
                        "<span class='text'>{$labelName}</span>",
                        '',
                        "class='btn btn-link {$active}'"
                    ) . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
<!--        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> --><?php //echo $lang->searchAB; ?><!--</a>-->
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export; ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('weeklyreportin', 'export') ? '' : 'class=disabled';
                $misc = common::hasPriv('weeklyreportin', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                $link = common::hasPriv('weeklyreportin', 'export') ? $this->createLink('weeklyreportin', 'export', "orderBy={$orderBy}&browseType={$browseType}") : '#';
                echo "<li {$class}>" . html::a($link, $lang->weeklyreport->export, '', $misc) . '</li>';
                ?>
            </ul>
        </div>
        <?php
        $qa = $this->loadModel('weeklyreport')->getUserQADept($app->user->account);
        if($qa['isogQA'] == 1){
            echo html::a($this->createLink('weeklyreportin', 'confirm', ''). '?onlybody=yes', '<i class="icon-ok"></i> <span class="text">' . $lang->weeklyreportin->confirm . '</span>', '', "class='btn btn-primary' data-toggle='modal' data-type='iframe'");
        }
        ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ('bysearch' == $browseType) {
            echo ' show';
        } ?>" id="queryBox" data-module='weeklyreportin'></div>
        <?php if (empty($weeklyreports)) { ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php } else { ?>
            <form class='main-table' id='weeklyreportinForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType={$browseType}&param={$param}&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"; ?>
                <table class='table table-fixed has-sort-head' id='secondorders'>
                    <thead>
                    <tr>
                        <th class='w-50px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->weeklyreportin->id); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('projectName', $orderBy, $vars, $lang->weeklyreportin->projectName); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('devDept', $orderBy, $vars, $lang->weeklyreportin->devDept); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('projectCode', $orderBy, $vars, $lang->weeklyreportin->projectCode); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('projectAlias', $orderBy, $vars, $lang->weeklyreportin->projectAlias); ?></th>
                        <th class='w-50px'><?php common::printOrderLink('year', $orderBy, $vars, $lang->weeklyreportin->projectplanYear); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('projectStartDate', $orderBy, $vars, $lang->weeklyreportin->projectStartDate); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('projectEndDate', $orderBy, $vars, $lang->weeklyreportin->projectEndDate); ?></th>
                        <th class='w-150px'><?php echo $lang->weeklyreportin->createTime; ?></th>
                        <th class='w-60px'><?php common::printOrderLink('projectStage', $orderBy, $vars, $lang->weeklyreportin->projectStage); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('progressStatus', $orderBy, $vars, $lang->weeklyreportin->progressStatus); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->weeklyreportin->createdBy); ?></th>
                        <th class='text-center w-150px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($weeklyreports as $weeklyreport) { ?>
                        <tr>
                            <td title="<?php echo $weeklyreport->id; ?>"><?php echo $weeklyreport->id; ?></td>
                            <td title="<?php echo $weeklyreport->projectName; ?>" class='text-ellipsis'>
                                <?php echo html::a( helper::createLink(
                                        'projectplan',
                                        'view',
                                        "planID={$weeklyreport->planID}", 'html'
                                ),$weeklyreport->projectName); ?></td>
                            <?php
                            $devDepts = explode(',', $weeklyreport->devDept);
                            $devDeptList = [];
                            foreach ($devDepts as $devDept){
                                $devDeptList[] = zget($depts, $devDept);
                            }
                            $devDepts = implode(',', $devDeptList);
                            ?>
                            <td title="<?php echo $devDepts; ?>" class='text-ellipsis'><?php echo $devDepts; ?></td>
                            <td><?php echo $weeklyreport->projectCode; ?></td>
                            <td title="<?php echo $weeklyreport->projectAlias; ?>" class='text-ellipsis'><?php echo $weeklyreport->projectAlias; ?></td>
                            <td><?php echo $weeklyreport->projectplanYear; ?></td>
                            <td><?php echo '0000-00-00' != $weeklyreport->projectStartDate ? $weeklyreport->projectStartDate : ''; ?></td>
                            <td><?php echo '0000-00-00' != $weeklyreport->projectEndDate ? $weeklyreport->projectEndDate : ''; ?></td>
                            <td><a href="<?php echo helper::createLink('weeklyreport','index','projectID='.$weeklyreport->projectId.'&reportId='.$weeklyreport->id,'html#app=project') ?>"><?php echo $weeklyreport->reportStartDate .'~'.$weeklyreport->reportEndDate.' 第'.$weeklyreport->weeknum.'周'; ?></a></td>
                            <td><?php echo $lang->weeklyreport->projectState[$weeklyreport->projectStage]; ?></td>
                            <td><?php echo $weeklyreport->progressStatus; ?></td>
                            <td><?php echo zget($users, $weeklyreport->createdBy); ?></td>
                            <td class='c-actions text-center'>
                                <?php
                                common::printIcon(
                                        'weeklyreport',
                                        'edit',
                                        "reportId={$weeklyreport->id}&refer=1",
                                        $weeklyreport,
                                        'list',
                                    'edit',
                                    '_self',
                                    '',
                                    false,
                                    "data-app='project'",'编辑',0,'html');
                                common::printIcon(
                                        'weeklyreport',
                                        'index',
                                        "projectID={$weeklyreport->projectId}&reportId={$weeklyreport->id}&refer=1",
                                        $weeklyreport,
                                        'list',
                                        'eye',
                                        '_self',
                                        '',
                                        false,
                                    "data-app='project'",'查看',0,'html'
                                );
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs'); ?>
                </div>
            </form>
        <?php } ?>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
