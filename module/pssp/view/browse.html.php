<?php include '../../common/view/header.html.php';?>
<div id='mainMenu' class='clearfix'>
  <div class="btn-group pull-left">
  </div>
  <?php if($projectID):?>
  <div class='pull-right'><?php echo html::a($this->createLink('pssp', 'update', "projectID=$projectID"), $lang->pssp->update, '', "class='btn btn-primary'");?></div>
  <?php endif;?>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-table'>
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
          <td rowspan='<?php echo $activeRows;?>'><?php echo isset($activity->reason) ? $activity->reason : '';?></td>
          <td rowspan='<?php echo $activeRows;?>'><?php echo isset($activity->result) ? zget($lang->pssp->resultList, $activity->result, '') : '';?></td>

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

          <td title='<?php echo $output->name;?>'><?php echo $output->name;?></td>
          <td><?php echo isset($output->reason) ? $output->reason : '';?></td>
          <td><?php echo isset($output->result) ? zget($lang->pssp->resultList, $output->result) : '';?></td>
          </tr>
          <?php continue;?>
          <?php endforeach;?>
          <?php endforeach;?>
          <?php endforeach;?>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
