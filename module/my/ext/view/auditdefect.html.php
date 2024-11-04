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
                        <th class='w-p5'><?php common::printOrderLink('id', $orderBy, $vars, $lang->defect->idAB);?></th>
                        <th style="width: 12%"><?php common::printOrderLink('title', $orderBy, $vars, $lang->defect->title);?></th>
                        <th style="width: 12%"><?php common::printOrderLink('product', $orderBy, $vars, $lang->defect->product);?></th>
                        <th style="width: 12%"><?php common::printOrderLink('project', $orderBy, $vars, $lang->defect->project);?></th>
                        <th style="width: 8%"><?php common::printOrderLink('pri', $orderBy, $vars, $lang->defect->pri);?></th>
                        <th style="width: 8%"><?php common::printOrderLink('severity', $orderBy, $vars, $lang->defect->severity);?></th>
                        <th class='w-p10'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->defect->createdDate);?></th>
                        <th style="width: 8%"><?php common::printOrderLink('status', $orderBy, $vars, $lang->defect->status);?></th>
                        <th style="width: 8%"><?php echo $lang->defect->nextUser;?></th>
                        <th class='w-p10'><?php common::printOrderLink('dealSuggest', $orderBy, $vars, $lang->defect->dealSuggest);?></th>
                        <th class='text-center w-p10'><?php echo $lang->actions;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $defect):?>
                        <tr>
                            <td title="<?php echo $defect->code;?>" class='text-ellipsis'><?php echo common::hasPriv('defect', 'view') ? html::a($this->createLink('defect', 'view', "defectID=$defect->id"), $defect->id) : $defect->id;?></td>
                            <td title="<?php echo $defect->title;?>" class='text-ellipsis'><?php echo $defect->title;?></td>
                            <td title="<?php echo zget($products, $defect->product);?>" class='text-ellipsis'><?php echo zget($products, $defect->product);?></td>
                            <td title="<?php echo zget($projects,$defect->project);?>" class='text-ellipsis'><?php echo zget($projects,$defect->project);?></td>
                            <td><?php echo zget($lang->bug->defectPriList, $defect->pri);?></td>
                            <td><?php echo zget($lang->bug->defectSeverityList, $defect->severity);?></td>
                            <td><?php echo $defect->createdDate  != '0000-00-00 00:00:00' ? $defect->createdDate : '';;?></td>
                            <td title="<?php echo zget($lang->defect->statusList, $defect->status);?>" class='text-ellipsis'>
                                <?php echo zget($lang->defect->statusList, $defect->status);?>
                            </td>
                            <td title="<?php echo zget($users, $defect->dealUser);?>" class='text-ellipsis'><?php echo zget($users, $defect->dealUser, '');?></td>
                            <td title="<?php echo zget($lang->bug->dealSuggestList, $defect->dealSuggest);?>" class='text-ellipsis'><?php echo zget($lang->bug->dealSuggestList, $defect->dealSuggest);?></td>
                            <td class='c-actions text-center'>
                                <?php
                                common::printIcon('defect', 'edit', "defectID=$defect->id", $defect, 'list', '', '','iframe', true);
                                common::printIcon('defect', 'confirm', "defectID=$defect->id", $defect, 'list', 'ok', '', 'iframe', true);
                                common::printIcon('defect', 'deal', "defectID=$defect->id", $defect, 'list', 'time', '', 'iframe', true);
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
