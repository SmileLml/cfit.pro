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
    <form class='main-table' method='post' id='localesupportForm'>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php
      $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
      ?>
        <table class='table has-sort-head' id='localesupport'>
          <thead>
            <tr>
                <th class='w-180px'>
                    <div class="checkbox-primary checkall" onclick="checkall()" title="<?php echo $lang->selectAll?>">
                        <label></label>
                    </div><?php common::printOrderLink('code', $orderBy, $vars, $lang->localesupport->code);?>
                </th>
                <th class='w-140px'><?php common::printOrderLink('startDate', $orderBy, $vars, $lang->localesupport->startDate);?></th>
                <th class='w-140px'><?php common::printOrderLink('endDate', $orderBy, $vars, $lang->localesupport->endDate);?></th>
                <th class='w-100px'><?php common::printOrderLink('area', $orderBy, $vars, $lang->localesupport->area);?></th>
                <th class='w-100px'><?php common::printOrderLink('stype', $orderBy, $vars, $lang->localesupport->stype);?></th>
                <th class='w-150px'><?php common::printOrderLink('reason', $orderBy, $vars, $lang->localesupport->reason);?></th>
                <th class='w-120px'><?php common::printOrderLink('appIds', $orderBy, $vars, $lang->localesupport->appIds);?></th>
                <th class='w-120px'><?php common::printOrderLink('deptIds', $orderBy, $vars, $lang->localesupport->deptIds);?></th>
                <th class='w-140px'><?php common::printOrderLink('supportUsers', $orderBy, $vars, $lang->localesupport->supportUsers);?></th>
                <th class='w-80px'><?php  echo $lang->localesupport->consumedTotal;?></th>
                <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->localesupport->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->localesupport->createdBy);?></th>
                <th class='w-110px'><?php common::printOrderLink('dealUsers', $orderBy, $vars, $lang->localesupport->dealUsers);?> </th>
                <th class='text-center w-140px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php
                $currentUser = $this->app->user->account;
                foreach($reviewList as $item):
                    $tempDealUsers = ['admin'];
                    $status = $item->status;
                    $dealUsers = explode(',', $item->dealUsers);
                    $tempDealUsers = array_merge($tempDealUsers, $dealUsers);
                    $isAllowReview = false;
                    if(common::hasPriv('localesupport', 'review') && (in_array($status, $lang->localesupport->allowReviewStatusArray) && (in_array($currentUser, $tempDealUsers)))){
                        $isAllowReview = true;
                    }
                    $areaInfo = zget($lang->localesupport->areaList, $item->area);
                    $stypeInfo = zget($lang->localesupport->stypeList, $item->stype);
                    $appInfo  = zmget($apps, $item->appIds, '');
                    //部门信息
                    $deptInfo  = $item->deptIds;
                    $deptIds = explode(',', $item->deptIds);
                    if(!empty($deptIds)){
                        $tempData = [];
                        foreach ($deptIds as $deptId){
                            $deptName = trim(zget($depts, $deptId), '/');
                            $tempData[] = $deptName;
                        }
                        $deptInfo =  implode(',', $tempData);;
                    }
                    $supportUsersInfo = zmget($users, $item->supportUsers, '');
                    $statusDesc = zget($lang->localesupport->statusList, $status, $status);
                    $createdByUser = zget($users, $item->createdBy);
                    $dealUserInfo = zmget($users, $item->dealUsers, '');
                    //承建单位
                    $owndept = json_decode($item->owndept, true);
                    $owndeptInfo = [];
                    $owndeptInfoStr = $item->owndept;
                    if(!empty($owndept) && is_array($owndept)) {
                        foreach ($owndept as $appId => $val) {
                            $appName = zget($apps, $appId);
                            $team = zget($lang->application->teamList, $val);
                            $owndeptInfo[] = $appName . '：' . $team;
                        }
                        $owndeptInfoStr = implode('<br/>', $owndeptInfo);
                    }

                    ?>

                    <tr data-val='<?php echo $item->id?>'>
                        <td title="<?php echo $item->id; ?>">
                        <?php
                            $codeInfo = common::hasPriv('localesupport', 'view') ? html::a($this->createLink('localesupport','view', "localesupportId=$item->id"), $item->code) : $item->code;
                            if($isAllowReview){
                                echo html::checkbox('ids', array($item->id => '')) . $codeInfo;
                            }else{
                                echo html::checkbox('ids', array($item->id => ''),'',"disabled readonly").$codeInfo;
                            }
                        ?>
                        <td class='text-ellipsis viewClick' title="<?php echo  $item->startDate;?>"><?php echo $item->startDate;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo  $item->endDate;?>"><?php echo $item->endDate;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo  $areaInfo;?>"><?php echo $areaInfo;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo  $stypeInfo;?>"><?php echo $stypeInfo;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo strip_tags($item->reason); ?>"><?php echo strip_tags($item->reason);?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo  $appInfo;?>"><?php echo $appInfo;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo  $deptInfo;?>"><?php echo $deptInfo;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo  $supportUsersInfo;?>"><?php echo $supportUsersInfo;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo   $item->consumedTotal;?>"><?php echo  $item->consumedTotal;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo   $statusDesc;?>"><?php echo  $statusDesc;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo   $createdByUser;?>"><?php echo  $createdByUser;?></td>
                        <td class='text-ellipsis viewClick' title="<?php echo  $dealUserInfo;?>"><?php echo $dealUserInfo;?></td>
                        <td  class='c-actions text-center'>
                            <?php
                            common::printIcon('localesupport', 'edit', "localesupportId=$item->id", $item, 'list');
                            common::printIcon('localesupport', 'reportWork', "localesupportId=$item->id&source=workWaitList", $item, 'list', 'clock', '', 'iframe', true);
                            common::printIcon('localesupport', 'submit', "localesupportId=$item->id", $item, 'list', 'play', 'hiddenwin');
                            common::printIcon('localesupport', 'review', "localesupportId=$item->id", $item, 'list', 'glasses', '', 'iframe', true);
                            //common::printIcon('localesupport', 'delete', "localesupportId=$item->id", $item, 'list', 'trash', 'hiddenwin');
                            ?>
                        </td>
                    </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'>
          <div class="checkbox-primary checkall" onclick="checkall()"><label><?php echo $lang->selectAll?></label></div>
          <div class="table-actions btn-toolbar">
              <a id="batchedit" href="#" onclick="setBatchUrl();"  class="btn" title="<?php echo $this->lang->localesupport->batchReview; ?>"><?php echo $this->lang->localesupport->batchReview; ?></a>
              <span class="hidden">
                 <a id="batcheditBut"  class="btn iframe" title="<?php echo $this->lang->localesupport->batchReview; ?>" data-app="platform"><?php echo $this->lang->localesupport->batchReview; ?></a>
            </span>
          </div>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<script>
    $(function () {
        $("#localesupportForm").addClass("has-row-checked");
        $("#localesupport tbody input[name='ids[]']").removeAttr("checked");
        $(".checkall").removeClass("checked");
        //$("#localesupportForm").removeClass("has-row-checked");
    });

    $('.viewClick').live('click', function(){
        var id = $(this).parent().attr('data-val');
        window.location = createLink('localesupport', 'view', "localesupportId="+id)
    });

    /**
     * 全选
     */
    function checkall(){
        var checkflag = false;
        var hascheck = $(".checkall").eq(0).hasClass("checked");
        if(hascheck){
            $("#localesupport tbody input[name='ids[]']").each(function (){
                var isdisabled = $(this).attr("disabled");

                if(!isdisabled){
                    $(this).removeAttr("checked");
                }else{
                    $(this).removeAttr("checked");
                }
            });
            $(".checkall").removeClass("checked");
            //$("#localesupportForm").removeClass("has-row-checked");
        }else{
            $("#localesupport tbody input[name='ids[]']").each(function (){
                var isdisabled = $(this).attr("disabled");
                if(!isdisabled){
                    $(this).attr("checked",true);
                    checkflag = true
                }else{
                    $(this).removeAttr("checked")
                }
            });
            if(checkflag){
                $(".checkall").addClass("checked")
                //$("#localesupportForm").addClass("has-row-checked");
            }
        }
    }

    /**
     * 取消全选
     */
    $("#localesupport tbody input[name='ids[]']").change(
        function (){
            if(!($(this).is(":checked"))){
                $(".checkall").removeClass("checked")
            }
            var checkflag = false;
            $("#localesupport tbody input[name='ids[]']").each(function (){
                if(($(this).is(":checked"))){
                    checkflag = true;
                    return false;
                }
            });
            if(checkflag){
                //$("#localesupportForm").addClass("has-row-checked");
            }else {
                //$("#localesupportForm").removeClass("has-row-checked");
            }
        }
    );

    /**
     * 设置批量操作链接
     *
     * @returns {boolean}
     */
    function setBatchUrl(){
        var idsArray = [];
        $("#localesupport tbody input[name='ids[]']:checked").each(function (){
            idsArray.push($(this).val());
        });
        if(idsArray.length == 0){
            alert("请选择要确认的现场支持");
            return false;
        }else {
            var localesupportIds = idsArray.join(",");
            $("#batcheditBut").attr("href",createLink("localesupport","batchReview","localesupportIds="+localesupportIds)+"?onlybody=yes");
            $('#batcheditBut').click();
        }
    }
</script>

<?php include '../../../common/view/footer.html.php'?>
