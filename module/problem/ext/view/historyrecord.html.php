<?php include '../../../common/view/header.html.php';?>

<style>.body-modal #mainMenu>.btn-toolbar .page-title {width: auto;}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php if(!isonlybody()):?>
    <?php $browseLink = $app->session->problemList != false ? $app->session->problemList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
  <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $problem->code?></span>
    </div>
  </div>
</div>
       <div id="mainContent" class="main-row">
           <div class="main-col ">
               <div class="cell">
                   <div class="detail">
                       <div class="detail-title"><?php echo $lang->problem->historyReviewComment;?></div>
                       <div class="detail-content article-content">
                           <?php if(!empty($allNodes)):?>
                           <table class="table ops">
                               <tr>
                                   <th ><?php echo $lang->problem->approveCount;?></th>
                                   <th ><?php echo $lang->problem->reviewNode;?></th>
                                   <th ><?php echo $lang->problem->reviewer;?></th>
                                   <th ><?php echo $lang->problem->reviewResult;?></th>
                                   <th class="w-300px"><?php echo $lang->problem->reviewComment;?></th>
                                   <th ><?php echo $lang->problem->reviewdate; ?></th>
                               </tr>
                               <?php
                                    //循环数据
                                $k = 1;
                                foreach ($allNodes as  $nodes) :
                                   // $count = count($lang->problem->reviewNodeLabelList);
                                   $count = count($nodes);
                                    $key = sprintf($lang->problem->countTip ,$k);
                                    $k ++;
                                    echo " <tr>";
                                    echo " <th rowspan = $count >"." $key ".'</th>';
                                    foreach ($lang->problem->reviewNodeLabelList as $key => $reviewNode):
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
                                        }
                               ?>
                                      <!--  <tr>-->
                                      <?php if($reviewerUsersShow) :?>
                                            <td><?php echo $reviewNode;?></td>
                                            <td title="<?php echo $reviewerUserTitle; ?>">
                                                <?php echo $reviewerUsersShow; ?>
                                            </td>
                                            <td>
                                                <?php echo zget($lang->problem->confirmResultList, $realReviewer->status, '');?>
                                                <?php
                                                if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'approvesuccess' || $realReviewer->status == 'externalsendback' || $realReviewer->status == 'closed' || $realReviewer->status == 'suspend' || $realReviewer->status == 'feedbacked'
                                                    || $realReviewer->status == 'firstpassed' || $realReviewer->status == 'finalpassed'):
                                                    ?>
                                                    &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                        if( $realReviewer->status == 'externalsendback'  && $reviewNode =='外部审批' && $problem->approverName) {
                                            echo "打回人：".$problem->approverName.'<br> 审批意见：' ;
                                          }
                                        ?>
                                               <?php echo $realReviewer->comment; ?></td>
                                            <td><?php echo isset($realReviewer->reviewTime) ? $realReviewer->reviewTime :''; ?></td>
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
<?php include '../../../common/view/footer.html.php';?>
