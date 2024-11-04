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
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->execution->name;?></span>
                <?php echo html::select('stage[]', $queryStages, $stage , "class='form-control chosen' multiple='multiple'");?>
              </div>
            </div>
            <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?></div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($stages)):?>
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
            <?php if(common::hasPriv('report', 'exportstageparticipantWorkload')) echo html::a(inLink('exportstageparticipantWorkload', array('projectID' => $projectID, 'param' => $param)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-120px text-left'><?php echo $lang->execution->name;?></th>
                <th class='w-120px text-left'><?php echo $lang->project->blockDeptName;?></th>
                <th class='w-60px text-left'><?php echo $lang->project->blockMember;?></th>
                <th class='w-60px text-left'><?php echo $lang->report->account;?></th>
                <th class='w-60px text-left'><?php echo $lang->report->employeeNumber;?></th>
                <th class="w-80px"><?php echo $lang->project->blockTotal;?></th>
                <th class="w-80px"><?php echo $lang->project->blockPerMonth;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($stages as $stage):?>
                <?php if($stage->parent == 0):?>
                <tr>
                  <td><?php echo $stage->name;?></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><?php echo isset($stage->total) ? $stage->total : '';?></td>
                  <td><?php echo isset($stage->perMonth) ? $stage->perMonth :'';?></td>
                </tr>
                <?php else:?>
                  <?php if($rowspanChild[$stage->id]['dept'] == 1 and $rowspanChild[$stage->id]['user'] == 1):?>
                  <tr>
                    <td><?php echo '&nbsp;&nbsp;[子]&nbsp;' . $stage->name;?></td>
                    <?php foreach($workloadList[$stage->id] as $deptID => $users):?>
                      <?php foreach($users as $index => $user):?>
                      <td><?php echo zget($deptMap, $deptID);?></td>
                      <td><?php echo $user->realname;?></td>
                      <td ><?php echo $user->account;?></td>
                      <td ><?php echo $user->employeeNumber;?></td>
                      <td><?php echo $user->total;?></td>
                      <td><?php echo $user->perMonth;?></td>
                      <?php endforeach;?>
                    <?php endforeach;?>
                  </tr>
                  <?php elseif($rowspanChild[$stage->id]['dept'] >= 1):?>
                    <?php $rowspanTotal = 1;
                    if($rowspanChild[$stage->id]['user'] > 1) $rowspanTotal += $rowspanChild[$stage->id]['user'];
                    ?>
                    <?php $i = 0;?>
                    <?php if($i == 0):?>
                      <td rowspan='<?php if($rowspanTotal > 1) echo $rowspanTotal;?>'><?php echo '&nbsp;&nbsp;[子]&nbsp;' . $stage->name;?></td>
                    <?php endif;?>
                    <?php $i++;?>
                    <?php foreach($workloadList[$stage->id] as $deptID => $users):?>
                    <tr>
                      <td rowspan='<?php echo $rowspanDept = count($users);?>'><?php echo zget($deptMap, $deptID);?></td>
                      <?php foreach($users as $index => $user):?>
                      <td><?php echo $user->realname;?></td>
                      <td ><?php echo $user->account;?></td>
                      <td ><?php echo $user->employeeNumber;?></td>
                      <td><?php echo $user->total;?></td>
                      <td><?php echo $user->perMonth;?></td>
                      <?php if($index == 0) break;?>
                      <?php endforeach;?>
                    </tr>
                    <?php foreach($users as $index => $user):?>
                    <?php if($index == 0) continue;?>
                    <tr>
                      <td><?php echo $user->realname;?></td>
                      <td ><?php echo $user->account;?></td>
                      <td ><?php echo $user->employeeNumber;?></td>
                      <td><?php echo $user->total;?></td>
                      <td><?php echo $user->perMonth;?></td>
                    </tr>
                    <?php endforeach;?>
                    <?php endforeach;?>
                  <?php elseif($rowspanChild[$stage->id]['dept'] == 0):?>
                  <tr>
                    <td><?php echo '&nbsp;&nbsp;[子]&nbsp;' . $stage->name;?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <?php endif;?>
                <?php endif;?>
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
