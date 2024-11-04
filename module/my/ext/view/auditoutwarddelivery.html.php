<?php include '../../../common/view/header.html.php'?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->my->myReviewList as $key => $type):?>
            <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
            <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
        <?php endforeach;?>
    </div>
</div>
<div id="mainContent" class="main-row fade">
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
                <table class='table has-sort-head' id='reviewList'>
                    <thead>
                    <tr>
                        <th class='w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->outwarddelivery->code);?></th>
                        <th class='w-100px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->outwarddelivery->app);?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->outwarddelivery->createdBy);?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->outwarddelivery->createdDept);?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->outwarddelivery->createdDate);?></th>
                        <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->outwarddelivery->status);?></th>
                        <th class='w-80px'> <?php echo $lang->outwarddelivery->dealUser;?></th>
                        <th class='text-left w-40px'><?php echo $lang->actions;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $outwarddelivery):?>
                        <tr>
                            <td><?php echo common::hasPriv('outwarddelivery', 'view') ? html::a($this->createLink('outwarddelivery', 'view', "outwarddeliveryID=$outwarddelivery->id"), $outwarddelivery->code) : $outwarddelivery->code;?></td>

                            <?php
                            $as = [];

                            foreach(explode(',', $outwarddelivery->app) as $app)
                            {
                                if(!$app) continue;

                                $app = explode('/', $app);
                                $app[0] = trim($app[0],'"');
                                $as[] = zget($apps, $app[0]);
                            }
                            $app = implode(', ', $as);
                            ?>
                            <td title="<?php echo $app;?>" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap"><?php echo $app;?></td>
                            <td><?php echo zget($users, $outwarddelivery->createdBy, '');?></td>
                            <td><?php echo zget($depts, $outwarddelivery->createdDept, '');?></td>
                            <td><?php echo substr($outwarddelivery->createdDate, 0, 11);?></td>
                            <td><?php echo zget($lang->outwarddelivery->statusList, $outwarddelivery->status);?></td>
                            <?php
                            $reviewers = $outwarddelivery->dealUser;
                            $reviewersArray = explode(',', $reviewers);
                            //所有审核人
                            $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                            $reviewerUsersStr = implode(',', $reviewerUsers);
                            $subCount = 3;
                            $reviewerUsersSubStr = getArraySubValuesStr($reviewerUsers, $subCount);
                            ?>
                            <td title="<?php echo $reviewerUsersStr; ?>">
                                <?php echo $reviewerUsersSubStr; ?>
                            </td>
                            <td class='c-actions'>
                                <?php
                                common::printIcon('outwarddelivery', 'edit', "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'list');
                                common::printIcon('outwarddelivery', 'submit', "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'list', 'play', '', 'iframe', true);
                                common::printIcon('outwarddelivery', 'review', "outwarddeliveryID=$outwarddelivery->id&version=$outwarddelivery->version&reviewStage=$outwarddelivery->reviewStage", $outwarddelivery, 'list', 'glasses', '', 'iframe', true);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class='table-footer'></div>
            </form>
        <?php endif;?>
    </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
