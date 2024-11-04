<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
td p {margin-bottom: 0;}
.w-175px {width: 175px;}
.task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
.task-toggle .icon{display: inline-block; transform: rotate(90deg);}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php $browseLink = $app->session->projectplanList != false ? $app->session->projectplanList : inlink('browse');?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php printf('%03d', $plan->id);?></span>
      <span class="text" title='<?php echo $plan->name;?>'><?php echo $plan->name;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->content;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($plan->content) ? $plan->content : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->projectCreation;?></div>
        <div class="detail-content article-content">
          <?php if(empty($plan->creation)):?>
          <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          <?php else:?>
          <table class='table'>
            <tr>
              <th class='w-175px'><?php echo $lang->projectplan->code;?></th>
              <td><?php echo $plan->creation->code;?></td>
              <th class='w-80px'><?php echo $lang->projectplan->mark;?></th>
              <td><?php echo $plan->creation->mark;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->name;?></th>
              <td colspan='3'><?php echo $plan->creation->name;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->PM;?></th>
              <td><?php echo zget($users, $plan->creation->PM, '');?></td>
              <th><?php echo $lang->projectplan->dept;?></th>
              <td><?php echo zget($depts, $plan->creation->dept, '');?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->source;?></th>
              <td colspan='3'><?php
                  $sources = [];
                  foreach (explode(',', $plan->creation->source) as $source) {
                      if (empty($source)) continue;
                      $sources[] = zget($lang->projectplan->basisList, $source, '');
                  }
                  echo implode(',', $sources);?></td>
            </tr>
            <tr>
              <th><?php echo $lang->opinion->union;?></th>
              <td colspan='3'>
                <?php
                foreach(explode(',', $plan->creation->union) as $union)
                {
                    echo zget($lang->opinion->unionList, $union, '') . ' ';
                }
                ?>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->type;?></th>
              <td colspan='3'><?php echo zget($lang->projectplan->typeList, $plan->creation->type, '');?></td>
            </tr>
            <tr>
            <!-- 原逻辑-->
             <!--<th>--><?php /*echo $lang->projectplan->linkPlan;*/?><!--</th>-->
             <!--20220130 修改linkPlan 为 outsideProject-->
              <th><?php echo $lang->projectplan->linkPlan;?></th>
              <td colspan='3'>
              <?php 
                  if(isset($plan->creation->linkPlan))
                  {
                      $linkPlans = explode(',', str_replace(' ', '', $plan->creation->linkPlan));
                      foreach($linkPlans as $linkPlan)
                      {
/*                          zget($plans, $linkPlan) ? html::a(common::printLink('projectplan','applicationview',"appID=$plan->id",zget($plans, $linkPlan),'','class="iframe"','',true)) : '';*/
/*                          zget($plans, $linkPlan) ? html::a(common::printLink('projectplan','projectindex',"name=$plan->name",zget($plans, $linkPlan),'','class="iframe" style = "width:2000px;"','',true)) : '';*/
                          if($linkPlan) echo zget($plans, $linkPlan);

                      }
                  }
                ?>              
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->beginAndEnd;?></th>
              <td colspan='3'><?php echo $plan->creation->begin . ' - ' . $plan->creation->end;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->workload . "({$lang->projectplan->monthly})";?></th>
              <td colspan='3'><?php echo $plan->creation->workload;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->background;?></th>
              <td colspan='3'><?php echo $plan->creation->background;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->range;?></th>
              <td colspan='3'><?php echo $plan->creation->range;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->goal;?></th>
              <td colspan='3'><?php echo $plan->creation->goal;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->stakeholder;?></th>
              <td colspan='3'><?php echo $plan->creation->stakeholder;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->projectplan->verify;?></th>
              <td colspan='3'><?php echo $plan->creation->verify;?></td>
            </tr>
          </table>
          <?php endif;?>
        </div>
      </div>
      <?php if($plan->creation) echo $this->fetch('file', 'printFiles', array('files' => $plan->creation->files, 'fieldset' => 'true'));?>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=projectplan&objectID=$plan->id");?>
    </div>
    <?php if(!empty($yearNodes)):?>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->yearReviewComment;?></div>
        <div class="detail-content article-content">
          <?php if(!empty($yearNodes)):?>
          <table class="table ops">
            <tr>
              <th class="w-200px"><?php echo $lang->projectplan->node;?></th>
              <td class="w-100px"><?php echo $lang->projectplan->reviewer;?></td>
              <!--<td class="w-150px"><?php echo $lang->projectplan->involved;?></td> -->
              <td class="w-100px"><?php echo $lang->projectplan->reviewResult;?></td>
              <td><?php echo $lang->projectplan->reviewComment;?></td>
            </tr>
            <tr>
              <th rowspan="<?php echo count($yearNodes[0]->reviewers);?>"><?php echo $lang->projectplan->managerOpinion;?></th>
              <td><?php echo zget($users, $yearNodes[0]->reviewers[0]->reviewer, '');?></td>
              <!--<td></td>-->
              <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[0]->reviewers[0]->status, '');?></td>
              <td><?php echo $yearNodes[0]->reviewers[0]->comment?></td>
            </tr>
            <!--
            <tr>
              <th rowspan="<?php echo count($yearNodes[1]->reviewers);?>"><?php echo $lang->projectplan->leaderOpinion;?></th>
              <td><?php echo zget($users, $yearNodes[1]->reviewers[0]->reviewer);?></td>
              <td></td>
              <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[1]->reviewers[0]->status, '');?></td>
              <td><?php echo $yearNodes[1]->reviewers[0]->comment?></td>
            </tr>
            -->

            <tr>
              <?php $reviewedCount = count($yearNodes[1]->reviewers);?>
              <th rowspan="<?php echo $reviewedCount;?>"><?php echo $lang->projectplan->deptsOpinion;?></th>
              <td><?php echo zget($users, $yearNodes[1]->reviewers[0]->reviewer, '');?></td>
              <!--
              <td>
                <?php
                  // $involved = json_decode($yearNodes[2]->reviewers[0]->extra);
                  // if($involved)
                  // {
                  //     foreach($involved->involved as $u) echo zget($users, $u, '') . ' ';
                  // }
                ?>
              </td> 
              -->
              <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[1]->reviewers[0]->status, '');?></td>
              <td><?php echo $yearNodes[1]->reviewers[0]->comment?></td>
            </tr>

            <?php if($reviewedCount > 1):?>
            <?php for($i = 1; $i < $reviewedCount; $i++):?>
            <tr>
              <td><?php echo zget($users, $yearNodes[1]->reviewers[$i]->reviewer, '');?></td>
              <!--
              <td>
                <?php
                  $involved = json_decode($yearNodes[1]->reviewers[$i]->extra);
                  if($involved)
                  {
                      foreach($involved->involved as $u) echo zget($users, $u, '') . ' ';
                  }
                ?>
              </td>
              -->
              <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[1]->reviewers[$i]->status, '');?></td>
              <td><?php echo $yearNodes[1]->reviewers[$i]->comment?></td>
            </tr>
            <?php endfor;?>
            <?php endif;?>
