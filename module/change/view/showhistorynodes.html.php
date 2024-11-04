<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    /*.detail-title-bold{font-size:14px;lisne-height:20px;font-weight:bold;}*/
    .desc>div{float:left}
    .detail-content{margin-top: 0px !important;}
    .detail-title{width:130px;text-align: left}
    .main-change>div{margin-bottom: 10px}
</style>
</div>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px; max-height: 500px;">
    <div class="main-header">
        <div class="page-title">
            <span class="text" title='<?php echo $this->lang->change->historyNodes; ?>'><?php echo $this->lang->change->historyNodes; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail-content article-content">
                <table class="table ops">
                    <tbody>
                    <tr>
                        <th class="w-160px"><?php echo $lang->change->reviewNodeNum; ?></th>
                        <th class="w-160px"><?php echo $lang->change->reviewNode;?></th>
                        <td class="w-230px"><?php echo $lang->change->reviewer;?></td>
                        <td class="w-200px"><?php echo $lang->change->reviewResult;?></td>
                        <td class="w-300px"><?php echo $lang->change->reviewComment;?></td>
                        <td class="w-200px"><?php echo $lang->change->dealDate;?></td>
                    </tr>
                    <?php
                    $i = 0;
                    foreach ($historyNodes as $nk=>$nv){
                    $i++;
                    $j = 0;
                    $node = (array)$nv['nodes'];
                        foreach ($node as $key => $value){
                            $reviewNode = $value->nodeCode;
                            $isSkipReviewerNode =  false;
                            if(!isset($nodes[$reviewNode]) || !$nodes[$reviewNode]){
                                $isSkipReviewerNode = true;
                            }
                            $currentNode = new stdClass();
                            $currentNode->reviewers = [];
                            $currentNode->status    = 'ignore';
                            $currentNode->isShow    = 2;

                            if(isset($value)){
                                $currentNode = $value;
                            }
                            $isShow = $currentNode->isShow;
                            if($isShow == 2){
                                continue;
                            }
                            $reviewerUserTitle = '';
                            $reviewerUsersShow = '';
                            $realReviewer = new stdClass();
                            $realReviewer->status = '';
                            $realReviewer->comment = '';
                            $realReviewer->createdDate = '';
                            $reviewers = $currentNode->reviewers;

                            //所有审核人
                            $reviewersArray = [];
                            if(!empty($reviewers)){
                                foreach ($reviewers as $reviewerInfo){
                                    if($reviewerInfo->reviewer){
                                        $reviewersArray[] = $reviewerInfo->reviewer;
                                    }
                                }
                            }
                            $userCount = count($reviewersArray);
                            if($userCount == 0){
                                $isSkipReviewerNode = true;
                            }

                            $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                            $reviewerUserTitle = implode(',', $reviewerUsers);
                            $subCount = 3;
                            $rowspanCount = count($reviewers);
                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $rowspanCount,',',false);
                            $reviewerUsersTdShow = getArraySubValuesStr($reviewerUsers, $subCount,',',true);
                            //获得实际审核人
                            if(!empty($reviewers)){
                                $ignoreComment = '';
                                if($reviewNode == 'deptLeader'){ //部门分管领导
                                    $ignoreComment = $lang->change->deptLeaderIgnoreComment;
                                }
                                $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers, $ignoreComment);
                                $date =  $this->loadModel('consumed')->getByIdToDate($change->id,'change',$realReviewer->reviewer); // 评审时间
                                $realReviewer->createdate = isset($date[$realReviewer->reviewer]) ? date('Y-m-d',strtotime($date[$realReviewer->reviewer])) : '';
                            }
                            ?>
                            <?php
                            if(in_array($reviewNode, $lang->change->needIndependShowUsersNodeCodeList)){
                                foreach ($reviewers as $index => $review){
                                    ?>
                                    <tr  >
                                        <?php if($j == 0):?>
                                            <th rowspan="<?php echo $nv['countNodes']?>"><?php echo "第".$i.'版';?></th>
                                            <?php $j++; endif;?>
                                        <?php
                                        if($index == 0){
                                            ?>
                                            <th rowspan="<?php echo $rowspanCount;?>">
                                                <?php echo zget($lang->change->reviewNodeCodeDescList,$reviewNode );?>
                                            </th>

                                            <?php

                                        }
                                        ?>

                                        <td title="<?php echo zget($users,$review->reviewer); ?>">
                                            <?php echo zget($users,$review->reviewer); ?>
                                        </td>
                                        <td>
                                            <?php if($isSkipReviewerNode){ ?>
                                                <?php echo $lang->change->skipReviewerNodesDesc;?>
                                            <?php }else{ ?>
                                                <?php if($reviewNode == $lang->change->reviewNodeCodeList['baseline']){ ?>
                                                    <?php if($review->status == 'pass'){ ?>

                                                        <?php echo zget($lang->change->condition, $change->baseLineCondition, '');?>
                                                    <?php }else{ ?>

                                                        <?php echo zget($lang->change->confirmResultList, $review->status, '');?>
                                                    <?php } ?>
                                                <?php }else{ ?>
                                                    <?php
                                                    if($review->reviewerType == 1){
                                                        echo zget($lang->change->confirmResultList, $review->status, '');

                                                        ?>

                                                        <?php if($review->status == 'pass' || $review->status == 'reject'){ ?>
                                                            &nbsp;（<?php echo zget($users, $review->reviewer, '');?>）
                                                        <?php }
                                                    }else{
                                                        echo '-';
                                                    }
                                                    ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                        <td><?php if(!$review->comment && $review->reviewerType == 2){ echo '-';}else{echo $review->comment;} ?></td>
                                        <td><?php
                                            if(!$review->comment && $review->reviewerType == 2){ echo '-';}else{echo $review->reviewTime == '0000-00-00 00:00:00' ? '' : $review->reviewTime;}?></td>

                                    </tr>
                                    <?php
                                }
                            }else{
                                ?>
                                <tr  >
                                    <?php if($j == 0):?>
                                        <th rowspan="<?php echo $nv['countNodes']?>"><?php echo "第".$i.'版';?></th>
                                        <?php $j++; endif;?>
                                    <th >
                                        <?php echo zget($lang->change->reviewNodeCodeDescList,$reviewNode );?>
                                    </th>

                                    <td class="text-ellipsis" title="<?php echo $reviewerUsersShow; ?>">
                                        <?php echo $reviewerUsersTdShow; ?>
                                    </td>
                                    <td>
                                        <?php if($isSkipReviewerNode){ ?>
                                            <?php echo $lang->change->skipReviewerNodesDesc;?>
                                        <?php }else{ ?>
                                            <?php if($reviewNode == $lang->change->reviewNodeCodeList['baseline']){ ?>
                                                <?php if($realReviewer->status == 'pass'){ ?>

                                                    <?php echo zget($lang->change->condition, $change->baseLineCondition, '');?>
                                                <?php }else{ ?>

                                                    <?php echo zget($lang->change->confirmResultList, $realReviewer->status, '');?>
                                                <?php } ?>
                                            <?php }else{ ?>
                                                <?php
                                                echo zget($lang->change->confirmResultList, $realReviewer->status, '');
                                                ?>

                                            <?php } ?>
                                            <?php if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){ ?>
                                                &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo $realReviewer->comment; ?></td>
                                    <td><?php echo $realReviewer->reviewTime == '0000-00-00 00:00:00' ? '' : $realReviewer->reviewTime;?></td>
                                </tr>
                                <?php
                            }
                            ?>

                    <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
