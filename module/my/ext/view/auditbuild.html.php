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
<div id="mainContent">
  <?php if(empty($reviewList)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->build->noBuild;?></span></p>
  </div>
  <?php else:?>
  <div class='main-table'  id='buildForm' data-ride="table" data-checkable="true">
    <table class="table text-center" id='buildList'>
      <thead>
        <tr>
            <th class="c-id-sm w-p5"><div class="checkbox-primary checkall" onclick="checkall()" title="<?php echo $lang->selectAll?>">
                    <label></label>
                </div><?php echo $lang->build->id; ?></th>
            <th class="c-name  w-p25 text-left"><?php echo $lang->build->name;?></th>
            <th class="c-name  w-p15 text-left"><?php echo $lang->build->product;?></th>
            <th class="c-name  w-p10 text-left"><?php echo $lang->build->version;?></th>
            <!--<th class="c-name  w-80px text-left"><?php /*echo $lang->build->purpose;*/?></th>
            <th class="c-name w-60px text-left"><?php /*echo $lang->build->rounds;*/?></th>-->
            <th class="c-date w-150px"><?php echo $lang->build->createdDate;?></th>
            <th class="c-user" style="width:4%"><?php echo $lang->build->createdBy;?></th>
            <th class="c-user"><?php echo $lang->build->status;?></th>
            <th class="c-user"><?php echo $lang->build->dealuser;?></th>
            <th class="c-actions-4"><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>

        <?php foreach($reviewList as $index => $build):?>
        <tr data-id="<?php echo $build->productID;?>">
            <td class="c-id-sm text-muted w-p5">
                <?php
                if(($this->app->user->account == 'admin' or in_array($this->app->user->account , array_filter(explode(',',$build->dealuser)))) and !in_array($build->status, array('testfailed', 'versionfailed', 'verifyfailed','back','wait','verifysuccess','testsuccess'))){
                    echo html::checkbox('builds', array($build->id => '')) . sprintf('%03d', $build->id);
                }else{
                    echo html::checkbox('builds', array($build->id => ''),'',"disabled readonly").sprintf('%03d', $build->id);
                }
                ?>
            </td>
<!--            <td class="c-id-sm text-muted"><?php /*echo html::a(helper::createLink('build', 'view', "buildID=$build->id"), sprintf('%03d', $build->id), '', "data-app='project'");*/?></td>
-->            <td class='c-name'>
                <?php if($build->branchName) echo "<span class='label label-outline label-badge'>{$build->branchName}</span>"?>
                <?php if($build->name )echo html::a($this->createLink('build', 'view', "build=$build->id"), $build->name, '', "data-app='project'");?>
            </td>
            <td class="c-name text-left" title='<?php echo $build->product == '99999' ? '无' : $build->productName;?>'><?php echo  $build->product == '99999' ? '无' : $build->productName;?></td>
            <td class="c-name text-left" title='<?php echo zget($build->versions,$build->version,'');?>'><?php echo zget($build->versions,$build->version,'');?></td>
           <!-- <td class="c-name text-left" title='<?php /*echo zget($lang->build->purposeList, $build->purpose, '');*/?>'><?php /*echo zget($lang->build->purposeList, $build->purpose, '');*/?></td>
            <td class="c-name text-left" title='<?php /*echo zget($lang->build->roundsList, $build->rounds, '');*/?>'><?php /*echo zget($lang->build->roundsList, $build->rounds, '');*/?></td>-->
            <td class="c-date"><?php echo $build->createdDate?></td>
            <td class="c-user em"><?php echo zget($users, $build->createdBy);?></td>
            <td class="c-user em"><?php echo zget($lang->build->statusList, $build->status);?></td>
            <td class="c-user em"><?php echo zmget($users, $build->dealuser);?></td>
            <td class="c-actions text-center" >
                <?php
                common::printIcon('build',   'edit', "buildID=$build->id&productID=$build->product&projectID=$build->project", $build, 'list');
                common::printIcon('build', 'deal', "buildID=$build->id", $build, 'list', 'time', '', 'iframe', true,'data-width="1200px"');
                common::printIcon('build', 'rebuild', "buildID=$build->id", $build, 'list', 'start', '', 'iframe', true);
                common::printIcon('build',   'ignore', "buildID=$build->id", $build, 'list','ban','hiddenwin');

                ?>
            </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
      <div class="table-footer">
          <div class="checkbox-primary checkall" onclick="checkall()"><label><?php echo $lang->selectAll?></label></div>
          <div class="table-actions btn-toolbar"><a id="batchedit" onclick="setbatchediturl()"  class="btn " title="批量处理项目制版">批量处理</a></div>
          <?php $pager->show('right', 'pagerjs'); ?>
      </div>
  </div>
  <?php endif;?>
</div>
<?php include '../../../common/view/footer.html.php';?>
<script>

    cleardischeckbox();
    function cleardischeckbox(){
        $("#buildList tbody input[name='builds[]']").each(function (){
            var isdisabled = $(this).attr("disabled");
            if(isdisabled){
                if($(this).is(":checked")){
                    $(this).removeAttr("checked");
                }
            }
        });
    }

    function checkall(){
        var checkflag = false;
        var hascheck = $(".checkall").eq(0).hasClass("checked");
        if(hascheck){
            $("#buildList tbody input[name='builds[]']").each(function (){
                var isdisabled = $(this).attr("disabled");
                if(!isdisabled){
                    $(this).removeAttr("checked")

                }else{
                    $(this).removeAttr("checked")
                }
            });
            $(".checkall").removeClass("checked")
            $("#buildForm").removeClass("has-row-checked")
        }else{
            $("#buildList tbody input[name='builds[]']").each(function (){
                var isdisabled = $(this).attr("disabled");

                if(!isdisabled){
                    $(this).attr("checked",true)
                    checkflag = true
                }else{
                    $(this).removeAttr("checked")
                }
            });
            if(checkflag){
                $(".checkall").addClass("checked")
                $("#buildForm").addClass("has-row-checked")
            }

        }
    }
    $("#buildList tbody input[name='builds[]']").change(
        function (){
            if(!($(this).is(":checked"))){
                $(".checkall").removeClass("checked")
            }
        }
    )
   var batchAddModalTrigger = new $.zui.ModalTrigger(
        {
            width: '1000px',
            // height:'420px',
            type: 'iframe',
            waittime: 3000
        });

    function setbatchediturl(){
        var buildidArr = [];
        var statusArr = [];
        $("#buildList tbody input[name='builds[]']:checked").each(function (){
            buildidArr.push($(this).val());
            statusArr.push($(this).closest('tr').find('td:eq(6)').text());
        });
        if(buildidArr.length == 0){
            alert("请选择要处理的项目制版");
            return false;
        }
        var unique = $.unique(statusArr);
        if(unique.length > 1){

            alert("您勾选的制版未处在相同审批节点！");
            return false;
        }else{
            buildidIdstr = buildidArr.join(",");
            var url = createLink("build","batchDeal","buildID="+buildidIdstr)+"?onlybody=yes";
            //$("#batchedit").attr("href",createLink("build","batchDeal","buildID="+buildidIdstr)+"?onlybody=yes")
            batchAddModalTrigger.show({url:url})
        }

    }
</script>