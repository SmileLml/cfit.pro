<?php
/**
 * The build view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: build.html.php 4262 2013-01-24 08:48:56Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php js::set('projectID', $projectID)?>
<?php js::set('createExecution', $lang->project->createExecution)?>
<?php js::set('confirmDelete', $lang->build->confirmDelete)?>
<style>
    .vertical-divider {
        display: inline-block;
        height: 20px;
        width: 1px;
        background-color: #adacac;
        margin: 0 3px; /* 左右间距 */
        vertical-align: middle;
    }
</style>
<div id="mainMenu" class="clearfix table-row">
    <div class="btn-toolbar pull-left">
        <!--    --><?php
        //    $label  = "<span class='text'>{$lang->execution->build}</span>";
        //    $active = '';
        //    if($type == 'all')
        //    {
        //        $active = 'btn-active-text';
        //        $label .= " <span class='label label-light label-badge'>{$buildsTotal}</span>";
        //    }
        //    echo html::a(inlink('build', "projectID=$projectID&type=all"), $label, '', "class='btn btn-link $active' id='all'")
        //    ?>
        <div class="input-control space w-150px" style="margin-left: 0;"><?php echo html::select('product', $products, $product, "onchange='changeProduct(this.value)' class='form-control chosen' data-placeholder='{$lang->product->AllProduct}'"); ?></div>
        <a class="btn btn-link querybox-toggle" id="bysearchTab"><i
                    class="icon icon-search muted"></i> <?php echo $lang->execution->byQuery; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <?php if (common::canModify('project', $project)) common::printLink('build', 'create', "executionID=&productID=&projectID=$projectID", "<i class='icon icon-plus'></i> " . $lang->build->create, '', "class='btn btn-primary' id='createBuild'"); ?>
    </div>
