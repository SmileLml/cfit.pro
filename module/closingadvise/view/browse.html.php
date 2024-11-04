<?php
/**
 * The project view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     my
 * @version     $Id
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
    #queryBox .table td{overflow: unset}
</style>
<div id="mainContent" class="main-row fade">
    <div class='main-col'>
        <?php if(empty($closingadvise)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <form class='main-table' id='closingadviseForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
                <?php $vars = "projectId=$projectID&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
                <table class='table table-fixed has-sort-head' id='closingadvises'>
                    <thead>
                    <tr>
                        <th class='w-50px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->closingadvise->id);?></th>
                        <th class='w-100px'><?php common::printOrderLink('source', $orderBy, $vars, $lang->closingadvise->type);?></th>
                        <th class='w-200px'><?php common::printOrderLink('advise', $orderBy, $vars, $lang->closingadvise->advise);?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->closingadvise->createdDate);?></th>
                        <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->closingadvise->status);?></th>
                        <th class='w-120px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->closingadvise->dealUser);?></th>
                        <th class='text-center w-60px'><?php echo $lang->actions;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($closingadvise as $item):?>
                        <?php $item->itemStatus = $itemStatus[$item->itemId]; ?>
                        <tr>
                            <td title="<?php echo $item->id;?>" class='text-ellipsis'><?php echo common::hasPriv('closingadvise', 'view') ? html::a(inlink('view', "projectID=$projectID&closingitemID=$item->id"), $item->id) : $item->id;?></td>
                            <td title="<?php echo $this->lang->closingadvise->sourceList[$item->source];?>" class='text-ellipsis'><?php echo common::hasPriv('closingadvise', 'view') ? html::a(inlink('view', "projectID=$projectID&closingitemID=$item->id"), $this->lang->closingadvise->sourceList[$item->source]) : $this->lang->closingadvise->sourceList[$item->source];?></td>
                            <td title="<?php echo $item->advise;?>" class='text-ellipsis'><?php echo common::hasPriv('closingadvise', 'view') ? html::a(inlink('view', "projectID=$projectID&closingitemID=$item->id"), $item->advise) : $item->advise;?></td>
                            <td><?php echo $item->createdDate  != '0000-00-00' ? $item->createdDate : '';;?></td>
                            <td><?php echo zget($lang->closingadvise->browseStatus + $feedbackResults, $item->status);?></td>
                            <?php if($item->itemStatus != 'alreadyFeedback'):?>
                            <td  title="<?php $userList = '';foreach(explode(',', trim($item->dealuser, ',')) as $user) $userList .= $users[$user] . ',';$userList = trim($userList, ',');echo $userList; ?>" class='text-ellipsis team'><?php echo $userList; ?></td>
                            <?php else:?>
                            <td  title="" class='text-ellipsis team'><?php echo ''; ?></td>
                            <?php endif;?>
                            <td class='c-actions text-center'>
                                <?php
                                common::printIcon('closingadvise', 'review', "closingadviseID=$item->id", $item, 'list', 'checked', '', 'iframe', true);
                                common::printIcon('closingadvise', 'assignUser', "closingadviseID=$item->id", $item, 'list', 'hand-right', '', 'iframe', true);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </form>
        <?php endif;?>
        <?php if(!empty($closingadvise)):?>
        <div class="table-footer">
            <?php $pager->show('right', 'pagerjs');?>
        </div>
        <?php endif;?>
    </div>
</div>
<script>
    $(function(){$('#closingadviseForm').table();})
</script>
<?php include '../../common/view/footer.html.php';?>