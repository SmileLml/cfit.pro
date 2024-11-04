<?php include '../../common/view/header.html.php'; ?>
<style>
    .table-children {
        border-left: 2px solid #cbd0db;
        border-right: 2px solid #cbd0db;
    }

    .table-nest-icon {
        margin-right: 3px;
    }

    .table-nest-toggle:before {
        line-height: 22px;
        content: "\e6f2";
    }

    .table-nest-child-hide.table-nest-toggle:before {
        line-height: 22px;
        content: "\e6f1";
    }

    .main-table tbody > tr > td.child {
        padding-left: 40px;
    }
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach ($lang->requirementinside->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            if($label == "|" or $label == 'vertical'){
                echo html::a($this->createLink('requirementinside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active' style='font-size:20px;padding-top:3px;color:gray;pointer-events:none' @click=xx()");
            }else{
                $lang->requirementinside->labelList['|'];
                echo html::a($this->createLink('requirementinside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
            }

            $i++;
            if ($i >= 12) break;
        }
        if ($i >= 12) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->requirementinside->labelList as $label => $labelName) {
                $i++;
                if ($i <= 12) continue;

                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink('requirementinside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                    class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('requirementinside', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('requirementinside', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('requirementinside', 'export') ? $this->createLink('requirementinside', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->requirementinside->export, '', $misc) . "</li>";

                $class = common::hasPriv('requirementinside', 'exportTemplate') ? '' : "class='disabled'";
                $link  = common::hasPriv('requirementinside', 'exportTemplate') ? $this->createLink('requirementinside', 'exportTemplate') : '#';
                $misc  = common::hasPriv('requirementinside', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->requirementinside->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if(common::hasPriv('requirementinside', 'import')) echo html::a($this->createLink('requirementinside', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->requirementinside->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
        </div>
        <?php if (common::hasPriv('requirementinside', 'create')) echo html::a($this->createLink('requirementinside', 'create'), "<i class='icon-plus'></i> {$lang->requirementinside->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='requirementinside'></div>
        <?php if (empty($requirements)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
            <form class='main-table' id='requirementForm' method='post' >
                <table class='table table-fixed has-sort-head' id='requirements'>
                    <thead>
                    <tr>
                        <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->requirementinside->code); ?></th>
                        <th class='w-150px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->requirementinside->name); ?></th>
                        <th class='w-240px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->requirementinside->project); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('dept', $orderBy, $vars, $lang->requirementinside->dept); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('owner', $orderBy, $vars, $lang->requirementinside->owner); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('deadLine', $orderBy, $vars, $lang->requirementinside->deadLine); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->requirementinside->planEnd); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->requirementinside->createdBy); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->requirementinside->createdDate); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->requirementinside->status); ?></th>
                        <th class='w-90px'><?php echo $lang->requirementinside->pending; ?></th>
                        <th class='text-center w-250px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($requirements as $requirement): ?>
                        <tr>
                            <td><?php echo $requirement->code; ?></td>
                            <td <?php if (!empty($requirement->children)):echo "class='has-child text-ellipsis'";  else: echo "class='text-ellipsis'"; endif;?>
                                    title="<?php echo $requirement->name; ?>">
                                <?php
                                echo '<span class="table-nest-child-hide table-nest-icon icon table-nest-toggle collapsed" data-id="' . $requirement->id . '"></span>';
                                echo common::hasPriv('requirementinside', 'view') ? html::a(inlink('view', "requirementID=$requirement->id"), htmlspecialchars_decode($requirement->name)) : htmlspecialchars_decode($requirement->name);
                                ?>
                            </td>
                            <?php
                            $projectTitle = '';
                            if (!empty($requirement->project)) {
                                foreach (explode(',', $requirement->project) as $project) {
                                    if (!empty($project)) $projectTitle .= zget($projectPlanList, $project, $project) . ',';
                                }
                            }
                            $projectTitle = trim($projectTitle, ',');
                            ?>
                            <td title="<?php echo $projectTitle; ?>" class="text-ellipsis"><?php echo $projectTitle; ?></td>
                            <?php
                            $deptTitle = '';
                            if (!empty($requirement->dept)) {
                                foreach (explode(',', $requirement->dept) as $dept) {
                                    if (!empty($dept)) $deptTitle .= zget($depts, $dept, $dept) . ',';
                                }
                            }
                            $deptTitle = trim($deptTitle, ',');
                            ?>
                            <td title="<?php echo $deptTitle; ?>" class="text-ellipsis"><?php echo $deptTitle; ?></td>
                            <?php
                            $ownerTitle = '';
                            if (!empty($requirement->owner)) {
                                foreach (explode(',', $requirement->owner) as $owner) {
                                    if (!empty($owner)) $ownerTitle .= zget($users, $owner, $owner) . ',';
                                }
                            }
                            $ownerTitle = trim($ownerTitle, ',');
                            ?>
                            <td title="<?php echo $ownerTitle; ?>" class="text-ellipsis"><?php echo $ownerTitle; ?></td>
                            <td><?php echo $requirement->deadLine; ?></td>
                            <td><?php if (!helper::isZeroDate($requirement->planEnd)) echo $requirement->planEnd; ?></td>
                            <td title="<?php echo zget($users, $requirement->createdBy, $requirement->createdBy); ?>" class="text-ellipsis"><?php echo zget($users, $requirement->createdBy, $requirement->createdBy); ?></td>
                            <td title="<?php echo $requirement->createdDate; ?>" class="text-ellipsis"><?php echo $requirement->createdDate; ?></td>
                            <td><?php echo zget($lang->requirementinside->statusList, $requirement->status); ?></td>

                            <?php
                            $reviewersTitle = '';
                            if (!empty($requirement->reviewer)) {
                                foreach (explode(',', $requirement->reviewer) as $reviewers) {
                                    if (!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                                }
                            }
                            $reviewersTitle = trim($reviewersTitle, ',');
                            ?>
                            <td title='<?php echo $reviewersTitle; ?>' class='text-ellipsis'>
                                <?php echo $reviewersTitle; ?>
                            </td>
                            <td class='c-actions'>
                                <?php
                                common::printIcon('requirementinside', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                                common::printIcon('requirementinside', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                                common::printIcon('requirementinside', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');
                                if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy) {
                                    if ($requirement->status == 'closed') {
                                        common::printIcon('requirementinside', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                                    } else {
                                        common::printIcon('requirementinside', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                                    }
                                }else if($requirement->status == 'closed'){
                                    echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                                }else{
                                    echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                                }

                                if ($requirement->ignoreStatus) {
                                    common::printIcon('requirementinside', 'recover', "requirementID=$requirement->id", $requirement, 'list', 'bell', '', 'iframe', true);
                                } else {
                                    common::printIcon('requirementinside', 'ignore', "requirementID=$requirement->id", $requirement, 'list', 'ban', '', 'iframe', true);
                                }
                                common::printIcon('requirementinside', 'delete', "requirementID=$requirement->id", $requirement, 'list', 'trash', '', 'iframe', true);

                                ?>
                            </td>
                        </tr>
                        <?php if (!empty($requirement->children)): ?>
                            <?php $i = 0; ?>
                            <?php foreach ($requirement->children as $key => $demand): ?>
                                <?php $class = $i == 0 ? ' table-child-top' : ''; ?>
                                <?php $class .= ($i + 1 == count($requirement->children)) ? ' table-child-bottom' : ''; ?>
                                <tr class='table-children<?php echo $class; ?> parent-<?php echo $requirement->id; ?>'
                                    data-id='<?php echo $demand->id ?>' data-status='<?php echo $demand->status ?>'
                                    style="display: none;">
                                    <td title="<?php echo $demand->code; ?>" class="text-ellipsis"><?php echo $demand->code; ?></td>
                                    <td class="child text-ellipsis" title="<?php echo $demand->title;?>">
                                        <?php
                                        echo common::hasPriv('demandinside', 'view') ? html::a(helper::createLink('demandinside', 'view', "demandID=$demand->id"), $demand->title) : $demand->title;
                                        ?>
                                    </td>
                                    <?php
//                                    $projectPlanArr = [];
//                                    foreach (explode(',', $demand->projectPlan) as $projectPlan) {
//                                        if (!$projectPlan) continue;
//                                        $projectPlanArr[] = zget($projectPlanList, $projectPlan);
//                                    }
//                                    $projectPlan = implode(', ', $projectPlanArr);
                                    $projectPlan = zmget($projects,$demand->project)
                                    ?>
                                    <td title="<?php echo $projectPlan; ?>" class='text-ellipsis'>
                                        <?php echo $projectPlan; ?>
                                    </td>
                                    <td title="<?php echo zget($depts, $demand->acceptDept); ?>" class="text-ellipsis"><?php echo zget($depts, $demand->acceptDept); ?></td>
                                    <td><?php echo zget($users, $demand->acceptUser, ''); ?></td>
                                    <td><?php echo $demand->endDate; ?></td>
                                    <td><?php echo $demand->end; ?></td>
                                    <td><?php echo zget($users, $demand->createdBy, ''); ?></td>
                                    <td><?php echo $demand->createdDate; ?></td>
                                    <td><?php echo zget($lang->demand->statusList, $demand->status, ''); ?></td>
                                    <td><?php echo zget($users, $demand->dealUser, ''); ?></td>
                                    <td class='c-actions'>
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
                                            if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or in_array($this->app->user->account, $executives)) {
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
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
<script>
    $(function () {
        $('#opinionList td.has-child .opinion-toggle').each(function () {
            var $td = $(this).closest('td');
            var labelWidth = 0;
            if ($td.find('.label').length > 0) labelWidth = $td.find('.label').width();
            $td.find('a').eq(0).css('max-width', $td.width() - labelWidth - 60);
        });
    })

    $(document).on('click', '.table-nest-icon', function (e) {
        var $toggle = $(this);
        var id = $(this).data('id');
        var isCollapsed = $toggle.toggleClass('collapsed').hasClass('collapsed');
        $toggle.closest('form').find('tr.parent-' + id).toggle(!isCollapsed);
        if (!isCollapsed) {
            $(this).removeClass('table-nest-child-hide');
        } else {
            $(this).addClass('table-nest-child-hide');
        }

        e.stopPropagation();
        e.preventDefault();
    });

</script>
<?php include '../../common/view/footer.html.php'; ?>
