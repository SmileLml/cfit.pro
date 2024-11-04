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
            <span class="text" title='<?php echo $this->lang->authorityapply->historyNodes; ?>'><?php echo $this->lang->authorityapply->historyNodes; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail">

                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <th class="w-180px"><?php echo $lang->authorityapply->reviewNodeNum; ?></th>
                                <th class="w-180px"><?php echo $lang->authorityapply->dealNode; ?></th>
                                <th class="w-180px"><?php echo $lang->authorityapply->dealer; ?></th>
                                <th class="w-180px"><?php echo $lang->authorityapply->dealResult; ?></th>
                                <th class="review-opinion"><?php echo $lang->authorityapply->dealOpinion; ?></th>
                                <th class="w-180px"><?php echo $lang->authorityapply->dealTime; ?></th>
                            </tr>

                            <?php
                            foreach ($nodes as $version => $currentVersionReviewNodes):

                                $reviewNodeCount = 0;
                                foreach ($currentVersionReviewNodes as $reviewNode){
                                    if(!empty($reviewNode['result'])&&$reviewNode['result']!='ignore'){
                                        $reviewNodeCount++;
                                    }
                                }
                                //数组第一个元素并且把该元素从数组中去掉
                                $firstReviewNode = array_shift($currentVersionReviewNodes);
                                $nodeCode = $firstReviewNode['nodeName'];
                                while(empty($firstReviewNode['result']) || $firstReviewNode['result'] == 'ignore' ){
                                    $firstReviewNode = array_shift($currentVersionReviewNodes);
                                }
                                $nodeName = zget($lang->authorityapply->statusList, $firstReviewNode['nodeName']);
                                $reviewerUsers = zmget($users, implode(',', $firstReviewNode['toDealUser']));

                                ?>
                                <tr>
                                    <th style="vertical-align: middle;text-align: center;" rowspan="<?php echo $reviewNodeCount;?>"><?php echo "第".$version.'次';?></th>
                                <?php if ($firstReviewNode['nodeName']!='waitsubmit'):?>

                                    <th><?php echo $nodeName; ?></th>
                                    <td title="<?php echo $reviewerUsers; ?>">
                                        <?php echo $reviewerUsers; ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo zget($lang->authorityapply->reviewList,'pass');
                                        ?>
                                        <?php if($firstReviewNode['dealUser']):?>
                                            （<?php echo zget($users, $firstReviewNode['dealUser']);?>）
                                        <?php endif;?>

                                    </td>
                                    <td> <?php echo $firstReviewNode['comment']; ?></td>
                                    <td> <?php echo $firstReviewNode['dealDate']; ?></td>
                                <?php endif;?>

                                </tr>

                                <?php if(!empty($currentVersionReviewNodes)):

                                $a = 0;
                                foreach ($currentVersionReviewNodes as $key => $reviewNode) {
                                    if(in_array($reviewNode['nodeName'], $lang->authorityapply->deptApprovalStatus)&&$reviewNode['result']!='ignore'  && $reviewNode['toDealUser'][0]!=''&& $reviewNode['nodeName']!='returned'
                                    ){
                                        $a = $a + 1;
                                    }
                                }
                                foreach ($currentVersionReviewNodes as $k => $reviewNode):
                                    $nodeCode = $reviewNode['nodeName'];
                                    $nodeName = zget($lang->authorityapply->statusList, $reviewNode['nodeName']);
                                    $reviewerUsers = zmget($users, implode(',', $reviewNode['toDealUser']));
                                    if(!empty($reviewNode['result'])&& $nodeCode!='waitsubmit' && $reviewNode['result'] != 'ignore'):
                                        ?>
                                        <tr>
                                            <?php if($k == 1):?>
                                                <th <?php echo "rowspan='$a'"; ?> >
                                                    <?php echo $nodeName; ?>
                                                </th>
                                            <?php elseif($k >= 1 + $a || $k < 1): ?>
                                                <th>
                                                    <?php echo $nodeName; ?>
                                                </th>
                                            <?php endif; ?>
                                            <td title="<?php echo $reviewerUsers; ?>">
                                                <?php echo $reviewerUsers; ?>
                                            </td>
                                            <td>

                                                <?php if($reviewNode['result']=='return'):?>
                                                    <?php echo $lang->authorityapply->statusList['withdrawn']; ?>
                                                    <?php if ($reviewNode['dealUser']): ?>
                                                        （<?php echo zget($users, $reviewNode['dealUser']); ?>）
                                                    <?php endif; ?>

                                                <?php elseif($reviewNode['result']=='terminate'):?>
                                                    <?php echo $lang->authorityapply->statusList['terminated']  ?>
                                                    <?php if ($reviewNode['dealUser']): ?>
                                                        （<?php echo zget($users, $reviewNode['dealUser']); ?>）
                                                    <?php endif; ?>
                                                <?php else:?>
                                                    <?php echo zget($lang->authorityapply->reviewList, $reviewNode['result']); ?>

                                                    <?php if ($reviewNode['dealUser']): ?>
                                                        （<?php echo zget($users, $reviewNode['dealUser']); ?>）
                                                    <?php endif; ?>

                                                <?php endif;?>

                                            </td>
                                            <td> <?php echo $reviewNode['comment']; ?></td>
                                            <td> <?php echo $reviewNode['dealDate']; ?></td>
                                        </tr>
                                    <?php endif;?>
                                <?php endforeach;?>
                            <?php endif;?>

                            <?php
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
