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
            <span class="text" title='<?php echo $this->lang->credit->historyNodes; ?>'><?php echo $this->lang->credit->historyNodes; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail">

                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                    <table class="table ops">
                        <tr>
                            <th class="w-180px"><?php echo $lang->credit->reviewNodeNum; ?></th>
                            <th class="w-180px"><?php echo $lang->credit->reviewNode; ?></th>
                            <td class="w-180px"><?php echo $lang->credit->reviewer; ?></td>
                            <td class="w-180px"><?php echo $lang->credit->dealResult; ?></td>
                            <td class="review-opinion"><?php echo $lang->credit->reviewOpinion; ?></td>
                            <td class="w-180px"><?php echo $lang->credit->reviewTime; ?></td>
                        </tr>
                        <?php
                            foreach ($nodes as $version => $currentVersionReviewNodes):
                                $reviewNodeCount = 0;
                                foreach ($currentVersionReviewNodes as $reviewNode){
                                    if(!empty($reviewNode['result'])){
                                        $reviewNodeCount++;
                                    }
                                }
                                //数组第一个元素并且把该元素从数组中去掉
                                $firstReviewNode = array_shift($currentVersionReviewNodes);
                                $nodeCode = $firstReviewNode['nodeName'];
                                $nodeName = zget($lang->credit->reviewNodeNameList, $firstReviewNode['nodeName']);
                                $reviewerUsers = zmget($users, implode(',', $firstReviewNode['toDealUser']));
                        ?>
                        <tr>
                            <th style="vertical-align: middle;text-align: center;" rowspan="<?php echo $reviewNodeCount;?>"><?php echo "第".$version.'次';?></th>
                            <th><?php echo $nodeName; ?></th>
                            <td title="<?php echo $reviewerUsers; ?>">
                                <?php echo $reviewerUsers; ?>
                            </td>
                            <td>
                                <?php
                                    if($creditInfo->status == 'cancel' && $firstReviewNode['result'] == 'pending'){
                                        echo '';
                                    }else{
                                        echo zget($lang->credit->reviewResultList, $firstReviewNode['result'], '');
                                    }
                                ?>

                                <?php if($firstReviewNode['dealUser']):?>
                                    （<?php echo zget($users, $firstReviewNode['dealUser']);?>）
                                <?php endif;?>

                            </td>
                            <td> <?php echo $firstReviewNode['comment']; ?></td>
                            <td> <?php echo $firstReviewNode['dealDate']; ?></td>
                        </tr>
                            <?php if(!empty($currentVersionReviewNodes)):
                                foreach ($currentVersionReviewNodes as $reviewNode):
                                    $nodeCode = $reviewNode['nodeName'];
                                    $nodeName = zget($lang->credit->reviewNodeNameList, $reviewNode['nodeName']);
                                    $reviewerUsers = zmget($users, implode(',', $reviewNode['toDealUser']));
                                    if(!empty($reviewNode['result'])):
                                ?>
                                <tr>
                                    <th><?php echo $nodeName; ?></th>
                                    <td title="<?php echo $reviewerUsers; ?>">
                                        <?php echo $reviewerUsers; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($creditInfo->status == 'cancel' && $reviewNode['result'] == 'pending'){
                                            echo '';
                                        }else{
                                            echo zget($lang->credit->reviewResultList, $reviewNode['result'], '');
                                        }
                                        ?>

                                        <?php if($reviewNode['dealUser']):?>
                                            （<?php echo zget($users, $reviewNode['dealUser']);?>）
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
