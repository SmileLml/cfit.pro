<?php include '../../../common/view/header.html.php';?>
<?php js::set('browseType', $browseType);?>
<?php js::set('mode', $mode);?>
<?php js::set('total', $pager->recTotal);?>
<?php js::set('rawMethod', $app->rawMethod);?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php $recTotalLabel = " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
    <?php if($app->rawMethod == 'contribute'):?>
    <?php echo html::a(inlink($app->rawMethod, "mode=$mode&browseType=createdByMe"),  "<span class='text'>{$lang->my->createdByMe}</span>"  . ($browseType == 'createdByMe'  ? $recTotalLabel : '') , '', "class='btn btn-link createdByMe'");?>
    <?php echo html::a(inlink($app->rawMethod, "mode=$mode&browseType=resolvedByMe"), "<span class='text'>{$lang->my->resolvedByMe}</span>" . ($browseType == 'resolvedByMe' ? $recTotalLabel : '') , '', "class='btn btn-link resolvedByMe'");?>
    <?php echo html::a(inlink($app->rawMethod, "mode=$mode&browseType=closedByMe"),   "<span class='text'>{$lang->my->closedByMe}</span>"   . ($browseType == 'closedByMe'   ? $recTotalLabel : '') , '', "class='btn btn-link closedByMe'");?>
    <?php endif;?>
  </div>
