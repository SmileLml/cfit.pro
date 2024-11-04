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
      <table class='table table-fixed has-sort-head' id='copyrightqz'>
        <thead>
            <tr>
                <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->copyrightqz->code); ?></th>
                <th class='w-80px'><?php common::printOrderLink('fullname', $orderBy, $vars, $lang->copyrightqz->fullname); ?></th>
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
        <?php foreach ($reviewList as $data): ?>
            <tr>
                <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo common::hasPriv('copyrightqz', 'view') ? html::a($this->createLink('copyrightqz','view',"copyrightqzId=$data->id"), $data->code) : $data->code;?></td>
                <td class='text-ellipsis' title="<?php echo $data->fullname ?>"><?php echo $data->fullname ;?></td>
                <td class='text-ellipsis' title="<?php echo $data->shortName ?>"><?php echo $data->shortName;?></td>
                <td class='text-ellipsis' title="<?php echo $data->version ?>"><?php echo $data->version;?></td>
                <td class='text-ellipsis' title="<?php echo $data->productenrollCode ?>">
                    <?php echo html::a($this->createLink('productenroll', 'view', 'productenrollID=' . $data->productenrollId, '', true), $data->productenrollCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ?>
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
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
