<?php include '../../../common/view/header.html.php';?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach($lang->my->myReviewList as $key => $type):
            ?>
            <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
            <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
        <?php
        endforeach;
        ?>
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
            <form class='main-table' id='productionchangeForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
                <?php $vars = "type=$mode&browseType=$browseType&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
                <table class='table table-fixed has-sort-head' id='productionchange'>
                    <thead>
                    <tr>
                        <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->productionchange->code); ?></th>
                        <th class='w-200px'><?php common::printOrderLink('abstract', $orderBy, $vars, $lang->productionchange->abstract); ?></th>
                        <th class='w-180px'><?php common::printOrderLink('application', $orderBy, $vars, $lang->productionchange->application); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('applicant', $orderBy, $vars, $lang->productionchange->applicant); ?></th>
                        <th class='w-140px'><?php common::printOrderLink('applicantDept', $orderBy, $vars, $lang->productionchange->applicantDept); ?></th>
                        <!--                        <th class='w-100px'>--><?php //common::printOrderLink('onlineType', $orderBy, $vars, $lang->productionchange->onlineType); ?><!--</th>-->
                        <!--                        <th class='w-90px'>--><?php //common::printOrderLink('createdBy', $orderBy, $vars, $lang->productionchange->createdBy); ?><!--</th>-->
                        <th class='w-140px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->productionchange->createdDate); ?></th>
                        <th class='w-140px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->productionchange->status); ?></th>
                        <th class='w-110px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->productionchange->dealUser); ?></th>
                        <th class='text-center w-250px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reviewList as $productionchange): ?>
                        <tr>
                            <td title='<?php echo $productionchange->code; ?>'>
                                <?php echo common::hasPriv('productionchange', 'view') ? html::a($this->createLink('productionchange', 'view', "preproductionID=$productionchange->id"), $productionchange->code) : $productionchange->code;?>
                            </td>
                            <td title='<?php echo $productionchange->abstract; ?>'><?php echo $productionchange->abstract; ?></td>
                            <td title='<?php echo zmget($apps,$productionchange->application); ?>'><?php echo zmget($apps,$productionchange->application); ?></td>
                            <td><?php echo zget($users,$productionchange->applicant); ?></td>
                            <td><?php echo zget($depts,$productionchange->applicantDept); ?></td>
                            <!--                            <td>--><?php //echo zget($lang->productionchange->onlineTypeList,$productionchange->onlineType); ?><!--</td>-->
                            <!--                            <td title='--><?php //echo zget($users,$productionchange->createdBy); ?><!--'>--><?php //echo zget($users,$productionchange->createdBy); ?><!--</td>-->
                            <td title='<?php echo $productionchange->createdDate; ?>'><?php echo $productionchange->createdDate; ?></td>
                            <td><?php echo zget($lang->productionchange->statusList,$productionchange->status); ?></td>
                            <td title='<?php echo zmget($users,$productionchange->dealUser); ?>'><?php echo zmget($users,$productionchange->dealUser); ?></td>
                            <td class='c-actions text-center' style="overflow:visible">
                                <?php
                                $account = $this->app->user->account;
                                common::printIcon('productionchange', 'edit', "preproductionID=$productionchange->id", $productionchange, 'list');
                                common::printIcon('productionchange', 'deal', "preproductionID=$productionchange->id", $productionchange, 'list', 'time', '', 'iframe', true);
                                common::printIcon('productionchange', 'review', "preproductionID=$productionchange->id", $productionchange, 'list', 'glasses', '', 'iframe', true);
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

