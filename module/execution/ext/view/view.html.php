<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php $disabled = $project->status == 'closed' ? ' disabled style="pointer-events: none;" ' : '';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
    <?php echo html::a($this->createLink('project', 'execution', "browseType=all&projectID=$execution->project"), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="text" title='<?php echo $execution->name;?>'><?php echo $execution->name;?></span>
      <?php if($execution->version > 1):?>
      <small class='dropdown'>
        <a href='#' data-toggle='dropdown' class='text-muted'><?php echo '#' . $version;?> <span class='caret'></span></a>
        <ul class='dropdown-menu'>
        <?php
        for($i = $execution->version; $i >= 1; $i --)
        {
            $class = $i == $version ? " class='active'" : '';
            echo '<li' . $class .'>' . html::a(inlink('view', "execution=$execution->id&version=$i"), '#' . $i) . '</li>';
        }
        ?>
        </ul>
      </small>
      <?php endif; ?>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->execution->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($execution->desc) ? $execution->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->execution->participants;?></div>
        <div class="detail-content article-content">
          <?php if(empty($participants)):?>
          <div class='text-center text-muted'><?php echo $lang->noData;?></div>
          <?php else:?>
          <div class='detail-content article-content'>
            <table class='table table-hover table-fixed'>
              <thead>
                <tr class='text-center'>
                  <th class='w-80px'><?php echo  $lang->execution->personnel1;?></th>
                  <th class='w-80px'><?php echo  $lang->execution->participants;?></th>
                  <th class='w-80px'><?php echo  $lang->execution->personnel2;?></th>
                  <th class='w-80px'><?php echo  $lang->execution->personnel3;?></th>
                  <th class='w-100px'><?php echo $lang->execution->personnel4;?></th>
                  <th class='w-100px'><?php echo $lang->execution->personnel5;?></th>
                  <th class='w-100px'><?php echo $lang->execution->personnel6;?></th>
                  <th class='w-100px'><?php echo $lang->execution->personnel7;?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($participants as $deptID => $users):?>
                <?php $rowspan = count($users);?>
                <?php foreach($users as $index => $user):?>
                <tr class='text-center'>
                  <?php if($index == 0):?>
                  <td rowspan='<?php echo $rowspan;?>'><?php echo zget($deptMap, $deptID);?></td>
                  <?php endif;?>
                  <td><?php echo $user->realname;?></td>
                  <td><?php echo $user->planDuration;?></td>
                  <td><?php echo $user->realDuration;?></td>
                  <td><?php echo $user->durationDeviation;?></td>
                  <td><?php echo $user->estimate;?></td>
                  <td><?php echo $user->total;?></td>
                  <td><?php echo $user->workloadDeviation;?></td>
                </tr>
                <?php endforeach;?>
                <?php endforeach;?>
              </tbody>
            </table>
          </div>
          <?php endif;?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->execution->taskStatistics;?></div>
        <div class="detail-content article-content">
          <?php if(empty($taskStatistics)):?>
          <div class='text-center text-muted'><?php echo $lang->noData;?></div>
          <?php else:?>
          <div class='detail-content article-content'>
            <table class='table table-hover table-fixed'>
              <thead>
                <tr class='text-center'>
                  <th class='w-60px'><?php echo  $lang->execution->statistics1;?></th>
                  <th class='w-60px'><?php echo  $lang->execution->statistics2;?></th>
                  <th class='w-60px'><?php echo  $lang->execution->statistics3;?></th>
                  <th class='w-60px'><?php echo  $lang->execution->statistics4;?></th>
                  <th class='w-120px'><?php echo $lang->execution->statistics5;?></th>
                  <th class='w-120px'><?php echo $lang->execution->statistics6;?></th>
                </tr>
              </thead>
              <tbody>
                <tr class='text-center'>
                  <td>
                  <?php
                  if($taskStatistics['total'])
                  {
                      echo html::a($this->createLink('execution', 'task', array('id' => $execution->id, 'browseType' => 'all')), $taskStatistics['total']);
                  }
                  else
                  {
                     echo 0;
                  }
                  ?>
                  </td>
                  <td>
                  <?php
                  if($taskStatistics['normal'])
                  {
                      echo html::a($this->createLink('execution', 'task', array('id' => $execution->id, 'browseType' => 'normal')), $taskStatistics['normal']);
                  }
                  else
                  {
                     echo 0;
                  }
                  ?>
                  </td>
                  <td>
                    <?php
                    if($taskStatistics['delayStart'])
                    {
                        echo html::a($this->createLink('execution', 'task', array('id' => $execution->id, 'browseType' => 'delaystart')), $taskStatistics['delayStart']);
                    }
                    else
                    {
                       echo 0;
                    }
                    ?>
                  </td>
                  <td>
                    <?php
                    if($taskStatistics['delayFinish'])
                    {
                        echo html::a($this->createLink('execution', 'task', array('id' => $execution->id, 'browseType' => 'delayfinish')), $taskStatistics['delayFinish']);
                    }
                    else
                    {
                       echo 0;
                    }
                    ?>
                  </td>
                  <td>
                    <?php
                    if($taskStatistics['overflow'])
                    {
                        echo html::a($this->createLink('execution', 'task', array('id' => $execution->id, 'browseType' => 'overflow')), $taskStatistics['overflow']);
                    }
                    else
                    {
                       echo 0;
                    }
                    ?>
                  </td>
                  <td>
                    <?php
                    if($taskStatistics['noOverflow'])
                    {
                        echo html::a($this->createLink('execution', 'task', array('id' => $execution->id, 'browseType' => 'noOverflow')), $taskStatistics['noOverflow']);
                    }
                    else
                    {
                       echo 0;
                    }
                    ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <?php endif;?>
        </div>
      </div>
    </div>
    <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=execution&objectID=$execution->id");?>
    <div class="cell"><?php include '../../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($this->createLink('project', 'execution', "browseType=all&projectID=$execution->project"));?>
        <div class='divider'></div>
        <?php
          if(empty($disabled))
          {
             common::printIcon('execution', 'edit', "executionID=$execution->id", $execution, 'button');
             if($execution->source){
                 if($this->app->user->account == 'admin'){
                     common::printIcon('execution', 'delete', "executionID=$execution->id", $execution, 'button', 'trash', 'hiddenwin');
                 }else{
                     echo '<button type="button" class="disabled btn"  style="pointer-events: unset;" title="' . $lang->execution->delete . '" ><i class="icon-common-delete  icon-trash"></i><span class="text">&nbsp' .'</span></button>';
                 }
             }else{
                 common::printIcon('execution', 'delete', "executionID=$execution->id", $execution, 'button', 'trash', 'hiddenwin');
             }
          }
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->execution->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->execution->name;?></th>
                <td><?php echo $execution->name;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->execution->code;?></th>
                <td><?php echo $execution->code;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->status;?></th>
                <td><?php echo zget($lang->execution->statusList, $execution->status);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->milestone;?></th>
                <td><?php echo zget($lang->execution->milestoneList, $execution->milestone);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->begin;?></th>
                <td><?php echo $execution->begin;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->end;?></th>
                <td><?php echo $execution->end;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->planManhour;?></th>
                <td><?php echo $execution->planDuration;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->deviation;?></th>
                <td><?php echo $execution->planDuration - $execution->planDuration;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->realBegan;?></th>
                <td><?php echo $execution->realBegan;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->realEnd;?></th>
                <td><?php echo $execution->realEnd;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->realDuration;?></th>
                <td><?php echo $execution->realDuration;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->plannedWorkload;?></th>
                <td><?php echo $execution->totalEstimate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->realWorkload;?></th>
                <td><?php echo $execution->totalConsumed;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->workloadDeviation;?></th>
                <td><?php echo $execution->totalConsumed - $execution->totalEstimate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->changeNumber;?></th>
                <td><?php echo $execution->version - 1;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->progress;?></th>
                <td><?php echo $execution->progress;?>%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->execution->actionInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th><?php echo $lang->execution->openedBy;?></th>
                <td><?php echo zget($users, $execution->openedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->openedDate;?></th>
                <td><?php echo substr($execution->openedDate, 0, 11);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->lastEditedBy;?></th>
                <td><?php echo zget($users, $execution->lastEditedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->lastEditedDate;?></th>
                <td><?php if(!helper::isZeroDate($execution->lastEditedDate)) echo substr($execution->lastEditedDate, 0, 11);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->startBy;?></th>
                <td><?php echo zget($users, $execution->startBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->startDate;?></th>
                <td><?php if(!helper::isZeroDate($execution->startDate)) echo substr($execution->startDate, 0, 11);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->finishBy;?></th>
                <td><?php echo zget($users, $execution->finishBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->finishDate;?></th>
                <td><?php if(!helper::isZeroDate($execution->finishDate)) echo substr($execution->finishDate, 0, 11);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->changeBy;?></th>
                <td><?php echo zget($users, $execution->changeBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->changeDate;?></th>
                <td><?php if(!helper::isZeroDate($execution->changeDate)) echo substr($execution->changeDate, 0, 11);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->splitBy;?></th>
                <td><?php echo zget($users, $execution->splitBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->execution->splitDate;?></th>
                <td><?php if(!helper::isZeroDate($execution->splitDate)) echo substr($execution->splitDate, 0, 11);?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
