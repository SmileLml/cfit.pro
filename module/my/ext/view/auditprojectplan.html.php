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
    <div class="page-title"><h4><?php echo $lang->projectplan->reviewingPlan;?></h4></div>
    <?php if(empty($reviewList['planyearreviewing'])):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='myReviewForm' data-ride='table'>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php
      $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
      ?>
        <table class='table has-sort-head' id='reviewList'>
          <thead>
            <tr>
                <th class='w-90px'><div class="checkbox-primary checkall" onclick="checkall()" title="<?php echo $lang->selectAll?>">
                        <label></label>
                    </div><?php common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id); ?></th>
<!--              <th class='w-40px'>--><?php //common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id);?><!--</th>-->
              <th class='w-200px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->projectplan->name);?></th>
              <th class='w-70px'><?php common::printOrderLink('year', $orderBy, $vars, $lang->projectplan->year);?></th>
              <th class='w-80px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->projectplan->code);?></th>
              <th class='w-80px'><?php common::printOrderLink('mark', $orderBy, $vars, $lang->projectplan->mark);?></th>
              <th class='w-100px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->projectplan->status);?></th>
              <th class='w-100px'><?php common::printOrderLink('begin', $orderBy, $vars, $lang->projectplan->begin);?></th>
              <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->projectplan->end);?></th>
              <th class='w-150px'><?php echo $lang->projectplan->outsides;?></th>
              <th class='text-left w-80px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($reviewList['planyearreviewing'] as $plan):?>
            <tr>
                <td>
                    <?php
                    if($plan->status == 'yearreviewing' && $plan->reviewers && in_array($this->app->user->account,explode(',',$plan->reviewers))){
                        echo html::checkbox('plans', array($plan->id => '')) . sprintf('%03d', $plan->id);
                    }else{
                        echo html::checkbox('plans', array($plan->id => ''),'',"disabled readonly").sprintf('%03d', $plan->id);
                    }
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
                // 判断是否审批年度计划
                if (in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearreject'))) {
                    common::printIcon('projectplan', 'yearReview', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true, '', $this->lang->projectplan->yearReview);
                    common::printIcon('projectplan', 'yearReviewing', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->yearReviewing);
                } else if (in_array($plan->status, array('yearpass', 'start'))) {
                    if ($plan->changeStatus != 'pending') {
                        echo "<button type='button' class='disabled btn' title='" . $this->lang->projectplan->review . "' style='pointer-events: unset;'><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                    } else {
                        if ($plan->reviewers == $this->app->user->account) {
                            common::printIcon('projectplan', 'changeReview', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->changeReview);
                        } else {
                            echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->changeReview . " '><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                        }
                    }
                } else {
                    common::printIcon('projectplan', 'review', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $plan->reviewStage == 2 ? $this->lang->projectplan->involved : $this->lang->projectplan->review);
                }
                if ($plan->status == 'pass') {
                    common::printIcon('projectplan', 'exec', "projectplanID=$plan->id", $plan, 'list', 'run', '', 'iframe', true);
                } else {
                    common::printIcon('projectplan', 'edit', "projectplanID=$plan->id", $plan, 'list');
                }
                ?>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'>
          <div class="checkbox-primary checkall" onclick="checkall()"><label><?php echo $lang->selectAll?></label></div>
          <div class="table-actions btn-toolbar"><div id="batchedit" onclick="setbatchediturl()"  class="btn" title="批量审批年度计划" data-app="platform">批量审批</div></div>
      </div>
    </form>
    <?php endif;?>
      <div class="page-title"><h4><?php echo $lang->projectplan->projectApprovalReviewing;?></h4></div>
      <?php if(empty($reviewList['planreviewing'])):?>
          <div class="table-empty-tip">
              <p>
                  <span class="text-muted"><?php echo $lang->noData;?></span>
              </p>
          </div>
      <?php else:?>
          <form class='main-table' method='post' id='myReviewForm' data-ride='table'>
              <div class="table-header fixed-right">
                  <nav class="btn-toolbar pull-right"></nav>
              </div>
              <?php
              $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
              ?>
              <table class='table has-sort-head' id='reviewList'>
                  <thead>
                  <tr>
                      <th class='w-90px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id); ?></th>
                      <!--              <th class='w-40px'>--><?php //common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id);?><!--</th>-->
                      <th class='w-200px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->projectplan->name);?></th>
                      <th class='w-70px'><?php common::printOrderLink('year', $orderBy, $vars, $lang->projectplan->year);?></th>
                      <th class='w-80px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->projectplan->code);?></th>
                      <th class='w-80px'><?php common::printOrderLink('mark', $orderBy, $vars, $lang->projectplan->mark);?></th>
                      <th class='w-100px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->projectplan->status);?></th>
                      <th class='w-100px'><?php common::printOrderLink('begin', $orderBy, $vars, $lang->projectplan->begin);?></th>
                      <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->projectplan->end);?></th>
                      <th class='w-150px'><?php echo $lang->projectplan->outsides;?></th>
                      <th class='text-left w-80px'><?php echo $lang->actions;?></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($reviewList['planreviewing'] as $plan):?>
                      <tr>
                          <td>
                              <?php printf('%03d', $plan->id);?>
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
                              // 判断是否审批年度计划
                              if (in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearreject'))) {
                                  common::printIcon('projectplan', 'yearReview', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true, '', $this->lang->projectplan->yearReview);
                                  common::printIcon('projectplan', 'yearReviewing', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->yearReviewing);
                              } else if (in_array($plan->status, array('yearpass', 'start'))) {
                                  if ($plan->changeStatus != 'pending') {
                                      echo "<button type='button' class='disabled btn' title='" . $this->lang->projectplan->review . "' style='pointer-events: unset;'><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                                  } else {
                                      if ($plan->reviewers == $this->app->user->account) {
                                          common::printIcon('projectplan', 'changeReview', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->changeReview);
                                      } else {
                                          echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->changeReview . " '><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                                      }
                                  }
                              } else {
                                  common::printIcon('projectplan', 'review', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $plan->reviewStage == 2 ? $this->lang->projectplan->involved : $this->lang->projectplan->review);
                              }
                              if ($plan->status == 'pass') {
                                  common::printIcon('projectplan', 'exec', "projectplanID=$plan->id", $plan, 'list', 'run', '', 'iframe', true);
                              } else {
                                  common::printIcon('projectplan', 'edit', "projectplanID=$plan->id", $plan, 'list');
                              }
                              ?>
                          </td>
                      </tr>
                  <?php endforeach;?>
                  </tbody>
              </table>

          </form>
      <?php endif;?>

      <div class="page-title"><h4><?php echo $lang->projectplan->myPlan;?></h4></div>
      <?php if(empty($reviewList['planowner'])):?>
          <div class="table-empty-tip">
              <p>
                  <span class="text-muted"><?php echo $lang->noData;?></span>
              </p>
          </div>
      <?php else:?>
          <form class='main-table' method='post' id='myReviewForm' data-ride='table'>
              <div class="table-header fixed-right">
                  <nav class="btn-toolbar pull-right"></nav>
              </div>
              <?php
              $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
              ?>
              <table class='table has-sort-head' id='reviewList'>
                  <thead>
                  <tr>
                      <th class='w-90px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id); ?></th>
                      <!--              <th class='w-40px'>--><?php //common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id);?><!--</th>-->
                      <th class='w-200px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->projectplan->name);?></th>
                      <th class='w-70px'><?php common::printOrderLink('year', $orderBy, $vars, $lang->projectplan->year);?></th>
                      <th class='w-80px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->projectplan->code);?></th>
                      <th class='w-80px'><?php common::printOrderLink('mark', $orderBy, $vars, $lang->projectplan->mark);?></th>
                      <th class='w-100px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->projectplan->status);?></th>
                      <th class='w-100px'><?php common::printOrderLink('begin', $orderBy, $vars, $lang->projectplan->begin);?></th>
                      <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->projectplan->end);?></th>
                      <th class='w-150px'><?php echo $lang->projectplan->outsides;?></th>
                      <th class='text-left w-80px'><?php echo $lang->actions;?></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach($reviewList['planowner'] as $plan):?>
                      <tr>
                          <td>
                              <?php printf('%03d', $plan->id);?>
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
                              // 判断是否审批年度计划
                              if (in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearreject'))) {
                                  common::printIcon('projectplan', 'yearReview', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true, '', $this->lang->projectplan->yearReview);
                                  common::printIcon('projectplan', 'yearReviewing', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->yearReviewing);
                              } else if (in_array($plan->status, array('yearpass', 'start'))) {
                                  if ($plan->changeStatus != 'pending') {
                                      echo "<button type='button' class='disabled btn' title='" . $this->lang->projectplan->review . "' style='pointer-events: unset;'><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                                  } else {
                                      if ($plan->reviewers == $this->app->user->account) {
                                          common::printIcon('projectplan', 'changeReview', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->changeReview);
                                      } else {
                                          echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->changeReview . " '><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                                      }
                                  }
                              } else {
                                  common::printIcon('projectplan', 'review', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $plan->reviewStage == 2 ? $this->lang->projectplan->involved : $this->lang->projectplan->review);
                              }
                              if ($plan->status == 'pass') {
                                  common::printIcon('projectplan', 'exec', "projectplanID=$plan->id", $plan, 'list', 'run', '', 'iframe', true);
                              } else {
                                  common::printIcon('projectplan', 'edit', "projectplanID=$plan->id", $plan, 'list');
                              }
                              ?>
                          </td>
                      </tr>
                  <?php endforeach;?>
                  </tbody>
              </table>

          </form>
      <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
<script>
    cleardischeckbox();
    function cleardischeckbox(){

        $("#reviewList tbody input[name='plans[]']").each(function (){
            var isdisabled = $(this).attr("disabled");

            if(isdisabled){

                if($(this).is(":checked")){
                    $(this).removeAttr("checked");
                }

            }
        });

    }

    function checkall(){

        var checkflag = false;
        var hascheck = $(".checkall").eq(0).hasClass("checked");

        if(hascheck){
            $("#reviewList tbody input[name='plans[]']").each(function (){
                var isdisabled = $(this).attr("disabled");

                if(!isdisabled){
                    $(this).removeAttr("checked")

                }else{
                    $(this).removeAttr("checked")
                }
            });
            $(".checkall").removeClass("checked")
            $("#myReviewForm").removeClass("has-row-checked")
        }else{
            $("#reviewList tbody input[name='plans[]']").each(function (){
                var isdisabled = $(this).attr("disabled");

                if(!isdisabled){
                    $(this).attr("checked",true)
                    checkflag = true
                }else{
                    $(this).removeAttr("checked")
                }
            });
            if(checkflag){
                $(".checkall").addClass("checked")
                $("#myReviewForm").addClass("has-row-checked")
            }

        }

    }
    $("#reviewList tbody input[name='plans[]']").change(
        function (){
            if(!($(this).is(":checked"))){
                $(".checkall").removeClass("checked")
            }

        }
    )
    function setbatchediturl(){
        var planidArr = [];
        $("#reviewList tbody input[name='plans[]']:checked").each(function (){
            planidArr.push($(this).val());
        });
        if(planidArr.length == 0){
            alert("请选择要审批的年度计划");

            return false;
        }


        planidIdstr = planidArr.join(",");

        // $("#batchedit").attr("href",createLink("projectplan","yearBatchReviewing","planID="+planidIdstr)+"?onlybody=yes")
        // $("#batchedit").attr("href",createLink("projectplan","yearBatchReviewing","planID="+planidIdstr)+"?onlybody=yes")
        $.zui.modalTrigger.show({iframe:createLink("projectplan","yearBatchReviewing","planID="+planidIdstr)+"?onlybody=yes",scrollInside:true});
    }
</script>