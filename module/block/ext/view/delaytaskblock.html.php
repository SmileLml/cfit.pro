<?php if(empty($tasks)): ?>
<div class='empty-tip'><?php echo $lang->block->emptyTip;?></div>
<?php else:?>
<style>
.block-delaytask .table-delaytask a{color:#0c60e1}
.block-delaytask .stage {position: absolute; top: 14px; left: 80px;color: red}
</style>
<span class='stage text-muted'>（共<?php echo $total;?>条）</span>
<div class='panel-body has-table scrollbar-hover'>
  <table class='table table-datatable table-bordered table-condensed table-striped table-fixed table-hover table-delaytask  <?php if(!$longBlock) echo 'block-sm';?>'>
    <thead>
    <tr>
        <th class='w-100px'><?php echo $lang->project->blockStageName;?></th>
        <th class='w-50px'><?php  echo $lang->task->common;?>ID</th>
        <th class='w-120px'><?php echo $lang->task->name;?></th>
        <?php if($longBlock):?>
        <th class='w-80px'><?php echo  $lang->project->taskBegin;?></th>
        <th class='w-80px'><?php echo  $lang->task->realStarted;?></th>
        <th class='w-80px'><?php echo  $lang->project->taskEnd;?></th>
        <th class='w-80px'><?php echo  $lang->task->finishedDate;?></th>
        <th class='w-80px'><?php echo  $lang->task->assignedTo;?></th>
        <th class='w-80px'><?php echo  $lang->project->status;?></th>
        <?php endif;?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($tasks as $task):?>
    <?php $stageName = zget($stages, $task->execution, '');?>
        <tr>
          <td class='text-left text-ellipsis' title='<?php echo $stageName;?>'><?php echo $stageName;?></td>
          <td class='text-left text-ellipsis'><?php echo html::a(helper::createLink('task', 'view', 'task=' . $task->id), sprintf('%03d', $task->id));?></td>
          <td class='text-left text-ellipsis' title='<?php echo $task->name?>'><?php echo html::a(helper::createLink('task', 'view', 'task=' . $task->id), $task->name);?></td>
          <?php if($longBlock):?>
          <td><?php echo $task->estStarted;?></td>
          <td><?php echo helper::isZeroDate($task->realStarted) ? '' : substr($task->realStarted, 0, 10);?></td>
          <td><?php echo $task->deadline;?></td>
          <td><?php echo helper::isZeroDate($task->finishedDate) ? '' : substr($task->finishedDate, 0, 10);?></td>
          <td><?php echo zget($members,$task->assignedTo);?></td>
          <td><?php echo zget($this->lang->project->featureBar, $task->status);?></td>
          <?php endif;?>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>
<?php endif;?>

