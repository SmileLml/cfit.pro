<?php include '../../../common/view/header.html.php'?>
<?php js::set('mode', $mode);?>
<?php js::set('total', $pager->recTotal);?>
<?php js::set('rawMethod', $app->rawMethod);?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->my->myReviewList as $key => $type):?>
    <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
    <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
    <?php endforeach;?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <?php if(empty($reviewList)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='myReviewForm'>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php
      $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
      ?>
        <table class='table has-sort-head' id='reviewList'>
          <thead>
            <tr>
              <th class='w-40px'><?php echo $lang->projectplan->id;?></th>
                <th class='w-80px'><?php echo $lang->projectplan->planCode; ?></th>
              <th class='w-180px'><?php echo $lang->projectplan->name;?></th>
              <th class='w-70px'><?php echo $lang->projectplan->code;?></th>
              <th class='w-80px'><?php echo $lang->projectplan->mark;?></th>
                <th class='w-40px'><?php echo $lang->projectplan->year;?></th>

              <th class='w-80px'><?php echo $lang->projectplan->begin;?></th>
              <th class='w-80px'><?php echo $lang->projectplan->end;?></th>
              <th class='w-120px'><?php echo $lang->projectplan->outsides;?></th>
                <th class='w-60px'><?php echo $lang->projectplan->status;?></th>
                <th class='w-60px'><?php echo $lang->projectplan->insideStatus; ?></th>
                <th class='w-50px'><?php echo $lang->projectplan->pending; ?></th>
              <th class='text-left w-120px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($reviewList as $plan):?>
            <tr>
              <td><?php printf('%03d', $plan->id);?></td>
              <td><?php echo $plan->planCode ;?></td>
              <td class="text-ellipsis" title=<?php echo $plan->name;?>><?php echo common::hasPriv('projectplansh', 'view') ? html::a($this->createLink('projectplan', 'view', "projectplanID=$plan->id"), $plan->name) : $plan->name;?></td>
              <td title=<?php echo $plan->code;?>><?php echo $plan->code;?></td>
              <td><?php echo $plan->mark;?></td>
                <td><?php echo $plan->year;?></td>
              <td><?php if(!helper::isZeroDate($plan->begin)) echo $plan->begin;?></td>
              <td><?php if(!helper::isZeroDate($plan->end)) echo $plan->end;?></td>
              <td title=<?php echo $plan->outsides;?> class='text-ellipsis'><?php echo $plan->outsides;?></td>
                <td><?php echo zget($lang->projectplan->statusList, $plan->status, '');?></td>
                <td class='text-ellipsis'
                    title=<?php echo zget($lang->projectplan->insideStatusList, $plan->insideStatus, ''); ?>>
                    <?php echo zget($lang->projectplan->insideStatusList, $plan->insideStatus, ''); ?>
                </td>
                <?php
                $reviewersTitle = '';
                if (!empty($plan->owner)) {
                    foreach (explode(',', $plan->owner) as $reviewers) {
                        if (!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                    }
                }
                $reviewersTitle = trim($reviewersTitle, ',');
                ?>
                <td title='<?php echo $reviewersTitle; ?>' class='text-ellipsis'>
                    <?php echo $reviewersTitle ; ?>
                </td>
                <td class='c-actions'>
                    <?php
                    common::printIcon('projectplansh', 'initProject', "projectplanID=$plan->id&creationID=$plan->creationID", $plan, 'list', 'file-text',"_blank");

                    // 判断是否审批年度计划
                    if (in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearreject'))) {
                        common::printIcon('projectplansh', 'yearReview', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true, '', $this->lang->projectplan->yearReview);
                        common::printIcon('projectplansh', 'yearReviewing', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->yearReviewing);
                        echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->planChange . " '><i class='icon-common-feedback disabled icon-feedback'></i></button>\n";

                    } else if(in_array($plan->status, array('yearpass','start'))) {
                        if ($plan->changeStatus != 'pending') {
                            common::printIcon('projectplansh', 'submit', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true);
                            echo "<button type='button' class='disabled btn' title='".$this->lang->projectplan->review."' style='pointer-events: unset;'><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
//                                        echo "<button type='button' class='disabled btn' title='".$this->lang->projectplan->submit."' style='pointer-events: unset;'><i class='icon-common-start disabled icon-start'></i></button>\n";
                            common::printIcon('projectplansh', 'planChange', "id=$plan->id", $plan, 'list', 'feedback', '_blank', '', '', $lang->projectplan->planChange);
                        } else {
                            if($plan->reviewers == $this->app->user->account){
                                common::printIcon('projectplansh', 'changeReview', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->changeReview);
                            }else{
                                echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->changeReview . " '><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                            }
                            common::printIcon('projectplansh', 'submit', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true);
                            echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->planChange . " '><i class='icon-common-feedback disabled icon-feedback'></i></button>\n";
                        }
                    }else{
                        common::printIcon('projectplansh', 'submit', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true);
                        common::printIcon('projectplansh', 'review', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $plan->reviewStage == 2 ? $this->lang->projectplan->involved : $this->lang->projectplan->review);
                        echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->planChange . " '><i class='icon-common-feedback disabled icon-feedback'></i></button>\n";

                    }
                    if ($plan->status == 'pass') {
                        common::printIcon('projectplansh', 'exec', "projectplanID=$plan->id", $plan, 'list', 'run', '', 'iframe', true);
                    } else {
                        common::printIcon('projectplansh', 'edit', "projectplanID=$plan->id", $plan, 'list');
                    }
                    common::printIcon('projectplansh', 'execEdit', "id=$plan->id", $plan, 'list','change','_blank','','','',$lang->projectplan->execEdit);
                    common::printIcon('projectplansh', 'delete', "projectplanID=$plan->id", $plan, 'list', 'trash', 'hiddenwin');
                    ?>
                </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'></div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
