<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<style>
    /*.detail-title-bold{font-size:14px;lisne-height:20px;font-weight:bold;}*/
    .desc>div{float:left}
    .detail-content{margin-top: 0px !important;}
    .detail-title{width:130px;text-align: left}
    .main-change>div{margin-bottom: 10px}
    .body-modal .main-header {
        position: fixed;
        top: 0;
        right: 20px;
        left: 20px;
        z-index: 100;
        padding: 13px 48px 35px 0;
        margin: 0;
        background: #fff;
    }
</style>
</div>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px; max-height: 500px;">
    <div class="main-header">
        <div class="page-title">
            <h5 class="text" title='<?php echo $this->lang->problem->historyReviewComment; ?>'><?php echo $this->lang->problem->historyReviewComment; ?></h5>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="cell">
            <div class="detail">
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                    <table class="table ops">
                        <tr>
                            <th class="w-180px"><?php echo $lang->problem->reviewNodeNum; ?></th>
                            <th class="w-180px"><?php echo $lang->problem->statusOpinion; ?></th>
                            <th class="w-180px"><?php echo $lang->problem->handler; ?></th>
                            <th class="w-200px"><?php echo $lang->problem->reviewResult; ?></th>
                            <th style="width:370px;"><?php echo $lang->problem->dealOpinion; ?></th>
                            <th class="w-180px"><?php echo $lang->problem->reviewOpinionTime; ?></th>
                        </tr>
                        <?php
                        $i = 0;
                        foreach ($nodes as $nk=>$nv):
                        $i++;
                        $j = 0;
                        $node = (array)$nv['nodes'];
                        if(isset($node['200']->nodeCode)){
                            if ($node['200']->nodeCode != 'toManager') {
                                $lang->problem->reviewNodeStatusLableList['toManager'] = '公司领导处理';
                                $lang->problem->reviewNodeStatusList[200] = 'toProductManager';
                            } else {
                                $lang->problem->reviewNodeStatusLableList['toManager'] = '分管领导处理';
                                $lang->problem->reviewNodeStatusList[200] = 'toManager';
                            }

                        }

                            foreach ($lang->problem->reviewNodeStatusList as $key => $reviewNode):
                            //$currentNode = $nodes[$key - 1];

                            $reviewerUserTitle = '';
                            $reviewerUsersShow = '';
                            $realReviewer = new stdClass();
                            $realReviewer->status = '';
                            $realReviewer->comment = '';
                            if (isset($node[$key])) {
                                $currentNode = $node[$key];
                                $reviewers = $currentNode->reviewers ?? '';
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
                                    if($realReviewer->status == 'wait'){
                                        continue;
                                    }
                                }
                            }else{
                                continue;
                            }

                            ?>
                            <tr>
                                <?php if($j == 0):?>
                                    <th rowspan="<?php echo $nv['countNodes']?>"><?php echo "第".$i.'次';?></th>

                                <?php $j++; endif;?>

                                <th><?php echo $reviewNode = zget($lang->problem->reviewNodeStatusLableList, $reviewNode);  ?></th>
                                <td title="<?php echo $reviewerUserTitle; ?>">
                                    <?php echo $reviewerUsersShow; ?>
                                </td>
                                <?php
                                if($reviewNode=='产品创新部处理'){
                                    $lang->problem->reviewStatusList['pass']='通过（不上报）';
                                }else{
                                    $lang->problem->reviewStatusList['pass']='通过';
                                }
                                ?>

                                <td><?php  $status = zget($lang->problem->reviewStatusList, $realReviewer->status, '');echo $status; ?>
                                    <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'||$realReviewer->status == 'report'): ?>
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

<?php include '../../../common/view/footer.html.php'; ?>
