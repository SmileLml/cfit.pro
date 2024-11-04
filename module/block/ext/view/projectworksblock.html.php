<?php if(empty($members)): ?>
<div class='empty-tip'><?php echo $lang->block->emptyTip;?></div>
<?php else:?>
<div class='panel-body has-table scrollbar-hover'>
  <table class='table table-datatable table-bordered table-condensed table-striped table-fixed table-hover  <?php if(!$longBlock) echo 'block-sm';?>'>
    <thead>
    <tr>
        <th class='w-100px' title="<?php echo $lang->project->blockDeptName;?>"><?php echo $lang->project->blockDeptName;?></th>
        <th class='w-100px' title="<?php echo $lang->project->blockMember;?>"><?php echo $lang->project->blockMember;?></th>
        <th class='w-80px' title="<?php echo $lang->project->blockLast7day;?>"><?php echo $lang->project->blockLast7day;?></th>
        <?php if($longBlock):?>
        <th class='w-80px' title="<?php echo  $lang->project->blockLastMonth;?>"><?php echo  $lang->project->blockLastMonth;?></th>
        <th class='w-80px' title="<?php echo  $lang->project->blockCurrentMonth;?>"><?php echo  $lang->project->blockCurrentMonth;?></th>
        <th class='w-80px' title="<?php echo  $lang->project->blockTotal;?>"><?php echo  $lang->project->blockTotal;?></th>
        <th class='w-80px' title="<?php echo  $lang->project->blockPerMonth;?>"><?php echo  $lang->project->blockPerMonth;?></th>
        <?php endif;?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($members as $deptUser):?>
      <?php $colspan = count($deptUser);?>
      <?php foreach($deptUser as $index => $user):?>
        <tr>
          <?php if(empty($index)):?>
          <td rowspan="<?php echo $colspan;?>" title="<?php echo $user->deptName;?>"><?php echo $user->deptName;?></td>
          <?php endif;?>
          <td><?php echo $user->realname;?></td>
          <td><?php echo $user->last7day;?></td>
          <?php if($longBlock):?>
          <td><?php echo $user->lastMonth;?></td>
          <td><?php echo $user->currentMonth;?></td>
          <td><?php echo $user->total;?></td>
          <td><?php echo $user->perMonth;?></td>
          <?php endif;?>
        </tr>
      <?php endforeach;?>
    <?php endforeach;?>
      <tr>
        <td><?php echo $amount['count'];?></td>
        <td><?php echo $amount['user'];?></td>
        <td><?php echo $amount['last7day'];?></td>
        <?php if($longBlock):?>
        <td><?php echo $amount['lastMonth'];?></td>
        <td><?php echo $amount['currentMonth'];?></td>
        <td><?php echo $amount['total'];?></td>
        <td><?php echo $amount['perMonth'];?></td>
        <?php endif;?>
      </tr>
    </tbody>
  </table>
</div>
<?php endif;?>

