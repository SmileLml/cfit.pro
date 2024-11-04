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
                            <th class="w-180px"><?php echo $lang->outwarddelivery->reviewNode; ?></th>
                            <td class="w-180px"><?php echo $lang->outwarddelivery->reviewer; ?></td>
                            <td class="w-180px"><?php echo $lang->outwarddelivery->reviewResult; ?></td>
                            <td style="width:370px;"><?php echo $lang->outwarddelivery->reviewOpinion; ?></td>
                            <td class="w-180px"><?php echo $lang->outwarddelivery->reviewTime; ?></td>
                        </tr>
                        <?php
                        $i = 0;
                        foreach ($nodes as $nk=>$nv) :
                        $i++;
                        $nodes = (array)$nv['nodes'];
                        $j = 0;
                        if ($outwarddelivery->level == 2):
                            unset($lang->outwarddelivery->reviewNodeList[6]);
                        elseif ($outwarddelivery->level == 3):
                            unset($lang->outwarddelivery->reviewNodeList[5]);
                            unset($lang->outwarddelivery->reviewNodeList[6]);
                        endif;
                        //循环数据
                        if (isset($modifycncc->createdDate) && $modifycncc->createdDate > "2024-04-02 23:59:59"){
                            unset($this->lang->outwarddelivery->reviewNodeList[3]);
                        }
                        foreach ($lang->outwarddelivery->reviewNodeList as $key => $reviewNode):
                            $reviewerUserTitle = '';
                            $reviewerUsersShow = '';
                            $realReviewer = new stdClass();
                            $realReviewer->status = '';
                            $realReviewer->comment = '';
                            if (isset($nodes[$key])) {
                                $currentNode = $nodes[$key];
                                $reviewers = $currentNode->reviewers;
                                if (!(is_array($reviewers) && !empty($reviewers))) {
                                    continue;
                                }
                                //所有审核人
                                $reviewersArray = array_column($reviewers, 'reviewer');
                                $reviewersArrayNew = $this->loadModel('common')->getAuthorizer('outwarddelivery', implode(',', $reviewersArray), $lang->outwarddelivery->reviewBeforeStatusList[$key], $lang->outwarddelivery->authorizeStatusList);
                                $reviewersArray = explode(',', $reviewersArrayNew);
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
                            if ( in_array($key, $lang->outwarddelivery->skipNodes) and (! in_array($realReviewer->status,['pass','reject']))) { continue; }
                            ?>
                        <?php if ($j==0):?>
                            <tr>
                                <th style="vertical-align: middle;text-align: center;" rowspan="<?php echo $nv['countNodes']?>"><?php echo "第".$i.'次';?></th>
                                <th><?php echo $reviewNode; ?></th>
                                <td title="<?php echo $reviewerUserTitle; ?>">
                                    <?php echo $reviewerUsersShow; ?>
                                </td>
                                <td>
<!--                                    waitsubmitted-->
                                    <?php if ($outwarddelivery->status != ''): ?>
                                        <?php
                                        if($realReviewer->status == 'ignore'){
                                            if($key != 3 and $key != 7){
                                                echo '本次跳过';
                                                $realReviewer->comment = '已通过';
                                            }else if($key == 3){
                                                echo '无需处理';
                                                $realReviewer->comment = implode('',array_unique(array_column((array)$reviewers, 'comment')));
                                            }else if($key == 7){
                                                echo '本次跳过';
                                                $realReviewer->comment = '';
                                            }
                                        }else{
                                            echo zget($lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                                        }
                                        ?>
                                        <?php
                                        if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                            ?>
                                            &nbsp;（
                                            <?php $extra = json_decode($realReviewer->extra);
                                            if(!empty($extra->proxy)){
                                                echo zget($users, $extra->proxy, '');
                                                echo "【".zget($users, $realReviewer->reviewer)."授权处理】";
                                            }else{
                                                echo zget($users, $realReviewer->reviewer, '');
                                            }?>）
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $realReviewer->comment; ?></td>
                                <td><?php echo $realReviewer->reviewTime; ?></td>
                            </tr>
                        <?php else:?>
                            <tr>
                                <th><?php echo $reviewNode; ?></th>
                                <td title="<?php echo $reviewerUserTitle; ?>">
                                    <?php echo $reviewerUsersShow; ?>
                                </td>
                                <td>
<!--                                    waitsubmitted-->
                                    <?php if ($outwarddelivery->status != ''): ?>
                                        <?php
                                        if($realReviewer->status == 'ignore'){
                                            if($key != 3 and $key != 7){
                                                echo '本次跳过';
                                                $realReviewer->comment = '已通过';
                                            }else if($key == 3){
                                                echo '无需处理';
                                                $realReviewer->comment = implode('',array_unique(array_column((array)$reviewers, 'comment')));
                                            }else if($key == 7){
                                                echo '本次跳过';
                                                $realReviewer->comment = '';
                                            }
                                        }else{
                                            echo zget($lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                                        }
                                        ?>
                                        <?php
                                        if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                            ?>
                                            &nbsp;（
                                            <?php $extra = json_decode($realReviewer->extra);
                                            if(!empty($extra->proxy)){
                                                echo zget($users, $extra->proxy, '');
                                                echo "【".zget($users, $realReviewer->reviewer)."授权处理】";
                                            }else{
                                                echo zget($users, $realReviewer->reviewer, '');
                                            }?>）
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $realReviewer->comment; ?></td>
                                <td><?php echo $realReviewer->reviewTime; ?></td>
                            </tr>

                        <?php endif;?>

                         <?php $j++;?>
                        <?php endforeach; ?>
                        <?php if(isset($reviewFailReason[$nk]) && !empty($reviewFailReason[$nk])):
                        foreach ($reviewFailReason[$nk] as $value):
                        foreach ($value as $itemNode => $item):
                        ?>
                        <tr>
                            <th><?php echo $lang->outwarddelivery->outerReviewNodeList[$item['reviewNode']]; ?></th>
                            <td><?php echo zget($users, $item['reviewUser'], ','); ?></td>
                            <td><?php echo $item['reviewResult'] ?></td>
                            <td><?php echo $item['reviewFailReason'] ?></td>
                            <td><?php echo $item['reviewPushDate'] ?></td>
                        </tr>
                        <?php endforeach; endforeach; endif; ?>

<!--                        --><?php //if ($outwarddelivery->isNewTestingRequest): ?>
<!--                            --><?php //if(isset($reviewFailReason[$nk][0]) && !empty($reviewFailReason[$nk][0])) :?>
<!--                                <tr>-->
<!--                                    <th>--><?php //echo $lang->outwarddelivery->outerReviewNodeList['0']; ?><!--</th>-->
<!--                                    <td>-->
<!--                                        --><?php //echo zget($users, 'guestjk', ','); ?>
<!--                                    </td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][0]['reviewResult']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][0]['reviewFailReason']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][0]['reviewPushDate']?><!--</td>-->
<!--                                </tr>-->
<!--                            --><?php //endif;?>
<!--                            --><?php //if(isset($reviewFailReason[$nk][1]) && !empty($reviewFailReason[$nk][1])) :?>
<!--                                <tr>-->
<!--                                    <th>--><?php //echo $lang->outwarddelivery->outerReviewNodeList['1']; ?><!--</th>-->
<!--                                    <td>-->
<!--                                        --><?php //echo zget($users, 'guestcn', ','); ?>
<!--                                    </td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][1]['reviewResult']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][1]['reviewFailReason']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][1]['reviewPushDate']?><!--</td>-->
<!--                                </tr>-->
<!--                            --><?php //endif;?>
<!--                        --><?php //endif; ?>
<!--                        --><?php //if ($outwarddelivery->isNewProductEnroll): ?>
<!--                            --><?php //if(isset($reviewFailReason[$nk][2]) && !empty($reviewFailReason[$nk][2])) :?>
<!--                                <tr>-->
<!--                                    <th>--><?php //echo $lang->outwarddelivery->outerReviewNodeList['2']; ?><!--</th>-->
<!--                                    <td>-->
<!--                                        --><?php //echo zget($users, 'guestjk', ','); ?>
<!--                                    </td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][2]['reviewResult']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][2]['reviewFailReason']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][2]['reviewPushDate']?><!--</td>-->
<!--                                </tr>-->
<!--                            --><?php //endif;?>
<!--                            --><?php //if(isset($reviewFailReason[$nk][3]) && !empty($reviewFailReason[$nk][3])) :?>
<!--                                <tr>-->
<!--                                    <th>--><?php //echo $lang->outwarddelivery->outerReviewNodeList['3']; ?><!--</th>-->
<!--                                    <td>-->
<!--                                        --><?php //echo zget($users, 'guestcn', ','); ?>
<!--                                    </td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][3]['reviewResult']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][3]['reviewFailReason']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][3]['reviewPushDate']?><!--</td>-->
<!--                                </tr>-->
<!--                            --><?php //endif;?>
<!--                        --><?php //endif; ?>
<!--                        --><?php //if ($outwarddelivery->isNewModifycncc): ?>
<!--                            --><?php //if(isset($reviewFailReason[$nk][4]) && !empty($reviewFailReason[$nk][4])) :?>
<!--                                <tr>-->
<!--                                    <th>--><?php //echo $lang->outwarddelivery->outerReviewNodeList['4']; ?><!--</th>-->
<!--                                    <td>-->
<!--                                        --><?php //echo zget($users, 'guestjk', ','); ?>
<!--                                    </td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][4]['reviewResult']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][4]['reviewFailReason']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][4]['reviewPushDate']?><!--</td>-->
<!--                                </tr>-->
<!--                            --><?php //endif;?>
<!--                            --><?php //if(isset($reviewFailReason[$nk][5]) && !empty($reviewFailReason[$nk][5])) :?>
<!--                                <tr>-->
<!--                                    <th>--><?php //echo $lang->outwarddelivery->outerReviewNodeList['5']; ?><!--</th>-->
<!--                                    <td>-->
<!--                                        --><?php //echo zget($users, 'guestcn', ','); ?>
<!--                                    </td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][5]['reviewResult']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][5]['reviewFailReason']?><!--</td>-->
<!--                                    <td>--><?php //echo $reviewFailReason[$nk][5]['reviewPushDate']?><!--</td>-->
<!--                                </tr>-->
<!--                            --><?php //endif;?>
<!--                        --><?php //endif; ?>
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
