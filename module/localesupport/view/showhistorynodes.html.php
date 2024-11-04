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
            <span class="text" title='<?php echo $this->lang->localesupport->historyNodes; ?>'><?php echo $this->lang->localesupport->historyNodes; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail">

                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                    <table class="table ops">
                        <tr>
                            <th class="w-150px"><?php echo $lang->localesupport->reviewNodeNum; ?></th>
                            <th class="w-150px"><?php echo $lang->localesupport->reviewNode; ?></th>
                            <td class="w-150px"><?php echo $lang->localesupport->reviewerDept; ?></td>
                            <td class="w-240px"><?php echo $lang->localesupport->reviewer; ?></td>
                            <td class="w-150px"><?php echo $lang->localesupport->dealResult; ?></td>
                            <td class="review-opinion"><?php echo $lang->localesupport->reviewOpinion; ?></td>
                            <td class="w-150px"><?php echo $lang->localesupport->reviewTime; ?></td>
                        </tr>
                        <?php
                            foreach ($nodes as $version => $reviewList):
                                foreach ($reviewList as $reviewNode):
                                    $nodeCode = $reviewNode->nodeCode;
                                    $nodeName = zget($lang->localesupport->reviewNodeNameList, $nodeCode);
                                    $currentNodeReviewerList = $reviewNode->reviewerList;
                                    $deptIds = array_keys($currentNodeReviewerList);
                                    $deptId = $deptIds[0];
                                    $count = count($currentNodeReviewerList);
                                    $currentDeptReviewerInfo = $currentNodeReviewerList[$deptId];
                                    $deptName = trim(zget($deptList, $deptId), '/');
                                    $reviewers = implode(',', $currentDeptReviewerInfo['reviewers']);
                                    $realReviewInfo = isset($currentDeptReviewerInfo['realReviewInfo']) ? $currentDeptReviewerInfo['realReviewInfo']: new stdClass();
                                    $reviewerUsers = zmget($users, $reviewers);

                        ?>
                        <tr>
                            <th style="vertical-align: middle;text-align: center;" rowspan="<?php echo $count;?>"><?php echo "第".$version.'次';?></th>
                            <th rowspan="<?php echo $count;?>"><?php echo $nodeName; ?></th>
                            <td title="<?php echo $deptName; ?>">
                                <?php echo $deptName; ?>
                            </td>
                            <td title="<?php echo $reviewerUsers; ?>">
                                <?php echo $reviewerUsers; ?>
                            </td>
                            <td>
                                <?php
                                if(isset($realReviewInfo->reviewer)):
                                    ?> <?php echo  zget($this->lang->localesupport->dealResultList, $realReviewInfo->status); ?>（<?php echo zget($users, $realReviewInfo->reviewer);?>）
                                <?php else:?>
                                    待处理
                                <?php endif;?>

                            </td>
                            <td> <?php echo isset($realReviewInfo->comment)? $realReviewInfo->comment : ''; ?></td>
                            <td> <?php echo isset($realReviewInfo->reviewTime)? $realReviewInfo->reviewTime : ''; ?></td>
                        </tr>
                                <?php if($count > 1):
                                unset($currentNodeReviewerList[$deptId]);
                                foreach ($currentNodeReviewerList as $deptId => $currentDeptReviewerInfo):
                                    $deptName = trim(zget($deptList, $deptId), '/');
                                    $reviewers = implode(',', $currentDeptReviewerInfo['reviewers']);
                                    $realReviewInfo = isset($currentDeptReviewerInfo['realReviewInfo']) ? $currentDeptReviewerInfo['realReviewInfo']: new stdClass();
                                    $reviewerUsers = zmget($users, $reviewers);

                                    ?>
                                    <tr>
                                        <td title="<?php echo $deptName; ?>">
                                            <?php echo $deptName; ?>
                                        </td>
                                        <td title="<?php echo $reviewerUsers; ?>">
                                            <?php echo $reviewerUsers; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if(isset($realReviewInfo->reviewer)):
                                                ?> <?php echo  zget($this->lang->localesupport->dealResultList, $realReviewInfo->status); ?>（<?php echo zget($users, $realReviewInfo->reviewer);?>）
                                            <?php else:?>
                                                待处理
                                            <?php endif;?>

                                        </td>
                                        <td> <?php echo isset($realReviewInfo->comment)? $realReviewInfo->comment : ''; ?></td>
                                        <td> <?php echo isset($realReviewInfo->reviewTime)? $realReviewInfo->reviewTime : ''; ?></td>
                                    </tr>
                                <?php endforeach;?>

                            <?php endif;?>

                        <?php
                                endforeach;
                            endforeach;
                        ?>
                    </table>
                    <?php else: ?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
