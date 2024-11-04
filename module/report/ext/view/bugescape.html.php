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
        <form action='<?php echo $this->createLink('report', 'bugescape', "queryType=search&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID");?>' method='post'>
          <div class="table-row" id='conditions'>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->projectOptions;?></span>
                <?php echo html::select('project[]', $projects, $projectList , "class='form-control picker-select' multiple='multiple'");?>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->deptOptions;?></span>
                <?php echo html::select('dept[]', $depts, $deptList , "class='form-control chosen' multiple='multiple'");?>
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
    <?php if(empty($projectDataList)):?>
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
            <?php if(common::hasPriv('report', 'exportBugEscape')) echo html::a(inLink('exportBugEscape', array()), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-100px text-left'><?php echo $lang->report->projectName;?></th>
                <th class='w-100px text-left'><?php echo $lang->report->deptOptions;?></th>
                <th class="w-60px"><?php echo $lang->report->defectBug;?></th>
                <th class="w-60px"><?php echo $lang->report->escapeBug;?></th>
                <th class="w-60px"><?php echo $lang->report->escapeRate;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($projectDataList as $project):?>
              <tr class="text-center">
                <td class='text-left'><?php echo $project->name;?></td>
                <td class='text-left'><?php echo zget($depts, $project->dept, '');?></td>
                <td><?php echo $project->defectTotal;?></td>
                <td><?php echo $project->bugTotal;?></td>
                <td><?php echo $project->rate;?></td>
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
      <?php echo $lang->report->bugEscapeTips;?>
    </div>
  </div>
</div>
<script>
$('#reset').click(function()
{
  $("#dept option:selected").removeAttr("selected");
  $('#dept').trigger('chosen:updated');

  $("#project option:selected").removeAttr("selected");
  $('#project').trigger('chosen:updated');
});
</script>
<?php include '../../../common/view/footer.html.php';?>
