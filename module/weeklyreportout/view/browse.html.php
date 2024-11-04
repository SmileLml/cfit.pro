<?php include '../../common/view/header.html.php'; ?>
<?php include '../../weeklyreport/lang/zh-cn.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        $active = $browseType == 'all' ? 'btn-active-text' : '';
        echo html::a($this->createLink(
            'weeklyreportout',
            'browse',
            "browseType=all&param=0&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"),
            '<span class="text">全部</span>',
            '',
            "class='btn btn-link {$active}'"
        );
        foreach ($lang->weeklyreport->outProjectStatusList as $label => $labelName) {
            $active = $browseType == (string)$label ? 'btn-active-text' : '';
            echo html::a($this->createLink(
                    'weeklyreportout',
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
            foreach ($lang->weeklyreport->outProjectStatusList as $label => $labelName) {
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
                $class = common::hasPriv('weeklyreportout', 'export') ? '' : 'class=disabled';
                $misc = common::hasPriv('weeklyreportout', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                $link = common::hasPriv('weeklyreportout', 'export') ? $this->createLink('weeklyreportout', 'export', "orderBy={$orderBy}&browseType={$browseType}") : '#';
                echo "<li {$class}>" . html::a($link, $lang->weeklyreport->export, '', $misc) . '</li>';
                ?>
            </ul>
        </div>
        <?php
        $this->loadModel('project');
        if(isset($lang->project->pushWeeklyreportQingZong[$app->user->account])){
            echo html::a(
                $this->createLink('weeklyreportout', 'pushWeeklyreportQingZong') . '?onlybody=yes',
                '<i class="icon-ok"></i> <span class="text">' . $lang->weeklyreportout->pushWeeklyreportQingZong . '</span>',
                '',
                "class='btn btn-primary' data-toggle='modal' data-type='iframe'",
                ''
            );
        }
        ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ('bysearch' == $browseType) {
            echo ' show';
        } ?>" id="queryBox" data-module='weeklyreportout'></div>
        <?php if (empty($weeklyreports)) { ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php } else { ?>
            <form class='main-table' id='weeklyreportoutForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType={$browseType}&param={$param}&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"; ?>
                <table class='table table-fixed has-sort-head' id='secondorders'>
                    <thead>
                    <tr>
                        <th class='w-50px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->weeklyreportout->id); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('outsideProjectName', $orderBy, $vars, $lang->weeklyreportout->outsideProjectName); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('outsideProjectCode', $orderBy, $vars, $lang->weeklyreportout->outsideProjectCode); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('outProjectStatus', $orderBy, $vars, $lang->weeklyreportout->outProjectStatus); ?></th>
                        <th class='w-100px'><?php echo $lang->weeklyreportout->outreportDate; ?></th>
                        <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->weeklyreportout->createdBy); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('outSyncStatus', $orderBy, $vars, $lang->weeklyreportout->outSyncStatus); ?></th>

                        <th class='text-center w-150px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($weeklyreports as $weeklyreport) { ?>
                        <tr>
                            <td title="<?php echo $weeklyreport->id; ?>"><?php echo $weeklyreport->id; ?></td>
                            <td title="<?php echo $weeklyreport->outsideProjectName; ?>" class='text-ellipsis'>
                                <?php echo html::a( helper::createLink(
                                        'outsideplan',
                                        'view',
                                        "planID={$weeklyreport->outProjectID}"
                                ),$weeklyreport->outsideProjectName); ?></td>
                            <td title="<?php echo $weeklyreport->outsideProjectCode; ?>" class='text-ellipsis'>
                                <?php echo $weeklyreport->outsideProjectCode; ?>
                            </td>
                            <td><?php echo zget($lang->weeklyreport->outProjectStatusList, $weeklyreport->outprojectStatus); ?></td>
                            <td>
                                <?php
                                echo html::a( helper::createLink(
                                    'weeklyreportout',
                                    'view',
                                    "outreportID={$weeklyreport->id}"
                                ,'html#app=platform'),$weeklyreport->outreportStartDate . '~' . $weeklyreport->outreportEndDate.' 第'.$weeklyreport->outweeknum.'周');
                                ?>
                            </td>
                            <td><?php echo zget($users, $weeklyreport->createdBy); ?></td>
                            <td><?php echo zget($lang->weeklyreportout->outSyncStatusList, $weeklyreport->outSyncStatus); ?></td>

                            <td class='c-actions text-center'>
                                <?php
                                common::printIcon(
                                        'weeklyreportout',
                                        'edit',
                                        "reportId={$weeklyreport->id}",
                                        $weeklyreport,
                                        'list',
                                        'edit',
                                        '',
                                        '',
                                        false,
                                        'project','编辑',0,'html#app=platform');
                                common::printIcon(
                                        'weeklyreportout',
                                        'view',
                                        "outreportID={$weeklyreport->id}",
                                        $weeklyreport,
                                        'list',
                                        'eye',
                                        '',
                                        '',
                                        false,
                                    'project','查看',0,'html#app=platform'
                                );
                                common::printIcon(
                                    'weeklyreportout',
                                    'regeneration',
                                    "",
                                    $weeklyreport,
                                    'list',
                                    'refresh',
                                    'hiddenwin','', '', "onclick=regeneration(this.href,{$weeklyreport->id})",
                                    '更新');

                                /*common::printIcon(
                                    'weeklyreportout',
                                    'pushOneWeeklyreportQingZong',
                                    "outreportId={$weeklyreport->outProjectID}",
                                    $weeklyreport,
                                    'list',
                                    'play',
                                    'hiddenwin','iframe', true, '',
                                    '推送');*/
                                if(common::hasPriv('weeklyreportout', 'pushOneWeeklyreportQingZong')){
                                    if($weeklyreport->iscbp == 1){

                                        echo "<a href='javascript:void(0)' class='btn' onclick='pushOneReport({$weeklyreport->id})' title='推送' data-app='platform'><i class='icon-weeklyreportout-pushOutWeeklyreportQingZong icon-play'></i></a>";
                                    }else{
                                        echo "<button type='button' class='disabled btn' style='pointer-events: unset;'><i class='icon-weeklyreportout-pushOneWeeklyreportQingZong disabled icon-play' title='推送' data-app='project'></i></button>";
                                    }
                                }

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
<style>body{background:white}</style>
<script>
    function regeneration(url, outreportID){
        if(confirm('您是否确定更新')){
            var params = {'outreportID': outreportID};
            $.post(url, params, function(data){
                data = $.parseJSON(data);
                location.reload();
            });
        }else {
            return false;
        }

    }

    function pushOneReport(outreportId){
        if(confirm('您是否确定推送')){
            var params = {'outreportId': outreportId};
            var url = createLink('weeklyreportout','pushOneWeeklyreportQingZong')
            $.post(url, params, function(data){

                alert(data.message);
                // data = $.parseJSON(data);
                if(data.code == 200){
                    location.reload();
                }

            },'json');
        }else {
            return false;
        }
    }
</script>
