<?php include '../../../common/view/header.html.php';?>
<?php if(common::checkNotCN()):?>
<style>#conditions .col-xs {width: 126px;}</style>
<?php endif;?>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include '../../../report/view/blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class="with-padding">
        <form action='<?php echo $this->createLink('report', 'bugdiscovery', "queryType=search&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID");?>' method='post'>
          <div class="table-row" id='conditions'>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->accountOptions;?></span>
                <?php echo html::select('account[]', $users, $accountList, "class='form-control chosen' multiple='multiple' data-drop-width='auto'");?>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->deptOptions;?></span>
                <?php echo html::select('dept[]', $depts, $deptList, "class='form-control chosen' multiple='multiple' data-drop-width='auto'");?>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->projectOptions;?></span>
                <?php echo html::select('project[]', $projects, $projectList, "class='form-control picker-select' multiple='multiple' data-drop-width='auto'");?>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->productOptions;?></span>
                <?php echo html::select('product[]', $products, $productList, "class='form-control chosen' multiple='multiple' data-drop-width='auto'");?>
              </div>
            </div>
            <div class='col-md-3 col-sm-6'>
            <?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?>
            <?php echo html::resetButton($lang->report->resetQuery, '', 'btn btn-primary');?>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($userList)):?>
    <div class="cell">
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
      </div>
    </div>
    <?php else:?>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title">
            <div class="table-row" id='conditions'>
              <div class="col-xs"><?php echo $title;?></div>
            </div>
          </div>
          <nav class="panel-actions btn-toolbar" style="z-index: 1;">
            <?php if(common::hasPriv('report', 'exportBugDiscovery')) echo html::a(inLink('exportBugDiscovery', array()), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-100px text-left'><?php echo $lang->report->deptOptions;?></th>
                <th class='w-60px text-left'><?php echo $lang->report->accountOptions;?></th>
                <th class='w-100px text-left'><?php echo $lang->report->projectOptions;?></th>
                <th class="w-60px"><?php echo $lang->report->discoveryBug;?></th>
                <th class="w-60px"><?php echo $lang->report->discoveryBugTest;?></th>
                <th class="w-60px"><?php echo $lang->report->defectUAT;?></th>
                <th class="w-60px"><?php echo $lang->report->discoveryBugRate;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($userList as $user):?>
              <tr class="text-center">
                <td class='text-left'><?php echo zget($depts, $user->dept, '');?></td>
                <td class='text-left'><?php echo $user->realname;?></td>
                <?php $projectName = $this->report->calculateProject($projects, $user->projects);?>
                <td class='text-left' title='<?php echo $projectName;?>'><?php echo $projectName;?></td>
                <td><?php echo $user->createBugTotal;?></td>
                <td><?php echo $user->discoveryBugTotal;?></td>
                <td><?php echo $user->defectTotal;?></td>
                <td><?php echo $this->report->calculatePercentage($user->createBugTotal, $user->discoveryBugTotal + $user->createBugTotal + $user->defectTotal);?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
        </div>
      </div>
    </div>
    <?php endif;?>
    <div class='cell'>
      <?php echo $lang->report->bugDiscoveryTips;?>
    </div>
  </div>
</div>
<script>
$('#reset').click(function()
{
  $("#account option:selected").removeAttr("selected");
  $('#account').val('').trigger('chosen:updated');

  $("#dept option:selected").removeAttr("selected");
  $('#dept').trigger('chosen:updated');

  $("#project option:selected").removeAttr("selected");
  $('#project').trigger('chosen:updated');

  $("#product option:selected").removeAttr("selected");
  $('#product').trigger('chosen:updated');
});
</script>
<?php include '../../../common/view/footer.html.php';?>