</div>
<div id="mainContent" class='main-table'>
    <div class="page-title"><h4>项目周报</h4></div>
  <?php if(empty($planProjectedList)):?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->noData;?></span>
    </p>
  </div>
  <?php else:?>
  <table class="table has-sort-head table-fixed" id='projectList'>
    <thead>
      <tr class=''>
          <th class='w-90px'>
              <?php echo $lang->projectplan->id; ?></th>
          <!--              <th class='w-40px'>--><?php //common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id);?><!--</th>-->
          <th class='w-200px'><?php echo $lang->projectplan->name;?></th>
          <th class='w-70px'><?php echo $lang->projectplan->year;?></th>
          <th class='w-80px'><?php echo $lang->projectplan->code;?></th>
          <th class='w-80px'><?php echo $lang->projectplan->mark;?></th>
          <th class='w-100px'><?php echo $lang->projectplan->status;?></th>
          <th class='w-100px'><?php echo $lang->projectplan->begin;?></th>
          <th class='w-100px'><?php echo $lang->projectplan->end;?></th>
          <th class='w-150px'><?php echo $lang->projectplan->outsides;?></th>
          <th class='text-left w-80px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($planProjectedList as $plan):?>

          <tr>
              <td>
                  <?php
                      echo  sprintf('%03d', $plan->id);
                  ?>
                  <?php ?>
              </td>
              <!--              <td>--><?php //printf('%03d', $plan->id);?><!--</td>-->
              <td class="text-ellipsis" title=<?php echo $plan->name;?>><?php echo common::hasPriv('projectplan', 'view') ? html::a($this->createLink('projectplan', 'view', "projectplanID=$plan->id"), $plan->name) : $plan->name;?></td>
              <td><?php echo $plan->year;?></td>
              <td class="text-ellipsis" title=<?php echo $plan->code;?>><?php echo $plan->code;?></td>
              <td class="text-ellipsis"><?php echo $plan->mark;?></td>
              <td class="text-ellipsis"><?php echo zget($lang->projectplan->statusList, $plan->status, '');?></td>
              <td class="text-ellipsis"><?php if(!helper::isZeroDate($plan->begin)) echo $plan->begin;?></td>
              <td class="text-ellipsis"><?php if(!helper::isZeroDate($plan->end)) echo $plan->end;?></td>
              <td class="text-ellipsis" title=<?php echo $plan->outsides;?>><?php echo $plan->outsides;?></td>
              <td class='c-actions'>
                  <?php
                  if($plan->weeklyreportID){
                      ?>
                      <button type="button" class="disabled btn" style="pointer-events: unset;"><i class="icon-weeklyreport-create disabled icon-expand-alt" title="新建周报" data-app="project"></i></button>
                      <a href='<?php echo helper::createLink("weeklyreport","edit","reportId=".$plan->weeklyreportID,"html#app=project"); ?>' class="btn" title="编辑周报" data-app="project"><i class="icon-weeklyreport-edit icon-edit"></i></a>




                      <?php
                  }else{
                      ?>
                      <a href='<?php echo helper::createLink("weeklyreport","create","reportId=".$plan->project,"html#app=project"); ?>' class="btn" title="新建周报" data-app="project"><i class="icon-weeklyreport-create icon-expand-alt"></i></a>

                      <button type="button" class="disabled btn" style="pointer-events: unset;"><i class="icon-weeklyreport-index disabled icon-edit" title="编辑周报" data-app="project"></i></button>

                      <?php
                  }
                  ?>

                  <?php

                  ?>


              </td>
          </tr>
      <?php endforeach;?>
    </tbody>
  </table>
      <div class="table-footer">


          <?php $pager->show('right', 'pagerjs');?>
      </div>
  <?php endif;?>


    <div class="page-title"><h4>年度计划跟踪</h4></div>
    <?php if(empty($planPssList)):?>
        <div class="table-empty-tip">
            <p>
                <span class="text-muted"><?php echo $lang->noData;?></span>
            </p>
        </div>
    <?php else:?>
        <table class="table has-sort-head table-fixed" id='projectList2'>
            <thead>
            <tr class=''>
                <th class='w-90px'>
                    <?php echo $lang->projectplan->id; ?></th>
                <!--              <th class='w-40px'>--><?php //common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id);?><!--</th>-->
                <th class='w-200px'><?php echo $lang->projectplan->name;?></th>
                <th class='w-70px'><?php echo $lang->projectplan->year;?></th>
                <th class='w-80px'><?php echo $lang->projectplan->code;?></th>
                <th class='w-80px'><?php echo $lang->projectplan->mark;?></th>
                <th class='w-100px'><?php echo $lang->projectplan->status;?></th>
                <th class='w-100px'><?php echo $lang->projectplan->begin;?></th>
                <th class='w-100px'><?php echo $lang->projectplan->end;?></th>
                <th class='w-150px'><?php echo $lang->projectplan->outsides;?></th>
                <th class='text-left w-80px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($planPssList as $plan):?>

                <tr>
                    <td>
                        <?php

                            echo  sprintf('%03d', $plan->id);

                        ?>
                        <?php ?>
                    </td>
                    <!--              <td>--><?php //printf('%03d', $plan->id);?><!--</td>-->
                    <td class="text-ellipsis" title=<?php echo $plan->name;?>><?php echo common::hasPriv('projectplan', 'view') ? html::a($this->createLink('projectplan', 'view', "projectplanID=$plan->id"), $plan->name) : $plan->name;?></td>
                    <td><?php echo $plan->year;?></td>
                    <td class="text-ellipsis" title=<?php echo $plan->code;?>><?php echo $plan->code;?></td>
                    <td class="text-ellipsis"><?php echo $plan->mark;?></td>
                    <td class="text-ellipsis"><?php echo zget($lang->projectplan->statusList, $plan->status, '');?></td>
                    <td class="text-ellipsis"><?php if(!helper::isZeroDate($plan->begin)) echo $plan->begin;?></td>
                    <td class="text-ellipsis"><?php if(!helper::isZeroDate($plan->end)) echo $plan->end;?></td>
                    <td class="text-ellipsis" title=<?php echo $plan->outsides;?>><?php echo $plan->outsides;?></td>
                    <td class='c-actions'>
                        <button type="button" data-type="iframe" data-url='<?php echo helper::createLink("projectplan","editStatus","planID=".$plan->id,"html","true"); ?>' class="btn" data-toggle="modal" title="修改状态" data-app="platform"><i class="icon-projectplan-editStatus icon-edit"></i></button>

                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    <?php endif;?>
</div>
<script>
$('.' + browseType).addClass('btn-active-text');
</script>
<?php include '../../../common/view/footer.html.php';?>
