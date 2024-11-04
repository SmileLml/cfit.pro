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
    <form class='main-table' method='post' id='qualitygateForm'>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php
      $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
      ?>
        <table class='table has-sort-head' id='qualitygate'>
          <thead>
            <tr>
                <th class='w-120px'>
                    <div class="checkbox-primary checkall" onclick="checkall()" title="<?php echo $lang->selectAll?>">
                        <label></label>
                    </div><?php common::printOrderLink('code', $orderBy, $vars, $lang->qualitygate->code);?>
                </th>
                <th class='w-100px'><?php common::printOrderLink('projectId', $orderBy, $vars, $lang->qualitygate->belongProject); ?></th>
                <th class='w-100px'><?php common::printOrderLink('productId', $orderBy, $vars, $lang->qualitygate->productName); ?></th>
                <th class='w-80px'><?php common::printOrderLink('productCode', $orderBy, $vars, $lang->qualitygate->productCode); ?></th>
                <th class='w-80px'><?php common::printOrderLink('productVersion', $orderBy, $vars, $lang->qualitygate->version); ?></th>
                <th class='w-80px'><?php common::printOrderLink('buildName', $orderBy, $vars, $lang->qualitygate->buildName); ?></th>
                <th class='w-80px'><?php common::printOrderLink('buildStatus', $orderBy, $vars, $lang->qualitygate->buildStatus); ?></th>
                <th class='w-80px'><?php echo $lang->qualitygate->severityGate; ?></th>
                <th class='w-50px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->qualitygate->status); ?></th>
                <th class='w-50px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->qualitygate->dealUser); ?></th>
                <th class='text-center w-100px'><?php echo $lang->actions; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
                $currentUser = $this->app->user->account;
                foreach($reviewList as $item):
                    $tempDealUsers = ['admin'];
                    $status = $item->status;
                    $dealUsers = $item->dealUser;
                    $tempDealUsers = array_merge($tempDealUsers, $dealUsers);
                    $isAllowReview = false;
                    if(common::hasPriv('qualitygate', 'deal') && (in_array($status, $lang->qualitygate->allowDealStatusArr) && (in_array($currentUser, $tempDealUsers)))){
                        $isAllowReview = true;
                    }
                    //部门信息
                    $deptInfo  = $item->createdDept;
                    $deptIds = explode(',', $item->createdDept);
                    if(!empty($deptIds)){
                        $tempData = [];
                        foreach ($deptIds as $deptId){
                            $deptName = trim(zget($depts, $deptId), '/');
                            $tempData[] = $deptName;
                        }
                        $deptInfo =  implode(',', $tempData);;
                    }
                    $statusDesc = zget($lang->qualitygate->statusList, $status, $status);
                    $createdByUser = zget($users, $item->createdBy);
                    $dealUserInfo = zget($users, $item->dealUsers);
                    ?>

                    <tr data-val='<?php echo $item->id?>'>
                        <td>
                        <?php
                            $codeInfo = common::hasPriv('qualitygate', 'view') ?
                                html::a($this->createLink('qualitygate','view', "qualityGateId=$item->id"), $item->code) : $item->code;
                            if($isAllowReview){
                                echo html::checkbox('ids', array($item->id => '')) . $codeInfo;
                            }else{
                                echo html::checkbox('ids', array($item->id => ''),'',"disabled readonly").$codeInfo;
                            }
                        ?>
                        <td title='<?php echo $item->projectName; ?>' class='text-ellipsis'><?php echo $item->projectName;?>
                        </td>
                        <td title='<?php echo $item->productName; ?>' class='text-ellipsis'><?php echo  $item->productName;?>
                        </td>
                        <td title='<?php echo $item->productCode; ?>' class='text-ellipsis'><?php echo $item->productCode; ?></td>
                        <td title='<?php echo $item->productVersionTitle; ?>' class='text-ellipsis'><?php echo $item->productVersionTitle; ?></td>
                        <td title='<?php echo $item->buildName; ?>' class='text-ellipsis'><?php echo empty($item->buildName) ? '' :
                                html::a($this->createLink('build', 'view', "buildID=$item->buildId", '', true),
                                    $item->buildName, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'")
                            ;?>
                        </td>
                        <td title='<?php echo zget($buildstatusList, $item->buildStatus); ?>' class='text-ellipsis'><?php echo zget($buildstatusList, $item->buildStatus); ?></td>
                        <td class='text-ellipsis'><?php echo html::a($this->createLink('report', 'qualityGateCheckResult', "projectId=$item->projectId&productId=$item->productId&productVersion=$item->productVersion&buildId=$item->buildId", '', true).'#app=project',
                                $lang->qualitygate->check, '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'");?>
                        </td>
                        <td class='text-ellipsis'><?php echo $this->loadModel('qualitygate')->diffColorStatus($item->status); ?></td>
                        <td title='<?php echo $item->dealUser; ?>' class='text-ellipsis'><?php echo $this->loadModel('qualitygate')->printAssignedHtml($item, $users);?>
                        </td>
                        <td class='c-actions text-ellipsis text-center'>
                            <?php
                            //common::printIcon('qualitygate', 'edit', "qualitygateID=$item->id", $item, 'list','edit', '', 'iframe', true);
                            common::printIcon('qualitygate', 'deal', "qualitygateId=$item->id", $item, 'list', 'time', '', 'iframe', true, '', $lang->qualitygate->todeal);
                            common::printIcon('qualitygate', 'delete', "qualitygateId=$item->id", $item, 'list', 'trash', '', 'iframe', true);
                            ?>
                        </td>
                    </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'>
          <div class="checkbox-primary checkall" onclick="checkall()"><label><?php echo $lang->selectAll?></label></div>
          <div class="table-actions btn-toolbar">
              <a id="batchedit" href="#" onclick="setBatchUrl();"  class="btn" title="<?php echo $this->lang->qualitygate->batchReview; ?>"><?php echo $this->lang->qualitygate->batchReview; ?></a>
              <span class="hidden">
                 <a id="batcheditBut"  class="btn iframe" title="<?php echo $this->lang->qualitygate->batchReview; ?>" data-app="platform"><?php echo $this->lang->qualitygate->batchReview; ?></a>
            </span>
          </div>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<script>
    $(function () {
        $("#qualitygateForm").addClass("has-row-checked");
        $("#qualitygate tbody input[name='ids[]']").removeAttr("checked");
        $(".checkall").removeClass("checked");
        //$("#qualitygateForm").removeClass("has-row-checked");
    });

    $('.viewClick').live('click', function(){
        var id = $(this).parent().attr('data-val');
        window.location = createLink('qualitygate', 'view', "qualitygateId="+id)
    });

    /**
     * 全选
     */
    function checkall(){
        var checkflag = false;
        var hascheck = $(".checkall").eq(0).hasClass("checked");
        if(hascheck){
            $("#qualitygate tbody input[name='ids[]']").each(function (){
                var isdisabled = $(this).attr("disabled");

                if(!isdisabled){
                    $(this).removeAttr("checked");
                }else{
                    $(this).removeAttr("checked");
                }
            });
            $(".checkall").removeClass("checked");
            //$("#qualitygateForm").removeClass("has-row-checked");
        }else{
            $("#qualitygate tbody input[name='ids[]']").each(function (){
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
                //$("#qualitygateForm").addClass("has-row-checked");
            }
        }
    }

    /**
     * 取消全选
     */
    $("#qualitygate tbody input[name='ids[]']").change(
        function (){
            if(!($(this).is(":checked"))){
                $(".checkall").removeClass("checked")
            }
            var checkflag = false;
            $("#qualitygate tbody input[name='ids[]']").each(function (){
                if(($(this).is(":checked"))){
                    checkflag = true;
                    return false;
                }
            });
            if(checkflag){
                //$("#qualitygateForm").addClass("has-row-checked");
            }else {
                //$("#qualitygateForm").removeClass("has-row-checked");
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
        $("#qualitygate tbody input[name='ids[]']:checked").each(function (){
            idsArray.push($(this).val());
        });
        if(idsArray.length == 0){
            alert("请选择要确认的现场支持");
            return false;
        }else {
            var qualitygateIds = idsArray.join(",");
            $("#batcheditBut").attr("href",createLink("qualitygate","batchReview","qualitygateIds="+qualitygateIds)+"?onlybody=yes");
            $('#batcheditBut').click();
        }
    }
</script>

<?php include '../../../common/view/footer.html.php'?>
