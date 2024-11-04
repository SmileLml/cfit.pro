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
            <span class="text" title='<?php echo $this->lang->sectransfer->historyNodes; ?>'><?php echo $this->lang->sectransfer->historyNodes; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail">
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                    <table class="table ops">
                        <tr>
                            <th class="w-180px"><?php echo $lang->sectransfer->reviewNodeNum; ?></th>
                            <th class="w-180px"><?php echo $lang->sectransfer->statusOpinion; ?></th>
                            <td class="w-180px"><?php echo $lang->sectransfer->reviewers; ?></td>
                            <td class="w-180px"><?php echo $lang->sectransfer->reviewResult; ?></td>
                            <td style="width:370px;"><?php echo $lang->sectransfer->dealOpinion; ?></td>
                            <td class="w-180px"><?php echo $lang->sectransfer->reviewOpinionTime; ?></td>
                        </tr>
                        <?php
                        $i = 0;
                        if(
                                (!empty($secondorder) && 'guestjx' == $secondorder->createdBy)
                                || ('1' == $sectransfer->jftype && $lang->sectransfer->cfjx == $sectransfer->externalRecipient)
                        ){
                            $lang->sectransfer->reviewNodeStatusList[7] = 'waitjx';
                        }
                        foreach ($nodes as $nk=>$nv):
                        $i++;
                        $j = 0;
                        $node = (array)$nv['nodes'];
                        foreach ($lang->sectransfer->reviewNodeStatusList as $key => $reviewNode):
                            //$currentNode = $nodes[$key - 1];

                            $reviewerUserTitle = '';
                            $reviewerUsersShow = '';
                            $realReviewer = new stdClass();
                            $realReviewer->status = '';
                            $realReviewer->comment = '';
                            if (isset($node[$key - 1])) {
                                $currentNode = $node[$key - 1];
                                $reviewers = $currentNode->reviewers;
                                if (!(is_array($reviewers) && !empty($reviewers))) {
                                    continue;
                                }
                                //所有审核人
                                $reviewersArray = array_column($reviewers, 'reviewer');
                                $userCount = count($reviewersArray);
                                if ($userCount > 0) {
                                    $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                    $reviewerUserTitle = implode(',', $reviewerUsers);
                                    $subCount = 10;
                                    $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                    //获得实际审核人
                                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                }
                            }else{
                                continue;
                            }

                            ?>
                            <tr>
                                <?php if($j == 0):?>
                                    <th rowspan="<?php echo $nv['countNodes']?>"><?php echo "第".$i.'次';?></th>
                                <?php $j++; endif;?>
                                <th><?php echo zget($lang->sectransfer->reviewNodeStatusLableList, $reviewNode); ?></th>
                                <td title="<?php echo $reviewerUserTitle; ?>">
                                    <?php echo $reviewerUsersShow; ?>
                                </td>
                                <td><?php echo zget($lang->sectransfer->reviewStatusList, $realReviewer->status, ''); ?>
                                    <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject' || $realReviewer->status == 'incorporate' || $realReviewer->status == 'appoint'): ?>
                                        &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $realReviewer->comment ?>
                                </td>
                                <td><?php echo $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: ''?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endforeach; ?>



                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
