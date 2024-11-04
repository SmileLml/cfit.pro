<?php include '../../../common/view/header.html.php'; ?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
    #queryBox .table td{overflow: unset}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach ($lang->demandinside->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            if($label == "|"){
                echo html::a($this->createLink('demandinside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active' style='font-size:20px;padding-top:3px;color:gray;pointer-events:none' @click=xx()");
            }else{
                //$lang->demandinside->labelList['|'];
                echo html::a($this->createLink('demandinside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
            }

            $i++;
            if ($i >= 12) break;
        }
        if ($i >= 12) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->demandinside->labelList as $label => $labelName) {
                $i++;
                if ($i <= 12) continue;

                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink('demandinside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
            <?php if (common::hasPriv('demandinside', 'importConclusion')) echo html::a($this->createLink('demandinside', 'importConclusion', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->progress->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('demandinside', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('demandinside', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('demandinside', 'export') ? $this->createLink('demandinside', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->demand->export, '', $misc) . "</li>";
                ?>
            </ul>

            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('demandinside', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('demandinside', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('demandinside', 'export') ? $this->createLink('demandinside', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->demand->export, '', $misc) . "</li>";

                $class = common::hasPriv('demandinside', 'exportTemplate') ? '' : "class='disabled'";
                $link  = common::hasPriv('demandinside', 'exportTemplate') ? $this->createLink('demandinside', 'exportTemplate') : '#';
                $misc  = common::hasPriv('demandinside', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->demandinside->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if (common::hasPriv('demandinside', 'import')) echo html::a($this->createLink('demandinside', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->demandinside->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
        </div>
        <?php if (common::hasPriv('demandinside', 'create')) echo html::a($this->createLink('demandinside', 'create'), "<i class='icon-plus'></i> {$lang->demandinside->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox" data-module='demandinside'></div>
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
                        <th class='w-110px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->demandinside->code); ?></th>
                        <th class='w-140px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->demandinside->title); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->demandinside->project); ?></th>
<!--                        <th class='w-60px'>--><?php //common::printOrderLink('fixType', $orderBy, $vars, $lang->demand->fixType); ?><!--</th>-->
                        <th class='w-100px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->demandinside->app); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('acceptDept', $orderBy, $vars, $lang->demandinside->acceptDept); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('acceptUser', $orderBy, $vars, $lang->demandinside->acceptUser); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('endDate', $orderBy, $vars, $lang->demandinside->endDate); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->demandinside->end); ?></th>
                        <th class='w-80px'> <?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->demandinside->createdBy); ?></th>
                        <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->demandinside->status); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->demandinside->dealUser); ?></th>
                        <th class='text-center w-250px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($demands as $demand): ?>
                        <tr>
                            <td title='<?php echo $demand->code; ?>'><?php echo common::hasPriv('demandinside', 'view') ? html::a(inlink('view', "demandID=$demand->id"), $demand->code) : $demand->code; ?></td>
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
                            <td><?php echo $demand->endDate; ?></td>
                            <?php $demand->end = helper::isZeroDate($demand->end) ? '' : $demand->end; ?>
                            <td><?php echo $demand->end; ?></td>
                            <td><?php echo zget($users, $demand->createdBy, ''); ?></td>
                            <td>
                                <?php echo zget($lang->demandinside->statusList, $demand->status); ?>
                            </td>
                            <td title="<?php echo zmget($users, $demand->dealUser); ?>"><?php echo zmget($users, $demand->dealUser); ?></td>
                            <td class='c-actions text-center'>
                                <?php
                                common::printIcon('demandinside', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                                common::printIcon('demandinside', 'deal', "demandID=$demand->id", $demand, 'list', 'time', '', 'iframe', true);
                                common::printIcon('demandinside', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'list');
                                common::printIcon('demandinside', 'assignment', "demandID=$demand->id", $demand, 'list', 'hand-right', '', 'iframe', true);
                                common::printIcon('demandinside', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'list', 'trash', '', 'iframe', true);
                                common::printIcon('demandinside', 'close', "demandID=$demand->id", $demand, 'list', 'off', '', 'iframe', true);
                                //忽略/恢复
                                if ($demand->ignoreStatus == 0){
                                    common::printIcon('demandinside', 'ignore', "demandID=$demand->id", $demand, 'list', 'ban', '', 'iframe', true);
                                }else{
                                    common::printIcon('demandinside', 'recoveryed', "demandID=$demand->id", $demand, 'list', 'bell', '', 'iframe', true);
                                }
                                //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                                if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or $this->app->user->account == $demand->dealUser or in_array($this->app->user->account, $executives)) {
                                    if ($demand->status == 'suspend') {
                                        common::printIcon('demandinside', 'start', "demandID=$demand->id", $demand, 'list', 'magic', '', 'iframe', true);
                                    } else {
                                        common::printIcon('demandinside', 'suspend', "demandID=$demand->id", $demand, 'list', 'pause', '', 'iframe', true);
                                    }
                                }
                                else if($demand->status == 'suspend'){
                                    echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->start . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                                }else{
                                    echo '<button type="button" class="disabled btn" title="' . $lang->demandinside->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
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
