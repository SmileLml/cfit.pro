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
        foreach ($lang->requirement->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            if($label == "|" or $label == 'vertical'){
                echo html::a($this->createLink('requirement', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active' style='font-size:20px;padding-top:3px;color:gray;pointer-events:none' @click=xx()");
            }else{
                $lang->requirement->labelList['|'];
                echo html::a($this->createLink('requirement', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
            }

            $i++;
            if ($i >= 12) break;
        }
        if ($i >= 12) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->requirement->moreStatus}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->requirement->labelList as $label => $labelName) {
                $i++;
                if ($i <= 12) continue;

                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink('requirement', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
                $class = common::hasPriv('requirement', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('requirement', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('requirement', 'export') ? $this->createLink('requirement', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->requirement->export, '', $misc) . "</li>";

                $class = common::hasPriv('requirement', 'exportTemplate') ? '' : "class='disabled'";
                $link  = common::hasPriv('requirement', 'exportTemplate') ? $this->createLink('requirement', 'exportTemplate') : '#';
                $misc  = common::hasPriv('requirement', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->requirement->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if($createButton):?>
                <?php if(common::hasPriv('requirement', 'import')) echo html::a($this->createLink('requirement', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->requirement->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
            <?php endif;?>
        </div>
        <?php if($createButton):?>
            <?php if (common::hasPriv('requirement', 'create')) echo html::a($this->createLink('requirement', 'create'), "<i class='icon-plus'></i> {$lang->requirement->create}", '', "class='btn btn-primary'"); ?>
        <?php endif;?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='requirement'></div>
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
                        <th class='w-110px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->requirement->code); ?></th>
                        <th class='w-150px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->requirement->name); ?></th>
                        <th class='w-240px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->requirement->project); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('dept', $orderBy, $vars, $lang->requirement->dept); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('owner', $orderBy, $vars, $lang->requirement->owner); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('deadLine', $orderBy, $vars, $lang->requirement->deadLine); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->requirement->planEnd); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->requirement->createdBy); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->requirement->createdDate); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('entriesCode', $orderBy, $vars, $lang->requirement->extNum); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('feedbackStatus', $orderBy, $vars, $lang->requirement->feedbackStatus); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->requirement->status); ?></th>
                        <th class='w-90px'><?php echo $lang->requirement->pending; ?></th>
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
                                echo common::hasPriv('requirement', 'view') ? html::a(inlink('view', "requirementID=$requirement->id"), htmlspecialchars_decode($requirement->name)) : htmlspecialchars_decode($requirement->name);
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
                            <td><?php echo !helper::isZeroDate($requirement->planEnd)? $requirement->planEnd : ''; ?></td>
                            <td title="<?php echo zget($users, $requirement->createdBy, $requirement->createdBy); ?>" class="text-ellipsis"><?php echo zget($users, $requirement->createdBy, $requirement->createdBy); ?></td>
                            <td title="<?php echo $requirement->createdDate; ?>" class="text-ellipsis"><?php echo $requirement->createdDate; ?></td>
                            <td title="<?php echo $requirement->entriesCode; ?>" class="text-ellipsis"><?php echo $requirement->entriesCode; ?></td>
                            <td><?php echo zget($lang->requirement->feedbackStatusList, $requirement->feedbackStatus); ?></td>
                            <td><?php echo zget($lang->requirement->statusList, $requirement->status); ?></td>

                            <?php
                            $reviewersTitle = '';

                            if ((!empty($requirement->reviewer)) && ($requirement->status != 'delivered')) {
                                foreach (explode(',', $requirement->reviewer) as $reviewers) {
                                    if (!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                                }
                            }
                            //迭代二十八 待处理人拼接变更单待处理人共同显示
                            if(!empty($requirement->changeDealUser)){
                                $changeDealUser = $requirement->changeDealUser;
                                foreach (explode(',', $changeDealUser) as $value) {
                                    if (!empty($value)) $reviewersTitle .= zget($users, $value, $value) . ',';
                                }
                            }
                            $reviewersTitleArray = array_filter(array_unique(explode(',',$reviewersTitle)));
                            $reviewersTitle = implode(',',$reviewersTitleArray);

                            ?>
                            <td title='<?php echo $reviewersTitle; ?>' class='text-ellipsis'>
                                <?php echo $reviewersTitle; ?>
                            </td>

                            <td class='c-actions text-center' style="overflow:visible">

                                <?php
                                common::printIcon('requirement', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                                common::printIcon('requirement', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                                common::printIcon('requirement', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');
                                //研发责任人取所有需求条目合集 迭代三十二 将变更流程发起人范围扩大至全部人员  变更中、已退回[2,3]
                                if(!in_array($requirement->requirementChangeStatus,[2,3]))
                                {
                                    common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'list','alter', '', 'iframe',true);
                                }else{
                                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                                }
                                common::printIcon('requirement', 'feedback', "requirementID=$requirement->id", $requirement, 'list');
                                ?>
                                <?php if($this->app->user->account == 'admin'
                                    or
                                    (
                                        ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false
                                    )
                                    and
                                    (
                                        (strstr($requirement->changeNextDealuser, $app->user->account) !== false)
                                    )
                                    ):
                                ?>
                                    <div class="btn-group">
                                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                                        <ul class="dropdown-menu">
                                            <li><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                            <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                        </ul>
                                    </div>
                                <?php elseif($requirement->status != 'deleteout' and ($this->app->user->account == 'admin' or  ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false)):?>
                                    <div class="btn-group">
                                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                                        <ul class="dropdown-menu">
                                            <li style=""><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                            <li style="margin-top:-14px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->reviewchange; ?></span></li>
                                        </ul>
                                    </div>
                                <?php elseif($requirement->status != 'deleteout' and ($this->app->user->account == 'admin' or (strstr($requirement->changeNextDealuser, $app->user->account) !== false))):?>
                                    <div class="btn-group">
                                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                                        <ul class="dropdown-menu">
                                            <li style="margin-top:-14px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->review; ?></span></li>
                                            <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                        </ul>
                                    </div>
                                <?php else:?>
                                    <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->requirement->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                                <?php endif;?>

                                <?php
                                if($this->app->user->account == 'admin' or (in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy)) {
                                    if ($requirement->status == 'closed') {
                                        common::printIcon('requirement', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                                    } else {
                                        common::printIcon('requirement', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                                    }
                                }else if($requirement->status == 'closed'){
                                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>'."\n";
                                }else{
                                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>'."\n";
                                }

                                if ($requirement->ignoreStatus) {
                                    common::printIcon('requirement', 'recover', "requirementID=$requirement->id", $requirement, 'list', 'bell', '', 'iframe', true);
                                } else {
                                    common::printIcon('requirement', 'ignore', "requirementID=$requirement->id", $requirement, 'list', 'ban', '', 'iframe', true);
                                }
                                common::printIcon('requirement', 'delete', "requirementID=$requirement->id", $requirement, 'list', 'trash', '', 'iframe', true);

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
                                        echo common::hasPriv('demand', 'view') ? html::a(helper::createLink('demand', 'view', "demandID=$demand->id"), $demand->title) : $demand->title;
                                        ?>
                                    </td>
                                    <?php
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
                                    <td></td>
                                    <td></td>
                                    <td><?php echo zget($lang->demand->statusList, $demand->status, ''); ?></td>
                                    <td title="<?php echo zmget($users, $demand->dealUser, ''); ?>"><?php echo zmget($users, $demand->dealUser, ''); ?></td>
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
