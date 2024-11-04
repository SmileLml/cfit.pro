<?php include '../../../common/view/header.html.php'?>
<?php include 'auditSetCommonJs.html.php';?>
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
              <th class='w-200px'><?php echo $lang->projectplan->name;?></th>
              <th class='w-70px'><?php echo$lang->projectplan->year;?></th>
              <th class='w-80px'><?php echo $lang->projectplan->code;?></th>
              <th class='w-80px'><?php echo $lang->projectplan->mark;?></th>
              <th class='w-100px'><?php echo $lang->projectplan->status;?></th>
              <th class='w-100px'><?php echo $lang->projectplan->begin;?></th>
              <th class='w-100px'><?php echo $lang->projectplan->end;?></th>
              <th class='w-180px'><?php echo $lang->projectplan->outsides;?></th>
              <th class='text-left w-40px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($reviewList as $plan):?>
            <tr>
              <td><?php printf('%03d', $plan->id);?></td>
              <td class="text-ellipsis" title=<?php echo $plan->name;?>><?php echo common::hasPriv('projectplan', 'view') ? html::a($this->createLink('projectplan', 'view', "projectplanID=$plan->id"), $plan->name) : $plan->name;?></td>
              <td><?php echo $plan->year;?></td>
              <td title=<?php echo $plan->code;?>><?php echo $plan->code;?></td>
              <td><?php echo $plan->mark;?></td>
                <?php
                $projectplanstatusstr = '';
                if($plan->status==$lang->projectplan->statusEnglishList['yearpass'] && $plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['pending']){
                    $projectplanstatusstr = $lang->projectplan->changeing;
                }else{
                    $projectplanstatusstr = zget($lang->projectplan->statusList, $plan->status, '');
                    if($plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['pass']){
                        $projectplanstatusstr .= $lang->projectplan->changePass;
                    }else if($plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['reject']){
                        $projectplanstatusstr .= $lang->projectplan->changeReject;
                    }
                }


                ?>
                <td class='text-ellipsis' title="<?php echo $projectplanstatusstr;?>">
                    <?php echo $projectplanstatusstr;?>
                </td>
<!--              <td>--><?php //echo zget($lang->projectplan->statusList, $plan->status, '');?><!--</td>-->
              <td><?php if(!helper::isZeroDate($plan->begin)) echo $plan->begin;?></td>
              <td><?php if(!helper::isZeroDate($plan->end)) echo $plan->end;?></td>
              <td title=<?php echo $plan->outsides;?>><?php echo $plan->outsides;?></td>
              <td class='c-actions'>
                <?php
                common::printIcon('projectplan', 'changeReview', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->changeReview);
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
