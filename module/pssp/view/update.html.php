<?php include '../../common/view/header.html.php';?>
<div id='mainMenu' class='clearfix'>
  <div class="btn-group pull-left">
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-table'>
    <form class='form-ajax' method='post'>
    <table class='table table-bordered has-sort-head table-fixed'>
      <thead>
        <tr>
          <th><?php echo $lang->pssp->processType;?></th>
          <th><?php echo $lang->pssp->processName;?></th>
          <th><?php echo $lang->pssp->activityName;?></th>
          <th><?php echo $lang->pssp->activityReason;?></th>
          <th><?php echo $lang->pssp->result;?></th>
          <th><?php echo $lang->pssp->outputName;?></th>
          <th><?php echo $lang->pssp->outputReason;?></th>
          <th><?php echo $lang->pssp->result;?></th>
        </tr>
      </thead>
      <tbody class='sortable'>
        <?php $groupStarted = false;?>
        <?php foreach($processList as $type => $group):?>
        <?php $processes = $group['processList'];?>
        <?php if(!$groupStarted) echo "<tr>";?>
        <?php if(!$groupStarted) $groupStarted = true;?>
          <td rowspan="<?php echo $group['rows'];?>"><?php echo zget($types, $type);?> </td>

          <?php $processStarted = true;?>
          <?php $processEnded   = false;?>

          <?php foreach($processes as $process):?>
          <?php if(!$processStarted) echo '<tr>';?>

          <td rowspan="<?php echo $process->outputNum ? $process->outputNum : 1;?>"><?php echo $process->name;?></td>
          <?php if(!$processStarted) $processStarted = true;?>

          <?php if(empty($process->activityList)):?>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          </tr>
          <?php continue;?>
          <?php endif;?>

          <?php $isFirstActivity = true;?>
          <?php $activityEnded   = false;?>
          <?php foreach($process->activityList as $activity):?>
          <?php if(!$isFirstActivity) echo '<tr>';?>
          <?php $isFirstActivity = false;?>
          <?php if($activity == end($process->activityList)) $activityEnded = true;?>
          <?php $activeRows = empty($activity->outputList) ? 1 : count($activity->outputList);?>
          <td rowspan='<?php echo $activeRows;?>' title='<?php echo $activity->name;?>'><?php echo $activity->name;?></td>

          <?php echo html::hidden("activity[$activity->id][process]", $process->id);?>
          <?php if($activity->optional == 'yes'):?>
          <td rowspan='<?php echo $activeRows;?>'></td>
          <td rowspan='<?php echo $activeRows;?>'></td>
          <?php else:?>
          <td rowspan='<?php echo $activeRows;?>'><?php echo html::input("activity[$activity->id][reason]", isset($activity->reason) ? $activity->reason : '', "class='form-control'");?></td>
          <td rowspan='<?php echo $activeRows;?>'><?php echo html::radio("activity[$activity->id][result]", $lang->pssp->resultList, zget($activity, 'result', 'yes'));?></td>
          <?php endif;?>

          <?php if(empty($activity->outputList)):?>
          <td></td>
          <td></td>
          <td></td>
          <?php $processStarted = false;?>
          </tr>
          <?php continue;?>
          <?php endif;?>

          <?php $isFirstOutput = true;?>
          <?php $outputEnded   = false;?>
      
          <?php foreach($activity->outputList as $output):?>
          <?php if(!$isFirstOutput) echo '<tr>';?>
          <?php $isFirstOutput = false;?>

          <?php echo html::hidden("output[$output->id][process]", $process->id);?>
          <td title='<?php echo $output->name;?>'><?php echo $output->name;?></td>
          <td><?php echo html::input("output[$output->id][reason]", isset($output->reason) ? $output->reason : '', "class='form-control'");?></td>
          <td><?php echo html::radio("output[$output->id][result]", $lang->pssp->resultList, zget($output, 'result', 'yes'));?></td>
          </tr>
          <?php continue;?>
          <?php endforeach;?>
          <?php endforeach;?>
          <?php endforeach;?>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='8' class='text-center'><?php echo html::submitButton() . ' ' . html::backButton();?></td>
        </tr>
      </tfoot>
    </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
