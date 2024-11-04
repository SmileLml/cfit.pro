<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('weekend', $config->execution->weekend);?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->projectplan->initProject;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform' enctype='mulitpart/form'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-150px'><?php echo $lang->projectplan->name;?></th>
            <td colspan='2'><?php echo isset($creation->name) ? $creation->name : $plan->name;?><input type='hidden' value=<?php echo $plan->name;?> name="name"></td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->type;?></th>
            <td><?php echo zget($lang->projectplan->typeList, isset($creation->type) ? $creation->type: $plan->type);?><input type='hidden' value=<?php echo $plan->type;?> name="type"></td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->code;?></th>
            <td><?php echo html::input('code', isset($creation->code) ? $creation->code : $plan->code, "class='form-control'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->projectplan->mark;?></span>
                <?php echo html::input('mark', isset($creation->mark) ? $creation->mark : $plan->mark, "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->PM;?></th>
            <td><?php echo html::select('PM', $users, isset($creation->PM) ? $creation->PM : $this->app->user->account, "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->projectplan->dept;?></span>
                <?php echo html::select('dept', $depts, isset($creation->dept) ? $creation->dept : $this->app->user->dept, "class='form-control chosen'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->source;?></th>
            <td><?php echo html::select('source', $lang->projectplan->sourceList, isset($creation->source) ? $creation->source : '', "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinion->union;?></span>
                <?php echo html::select('union', $lang->opinion->unionList, isset($creation->union) ? $creation->union : '', "class='form-control chosen'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->linkPlan;?></th>
            <td colspan='2'><?php echo html::select('linkPlan[]', $plans, isset($creation->linkPlan) ? $creation->linkPlan : '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->begin;?></th>
            <td><?php echo html::input('begin', isset($creation->begin) ? $creation->begin : '', "class='form-control form-date' onchange='computeWorkDays()'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->projectplan->end;?></span>
                <?php echo html::input('end', isset($creation->end) ? $creation->end : '', "class='form-control form-date' onchange='computeWorkDays()'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->workload;?></th>
            <td>
              <div class='input-group'>
                <?php echo html::input('workload', isset($creation->workload) ? $creation->workload : '', "class='form-control'");?>
                <span class='input-group-addon'><?php echo $lang->projectplan->monthly;?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->background;?></th>
            <td colspan='2'><?php echo html::textarea('background', isset($creation->background) ? $creation->background : '', "class='form-control' placeholder='{$lang->projectplan->backgroundNotice}'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->range;?></th>
            <td colspan='2'><?php echo html::textarea('range', isset($creation->range) ? $creation->range : '', "class='form-control' placeholder='{$lang->projectplan->rangeNotice}'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->goal;?></th>
            <td colspan='2'><?php echo html::textarea('goal', isset($creation->goal) ? $creation->goal : '', "class='form-control' placeholder='{$lang->projectplan->goalNotice}'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->stakeholder;?></th>
            <td colspan='2'><?php echo html::textarea('stakeholder', isset($creation->stakeholder) ? $creation->stakeholder : '', "class='form-control' placeholder='{$lang->projectplan->stakeholderNotice}'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->verify;?></th>
            <td colspan='2'><?php echo html::textarea('verify', isset($creation->verify) ? $creation->verify : '', "class='form-control' placeholder='{$lang->projectplan->verifyNotice}'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::a(inlink('browse'), $lang->goback, '', "class='btn btn-back btn-wide'");?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
