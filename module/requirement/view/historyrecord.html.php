<?php include '../../common/view/header.html.php';?>

<style>.body-modal #mainMenu>.btn-toolbar .page-title {width: auto;}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php if(!isonlybody()):?>
    <?php $browseLink = $app->session->requirementList != false ? $app->session->requirementList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
  <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $requirement->code?></span>
    </div>
  </div>
</div>
       <div id="mainContent" class="main-row">
           <div class="main-col ">
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->requirement->historyReviewComment;?></div>
                       <div class="detail-content article-content">
                           <?php if(!empty($allNodes)):?>
                           <table class="table ops">
                               <tr>
                                   <th ><?php echo $lang->requirement->approveCount;?></th>
                                   <th ><?php echo $lang->requirement->reviewnodes;?></th>
                                   <th ><?php echo $lang->requirement->currentreview;?></th>
                                   <th ><?php echo $lang->requirement->reviewresults;?></th>
                                   <th class="w-300px"><?php echo $lang->requirement->reviewnodecomment;?></th>
                                   <th ><?php echo $lang->requirement->reviewdate; ?></th>
                               </tr>
                               <?php
                               //循环数据
                               $k = 1;
                               foreach ($allNodes as  $nodes) :
//                                   $count = count($lang->requirement->reviewerList);
                                   $count = count($nodes);
                                   $key = sprintf($lang->requirement->countTip ,$k);
                                   $k ++;
                                   echo " <tr>";
                                   echo " <th rowspan = $count >"." $key ".'</th>';
                                   foreach ($lang->requirement->reviewerStageList as $key => $reviewNode):
                                       $reviewerUserTitle = '';
                                       $reviewerUsersShow = '';
                                       $realReviewer = new stdClass();
                                       $realReviewer->status = '';
                                       $realReviewer->comment = '';
                                       if(isset($nodes[$key])) {
                                           $currentNode = $nodes[$key];
                                           $reviewers = $currentNode->reviewers;
                                           if(is_array($reviewers) || !empty($reviewers)) {
                                               //所有审核人
                                               $reviewersArray = array_column($reviewers, 'reviewer');
                                               $userCount = count($reviewersArray);
                                               if ($userCount > 0) {
                                                   $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                                   $reviewerUserTitle = implode(',', $reviewerUsers);
                                                   $subCount = 3;
                                                   $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $userCount);
                                                   //获得实际审核人
                                                   $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                               }
                                           }
                                       }else{
                                           continue;
                                       }
                                       ?>
<!--                                         <tr>-->
                                       <?php if($reviewerUsersShow) :?>
                                           <th><?php echo $reviewNode;?></th>
                                           <td title="<?php echo $reviewerUserTitle; ?>">
                                               <?php echo $reviewerUsersShow; ?>
                                           </td>
                                           <td>
                                               <?php echo zget($lang->requirement->resultstatusList, $realReviewer->status, '');?>
                                               <?php
                                               if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'syncfail' || $realReviewer->status == 'syncsuccess' || $realReviewer->status == 'feedbacksuccess' || $realReviewer->status == 'feedbackfail'):
                                                   ?>
                                                   &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                               <?php endif; ?>
                                           </td>
                                           <td><?php
                                               echo $realReviewer->comment;
                                               ?></td>
                                           <td><?php echo $realReviewer->reviewTime; ?></td>
                                       </tr>

                                   <?php endif;?>
                                       </tr>

                                   <?php endforeach;?>
                               <?php endforeach;?>
                               <?php else:?>
                                   <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                               <?php endif;?>

                           </table>
                           <div class='form-actions text-center' colspan='6'><?php echo  html::closeModalButton('关闭');?></div>
                       </div>
                   </div>
               </div>

           </div>
       </div>
<?php include '../../common/view/footer.html.php';?>
