<?php include '../../common/view/header.html.php';?>
<?php if(common::checkNotCN()):?>
<style>#conditions .col-xs {width: 126px;}</style>
<?php endif;?>
<style>
.form-row{
    margin-bottom: 10px;
}
.picker-select:disabled + .picker .picker-selections{
    background-color: #f5f5f5;
}
.picker-select:disabled + .picker{
    pointer-events: none;
    position: relative;
}
.input-group>.input-group-addon:first-child{
    min-width: 80px;
}
</style>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg'>
    <div class='panel'>
      <div class='panel-heading'>
        <div class='panel-title'><?php echo $lang->qareport->report->select;?></div>
      </div>
      <div class='cell' style='box-shadow: none;'>
        <?php include './commonheader.html.php'?>
      </div>
    </div>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class="with-padding">
        <form action='<?php echo $this->createLink('qareport', 'bugtester', "queryType=search&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID");?>' method='post' id='testerForm'>
          <div class="table-row" id='conditions'>
            <div class='form-row'>
              <div class='col-md-4 col-sm-6 input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->startTime;?></span>
                <?php echo html::input('begin', $begin, "class='form-control form-date'");?>
                <span class='input-group-addon text-ellipsis'><?php echo '~';?></span>
                <?php echo html::input('end', $end, "class='form-control form-date'");?>
              </div>
            </div>
            <div class='form-row'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->deptOptions;?></span>
                <?php echo html::select('dept[]', $depts, $deptList , "class='form-control chosen' multiple='multiple' data-drop-width='auto'");?>
              </div>
            </div>
            <div class='form-row'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->projectOptions;?></span>
                <?php echo html::select('project[]', $projects, $projectList , "class='form-control picker-select' multiple='multiple' data-drop-width='auto'");?>
              </div>
            </div>
            <div class='form-row'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->accountOptions;?></span>
                <?php echo html::select('account[]', $users, $accountList, "class='form-control picker-select' multiple='multiple'");?>
              </div>
            </div>
            <div class='form-row' style="text-align: center;">
              <?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?>
              <?php echo html::resetButton($lang->report->resetQuery, '', 'btn btn-primary');?>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($userInfoList)):?>
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
            <?php if(common::hasPriv('report', 'exportBugTester')) echo html::a($this->createLink('report', 'exportBugTester', array()), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-100px text-left'><?php echo $lang->report->fullname;?></th>
                <th class='w-60px text-left'><?php echo $lang->report->deptOptions;?></th>
                <th class='w-100px text-left'><?php echo $lang->report->participateProject;?></th>
                <th class="w-60px"><?php echo $lang->report->writtenCases;?></th>
                <th class="w-60px"><?php echo $lang->report->executedCases;?></th>
                <th class="w-60px"><?php echo $lang->report->submittedBugs;?></th>
                <th class="w-60px"><?php echo $lang->report->effectiveBugs;?></th>
                <th class="w-60px hidden"><?php echo $lang->report->autoCases;?></th>
                <th class="w-60px hidden"><?php echo $lang->report->automationRate;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($userInfoList as $user):?>
              <tr class="text-center">
                <td class='text-left'><?php echo $user->realname;?></td>
                <td class='text-left'><?php echo zget($depts, $user->dept, '');?></td>
                <?php $projectName = $this->report->calculateProject($projects, $user->projects);?>
                <td class='text-left' title='<?php echo $projectName;?>'><?php echo $projectName;?></td>
                <td><?php echo $user->caseTotal;?></td>
                <td><?php echo $user->runs;?></td>
                <td><?php echo $user->bugTotal;?></td>
                <td><?php echo $user->effectiveTotal;?></td>
                <td class='hidden'><?php echo $user->categories;?></td>
                <td class='hidden'><?php echo $user->rate;?></td>
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
      <?php echo $lang->report->bugTesterTips;?>
    </div>
  </div>
</div>

<?php js::set('emptyDate', $lang->report->emptyDate);?>
<?php js::set('greaterEndDate', $lang->report->greaterEndDate);?>
<?php js::set('selectDeptTips', $lang->report->selectDeptTips);?>
<?php js::set('noUsers', $lang->report->noUsers);?>
<script>
$('#submit').click(function()
{
    var begin = $('#begin').val();
    var end   = $('#end').val();

    if(begin && end && begin > end)
    {
        alert(greaterEndDate);
        return false;
    }
    $('#testerForm').submit();
});

$('#reset').click(function()
{
    $("#begin").attr('value', '');
    $("#end").attr('value', '');

    $("#dept option:selected").removeAttr("selected");
    $('#dept').trigger('chosen:updated');

    $("#project option:selected").removeAttr("selected");
    $('#project').trigger('chosen:updated');

    $("#account option:selected").removeAttr("selected");
    $('#account').val('').trigger('chosen:updated');
});
</script>
<?php include '../../common/view/footer.html.php';?>
