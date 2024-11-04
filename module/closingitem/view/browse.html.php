<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
    #queryBox .table td{overflow: unset}
</style>
<div id="mainMenu" class="clearfix table-row">
    <div class="btn-toolbar pull-right">
        <?php if(common::hasPriv('closingitem', 'create')) echo html::a($this->createLink('closingitem', 'create', "projectID=$projectID"), "<i class='icon-plus'></i> {$lang->closingitem->create}", '', "class='btn btn-primary'");?>
    </div>
</div>
<div id="mainContent" class="main-row fade">
    <div class='main-col'>
        <?php if(empty($closingitem)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <form class='main-table' id='closingitemForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
                <table class='table table-fixed has-sort-head' id='closingitems'>
                    <thead>
                    <tr>
                        <th class='w-30px'><?php echo $lang->closingitem->id;?></th>
                        <th class='w-60px'><?php echo $lang->closingitem->projectType;?></th>
                        <th class='w-80px'><?php echo $lang->closingitem->isAssembly;?></th>
                        <th class='w-60px'><?php echo $lang->closingitem->assemblyNum;?></th>
                        <th class='w-60px'><?php echo $lang->closingitem->assemblyAdvise;?></th>
                        <th class='w-60px'><?php echo $lang->closingitem->toolsUsage;?></th>
                        <th class='w-70px'><?php echo $lang->closingitem->toolsAdvise;?></th>
                        <th class='w-50px'><?php echo $lang->closingitem->osspAdvise;?></th>
                        <th class='w-50px'><?php echo $lang->closingitem->platformAdvise;?></th>
                        <th class='w-50px'><?php echo $lang->closingitem->adviseChecklist;?></th>
                        <th class='w-50px'><?php echo $lang->closingitem->realPoints;?></th>
<!--                        <th class='w-50px'>--><?php //echo $lang->closingitem->demandAdviseName;?><!--</th>-->
<!--                        <th class='w-50px'>--><?php //echo $lang->closingitem->constructionAdviseName;?><!--</th>-->
                        <th class='w-50px'><?php echo $lang->closingitem->createdBy;?></th>
                        <th class='w-50px'><?php echo $lang->closingitem->createdDate;?></th>
                        <th class='w-50px'><?php echo $lang->closingitem->status;?></th>
                        <th class='w-40px'><?php echo $lang->closingitem->dealUser;?></th>
                        <th class='text-center w-60px'><?php echo $lang->actions;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($closingitem as $item):?>
                        <tr>
                            <td title="<?php echo $item->id;?>" class='text-ellipsis'><?php echo common::hasPriv('closingitem', 'view') ? html::a(inlink('view', "projectID=$projectID&closingitemID=$item->id"), $item->id) : $item->id;?></td>
                            <td title="<?php echo $typeList[$item->projectType];?>" class='text-ellipsis'><?php echo common::hasPriv('closingitem', 'view') ? html::a(inlink('view', "projectID=$projectID&closingitemID=$item->id"), $typeList[$item->projectType]) : $typeList[$item->projectType];?></td>
                            <td><?php echo zget($this->lang->closingitem->typeIsList, $item->isAssembly);?></td>
                            <td title="<?php echo $item->assemblyNum;?>" class='text-ellipsis'><?php echo $item->assemblyNum;?></td>
                            <td><?php echo zget($lang->closingitem->typeHasList, $item->assemblyAdvise);?></td>
                            <td><?php echo zget($lang->closingitem->typeHasList, $item->toolsUsage);?></td>
                            <td><?php echo zget($lang->closingitem->typeHasList, $item->toolsAdvise);?></td>
                            <td><?php echo zget($lang->closingitem->typeHasList, $item->osspAdvise);?></td>
                            <td><?php echo zget($lang->closingitem->typeHasList, $item->platformAdvise);?></td>
                            <td><?php echo zget($lang->closingitem->typeHasList, $item->adviseChecklist);?></td>
                            <td title="<?php echo $item->realPoints;?>" class='text-ellipsis'><?php echo $item->realPoints;?></td>
<!--                            <td>--><?php //echo zget($lang->closingitem->demandAdvise, $item->demandAdvise);?><!--</td>-->
<!--                            <td>--><?php //echo zget($lang->closingitem->constructionAdvise, $item->constructionAdvise);?><!--</td>-->
                            <td><?php echo zget($users, $item->createdBy);?></td>
                            <td title="<?php echo zget($lang->closingitem->browseStatus, $item->createdDate);?>" class='text-ellipsis'><?php echo zget($lang->closingitem->browseStatus, $item->createdDate);?></td>
                            <td title="<?php echo zget($lang->closingitem->browseStatus, $item->status);?>" class='text-ellipsis'><?php echo zget($lang->closingitem->browseStatus, $item->status);?></td>
                            <td  title="<?php $userList = '';foreach(explode(',', trim($item->dealuser, ',')) as $user) $userList .= $users[$user] . ',';$userList = trim($userList, ',');echo $userList; ?>" class='text-ellipsis team'><?php echo $userList; ?></td>
                            <td class='c-actions text-center'>
                                <?php
                                common::printIcon('closingitem', 'edit', "closingitemID=$item->id&projectID=$projectID", $item, 'list');
                                common::printIcon('closingitem', 'submit', "closingitemID=$item->id&projectID=$projectID", $item, 'list','play', 'hiddenwin');
                                common::printIcon('closingitem', 'review', "closingitemID=$item->id", $item, 'list', 'glasses', '', 'iframe', true);
                                common::printIcon('closingitem', 'delete', "closingitemID=$item->id", $item, 'list', 'trash', 'hiddenwin');
                                ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </form>
        <?php endif;?>
    </div>
</div>
<script>
    $(function(){$('#closingitemForm').table();})
</script>
<?php include '../../common/view/footer.html.php';?>