<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach ($lang->copyrightqz->labelList as $label => $labelName) {
            $active = $browseType == strtolower($label) ? 'btn-active-text' : '';
            echo html::a($this->createLink('copyrightqz', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
                $class = common::hasPriv('copyrightqz', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('copyrightqz', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('copyrightqz', 'export') ? $this->createLink('copyrightqz', 'export', "action=gain&orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->copyrightqz->export, '', $misc) . "</li>";
                ?>
            </ul>
        </div>
        <?php if(common::hasPriv('copyrightqz', 'create')) echo html::a($this->createLink('copyrightqz', 'create'), "<i class='icon-plus'></i> {$lang->copyrightqz->create}", '', "class='btn btn-primary'");?>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='copyrightqz'></div>
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
                        <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->copyrightqz->code); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('fullname', $orderBy, $vars, $lang->copyrightqz->fullname); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('emisCode', $orderBy, $vars, $lang->copyrightqz->emisCode); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('shortName', $orderBy, $vars, $lang->copyrightqz->shortName); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('version', $orderBy, $vars, $lang->copyrightqz->version); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('productenrollCode', $orderBy, $vars, $lang->copyrightqz->productenrollCode); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('applicant', $orderBy, $vars, $lang->copyrightqz->applicant); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('applicantDept', $orderBy, $vars, $lang->copyrightqz->applicantDept); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('createdTime', $orderBy, $vars, $lang->copyrightqz->createdTime); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->copyrightqz->status); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->copyrightqz->dealUser); ?></th>
                        <th class='text-center w-120px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <tr>
                            <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo common::hasPriv('copyrightqz', 'view') ? html::a(inlink('view', "copyrightqzId=$data->id"), $data->code) : $data->code;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->fullname ?>"><?php echo $data->fullname ;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->emisCode ?>"><?php echo $data->emisCode ;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->shortName ?>"><?php echo $data->shortName;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->version ?>"><?php echo $data->version;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->productenrollDeleted=='0'? $data->productenrollCode : ""?>">
                                <?php echo  $data->productenrollDeleted=='0'? html::a($this->createLink('productenroll', 'view', 'productenrollID=' . $data->productenrollId, '', true), $data->productenrollCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") : "" ?>
                            </td>
                            <td class='text-ellipsis' title="<?php echo zget($users,$data->applicant) ?>"><?php echo zget($users,$data->applicant);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($depts,$data->applicantDept) ?>"><?php echo zget($depts,$data->applicantDept);?></td>
                            <td class='text-ellipsis' title="<?php echo $data->createdTime ?>"><?php echo $data->createdTime;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->copyrightqz->statusList,$data->status) ?>"><?php echo zget($lang->copyrightqz->statusList,$data->status);?></td>
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
                                common::printIcon('copyrightqz', 'edit',  "copyrightqzID=$data->id", $data, 'list');
                                common::printIcon('copyrightqz', 'review', "copyrightqzId=$data->id&changeVersion=$data->changeVersion&reviewStage=$data->reviewStage", $data, 'list', 'glasses', '', 'iframe', true);
                                common::printIcon('copyrightqz', 'reject', "copyrightqzId=$data->id", $data, 'list', 'left-circle','','iframe', true);
                                common::printIcon('copyrightqz', 'delete', "copyrightqzID=$data->id", $data, 'list', 'trash','','iframe', true);
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
