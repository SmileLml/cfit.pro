<?php include '../../../common/view/header.html.php'; ?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
    #queryBox .table td{overflow: unset}
</style>
<?php $this->app->loadLang('datamanagement'); ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach ($lang->demand->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            if($label == "|"){
                echo html::a($this->createLink('demand', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active' style='font-size:20px;padding-top:3px;color:gray;pointer-events:none' @click=xx()");
            }else{
                $lang->demand->labelList['|'];
                echo html::a($this->createLink('demand', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
            }

            $i++;
            if ($i >= 13) break;
        }
        if ($i >= 13) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->demand->labelList as $label => $labelName) {
                $i++;
                if ($i <= 13) continue;

                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink('demand', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                    class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <?php $this->app->loadLang('progress'); ?>
            <?php if (common::hasPriv('demand', 'importConclusion')) echo html::a($this->createLink('demand', 'importConclusion', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->progress->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('demand', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('demand', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('demand', 'export') ? $this->createLink('demand', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->demand->export, '', $misc) . "</li>";
                ?>
            </ul>

            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('demand', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('demand', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('demand', 'export') ? $this->createLink('demand', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->demand->export, '', $misc) . "</li>";

                $class = common::hasPriv('demand', 'exportTemplate') ? '' : "class='disabled'";
                $link  = common::hasPriv('demand', 'exportTemplate') ? $this->createLink('demand', 'exportTemplate') : '#';
                $misc  = common::hasPriv('demand', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->demand->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if (common::hasPriv('demand', 'import')) echo html::a($this->createLink('demand', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->demand->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
        </div>
        <?php if (common::hasPriv('demand', 'create')) echo html::a($this->createLink('demand', 'create'), "<i class='icon-plus'></i> {$lang->demand->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox" data-module='demand'></div>
        <?php if (empty($demands)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='demandForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='demands'>
                    <thead>
                    <tr>
                        <th class='w-110px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->demand->code); ?></th>
                        <th class='w-140px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->demand->title); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->demand->project); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->demand->app); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('acceptDept', $orderBy, $vars, $lang->demand->acceptDept); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('acceptUser', $orderBy, $vars, $lang->demand->acceptUser); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->demand->end); ?></th>
                        <th class='w-80px'> <?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->demand->createdBy); ?></th>
                        <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->demand->status); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->demand->dealUser); ?></th>
                        <th class='text-center w-250px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($demands as $demand): ?>
                        <tr>
                            <td title='<?php echo $demand->code; ?>'><?php echo common::hasPriv('demand', 'view') ? html::a(inlink('view', "demandID=$demand->id"), $demand->code) : $demand->code; ?></td>
                            <td title='<?php echo $demand->title; ?>'><?php echo $demand->title; ?></td>
                            <?php
                            $projectArr = [];
                            foreach (explode(',', $demand->project) as $project) {
                                if (!$project) continue;
                                $projectArr[] = zget($projectPlanList, $project);
                            }
                            $project = implode(', ', $projectArr);
                            ?>
                            <td title="<?php echo $project; ?>" class='text-ellipsis'>
                                <?php echo $project; ?>
                            </td>
                            <?php
                            $as = [];
                            foreach (explode(',', $demand->app) as $app) {
                                if (!$app) continue;
                                $as[] = zget($apps, $app);
                            }
                            $app = implode(', ', $as);
                            ?>
                            <td title="<?php echo $app; ?>" class='text-ellipsis'>
                                <?php echo $app; ?>
                            </td>
                            <td><?php echo zget($depts, $demand->acceptDept, $demand->acceptDept); ?></td>
                            <td><?php echo zget($users, $demand->acceptUser, $demand->acceptUser); ?></td>
                            <?php $demand->end = helper::isZeroDate($demand->end) ? '' : $demand->end; ?>
                            <td><?php echo $demand->end; ?></td>
                            <td><?php echo zget($users, $demand->createdBy, ''); ?></td>
                            <td>
                                <?php echo zget($lang->demand->statusList, $demand->status); ?>
                            </td>
                            <td title="<?php echo zmget($users, $demand->dealUser); ?>"><?php echo zmget($users, $demand->dealUser); ?></td>
                            <td class='c-actions text-center' style="overflow:visible">
                                <?php if(common::hasPriv('demand', 'reviewdelay')): ?>
                                <?php if(in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and !in_array($demand->status,['deleteout']) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                                    <div class="btn-group">
                                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title="<?php echo $lang->demand->review; ?>"><i class="icon icon-glasses"></i></button>
                                        <ul class="dropdown-menu">
                                            <?php if(in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                                                <li><?php echo html::a($this->createLink('demand', 'reviewdelay', 'demandID=' . $demand->id , '', true), $lang->demand->reviewdelay , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                            <?php endif;?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                        <button type="button" class="btn disabled" title="<?php echo $lang->demand->review ?>"><i class="icon icon-glasses disabled"></i></button>
                                <?php endif; ?>
                                <?php endif;?>
                                <?php
                                $account = $this->app->user->account;
                                common::printIcon('demand', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                                common::printIcon('demand', 'deal', "demandID=$demand->id", $demand, 'list', 'time', '', 'iframe', true);
                                common::printIcon('demand', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                                common::printIcon('demand', 'assignment', "demandID=$demand->id", $demand, 'list', 'hand-right', '', 'iframe', true);
                                common::printIcon('demand', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'list', 'trash', '', 'iframe', true);

                                //迭代三十三 admin、二线专员、产品经理、创建人、研发责任人、后台配置的关闭人
                                if ($account == 'admin' or $account == $demand->createdBy or $account == $demand->acceptUser or in_array($account, $executives)) {
                                    common::printIcon('demand', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                                }else{
                                    echo '<button type="button" class="disabled btn" title="' . $lang->demand->close . '"><i class="icon-demand-close disabled icon-off"></i></button>'."\n";
                                }
                                //忽略/恢复
                                if ($demand->ignoreStatus == 0){
                                    common::printIcon('demand', 'ignore', "demandID=$demand->id", $demand, 'list', 'ban', '', 'iframe', true);
                                }else{
                                    common::printIcon('demand', 'recoveryed', "demandID=$demand->id", $demand, 'list', 'bell', '', 'iframe', true);
                                }
                                //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                                if ($account == 'admin' or $account == $demand->createdBy or in_array($account, $executives)) {
                                    if ($demand->status == 'suspend') {
                                        common::printIcon('demand', 'start', "demandID=$demand->id", $demand, 'list', 'magic', '', 'iframe', true);
                                    } else {
                                        common::printIcon('demand', 'suspend', "demandID=$demand->id", $demand, 'list', 'pause', '', 'iframe', true);
                                    }
                                }
                                else if($demand->status == 'suspend'){
                                    echo '<button type="button" class="disabled btn" title="' . $lang->demand->start . '"><i class="icon-common-start disabled icon-magic"></i></button>'."\n";
                                }else{
                                    echo '<button type="button" class="disabled btn" title="' . $lang->demand->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>'."\n";
                                }
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
<?php include '../../../common/view/footer.html.php'; ?>
