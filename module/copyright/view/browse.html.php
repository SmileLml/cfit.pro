<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach ($lang->copyright->labelList as $label => $labelName) {
            $active = $browseType == strtolower($label) ? 'btn-active-text' : '';
            echo html::a($this->createLink('copyright', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
                $class = common::hasPriv('copyright', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('copyright', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('copyright', 'export') ? $this->createLink('copyright', 'export', "action=gain&orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->copyright->export, '', $misc) . "</li>";
                ?>
            </ul>
        </div>
        <?php if(common::hasPriv('copyright', 'create')) echo html::a($this->createLink('copyright', 'create'), "<i class='icon-plus'></i> {$lang->copyright->create}", '', "class='btn btn-primary'");?>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='copyright'></div>
        <?php if (empty($datas)): ?>
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
                        <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->copyright->code); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('modifyCode', $orderBy, $vars, $lang->copyright->modifyCode); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('fullname', $orderBy, $vars, $lang->copyright->fullname); ?></th>
                        <th class='w-60px'><?php echo $lang->copyright->shortName; ?></th>
                        <th class='w-60px'><?php echo $lang->copyright->version; ?></th>
                        <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->copyright->createdBy); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->copyright->createdDept); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('createdTime', $orderBy, $vars, $lang->copyright->createdTime); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->copyright->status); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->copyright->dealUser); ?></th>
                        <th class='text-center w-120px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <tr>
                            <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo common::hasPriv('copyright', 'view') ? html::a(inlink('view', "copyrightId=$data->id"), $data->code) : $data->code;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->modifyCode;?>"><?php echo common::hasPriv('modify', 'view') ? html::a($this->createLink('modify', 'view', "modifyId=$data->modifyId"), $data->modifyCode) : $data->modifyCode;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->fullname ?>"><?php echo $data->fullname ;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->shortName ?>"><?php echo $data->shortName;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->version ?>"><?php echo $data->version;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($users,$data->createdBy) ?>"><?php echo zget($users,$data->createdBy);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($depts,$data->createdDept) ?>"><?php echo zget($depts,$data->createdDept);?></td>
                            <td class='text-ellipsis' title="<?php echo $data->createdTime ?>"><?php echo $data->createdTime;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->copyright->statusList,$data->status) ?>"><?php echo zget($lang->copyright->statusList,$data->status);?></td>
                            <?php
                            $dealUserTitle = '';
                            $dealUsersTitles = '';
                            if (!empty($data->dealUser)) {
                                foreach (explode(',', $data->dealUser) as $dealUser) {
                                    if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                                }
                            }
                            $dealUsersTitles = trim($dealUserTitle, ',');
                            ?>
                            <td title='<?php echo $dealUsersTitles; ?>' class='text-ellipsis'>
                                <?php echo $dealUsersTitles; ?>
                            </td>
                            <td class='c-actions text-center' style="overflow:visible">
                                <?php
                                common::printIcon('copyright', 'edit',  "copyrightID=$data->id", $data, 'list');
                                common::printIcon('copyright', 'review', "copyrightId=$data->id&changeVersion=$data->changeVersion&reviewStage=$data->reviewStage", $data, 'list', 'glasses', '', 'iframe', true);
                                common::printIcon('copyright', 'delete', "copyrightId=$data->id", $data, 'list', 'trash','','iframe', true);

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
