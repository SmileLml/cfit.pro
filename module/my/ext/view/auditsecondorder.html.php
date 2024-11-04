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
    <form class='main-table' id='secondorderForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "type=$mode&browseType=$browseType&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
        <table class='table table-fixed has-sort-head' id='secondorders'>
            <thead>
            <tr>
                <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->secondorder->code);?></th>
                <th class='w-150px'><?php common::printOrderLink('summary', $orderBy, $vars, $lang->secondorder->summary);?></th>
                <th class='w-60px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->secondorder->type);?></th>
                <th class='w-130px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->secondorder->app);?></th>
                <th class='w-60px'><?php common::printOrderLink('source', $orderBy, $vars, $lang->secondorder->source);?></th>
                <th class='w-80px'><?php common::printOrderLink('team', $orderBy, $vars, $lang->secondorder->team);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->secondorder->createdUser);?></th>
                <th class='w-80px'><?php common::printOrderLink('exceptDoneDate', $orderBy, $vars, $lang->secondorder->exceptDoneDate);?></th>
                <th class='w-60px'><?php common::printOrderLink('ifAccept', $orderBy, $vars, $lang->secondorder->ifAccept);?></th>
                <th class='w-60px'><?php common::printOrderLink('acceptDept', $orderBy, $vars, $lang->secondorder->acceptDept);?></th>
                <th class='w-60px'><?php common::printOrderLink('acceptUser', $orderBy, $vars, $lang->secondorder->acceptUser);?></th>
                <th class='w-60px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->secondorder->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->secondorder->dealUser);?></th>
                <th class='text-center w-100px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($reviewList as $secondorder):?>
                <tr>
                    <td><?php echo common::hasPriv('secondorder', 'view') ? html::a($this->createLink('secondorder','view', "secondorderID=$secondorder->id"), $secondorder->code) : $secondorder->code;?></td>
                    <td title="<?php echo $secondorder->summary;?>" class='text-ellipsis'><?php echo $secondorder->summary;?></td>
                    <td><?php echo zget($lang->secondorder->typeList, $secondorder->type);?></td>
                    <td title="<?php echo zget($apps,$secondorder->app);?>" class='text-ellipsis'><?php echo zget($apps,$secondorder->app);?></td>
                    <td><?php echo zget($lang->secondorder->sourceList, $secondorder->source);?></td>
                    <td><?php echo zget($lang->application->teamList, $secondorder->team);?></td>
                    <td><?php echo zget($users, $secondorder->createdBy);?></td>
                    <td><?php echo $secondorder->exceptDoneDate;?></td>
                    <td><?php
                        if(!empty($secondorder->ifAccept) || $secondorder->ifAccept === '0'){
                            echo zget($lang->secondorder->ifAcceptList, $secondorder->ifAccept, '');
                        }elseif (!empty($secondorder->ifReceived)){
                            echo zget($lang->secondorder->ifReceivedList, $secondorder->ifReceived, '');
                        }else{
                            echo '';
                        }
                        ?></td>
                    <td title="<?php echo zget($depts, $secondorder->acceptDept);?>" class='text-ellipsis'><?php echo zget($depts, $secondorder->acceptDept, '');?></td>
                    <td title="<?php echo zget($users, $secondorder->acceptUser);?>" class='text-ellipsis'><?php echo zget($users, $secondorder->acceptUser, '');?></td>
                    <td>
                        <?php echo zget($lang->secondorder->statusList, $secondorder->status);?>
                    </td>
                    <td title="<?php echo zmget($users, $secondorder->dealUser);?>" class='text-ellipsis'><?php echo $this->loadModel('secondorder')->printAssignedHtml($secondorder, $users);?></td>
                    <td class='c-actions text-center'>
                        <?php
                        $statusList = $secondorder->formType == 'external' ? [
                            'toconfirmed'
                        ] : [
                            'toconfirmed', 'backed'
                        ];
                        $closeflag = $this->loadModel('secondorder')->isClickable($secondorder, 'close');
                        if(in_array($secondorder->status,$statusList)  and $app->user->account == $secondorder->createdBy) {
                            common::printIcon('secondorder', 'edit', "secondorderID=$secondorder->id", $secondorder, 'list', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = 'data-app=secondorder' );
                        }
                        if($secondorder->status == 'toconfirmed' && $app->user->account == $secondorder->dealUser){
                            common::printIcon('secondorder', 'confirmed', "secondorderID=$secondorder->id", $secondorder, 'list', 'checked', '', 'iframe', true);
                        }
                        $statusList = ['assigned', 'tosolve'];
                        if(in_array($secondorder->status,$statusList) and ($app->user->account == $secondorder->dealUser || $app->user->account == 'admin')) {
                            common::printIcon('secondorder', 'deal', "secondorderID=$secondorder->id", $secondorder, 'list', 'time', '', 'iframe', true);
                        }
                        if(in_array($secondorder->status,['returned'])){
                            common::printIcon('secondorder', 'returned', "secondorderID=$secondorder->id", $secondorder, 'list', 'back', '', 'iframe', true);
                        }
                        if($secondorder->formType == 'internal'){
                            common::printIcon('secondorder', 'copy', "secondorderID=$secondorder->id", $secondorder, 'list', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = 'data-app=secondorder');
                            if($secondorder->status != 'closed') {
                                //common::printIcon('secondorder', 'close', "secondorderID=$secondorder->id", $secondorder, 'list','off', '', 'iframe', true);
                                if(common::hasPriv('secondorder', 'close'))
                                {
                                    if($closeflag)
                                    {
                                        echo "<a  href='javascript:;' onclick='closeCheck(".$secondorder->finallyHandOver.",".$secondorder->id.")' class='btn ' title='{$this->lang->secondorder->close}'><i class='icon-secondorder-close icon-off'></i></a>";
                                    }
                                    else
                                    {
                                        common::printIcon('secondorder', 'close', "secondorderID=$secondorder->id", $secondorder, 'list','off', '', 'iframe ', true," disabled");

                                    }
                                }
                            }
                            common::printIcon('secondorder', 'delete', "secondorderID=$secondorder->id", $secondorder, 'list', 'trash', '', 'iframe', true);
                        }
                        ?>
                        <a  data-app="secondorder" href="<?php echo $this->createLink('secondorder','close',"secondorderID=$secondorder->id").'?onlybody=yes';?>" id="closed<?php echo $secondorder->id?>"   class="btn iframe hidden " ></a>
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
    //关闭时检查工单是否最终移交
    function closeCheck(flag,id){
        if(flag == '2'){
            alert('工单没有完成全部移交，不能关闭！');
            return true;
        }else{
            $('#closed'+id).click();
        }
    }
</script>
<?php include '../../../common/view/footer.html.php';?>

