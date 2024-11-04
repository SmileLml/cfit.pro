<?php include '../../../common/view/header.html.php';?>
<?php if(common::checkNotCN()):?>
<style>#conditions .col-xs {width: 126px;}</style>
<?php endif;?>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include './blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class="with-padding">
        <form method='post'>
          <div class="table-row" id='conditions'>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->report->begin;?></span>
                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='form-control form-date' onchange='changeDate(this.value, \"$end\")'");?></div>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->report->end;?></span>
                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control form-date' onchange='changeDate(\"$begin\", this.value)'");?></div>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->project->blockMember;?></span>
                <?php echo html::select('account[]', $participants, $account , "class='form-control chosen' multiple='multiple'");?>
              </div>
            </div>
            <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?></div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($workloadList)):?>
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
          <nav class="panel-actions btn-toolbar">
            <?php if(common::hasPriv('report', 'exportPersonnelworkload')) echo html::a(inLink('exportPersonnelworkload', array('projectID' => $projectID, 'param' => $param)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-left'>
                <th class='w-35px'><?php echo $lang->report->personnelID;?></th>
                <th class='w-30px'><?php echo $lang->report->personnelStageID;?></th>
                <th class='w-100px'><?php echo $lang->report->personnelStageName;?></th>
                <th class='w-30px'><?php echo $lang->report->personnelTaskID;?></th>
                <th class='w-100px'><?php echo $lang->report->personnelTaskName;?></th>
                <th class='w-35px'><?php echo $lang->report->personnelRealname;?></th>
                <th class='w-50px'><?php echo $lang->report->account;?></th>
                <th class='w-50px '><?php echo $lang->report->employeeNumber;?></th>
                <th class='w-70px'><?php echo $lang->report->personnelContent;?></th>
                  <th class='w-35px'><?php echo $lang->report->personnelConnectDept;?></th>
                <th class='w-50px'><?php echo $lang->report->personnelDate;?></th>
                  <th class='w-70px'><?php echo $lang->report->personnelOpenDate;?></th>
                <th class='w-60px'><?php echo $lang->report->personnelConsumed;?></th>
                <th class='w-60px'><?php echo $lang->report->personnelLeft;?></th>
                <th class='w-30px'><?php echo $lang->report->personnelProgress;?></th>
                <th class='w-40px'><?php echo $lang->report->personnelStart;?></th>
                <th class='w-40px'><?php echo $lang->report->personnelDeadline;?></th>
            </thead>
            <tbody>
              <?php foreach($workloadList as $workload):?>
                <tr>
                  <td><?php echo $workload->id;?></td>
                  <td><?php echo $workload->execution;?></td>
                  <td class='text-ellipsis' title='<?php echo $workload->executionName;?>'><?php echo $workload->executionName;?></td>
                  <td><?php echo $workload->objectID;?></td>
                  <td class='text-ellipsis' title='<?php echo $workload->taskName;?>'><?php echo $workload->taskName;?></td>
                  <td><?php echo $workload->realname;?></td>
                  <td><?php echo $workload->account;?></td>
                  <td><?php echo $workload->employeeNumber;?></td>
                  <td class='text-ellipsis' title='<?php echo $workload->work;?>'><?php echo $workload->work;?></td>
                    <td><?php echo $depts[$workload->deptID];?></td>
                  <td><?php echo $workload->date;?></td>
                    <td><?php echo $workload->realDate;?></td>
                  <td><?php echo $workload->consumed;?></td>
                  <td><?php echo $workload->left;?></td>
                  <td><?php echo $workload->progress;?>%</td>
                  <td><?php echo $workload->estStarted;?></td>
                  <td><?php echo $workload->deadline;?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
