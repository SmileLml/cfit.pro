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
                      <span class='input-group-addon text-ellipsis'><?php echo $lang->report->accountType;?></span>
                      <?php echo html::select('accountType[]', $lang->user->staffTypeList, $accountType , "class='form-control chosen' multiple='multiple'");?>
                  </div>
              </div>
            <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?></div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($members)):?>
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
            <?php if(common::hasPriv('report', 'exportParticipantWorkload')) echo html::a(inLink('exportParticipantWorkload', array('projectID' => $projectID, 'param' => $param)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-120px text-left'><?php echo $lang->project->blockDeptName;?></th>
                <th class='w-60px text-left'><?php echo $lang->project->blockMember;?></th>
                <th class='w-60px text-left'><?php echo $lang->report->account;?></th>
                <th class='w-60px text-left'><?php echo $lang->report->employeeNumber;?></th>
                <th class='w-60px text-left'><?php echo $lang->report->accountType;?></th>
                <th class="w-80px"><?php echo $lang->project->blockTotal;?></th>
                <th class="w-80px"><?php echo $lang->project->blockPerMonth;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($members as $users):?>
              <?php foreach($users as  $index => $member):?>
              <tr class="text-center">
                <?php if(empty($index)):?>
                <td class='text-left' rowspan="<?php echo count($users);?>"><?php echo $member->deptName;?></td>
                <?php endif;?>
                <td class='text-left'><?php echo $member->realname;?></td>
                <td class='text-left'><?php echo $member->account;?></td>
                <td class='text-left'><?php echo $member->employeeNumber;?></td>
                <td class='text-left'><?php echo zget($lang->user->staffTypeList, $member->staffType,'');?></td>
                <td><?php echo $member->total;?></td>
                <td><?php echo $member->perMonth;?></td>
              </tr>
              <?php endforeach;?>
              <?php endforeach;?>
              <tr class="text-center">
                <td class='text-left'><?php echo $amount['count'];?></td>
                <td class='text-left'><?php echo $amount['user'];?></td>
                <td class='text-left'></td>
                <td class='text-left'></td>
                <td class='text-left'></td>
                <td><?php echo $amount['total'];?></td>
                <td><?php echo $amount['perMonth'];?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
