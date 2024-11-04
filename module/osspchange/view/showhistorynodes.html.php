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
            <span class="text" title='<?php echo $this->lang->osspchange->historyNodes; ?>'><?php echo $this->lang->osspchange->historyNodes; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail">
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                    <table class="table ops">
                        <tr>
                            <th class="w-180px"><?php echo $lang->osspchange->reviewNodeNum; ?></th>
                            <th class="w-180px"><?php echo $lang->osspchange->statusOpinion; ?></th>
                            <td class="w-180px"><?php echo $lang->osspchange->reviewer; ?></td>
                            <td class="w-180px"><?php echo $lang->osspchange->reviewResult; ?></td>
                            <td style="width:370px;"><?php echo $lang->osspchange->dealOpinion; ?></td>
                            <td class="w-180px"><?php echo $lang->osspchange->reviewOpinionTime; ?></td>
                        </tr>
                        <?php
                        $i = 0;
                        foreach ($nodes as $nk=>$nv):
                            $i++;
                            $j = 0;
                            $node = (array)$nv['nodes'];
                            foreach ($node as $key => $reviewNode):

                                $reviewerUserTitle = '';
                                $reviewerUsersShow = '';
                                $realReviewer = new stdClass();
                                $realReviewer->status = '';
                                $realReviewer->comment = '';
                                $reviewers = $reviewNode->reviewers;
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
                                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($reviewNode->status, $reviewers);
                                    if($realReviewer->extra){
                                        $currentExtra = json_decode($realReviewer->extra, true);
                                        $reviewInfo = $currentExtra['reviewInfo'];
                                    }
                                }
                                ?>
                                <tr>
                                    <?php if($j == 0):?>
                                        <th rowspan="<?php echo $nv['countNodes']?>"><?php echo "第".$i.'版';?></th>
                                        <?php $j++; endif;?>
                                    <th><?php echo zget($lang->osspchange->reviewNameList, $reviewNode->nodeCode); ?></th>
                                    <td title="<?php echo $reviewerUserTitle; ?>">
                                        <?php echo $reviewerUsersShow; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($reviewNode->status == $lang->osspchange->pendingStatus){
                                            $result = $lang->osspchange->pending;
                                        }elseif($reviewNode->status == $lang->osspchange->ignoreStatus){
                                            $result = $lang->osspchange->ignore;
                                        }elseif($reviewNode->nodeCode == $lang->osspchange->statusList['waitConfirm']){
                                            $result = zget($resultList, $reviewInfo, '');
                                        }elseif($reviewNode->nodeCode == $lang->osspchange->statusList['waitDeptApprove']){
                                            $result = zget($systemManagerList, $reviewInfo, '');
                                        }elseif($reviewNode->nodeCode == $lang->osspchange->statusList['waitQMDApprove']){
                                            $result = zget($QMDmanagerList, $reviewInfo, '');
                                        }elseif($reviewNode->nodeCode == $lang->osspchange->statusList['waitMaxLeaderApprove']){
                                            $result = zget($maxLeaderList, $reviewInfo, '');
                                        }elseif($reviewNode->nodeCode == $lang->osspchange->statusList['waitClosed']){
                                            $result = zget($closedList, $reviewInfo, '');
                                        }
                                        echo $result;
                                        $reviewInfo = '';
                                        ?>
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
