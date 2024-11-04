<?php include '../../../common/view/header.html.php';?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->my->myReviewList as $key => $type):?>
    <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
    <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
    <?php endforeach;?>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <?php if(empty($reviewList)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='myReviewForm'>
        <div class="table-header fixed-right">
            <nav class="btn-toolbar pull-right"></nav>
        </div>
        <?php
        $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
        ?>
        <table class='table table-fixed has-sort-head' id='problems'>
            <thead>
            <tr>
                <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->copyright->code); ?></th>
                <th class='w-120px'><?php common::printOrderLink('modifyCode', $orderBy, $vars, $lang->copyright->modifyCode); ?></th>
                <th class='w-80px'><?php common::printOrderLink('fullname', $orderBy, $vars, $lang->copyright->fullname); ?></th>
                <th class='w-60px'><?php echo $lang->copyright->shortName;?></th>
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
            <?php foreach ($reviewList as $data): ?>
                <tr>
                    <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo common::hasPriv('copyright', 'view') ? html::a($this->createLink('copyright','view',"copyrightId=$data->id"), $data->code) : $data->code;?></td>
                    <td class='text-ellipsis' title="<?php echo $data->modifyCode;?>"><?php echo common::hasPriv('modify', 'view') ?
                            html::a($this->createLink('modify', 'view', 'modifyId=' . $data->modifyId, '', true), $data->modifyCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'")  : $data->modifyCode;?></td>
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
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
