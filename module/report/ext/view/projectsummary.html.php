<?php include '../../../common/view/header.html.php';?>
<?php if(common::checkNotCN()):?>
<style>#conditions .col-xs {width: 126px;}</style>
<?php endif;?>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include './blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
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
            <?php if(common::hasPriv('report', 'exportProjectStageSummary')) echo html::a(inLink('exportProjectStageSummary', array('projectID' => $projectID)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-200px text-left'><?php echo $lang->execution->name;?></th>

                <th class="w-100px"><?php echo $lang->report->plannedStartDate;?></th>
                <th class="w-100px"><?php echo $lang->report->actualStartDate;?></th>
                <th class="w-80px"><?php echo $lang->report->plannedEndDate;?></th>
                <th class="w-80px"><?php echo $lang->report->actualEndDate;?></th>
                <th class="w-60px"><?php echo $lang->report->plannedWorkload;?></th>
                <th class="w-60px"><?php echo $lang->report->actualWorkload;?></th>

                <th class="w-70px"><?php echo $lang->execution->taskCount;?></th>
                <th class="w-70px"><?php echo $lang->report->titleList['staff'];?></th>
                <th class="w-100px"><?php echo $lang->report->titleList['dv'];?></th>
                <th class="w-100px"><?php echo $lang->report->titleList['dvrate'];?></th>
              </tr>
            </thead>
            <tbody>
              <?php $total = 0;foreach($stages as $stage):?>
              <tr class="text-center">
                <?php  $stage->grade == 1 ? $total += zget($evList, $stage->id, 0): 0;?>
                <?php $extraTitle = $stage->grade == 2 ? '&nbsp;&nbsp;[子]&nbsp;' : '';?>
                <td class='text-left' title="<?php echo $stage->name;?>"><?php echo $extraTitle . $stage->name;?></td>
                <td><?php echo $stage->begin;?></td>
                <td><?php echo $stage->realBegan;?></td>
                <td><?php echo $stage->end;?></td>
                <td><?php echo $stage->realEnd;?></td>
                <?php $pv = zget($pvList, $stage->id, 0);?>
                <?php $ev = zget($evList, $stage->id, 0);?>
                <td><?php echo $pv;?></td>
                <td><?php echo $ev;?></td>
                <td><?php echo $stage->tasks;?></td>
                <td><?php echo zget($staffList, $stage->id, 0);?></td>

                <td><?php echo number_format(($ev - $pv),2);?></td>
                <td>
                <?php
                if($pv == 0)
                {
                    echo '0.00%';
                }
                else
                {
                    $dvrate = ($ev - $pv) / $pv * 100;
                    echo sprintf('%.2f', $dvrate) . '%';
                }
                ?>
                </td>
              </tr>
              <?php endforeach;?>
            <tr>
                <td class="text-center"> <b><?php echo $lang->report->actualWorkloadTotal?></b></td>
                <td colspan="10" class="text-center"> <b><?php echo $total?></b></td>
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