</div>
<div id="mainContent">
  <div class="cell <?php if($type == 'bysearch') echo 'show';?>" id="queryBox" data-module='projectBuild'></div>
  <?php if(empty($projectBuilds)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->build->noBuild;?></span></p>
  </div>
  <?php else:?>
  <div class='main-table' id='buildForm' data-ride="table" data-checkable="true">
      <?php $vars = "param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
<!--<form class='main-table' id='buildForm'   method='post' data-ride='table' data-nested='true' data-checkable='true'>-->
    <table class="table has-sort-head" id='buildList'>
      <thead>
        <tr>
            <th class='w-60px' style="left: 0px;"><div class="checkbox-primary checkall" onclick="checkall()" title="<?php echo $lang->selectAll?>">
                    <label></label>
                </div><?php echo $lang->build->id; ?></th>
            <th class="c-name w-p15 text-left"><?php echo $lang->build->name;?></th>
            <th class="c-name w-p15 text-left"><?php echo $lang->build->product;?></th>
            <th class="c-name w-p10 text-left"><?php echo $lang->build->version;?></th>
            <!--<th class="c-name  text-left" style="width:6%"><?php /*echo $lang->build->purpose;*/?></th>
            <th class="c-name w-p5 text-left"><?php /*echo $lang->build->rounds;*/?></th>-->
            <th class="c-date w-date"><?php echo $lang->build->createdDate;?></th>
            <th class="c-user" style="width:6%"><?php echo $lang->build->createdBy;?></th>
            <th class=" col-xs-1" style="width:6%"><?php echo $lang->build->status;?></th>
            <th class="c-user" style="width:6%"><?php echo $lang->build->dealuser;?></th>
            <th class="c-actions text-center" style="width:18%"><?php echo $lang->actions;?></th>

        </tr>
      </thead>
      <tbody>
          <!-- --><?php /*foreach($projectBuilds as $productID => $builds):*/?>
          <?php foreach($projectBuilds as $index => $build):?>
          <tr style="left: 0px;" data-id="<?php echo $build->productID;?>">
          <td class="c-id-sm text-muted w-p5">
              <?php
              if(($this->app->user->account == 'admin' or in_array($this->app->user->account , array_filter(explode(',',$build->dealuser)))) and !in_array($build->status, array('testfailed', 'versionfailed', 'verifyfailed','back','wait','verifysuccess','testsuccess'))){
                  echo html::checkbox('builds', array($build->id => '')) . sprintf('%03d', $build->id);
              }else{
                  echo html::checkbox('builds', array($build->id => ''),'',"disabled readonly").sprintf('%03d', $build->id);
              }
              ?>
          </td>
<!--          <td class="c-id-sm text-muted w-p5">--><?php //echo html::a(helper::createLink('build', 'view', "buildID=$build->id"), sprintf('%03d', $build->id), '', "data-app='project'");?><!--</td>-->
          <td class="c-name w-p15" title='<?php echo $build->name;?>'>
              <?php if($build->branchName) echo "<span class='label label-outline label-badge'>{$build->branchName}</span>"?>
              <?php if($build->name )echo html::a($this->createLink('build', 'view', "build=$build->id"), $build->name, '', "data-app='project'");?>
          </td>
          <td class="c-name text-left w-p10" title='<?php echo $build->product == '99999' ? '无' : $build->productName;?>'><?php echo $build->product == '99999' ? '无' :  $build->productName;?></td>
          <td class="c-name text-left w-p10" title='<?php echo zget($versions,$build->version,'');?>'><?php echo zget($versions,$build->version,'');?></td>

          <!--<td class="c-name text-left" style="width:6%" title='<?php /*echo zget($lang->build->purposeList, $build->purpose, '');*/?>'><?php /*echo zget($lang->build->purposeList, $build->purpose, '');*/?></td>
          <td class="c-name text-left w-p5" title='<?php /*echo zget($lang->build->roundsList, $build->rounds, '');*/?>'><?php /*echo zget($lang->build->roundsList, $build->rounds, '');*/?></td>-->
<!--          <td class="c-url" title="--><?php //echo $build->scmPath?><!--">--><?php // echo strpos($build->scmPath,  'http') === 0 ? html::a($build->scmPath)  : $build->scmPath;?><!--</td>-->
<!--          <td class="c-url" title="--><?php //echo $build->filePath?><!--">--><?php //echo strpos($build->filePath, 'http') === 0 ? html::a($build->filePath) : $build->filePath;?><!--</td>-->
          <td class="c-date w-date"><?php echo $build->createdDate?></td>
            <td class="c-user em" style="width:6%" title='<?php echo zget($users, $build->createdBy);?>'><?php echo zget($users, $build->createdBy);?></td>
            <td class=" em col-xs-1" title='<?php echo zget($status, $build->status);?>' node-val="<?php echo $build->status; ?>" node-isQualityGate="<?php echo $build->isQualityGate;?>"><?php echo zget($status, $build->status);?></td>
          <td class="c-user em" style="width:6%" title='<?php echo zmget($users, $build->dealuser);?>'><?php echo zmget($users, $build->dealuser);?></td>
          <td class="c-actions text-center" style="width:18%">

              <?php
            common::printIcon('build',   'edit', "buildID=$build->id&productID=$build->product&projectID=$projectID", $build, 'list');
            common::printIcon('build', 'deal', "buildID=$build->id", $build, 'list', 'time', '', 'iframe', true,"data-width='1200px'");
            echo "<span class='vertical-divider'></span>";
            if(common::hasPriv('build', 'linkstory') and common::hasPriv('build', 'view') and common::canBeChanged('build', $build) and $build->status != 'wait')
            {
                echo html::a($this->createLink('build', 'view', "buildID=$build->id&type=story&link=true"), "<i class='icon icon-link'></i>", '', "class='btn' title='{$lang->build->linkStory}' data-app='project'");
            }else{
                echo '<button type="button" class="disabled btn" title="' . $lang->build->linkStory . '"><i class="icon-common-suspend disabled icon-link"></i><span class="text">&nbsp' . $lang->build->linkStory .'</span></button>';
            }
            if($build->createdBy == $this->app->user->account and $build->status != 'wait'){
                common::printIcon('testtask', 'create', "applicationID=0&product=$build->product&build=$build->id&projectID=$projectID", $build, 'list', 'bullhorn', '', '', '', "data-app='project'");
            }else{
                echo '<button type="button" class="disabled btn" title="' . $lang->build->submitTest . '"><i class="icon-common-suspend disabled icon-bullhorn"></i><span class="text">&nbsp' . $lang->build->submitTest .'</span></button>';
            }
            $lang->build->view = $lang->project->bug;
            common::printIcon('build', 'view', "buildID=$build->id&type=generatedBug", $build, 'list', 'bug', '', '', '', "data-app='project'");
            echo "<span class='vertical-divider'></span>";
            common::printIcon('build',   'rebuild', "buildID=$build->id&productID=$build->product&projectID=$projectID", $build, 'list','start', '', 'iframe', true);
            common::printIcon('build',   'copy', "buildID=$build->id&productID=$build->product&projectID=$projectID", $build, 'list');

            common::hasPriv('build',  'delete') ?  common::printIcon('build',   'delete', "buildID=$build->id&confirm=no", $build, 'list','trash','hiddenwin') : '';
            common::hasPriv('build',  'ignore') ?  common::printIcon('build',   'ignore', "buildID=$build->id&confirm=no", $build, 'list','ban','hiddenwin') : '';

            /* if(common::hasPriv('build',  'delete', $build))
            {
                $deleteURL = $this->createLink('build', 'delete', "buildID=$build->id&confirm=yes");
                echo html::a("###", '<i class="icon-trash"></i>', '', "onclick='ajaxDelete(\"$deleteURL\", \"buildList\", confirmDelete)' class='btn' title='{$lang->build->delete}'");
            }*/
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      <!-- --><?php /*endforeach;*/?>
      </tbody>
    </table>
  <div class="table-footer">
      <div class="checkbox-primary checkall" onclick="checkall()"><label><?php echo $lang->selectAll?></label></div>
      <div class="table-actions btn-toolbar"><a id="batchedit" onclick="setbatchediturl()"  class="btn " title="批量处理项目制版">批量处理</a></div>
      <?php $pager->show('right', 'pagerjs'); ?>
  </div>
<!--</form>-->
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
        var qualityGateBuildidArr = [];
        $("#buildList tbody input[name='builds[]']:checked").each(function (){
            buildidArr.push($(this).val());
            statusArr.push($(this).closest('tr').find('td:eq(6)').attr('node-val'));
            if($(this).closest('tr').find('td:eq(6)').attr('node-isQualityGate')){
                qualityGateBuildidArr.push($(this).val());
            }
        });
        var buildidCount = buildidArr.length;
        if(buildidCount == 0){
            alert("请选择要处理的项目制版");
            return false;
        }
        var unique = $.unique(statusArr);
        if(unique.length > 1){
            alert("您勾选的制版未处在相同审批节点！");
            return false;
        }else{
            if(($.inArray('waitdeptmanager', statusArr) !== -1) && (buildidCount > 1)){
                alert("待部门负责人审批属于特批制版审批，不允许批量操作！");
                return false;
            }
            if((qualityGateBuildidArr.length > 0) && (buildidCount > 1)){
                var qualityGateBuildidStr = qualityGateBuildidArr.join(',');
                alert("制版ID"+qualityGateBuildidStr+ '中包含质量门禁不能批量处理!');
                return false;
            }

            buildidIdstr = buildidArr.join(",");
            var url = createLink("build","batchDeal","buildID="+buildidIdstr)+"?onlybody=yes";
            //$("#batchedit").attr("href",createLink("build","batchDeal","buildID="+buildidIdstr)+"?onlybody=yes")
            batchAddModalTrigger.show({url:url})
        }

    }
</script>