<!--
            <tr>
              <th rowspan="<?php echo count($yearNodes[3]->reviewers);?>"><?php echo $lang->projectplan->gmOpinion;?></th>
              <td><?php echo zget($users, $yearNodes[3]->reviewers[0]->reviewer);?></td>
              <td></td>
              <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[3]->reviewers[0]->status, '');?></td>
              <td><?php echo $yearNodes[3]->reviewers[0]->comment?></td>
            </tr>
-->
          </table>
          <?php else:?>
          <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          <?php endif;?>
        </div>
      </div>
    </div>
    <?php endif;?>

    <?php if(!empty($bookNodes)):?>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->bookReviewComment;?></div>
          <div class="detail-content article-content">
              <?php if (!empty($bookNodes)): ?>
                  <table class="table ops">
                      <tr>
                          <th class="w-200px"><?php echo $lang->projectplan->node; ?></th>
                          <td class="w-100px"><?php echo $lang->projectplan->reviewer; ?></td>
                          <td class="w-150px"><?php echo $lang->projectplan->involved; ?></td>
                          <td class="w-100px"><?php echo $lang->projectplan->reviewResult; ?></td>
                          <td><?php echo $lang->projectplan->reviewComment; ?></td>
                      </tr>
                      <?php
                      foreach ($bookNodes as $bnode){
                          $reviewedCount = count($bnode->reviewers);
                          foreach ($bnode->reviewers as $k=>$bnReviewer){
                              if($k == 0){
                                  ?>
                                  <tr>
                                      <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($lang->projectplan->submitnodeCodeDesc,$bnode->nodeCode); ?></th>
                                      <td><?php echo zget($users, $bnReviewer->reviewer, ''); ?></td>
                                      <td>
                                          <?php
                                          if(in_array($bnode->nodeCode,$lang->projectplan->reviewinvolvedNode) ){
                                              $involved = json_decode($bnReviewer->extra);
                                              if ($involved && $involved->involved) {
                                                  foreach ($involved->involved as $u) echo zget($users, $u, '') . ' ';
                                              }
                                          }
                                          ?>
                                      </td>
                                      <td><?php echo zget($lang->projectplan->reviewStatusList, $bnReviewer->status, ''); ?></td>
                                      <td><?php echo $bnReviewer->comment ?></td>
                                  </tr>
                                  <?php
                              }else{
                                  ?>
                                  <tr>

                                      <td><?php echo zget($users, $bnReviewer->reviewer, ''); ?></td>
                                      <td><?php
                                          if(in_array($bnode->nodeCode,$lang->projectplan->reviewinvolvedNode) ){
                                              $involved = json_decode($bnReviewer->extra);
                                              if ($involved && $involved->involved) {
                                                  foreach ($involved->involved as $u) echo zget($users, $u, '') . ' ';
                                              }
                                          }
                                          ?></td>
                                      <td><?php echo zget($lang->projectplan->reviewStatusList, $bnReviewer->status, ''); ?></td>
                                      <td><?php echo $bnReviewer->comment ?></td>
                                  </tr>
                                  <?php
                              }
                          }
                      }
                      ?>
                  </table>
              <?php else: ?>
                  <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
              <?php endif; ?>
          </div>
      </div>
    </div>
    <?php endif;?>
    <div class="cell"><?php include '../../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
          common::printIcon('projectplan', 'initProject', "projectplanID=$plan->id", $plan, 'list', 'file-text');
          // 判断是否审批年度计划
          if(in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearreject')))
          {
              common::printIcon('projectplan', 'yearReview', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true, '', $this->lang->projectplan->yearReview);
              common::printIcon('projectplan', 'yearReviewing', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->yearReviewing);
          }
          else
          {
              common::printIcon('projectplan', 'submit', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true);
              common::printIcon('projectplan', 'review', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', false, '', $plan->reviewStage == 2 ? $this->lang->projectplan->involved : $this->lang->projectplan->review);
          }
          if($plan->status == 'pass')
          {
              common::printIcon('projectplan', 'exec', "projectplanID=$plan->id", $plan, 'list', 'run', '', 'iframe', true);
          }
          else
          {
              common::printIcon('projectplan', 'edit', "projectplanID=$plan->id", $plan, 'list');
          }
          common::printIcon('projectplan', 'delete', "projectplanID=$plan->id", $plan, 'button', 'trash', 'hiddenwin');
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->opinion->category;?></th>
                <td><?php echo zget($lang->opinion->categoryList, $plan->category, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->status;?></th>
                <td><?php echo zget($lang->projectplan->statusList, $plan->status, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->year;?></th>
                <td><?php echo $plan->year;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->secondLine;?></th>
                <td colspan='2'><?php echo zget($lang->projectplan->secondLineList, $plan->secondLine, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->code;?></th>
                <td><?php echo $plan->code;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->mark;?></th>
                <td><?php echo $plan->mark;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->type;?></th>
                <td><?php echo zget($lang->projectplan->typeList, $plan->type, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->basis;?></th>
                <td><?php echo zget($lang->projectplan->basisList, $plan->basis, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->app;?></th>
                <td>
                  <?php
                  $appList = explode(',', str_replace(' ', '', $plan->app));
                  foreach($appList as $a)
                  {
                    echo html::a($this->createLink('application', 'view', "appID=$a"), zget($apps, $a, ''));

                  }
                  ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->line;?></th>
                <td>
                <?php
                $lineList = explode(',', str_replace(' ', '', $plan->line));
                foreach($lineList as $lineID)
                {
                    if($lineID) echo ' ' . zget($lines, $lineID, '');
                }
                ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->storyStatus;?></th>
                <td><?php echo zget($lang->projectplan->storyStatusList, $plan->storyStatus, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->outsideProject;?></th>
                <td>
                <?php
                  if(isset($plan->outsideProject))
                  {
                      $ps = explode(',', str_replace(' ', '', $plan->outsideProject));

                      foreach($ps as $planID)
                      {
/*                          if($planID) echo '<p>' . html::a($this->createLink('outsideplan', 'view', "planID=$planID"), zget($outsideproject, $planID, ''), '', "style='color: #0c60e1;'") . '</p>';*/
                          zget($outsideproject, $planID) ;

                      }
                  }
                ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->opinion->category;?></th>
                <td><?php echo zget($lang->opinion->categoryList, $plan->category, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->structure;?></th>
                <td><?php echo zget($lang->projectplan->structureList, $plan->structure, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->localize;?></th>
                <td><?php echo zget($lang->projectplan->localizeList, $plan->localize, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->begin;?></th>
                <td><?php echo $plan->begin == '0000-00-00' ? '' : $plan->begin;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->end;?></th>
                <td><?php echo $plan->end == '0000-00-00' ? '' : $plan->end;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->workload;?></th>
                <td><?php if($plan->workload) echo $plan->workload . ' ' . $lang->projectplan->monthly;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->duration;?></th>
                <td><?php if($plan->duration) echo $plan->duration . ' ' . $lang->projectplan->day;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->bearDept;?></th>
                <td><?php echo zget($depts, $plan->bearDept);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->depts;?></th>
                <td>
                <?php
                if(isset($plan->depts))
                {
                    $planDepts = explode(',', str_replace(' ', '', $plan->depts));
                    foreach($planDepts as $deptID)
                    {
                        if($deptID) echo ' ' . zget($depts, $deptID, '');
                    }
                }
                ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->reviewDate;?></th>
                <td><?php echo $plan->reviewDate === '0000-00-00' ? '' : $plan->reviewDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->owner;?></th>
                <td><?php echo zget($users, $plan->owner, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->phone;?></th>
                <td><?php echo $plan->phone;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->createdBy;?></th>
                <td><?php echo zget($users, $plan->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->projectplan->createdDate;?></th>
                <td><?php echo $plan->createdDate;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->product;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <?php foreach($products as $product):?>
              <tr>
                <td class='text-ellipsis'><?php echo html::a($this->createLink('product', 'view', 'id=' . $product['productID']), $product['productName'], '', "title={$product['productName']}");?></td>
                <td class='text-ellipsis'><?php if($product['planID']) echo html::a($this->createLink('productplan', 'view', 'id=' . $product['planID']), $product['planName'], '', "title={$product['planName']}");?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->requirementList;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <?php $requirementIndex = 0;?>
              <?php foreach($requirementList as $id => $name):?>
                <?php if($requirementIndex <= 5):?>
                <tr>
                  <td class='text-ellipsis'><?php echo html::a($this->createLink('requirement', 'view', 'requirementID=' . $id), $name, '', "title=$name");?></td>
                </tr>
                <?php endif;?>
                <?php if($requirementIndex == 6):;?>
                <tr>
                  <td class='text-ellipsis'><a class="more-show" id='moreTips'>点击展开更多</a></td>
                </tr>
                <?php endif;?>
                <?php if($requirementIndex >= 6):;?>
                <tr class='requirement-more hidden'>
                  <td class='text-ellipsis'><?php echo html::a($this->createLink('requirement', 'view', 'requirementID=' . $id), $name, '', "title=$name");?></td>
                </tr>
                <?php endif;?>
              <?php $requirementIndex++;?>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php foreach($relatedObject as $type => $objects):?>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->projectplan->{$type};?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <?php foreach($objects as $id => $name):?>
              <tr>
                <?php $objectModule = zget($config->projectplan->objectTables, $type);?>
                <td class='text-ellipsis'><?php echo html::a($this->createLink($objectModule, 'view', 'objectID=' . $id), $name, '', "title=$name");?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  </div>
</div>
<script>
$('#moreTips').bind('click', function()
{
    var currentClass = $(this).attr('class');
    if(currentClass == 'more-show')
    {
        $('.requirement-more').removeClass('hidden');
        $(this).text('点击收起更多');
        $(this).attr('class', 'more-hidden');
    }
    else
    {
        $('.requirement-more').addClass('hidden');
        $(this).text('点击展开更多');
        $(this).attr('class', 'more-show');
    }
});
</script>
<?php include '../../../common/view/footer.html.php';?>
