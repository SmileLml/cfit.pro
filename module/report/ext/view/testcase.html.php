<?php include '../../../common/view/header.html.php';?>
<style>
#mainContent > .side-col.col-lg{width: 235px}
.hide-sidebar #sidebar{width: 0 !important}
.form-actions {
  margin-top: 16px;
}
.table-fixed td, .table-fixed th{
    white-space: break-spaces;
}
</style>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include '../../view/blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class='with-padding'>
        <form id="testcaseForm">
            <div id='conditions'>
              <div class='table-row'>
                <div class='w-220px col-md-3 col-sm-6'>
                    <div class='input-group'>
                    <span class='input-group-addon text-ellipsis'><?php echo $lang->report->applicationOptions;?></span>
                    <?php echo html::select('applicationID', $applications, $applicationID, 'class="form-control chosen"')?>
                    </div>
                </div>
                <div class='w-220px col-md-3 col-sm-6'>
                    <div class='input-group'>
                    <span class='input-group-addon text-ellipsis'><?php echo $lang->report->productOptions;?></span>
                    <?php echo html::select('productID', $products, $productID, 'class="form-control chosen"')?>
                    </div>
                </div>
                <div class='w-220px col-md-3 col-sm-6'>
                    <div class='input-group'>
                    <span class='input-group-addon text-ellipsis'><?php echo $lang->report->projectOptions;?></span>
                    <?php echo html::select('projectID', $projects, $projectID, 'class="form-control chosen"')?>
                    </div>
                </div>
              </div>
              <div class='table-row form-actions' style="text-align: center;">
                <div class="col-md-12 col-sm-12">
                  <?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?>
                  <?php echo html::resetButton($lang->report->resetQuery, '', 'btn btn-primary');?>
                </div>
              </div>
            </div>
        </form>
      </div>
    </div>
    <?php if(empty($modules)):?>
    <div class="cell">
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
      </div>
    </div>
    <?php else:?>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title"><?php echo $title;?></div>
          <nav class="panel-actions btn-toolbar"></nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered tablesorter table-fixed active-disabled' id="testCaseList">
            <thead>
              <tr class='colhead text-center'>
                <th><?php echo $lang->report->applicationOptions;?></th>
                <th><?php echo $lang->report->productOptions;?></th>
                <th><?php echo $lang->report->projectOptions;?></th>
                <th><?php echo $lang->report->module;?></th>
                <th><?php echo $lang->report->case->total;?></th>
                <th><?php echo $lang->testcase->resultList['pass'];?></th>
                <th><?php echo $lang->testcase->resultList['fail'];?></th>
                <th><?php echo $lang->testcase->resultList['blocked'];?></th>
                <th><?php echo $lang->report->case->run;?></th>
                <th><?php echo $lang->report->case->passRate;?></th>
              </tr>
            </thead>
            <?php if($modules):?>
            <tbody>
              <?php $allTotal = $allPass = $allFail = $allBlocked = $allRun = 0;?>
              <?php foreach($modules as $module):?>
              <tr class="text-center">
                <?php
                $allTotal += $module->total;
                $allPass += $module->pass;
                $allFail += $module->fail;
                $allBlocked += $module->blocked;
                $allRun += $module->run;
                ?>
                <td><?php echo $module->applicationName;?></td>
                <td><?php echo $module->productName;?></td>
                <td><?php echo $module->projectName;?></td>
                <td><?php echo $module->name;?></td>
                <td><?php echo $module->total;?></td>
                <td><?php echo $module->pass;?></td>
                <td><?php echo $module->fail;?></td>
                <td><?php echo $module->blocked;?></td>
                <td><?php echo $module->run;?></td>
                <td><?php echo $module->run ? round(($module->pass / $module->run) * 100, 2) . '%' : 'N/A';?></td>
              </tr>
              <?php endforeach;?>
              <tr class="text-center">
                <td colspan="4"><?php echo $lang->report->total;?></td>
                <td><?php echo $allTotal;?></td>
                <td><?php echo $allPass;?></td>
                <td><?php echo $allFail;?></td>
                <td><?php echo $allBlocked;?></td>
                <td><?php echo $allRun;?></td>
                <td><?php echo $allRun ? round(($allPass / $allRun) * 100, 2) . '%' : 'N/A';?></td>
              </tr>
            </tbody>
            <?php endif;?>
          </table>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<script>
$('#submit').click(function(){
    var applicationID = $('#applicationID').val();
    var productID     = $('#productID').val();
    var projectID     = $('#projectID').val();

    if(!applicationID && !productID && !projectID)
    {
        alert('<?php echo $lang->report->requiredTestcaseForm;?>');
        return false;
    }

    var link = createLink('report', 'testcase', 'applicationID=' + applicationID + '&productID=' + productID + '&projectID=' + projectID);
    location.href = link;

    return false
});
$('#reset').click(function()
{
    $("#applicationID option:selected").removeAttr("selected");
    $('#applicationID').trigger('chosen:updated');

    $("#productID option:selected").removeAttr("selected");
    $('#productID').trigger('chosen:updated');

    $("#projectID option:selected").removeAttr("selected");
    $('#projectID').trigger('chosen:updated');

});
</script>
<?php if(common::hasPriv('report', 'reportExport')):?>
<script>
$(function()
{
    var $link = $(<?php echo json_encode(html::a(inlink('reportExport', "type=testcase&params=". base64_encode("applicationID={$applicationID}&productID={$productID}&projectID={$projectID}")), $lang->export, '', "class='iframe btn btn-primary btn-sm'"));?>).modalTrigger({iframe:true, transition:'elastic'});
    $('.main-col .cell .panel .panel-heading .panel-actions').append($link);
})
</script>
<?php endif;?>
<?php include '../../../common/view/footer.html.php';?>
