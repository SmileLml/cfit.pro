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
            <span class="text" title='<?php echo $this->lang->outwarddelivery->historyNodes; ?>'><?php echo $this->lang->outwarddelivery->historyNodes; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail">
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                    <table class="table ops">
                        <tr>
                            <th class="w-180px"><?php echo $lang->outwarddelivery->reviewNodeNum; ?></th>
                            <th class="w-200px"><?php echo $lang->infoqz->reviewNode;?></th>
                            <td class="w-200px"><?php echo $lang->infoqz->reviewer;?></td>
                            <td class="w-200px"><?php echo $lang->infoqz->reviewResult;?></td>
                            <td style="width:370px"><?php echo $lang->infoqz->reviewComment;?></td>
                        </tr>
                        <?php
                        $i = 0;
                        foreach ($nodes as $nk=>$nv) :
                        $i++;
                        $nodes = (array)$nv['nodes'];
                        $j = 0;
                        if ($info->createdDate > "2024-04-02 23:59:59"){
                            unset($this->lang->infoqz->reviewerList[3]);
                        }
                        foreach ($lang->infoqz->reviewerList as $key => $reviewNode):
                            if($key=='4') {
                                continue;
                            }
                            else{
                                $reviewerUserTitle = '';
                                $reviewerUsersShow = '';
                                $realReviewer = new stdClass();
                                $realReviewer->status = '';
                                $realReviewer->comment = '';
                                if(isset($nodes[$key])){
                                    $currentNode = $nodes[$key];
                                    $reviewers = $currentNode->reviewers;
                                    if(!(is_array($reviewers) && !empty($reviewers))) {
                                        continue;
                                    }
                                    //所有审核人
                                    $reviewersArray = array_column($reviewers, 'reviewer');
                                    $userCount = count($reviewersArray);
                                    if($userCount > 0) {
                                        $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                                        $reviewerUserTitle = implode(',', $reviewerUsers);
                                        $subCount = 3;
                                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                        //获得实际审核人
                                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                    }

                                }else{
                                    continue;
                                }
                            }
                            ?>
                        <?php if ($j==0):?>
                            <tr>
                                <th style="text-align: center;vertical-align: middle;" rowspan="<?php echo $nv['countNodes']?>"><?php echo "第".$i.'次';?></th>
                                <th class="w-30px"><?php echo $reviewNode;?></th>
                                <td title="<?php echo $reviewerUserTitle; ?>" class="w-30px">
                                    <?php echo $reviewerUsersShow; ?>
                                </td>
                                <td class="w-30px">
                                    <?php echo zget($lang->infoqz->confirmResultList, $realReviewer->status, '');?>
                                    <?php
                                    if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                        ?>
                                        &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                    <?php endif; ?>
                                </td>
                                <td class="w-80px"><?php echo $realReviewer->comment; ?></td>
                            </tr>
                        <?php else:?>
                            <tr>
                                <th class="w-30px"><?php echo $reviewNode;?></th>
                                <td title="<?php echo $reviewerUserTitle; ?>" class="w-30px">
                                    <?php echo $reviewerUsersShow; ?>
                                </td>
                                <td class="w-30px">
                                    <?php echo zget($lang->infoqz->confirmResultList, $realReviewer->status, '');?>
                                    <?php
                                    if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                        ?>
                                        &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                    <?php endif; ?>
                                </td>
                                <td class="w-80px"><?php echo $realReviewer->comment; ?></td>
                            </tr>
                        <?php endif;?>
                            <?php $j++;?>
                        <?php endforeach;?>
                            <?php if (isset($reviewFailReason[$nk]['guestjk']) && !empty($reviewFailReason[$nk]['guestjk'])):?>
                                <tr>
                                    <th><?php echo $lang->infoqz->guestjk;?></th>
                                    <td>
                                        <?php echo zget($users,'guestjk',','); ?>
                                    </td>
                                    <td><?php echo $reviewFailReason[$nk]['guestjk']['reviewResult']?></td>
                                    <td><?php echo $reviewFailReason[$nk]['guestjk']['reviewFailReason']?></td>
                                </tr>
                            <?php endif;?>
                            <?php if (isset($reviewFailReason[$nk]['guestcn']) && !empty($reviewFailReason[$nk]['guestcn'])):?>
                            <tr>
                                <th><?php echo $lang->infoqz->guestcn;?></th>
                                <td>
                                    <?php echo zget($users,'guestcn',','); ?>
                                </td>
                                <td><?php echo $reviewFailReason[$nk]['guestcn']['reviewResult']?></td>
                                <td><?php echo $reviewFailReason[$nk]['guestcn']['reviewFailReason']?></td>
                            </tr>
                        <?php endif;?>
                        <?php endforeach;?>
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
