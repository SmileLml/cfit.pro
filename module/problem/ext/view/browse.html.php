<?php include '../../../common/view/header.html.php'; ?>
<?php
echo js::set('authStatusError', $this->lang->problem->authStatusError);
?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;

        foreach ($lang->problem->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            if($labelName == '|'){
                echo   html::a($this->createLink('problem', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pageID}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active' style='font-size:20px;padding-top:3px;color:gray;pointer-events:none' @click=xx()");
            }else{
                echo   html::a($this->createLink('problem', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pageID}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

            }
            if($labelName != '|') $i++;
            if ($i >= 13) break;
        }
        if ($i >= 13) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->problem->ReviewStatus}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->problem->feedbackStatusSearchList as $label => $labelName) {
                $i++;
               // if ($i <= 11) continue;

                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink('problem', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pageID}&flag=true"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
            <?php if (common::hasPriv('problem', 'importByQA')) echo html::a($this->createLink('problem', 'importByQA', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->progress->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('problem', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('problem', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('problem', 'export') ? $this->createLink('problem', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->problem->export, '', $misc) . "</li>";

                $class = common::hasPriv('problem', 'exportTemplate') ? '' : "class='disabled'";
                $link = common::hasPriv('problem', 'exportTemplate') ? $this->createLink('problem', 'exportTemplate') : '#';
                $misc = common::hasPriv('problem', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->problem->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if (common::hasPriv('problem', 'import')) echo html::a($this->createLink('problem', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->problem->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
        </div>
        <?php if (common::hasPriv('problem', 'create')) echo html::a($this->createLink('problem', 'create'), "<i class='icon-plus'></i> {$lang->problem->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='problem'></div>
        <?php if (empty($problems)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='problemForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='problems'>
                    <thead>
                    <tr>
                        <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->problem->code); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('abstract', $orderBy, $vars, $lang->problem->abstract); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('IssueId', $orderBy, $vars, $lang->problem->IssueId); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('projectPlan', $orderBy, $vars, $lang->problem->projectPlan); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->problem->app); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('ifRecive', $orderBy, $vars, $lang->problem->ifRecive); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('acceptUser', $orderBy, $vars, $lang->problem->acceptUser); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->problem->createdBy); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('IfultimateSolution', $orderBy, $vars, $lang->problem->Ifultimate); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('feedbackExpireTime', $orderBy, $vars, $lang->problem->feedbackExpireTime); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('ReviewStatus', $orderBy, $vars, $lang->problem->ReviewStatus); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->problem->status); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->problem->dealUser); ?></th>
                        <th class='text-center c-actions-1 w-150px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($problems as $problem): ?>
                        <tr>
                            <td><?php echo common::hasPriv('problem', 'view') ? html::a(inlink('view', "problemID=$problem->id"), $problem->code) : $problem->code; ?></td>
                            <?php
                            $as = [];
                            foreach (explode(',', $problem->app) as $app) {
                                if (!$app) continue;
                                $as[] = zget($apps, $app);
                            }
                            $app = implode(', ', $as);
                            ?>
                            <td title="<?php echo $problem->abstract; ?>"
                                class='text-ellipsis'><?php echo $problem->abstract; ?></td>
                            <td title="<?php echo $problem->IssueId; ?>"
                                class='text-ellipsis'><?php echo $problem->IssueId; ?></td>
                            <td title="<?php echo zget($plans, $problem->projectPlan,''); ?>"
                                class='text-ellipsis'><?php echo zget($plans, $problem->projectPlan,''); ?></td>
                            <td title="<?php echo $app; ?>" class='text-ellipsis'><?php echo $app; ?></td>
                            <td><?php echo zget($lang->problem->ifReturnList , $problem->ifReturn); ?></td>
                            <td><?php echo zget($users, $problem->acceptUser, ''); ?></td>
                            <td><?php echo zget($users, $problem->createdBy, ''); ?></td>
                            <td><?php echo zget($lang->problem->ifultimateSolutionList, $problem->IfultimateSolution, ''); ?></td>
                            <td title="<?php echo $problem->feedbackExpireTime; ?>" class='text-ellipsis'><?php echo $problem->feedbackExpireTime; ?></td>
                            <td title="<?php echo zget($lang->problem->feedbackStatusList, $problem->ReviewStatus); ?>"
                                class='text-ellipsis'><?php echo zget($lang->problem->feedbackStatusList, $problem->ReviewStatus); ?></td>
                            <td>
                                <?php echo zget($lang->problem->statusList, $problem->status); ?>
                            </td>
<!--                            迭代35 需求收集 3531/3563 问题单指派-->
<!--                            <td title='--><?php //echo $problem->dealUsers;?><!--' class='text-ellipsis'>--><?php //echo $problem->dealUsers;?><!--</td>-->
                            <td title="<?php echo $problem->dealUsers;?>" class='text-ellipsis'><?php echo $this->loadModel('problem')->printAssignedHtml($problem, $users);?></td>
                            <td class='c-actions text-center' style="overflow:visible">
                                <?php
                                common::printIcon('problem', 'edit', "problemID=$problem->id", $problem, 'list');
                                $status = array('confirmed','assigned', 'toclose'); //20220930 待分配和待分析 或待开发且不是问题 高亮
                                if($this->app->user->admin or ( $this->app->user->account == $problem->dealUser && (in_array($problem->status,$status) or ($problem->status == 'feedbacked' && $problem->type == 'noproblem'))))//非当前处理人，图标置灰不能操作
                                {
                                    echo '<button type="button" class="btn" title="' . $lang->problem->deal . '" onclick="isClickable('.$problem->id.', \'deal\')"><i class="icon-common-suspend icon-time"></i></button>';
                                    common::printIcon('problem', 'deal', "problemID=$problem->id", $problem, 'list', 'time', '', 'iframe hidden', true, 'id=isClickable_deal' . $problem->id);
                                }
                                else
                                {
                                    echo '<button type="button" class="disabled btn" title="' . $lang->problem->deal . '"><i class="icon-common-suspend disabled icon-time"></i></button>';
                                }

                                //新建问题反馈单
                                common::printIcon('problem', 'createfeedback', "problemID=$problem->id", $problem, 'list', 'feedback', '', 'iframe', true);
                                //审批反馈单
//                                common::printIcon('problem', 'approvefeedback', "problemID=$problem->id", $problem, 'list', 'glasses', '', 'iframe', true);
                                $delayFlag = common::hasPriv('problem', 'reviewdelay') && in_array($problem->changeStatus, array_keys($this->lang->problem->reviewNodeStatusLableList)) && in_array($this->app->user->account, explode(',', $problem->changeDealUser)) ;
                                if($delayFlag && $problem->feedBackFlag){
                                    $str =  '<div class="btn-group">';
                                    $str .= "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' title='" . $this->lang->problem->review . "'><i class='icon icon-glasses'></i></button>";
                                    $str .= '<ul class="dropdown-menu">';
                                    $str .= '<li>' . html::a($this->createLink('problem', 'reviewdelay', 'problemId=' . $problem->id , '', true), $this->lang->problem->reviewdelay, '', "data-toggle='modal' data-type='iframe' ") . '</li>';
                                    $str .= '<li>' . html::a($this->createLink('problem', 'approvefeedback', 'problemID=' . $problem->id , '', true), $this->lang->problem->approvefeedback, '', "data-toggle='modal' data-type='iframe' ") . '</li>';
                                    $str .= '</ul></div>';
                                    echo $str;
                                }elseif($delayFlag){
                                    common::printIcon('problem', 'reviewdelay', "problemID=$problem->id", $problem, 'list', 'glasses', '', 'iframe', true);
                                }elseif ($problem->feedBackFlag){
                                    common::printIcon('problem', 'approvefeedback', "problemID=$problem->id", $problem, 'list', 'glasses', '', 'iframe', true);
                                }else{
                                    echo "<button class='btn disabled' title='" . $this->lang->problem->review . "'><i class='icon icon-glasses disabled'></i></button>";
                                }

                                common::printIcon('problem', 'copy', "problemID=$problem->id", $problem, 'list');
                                if( $problem->IssueId != null && $this->app->user->account == 'admin'){
                                    common::printIcon('problem', 'delete', "problemID=$problem->id", $problem, 'list', 'trash', '', 'iframe', true);
                                }elseif(empty($problem->IssueId)){
                                    common::printIcon('problem', 'delete', "problemID=$problem->id", $problem, 'list', 'trash', '', 'iframe', true);
                                }else{
                                   echo common::hasPriv('problem','delete') ?  '<button type="button" class="disabled btn" title="' . $lang->problem->delete . '"><i class="icon-common-suspend disabled icon-trash"></i></button>' :'';
                                }

                                common::printIcon('problem', 'close', "problemID=$problem->id", $problem, 'list', 'off', '', 'iframe', true);
                                //迭代34 删除问题单挂起功能
                                /*if ($this->app->user->admin or (in_array($this->app->user->account, $executives) && 'closed' != $problem->status)) {
                                    if ($problem->status == 'suspend') {
                                        common::printIcon('problem', 'start', "problemID=$problem->id", $problem, 'list', 'start', '', 'iframe', true);
                                    } else {
                                        common::printIcon('problem', 'suspend', "problemID=$problem->id", $problem, 'list', 'pause', '', 'iframe', true);
                                    }
                                } else {
                                    echo '<button type="button" class="disabled btn" title="' . $lang->problem->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                                }*/
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
