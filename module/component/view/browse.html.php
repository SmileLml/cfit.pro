<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach ($lang->component->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a($this->createLink('component', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
                $class = common::hasPriv('component', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('component', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('component', 'export') ? $this->createLink('component', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->component->export, '', $misc) . "</li>";
                ?>
            </ul>
        <?php if (common::hasPriv('component', 'create')) echo html::a($this->createLink('component', 'create'), "<i class='icon-plus'></i> {$lang->component->create}", '', "class='btn btn-primary'"); ?>
        </div>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='component'></div>
        <?php if (empty($components)): ?>
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
                        <th class='w-60px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->component->id); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->component->name); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->component->componentType); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->component->level); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('applicationMethod', $orderBy, $vars, $lang->component->application); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('version', $orderBy, $vars, $lang->component->version); ?></th>
                        <th class='w-200px'><?php common::printOrderLink('projectId', $orderBy, $vars, $lang->component->project); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->component->status); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->component->dealUser); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->component->createdBy); ?></th>
                        <th class='w-180px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->component->createdDept); ?></th>
                        <th class='w-150px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->component->createdDate); ?></th>
                        <th class='text-center c-actions-1 w-120px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($components as $component): ?>
                        <tr>
                            <td><?php echo $component->id;?></td>
                            <td class='text-ellipsis' title="<?php echo $component->name; ?>"><?php echo common::hasPriv('component', 'view') ? html::a(inlink('view', "componentID=$component->id"), $component->name) : $component->name;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->type,$component->type) ?>"><?php echo zget($lang->component->type,$component->type);?></td>
                            <td class='text-ellipsis' title="<?php echo $component->type == 'public' ? zget($lang->component->levelList,$component->level):'/' ?>"><?php echo $component->type == 'public' ? zget($lang->component->levelList,$component->level):'/';?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->applicationMethod,$component->applicationMethod) ?>"><?php echo zget($lang->component->applicationMethod,$component->applicationMethod);?></td>
                            <td class='text-ellipsis' title="<?php echo $component->version; ?>"><?php echo $component->version; ?></td>
                            <td class='text-ellipsis' title="<?php echo zget($projectPlanList,$component->projectId) ?>"><?php echo zget($projectPlanList,$component->projectId);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->statusList,$component->status) ?>"><?php echo zget($lang->component->statusList,$component->status);?></td>
                            <?php
                            $dealUserTitle = '';
                            $dealUsersTitles = '';
                            if (!empty($component->dealUser)) {
                                foreach (explode(',', $component->dealUser) as $dealUser) {
                                    if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                                }
                            }
                            $dealUsersTitles = trim($dealUserTitle, ',');
                            ?>
                            <td title='<?php echo $dealUsersTitles; ?>' class='text-ellipsis'>
                                <?php echo $dealUsersTitles; ?>
                            </td>
                            <td class='text-ellipsis' title="<?php echo zget($users,$component->createdBy) ?>"><?php echo zget($users,$component->createdBy);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($depts,$component->createdDept) ?>"><?php echo zget($depts,$component->createdDept);?></td>
                            <td class='text-ellipsis' title="<?php echo $component->createdDate; ?>"><?php echo $component->createdDate; ?></td>
                            <td class='c-actions text-center'>
                                <?php
                                common::printIcon('component', 'edit', "componentID=$component->id", $component, 'list', 'edit');
                                common::printIcon('component', 'submit', "componentID=$component->id", $component, 'list', 'play','','iframe', true);
                                common::printIcon('component', 'review', "componentID=$component->id&changeVersion=$component->changeVersion&reviewStage=$component->reviewStage", $component, 'list', 'glasses', '', 'iframe', true);
                                common::printIcon('component', 'publish', "componentID=$component->id", $component, 'list', 'folder-open','','iframe', true);
                                common::printIcon('component', 'delete', "componentID=$component->id", $component, 'list', 'trash','hiddenwin');
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
