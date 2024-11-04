<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<style>
    td p {
        margin-bottom: 0;
    }

    .w-175px {
        width: 175px;
    }

    .task-toggle {
        line-height: 28px;
        color: #0c60e1;
        cursor: pointer;
    }

    .task-toggle .icon {
        display: inline-block;
        transform: rotate(90deg);
    }
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php $browseLink = $app->session->projectplanList != false ? $app->session->projectplanList : inlink('browse'); ?>
        <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php printf('%03d', $plan->id); ?></span>
            <span class="text" title='<?php echo $plan->name; ?>'><?php echo $plan->name; ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->content; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($plan->content) ? $plan->content : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->planStages; ?> </div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <?php
                        foreach ($planStages as $planStage):
                            if (empty($planStage['stageBegin'])) continue; ?>
                            <tr>

                                <td class='text-ellipsis'><span
                                            class="stageName"></span>:<?php echo "{$planStage['stageBegin']}~{$planStage['stageEnd']}"; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if ($ChangeList):    ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->projectplan->planChangeInfo;?><span id="showAllChangeInfo" onclick="showAllChangeInfo()" class="pull-right text-blue"><?php echo $lang->projectplan->clickShowChangeInfo;?></span></div>
                    <div class="detail-content article-content">
                        <table class="table ops">
                            <tr>
                                <th class="w-100px text-center" ><?php echo $lang->projectplan->changeHistoryTime;?></th>
                                <th class="text-center"><?php echo $lang->projectplan->planChangeInfo;?></th>
                                <th class="w-120px text-center"><?php echo $lang->projectplan->changeContent;?></th>
                                <th class="w-120px text-center" ><?php echo $lang->projectplan->auditResults;?></th>
                            </tr>
                            <?php foreach ($ChangeList as $key => $list): ?>
                            <?php if($list->status == 'reject' || $list->isreview == 2){
                                continue;
                                } ?>
                                <tr>
                                    <td class="w-100px" ><?php echo $list->createdDate; ?></td>
                                    <td ><?php echo $list->planRemark; ?></td>
                                    <td class="text-center"><button href="<?php echo $this->createLink('projectplan', 'ajaxshowdiffchange', "changeID=$list->id");?>" onclick="showajaxdiff(this)" class="btn" data-app="platform">查看</button></td>
                                    <td class="w-100px text-center"><?php echo $lang->projectplan->changeStatus[$list->status]; ?>
                                        <?php
                                        if($list->isreview == 2){
                                            echo '('.$lang->projectplan->noChangeplanReview.')';
                                        }

                                        ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->planRemark;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($plan->planRemark) ? $plan->planRemark : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($changeYearNodesList)): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->projectplan->changeReviewComment; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($changeYearNodesList)): ?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-200px"><?php echo $lang->projectplan->node; ?></th>
                                    <td class="w-100px"><?php echo $lang->projectplan->reviewer; ?></td>
                                    <td class="w-120px"><?php echo $lang->projectplan->reviewResult; ?></td>
                                    <td class="w-160px"><?php echo $lang->projectplan->dealDate; ?></td>
                                    <td><?php echo $lang->projectplan->reviewComment; ?></td>
                                </tr>
                                <?php foreach ($changeYearNodesList as $key => $changeyearNodes): ?>
                                <?php foreach ($changeyearNodes as $nodekey => $node) {
                                    ?>
                                        <?php $reviewedCount = count($node->reviewers); ?>
                                            <?php if($reviewedCount>1){
                                            foreach ($node->reviewers as $reviewkey=>$review){
                                                    if($reviewkey==0){
                                                        ?>
                                                        <tr>
                                                            <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $node->nodeCode, ''); ?></th>
                                                            <td><?php echo zget($users, $review->reviewer, ''); ?></td>


                                                            <?php if ($nodekey==1 && !empty($changeyearNodes[2]) && $review->status =='pass'): ?>
                                                                <td><?php echo zget($lang->projectplan->reviewStatusList, $review->status, '')."(并上报)"; ?></td>
                                                            <?php elseif($nodekey==1 && empty($changeyearNodes[2]) && $review->status =='pass'):?>
                                                                <td><?php echo zget($lang->projectplan->reviewStatusList, $review->status, '')."(直接通过)"; ?></td>
                                                            <?php else: ?>
                                                                <td><?php echo zget($lang->projectplan->reviewStatusList, $review->status, ''); ?></td>
                                                            <?php endif; ?>


                                                            <td><?php echo $review->reviewTime; ?></td>
                                                            <td><?php echo $review->comment; ?></td>
                                                        </tr>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <tr>

                                                            <td><?php echo zget($users, $review->reviewer, ''); ?></td>


                                                            <?php if ($nodekey==1 && !empty($changeyearNodes[2]) && $review->status =='pass'): ?>
                                                                <td><?php echo zget($lang->projectplan->reviewStatusList, $review->status, '')."(并上报)"; ?></td>
                                                            <?php elseif($nodekey==1 && empty($changeyearNodes[2]) && $review->status =='pass'):?>
                                                                <td><?php echo zget($lang->projectplan->reviewStatusList, $review->status, '')."(直接通过)"; ?></td>
                                                            <?php else: ?>
                                                                <td><?php echo zget($lang->projectplan->reviewStatusList, $review->status, ''); ?></td>
                                                            <?php endif; ?>


                                                            <td><?php echo $review->reviewTime; ?></td>
                                                            <td><?php echo $review->comment; ?></td>
                                                        </tr>
                                                   <?php
                                                    }
                                            }

                                        }else{
                                                ?>

                                            <tr>
                                                <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $node->nodeCode, ''); ?></th>
                                                <td><?php echo zget($users, $node->reviewers[0]->reviewer, ''); ?></td>


                                                <?php if ($nodekey==1 && !empty($changeyearNodes[2]) && $changeyearNodes[1]->reviewers[0]->status =='pass'): ?>
                                                    <td><?php echo zget($lang->projectplan->reviewStatusList, $changeyearNodes[1]->reviewers[0]->status, '')."(并上报)"; ?></td>
                                                <?php elseif($nodekey==1 && empty($changeyearNodes[2]) && $changeyearNodes[1]->reviewers[0]->status =='pass'):?>
                                                    <td><?php echo zget($lang->projectplan->reviewStatusList, $changeyearNodes[1]->reviewers[0]->status, '')."(直接通过)"; ?></td>
                                                <?php else: ?>
                                                    <td><?php echo zget($lang->projectplan->reviewStatusList, $node->reviewers[0]->status, ''); ?></td>
                                                <?php endif; ?>


                                                <td><?php echo $node->reviewers[0]->reviewTime; ?></td>
                                                <td><?php echo $node->reviewers[0]->comment; ?></td>
                                            </tr>

                                        <?php
                                        }
                                        ?>


                                    <?php
                                }
                                ?>

                                    <?php if ($key + 1 != 1): ?>
                                        <tr>
                                            <td colspan="5"></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($yearNodes)): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->projectplan->yearReviewComment; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($yearNodes)): ?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-200px"><?php echo $lang->projectplan->node; ?></th>
                                    <td class="w-100px"><?php echo $lang->projectplan->reviewer; ?></td>
                                    <td class="w-120px"><?php echo $lang->projectplan->reviewResult; ?></td>
                                    <td class="w-160px"><?php echo $lang->projectplan->dealDate; ?></td>
                                    <td><?php echo $lang->projectplan->reviewComment; ?></td>
                                </tr>

                                <tr>
                                    <th rowspan="<?php echo count($yearNodes[0]->reviewers); ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $yearNodes[0]->nodeCode, ''); ?></th>
                                    <td><?php echo zget($users, $yearNodes[0]->reviewers[0]->reviewer, ''); ?></td>
                                    <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[0]->reviewers[0]->status, ''); ?></td>
                                    <td><?php echo $yearNodes[0]->reviewers[0]->reviewTime; ?></td>
                                    <td><?php echo $yearNodes[0]->reviewers[0]->comment; ?></td>
                                </tr>
                                <?php if (isset($allow) && $isShow == true): ?>
                                    <?php if (!empty($yearNodes[1])) { ?>
                                        <?php $reviewedCount = count($yearNodes[1]->reviewers); ?>
                                        <tr>
                                            <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $yearNodes[1]->nodeCode, ''); ?></th>
                                            <td><?php echo zget($users, $yearNodes[1]->reviewers[0]->reviewer, ''); ?></td>
                                            <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[1]->reviewers[0]->status, ''); ?></td>
                                            <td><?php echo $yearNodes[1]->reviewers[0]->reviewTime; ?></td>
                                            <td><?php echo $yearNodes[1]->reviewers[0]->comment; ?></td>
                                        </tr>
                                        <?php if ($reviewedCount > 1): ?>
                                            <?php for ($i = 1; $i < $reviewedCount; $i++): ?>
                                                <tr>
                                                    <td><?php echo zget($users, $yearNodes[1]->reviewers[$i]->reviewer, ''); ?></td>
                                                    <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[1]->reviewers[$i]->status, ''); ?></td>
                                                    <td><?php echo $yearNodes[1]->reviewers[$i]->reviewTime ?></td>
                                                    <td><?php echo $yearNodes[1]->reviewers[$i]->comment ?></td>
                                                </tr>
                                            <?php endfor; ?>
                                        <?php endif;
                                    }
                                    ?>

                                    <?php if (!empty($yearNodes[2])) { ?>
                                        <tr>
                                            <?php $reviewedCount = count($yearNodes[2]->reviewers); ?>
                                            <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $yearNodes[2]->nodeCode, ''); ?></th>
                                            <td><?php echo zget($users, $yearNodes[2]->reviewers[0]->reviewer, ''); ?></td>
                                            <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[2]->reviewers[0]->status, ''); ?></td>
                                            <td><?php echo $yearNodes[2]->reviewers[0]->reviewTime ?></td>
                                            <td><?php echo $yearNodes[2]->reviewers[0]->comment ?></td>
                                        </tr>

                                        <?php if ($reviewedCount > 1): ?>
                                            <?php for ($i = 1; $i < $reviewedCount; $i++): ?>
                                                <tr>
                                                    <td><?php echo zget($users, $yearNodes[2]->reviewers[$i]->reviewer, ''); ?></td>
                                                    <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[2]->reviewers[$i]->status, ''); ?></td>
                                                    <td><?php echo $yearNodes[2]->reviewers[$i]->reviewTime ?></td>
                                                    <td><?php echo $yearNodes[2]->reviewers[$i]->comment ?></td>
                                                </tr>
                                            <?php endfor; ?>
                                        <?php endif;
                                    }
                                    ?>
                                <?php endif; ?>

                                <?php if (!empty($yearNodes[3])): ?>
                                    <tr>
                                        <?php $reviewedCount = count($yearNodes[3]->reviewers); ?>
                                        <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $yearNodes[3]->nodeCode, ''); ?></th>
                                        <td><?php echo zget($users, $yearNodes[3]->reviewers[0]->reviewer, ''); ?></td>
                                        <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[3]->reviewers[0]->status, ''); ?></td>
                                        <td><?php echo $yearNodes[3]->reviewers[0]->reviewTime; ?></td>
                                        <td><?php echo $yearNodes[3]->reviewers[0]->comment; ?></td>
                                    </tr>
                                <?php endif; ?>

<!--                                --><?php //if (!empty($yearNodes[4])): ?>
<!--                                    <tr>-->
<!--                                        --><?php //$reviewedCount = count($yearNodes[4]->reviewers); ?>
<!--                                        <th rowspan="--><?php //echo $reviewedCount; ?><!--">--><?php //echo zget($this->lang->projectplan->nodeCodeDesc, $yearNodes[4]->nodeCode, ''); ?><!--</th>-->
<!--                                        <td>--><?php //echo zget($users, $yearNodes[4]->reviewers[0]->reviewer, ''); ?><!--</td>-->
<!--                                        <td>--><?php //echo zget($lang->projectplan->reviewStatusList, $yearNodes[4]->reviewers[0]->status, ''); ?><!--</td>-->
<!--                                        <td>--><?php //echo $yearNodes[4]->reviewers[0]->reviewTime; ?><!--</td>-->
<!--                                        <td>--><?php //echo $yearNodes[4]->reviewers[0]->comment; ?><!--</td>-->
<!--                                    </tr>-->
<!--                                --><?php //endif; ?>



                                 <?php if (!empty($yearNodes[4])) { ?>
                                        <tr>
                                            <?php $reviewedCount = count($yearNodes[4]->reviewers); ?>
                                            <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $yearNodes[4]->nodeCode, ''); ?></th>
                                            <td><?php echo zget($users, $yearNodes[4]->reviewers[0]->reviewer, ''); ?></td>
                                            <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[4]->reviewers[0]->status, ''); ?></td>
                                            <td><?php echo $yearNodes[4]->reviewers[0]->reviewTime ?></td>
                                            <td><?php echo $yearNodes[4]->reviewers[0]->comment ?></td>
                                        </tr>

                                        <?php if ($reviewedCount > 1): ?>
                                            <?php for ($i = 1; $i < $reviewedCount; $i++): ?>
                                                <tr>
                                                    <td><?php echo zget($users, $yearNodes[4]->reviewers[$i]->reviewer, ''); ?></td>
                                                    <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[4]->reviewers[$i]->status, ''); ?></td>
                                                    <td><?php echo $yearNodes[4]->reviewers[$i]->reviewTime ?></td>
                                                    <td><?php echo $yearNodes[4]->reviewers[$i]->comment ?></td>
                                                </tr>
                                            <?php endfor; ?>
                                        <?php endif;
                                    }
                                    ?>

                                <?php if (!empty($yearNodes[5])): ?>
                                    <tr>
                                        <?php $reviewedCount = count($yearNodes[5]->reviewers); ?>
                                        <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($this->lang->projectplan->nodeCodeDesc, $yearNodes[5]->nodeCode, ''); ?></th>
                                        <td><?php echo zget($users, $yearNodes[5]->reviewers[0]->reviewer, ''); ?></td>
                                        <td><?php echo zget($lang->projectplan->reviewStatusList, $yearNodes[5]->reviewers[0]->status, ''); ?></td>
                                        <td><?php echo $yearNodes[5]->reviewers[0]->reviewTime; ?></td>
                                        <td><?php echo $yearNodes[5]->reviewers[0]->comment; ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($bookNodes)): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->projectplan->bookReviewComment; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($bookNodes)): ?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-200px"><?php echo $lang->projectplan->node; ?></th>
                                    <td class="w-100px"><?php echo $lang->projectplan->reviewer; ?></td>
                                    <td class="w-150px"><?php echo $lang->projectplan->involved; ?></td>
                                    <td class="w-100px"><?php echo $lang->projectplan->reviewResult; ?></td>
                                    <td><?php echo $lang->projectplan->reviewComment; ?></td>
                                </tr>
                                <?php
                                foreach ($bookNodes as $bnode){
                                    $reviewedCount = count($bnode->reviewers);
                                    foreach ($bnode->reviewers as $k=>$bnReviewer){
                                        if($k == 0){
                                        ?>
                                        <tr>
                                            <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($lang->projectplan->submitnodeCodeDesc,$bnode->nodeCode); ?></th>
                                            <td><?php echo zget($users, $bnReviewer->reviewer, ''); ?></td>
                                            <td>
                                            <?php
                                            if(in_array($bnode->nodeCode,$lang->projectplan->reviewinvolvedNode) ){
                                                $involved = json_decode($bnReviewer->extra);
                                                if ($involved && $involved->involved) {
                                                    foreach ($involved->involved as $u) echo zget($users, $u, '') . ' ';
                                                }
                                            }
                                            ?>
                                            </td>
                                            <td><?php echo zget($lang->projectplan->reviewStatusList, $bnReviewer->status, ''); ?></td>
                                            <td><?php echo $bnReviewer->comment ?></td>
                                        </tr>
                                        <?php
                                        }else{
                                            ?>
                                            <tr>

                                                <td><?php echo zget($users, $bnReviewer->reviewer, ''); ?></td>
                                                <td><?php
                                                    if(in_array($bnode->nodeCode,$lang->projectplan->reviewinvolvedNode) ){
                                                        $involved = json_decode($bnReviewer->extra);
                                                        if ($involved && $involved->involved) {
                                                            foreach ($involved->involved as $u) echo zget($users, $u, '') . ' ';
                                                        }
                                                    }
                                                    ?></td>
                                                <td><?php echo zget($lang->projectplan->reviewStatusList, $bnReviewer->status, ''); ?></td>
                                                <td><?php echo $bnReviewer->comment ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                                    ?>
                                    

                            </table>
                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->projectCreation; ?></div>
                <div class="detail-content article-content">
                    <?php if (empty($plan->creation)): ?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php else: ?>
                        <table class='table'>
                            <tr>
                                <th class='w-175px'><?php echo $lang->projectplan->code; ?></th>
                                <td><?php echo $plan->creation->code; ?></td>
                                <th class='w-80px'><?php echo $lang->projectplan->mark; ?></th>
                                <td><?php echo $plan->creation->mark; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->name; ?></th>
                                <td colspan='3'><?php echo $plan->creation->name; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->PM; ?></th>
                                <td><?php echo zget($users, $plan->creation->PM, ''); ?></td>
                                <th><?php echo $lang->projectplan->dept; ?></th>
                                <td>
                                    <?php
                                    $plan->creation->dept = isset($plan->creation->dept) ? explode(',', $plan->creation->dept) : [];
                                    foreach ($plan->creation->dept as $dept) {
                                        echo zget($depts, $dept, '') . PHP_EOL;
                                    };
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->source; ?></th>
                                <td colspan='3'>
                                    <?php
                                    //2022-4-25 tongyanqi 立项来源改多选
                                    $sources = [];
                                    foreach (explode(',', $plan->creation->source) as $source) {
                                        if (empty($source)) continue;
                                        $sources[] = zget($lang->projectplan->basisList, $source, '');
                                    }
                                    echo implode(',', $sources);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->opinion->union; ?></th>
                                <td colspan='3'>
                                    <?php
                                    foreach (explode(',', $plan->creation->union) as $union) {
                                        echo zget($lang->opinion->unionList, $union, '') . ' ';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->type; ?></th>
                                <td colspan='3'><?php echo zget($lang->projectplan->typeList, $plan->creation->type, ''); ?></td>
                            </tr>
                            <tr>

                                <th><?php echo $lang->projectplan->linkPlan; ?></th>
                                <td colspan='3'>
                                    <?php
                                    if (isset($plan->creation->linkPlan)) {
                                        $linkPlans = explode(',', str_replace(' ', '', $plan->creation->linkPlan));
                                        $i = 0;
                                        $linkPlanLen = count($linkPlans);
                                        foreach ($linkPlans as $linkPlan) {
                                            zget($plans, $linkPlan, '') ? html::a(common::printLink('projectplan', 'planview', "appid=$linkPlan", zget($plans, $linkPlan), '', 'class="iframe"', '', true)) : '';
                                            $i++;
                                            if ($i <> $linkPlanLen) echo ', '; //2020-04-21 tongyanqi 项目分割
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->beginAndEnd; ?></th>
                                <td colspan='3'><?php echo $plan->creation->begin . ' - ' . $plan->creation->end; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->workload . "({$lang->projectplan->monthly})"; ?></th>
                                <td colspan='3'><?php echo $plan->creation->workload; ?></td>
                            </tr>
                           <!-- <tr>
                                <th><?php /*echo $lang->projectplan->workloadBase . "({$lang->projectplan->monthly})"; */?></th>
                                <td colspan='3'><?php /*echo $plan->workloadBase; */?></td>

                            </tr>
                            <tr>
                                <th><?php /*echo $lang->projectplan->workloadChengdu . "({$lang->projectplan->monthly})"; */?></th>
                                <td colspan='3'><?php /*echo $plan->workloadChengdu; */?></td>
                            </tr>
                            <tr>
                                <th><?php /*echo $lang->projectplan->nextYearWorkloadBase . "({$lang->projectplan->monthly})"; */?></th>
                                <td colspan='3'><?php /*echo $plan->nextYearWorkloadBase; */?></td>
                            </tr>
                            <tr>
                                <th><?php /*echo $lang->projectplan->nextYearWorkloadChengdu . "({$lang->projectplan->monthly})"; */?></th>
                                <td colspan='3'><?php /*echo $plan->nextYearWorkloadChengdu; */?></td>
                            </tr>-->
                            <tr>
                                <th><?php echo $lang->projectplan->background; ?></th>
                                <td colspan='3'><?php echo $plan->creation->background; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->range; ?></th>
                                <td colspan='3'><?php echo $plan->creation->range; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->goal; ?></th>
                                <td colspan='3'><?php echo $plan->creation->goal; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->stakeholder; ?></th>
                                <td colspan='3'><?php echo $plan->creation->stakeholder; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->verify; ?></th>
                                <td colspan='3'><?php echo $plan->creation->verify; ?></td>
                            </tr>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($plan->creation) echo $this->fetch('file', 'printFiles', array('files' => $plan->creation->files, 'fieldset' => 'true')); ?>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=projectplan&objectID=$plan->id"); ?>
        </div>
        <div class="cell"><?php include '../../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($browseLink); ?>
                <div class='divider'></div>
                <?php
                common::printIcon('projectplan', 'initProject', "projectplanID=$plan->id&creationID=$creationID", $plan, 'button', 'file-text');

                // 判断是否审批年度计划
                if (in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearreject'))) {
                    common::printIcon('projectplan', 'yearReview', "projectplanID=$plan->id", $plan, 'button', 'start', '', 'iframe', true, '', $this->lang->projectplan->yearReview);
                    common::printIcon('projectplan', 'yearReviewing', "projectplanID=$plan->id", $plan, 'button', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->yearReviewing);
                } else if (in_array($plan->status, array('yearpass', 'start'))) {
                    if ($plan->changeStatus != 'pending') {
                        common::printIcon('projectplan', 'submit', "projectplanID=$plan->id", $plan, 'button', 'start', '', 'iframe', true);
                        echo "<button type='button' class='disabled btn' title='" . $this->lang->projectplan->review . "' style='pointer-events: unset;'><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
//                                        echo "<button type='button' class='disabled btn' title='".$this->lang->projectplan->submit."' style='pointer-events: unset;'><i class='icon-common-start disabled icon-start'></i></button>\n";
                        common::printIcon('projectplan', 'planChange', "id=$plan->id", $plan, 'button', 'feedback', '', '', '', $lang->projectplan->planChange);
                    } else {
                        if ($plan->reviewers == $this->app->user->account) {
                            common::printIcon('projectplan', 'changeReview', "projectplanID=$plan->id", $plan, 'button', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->changeReview);
                        } else {
                            echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->changeReview . " '><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                        }
                        common::printIcon('projectplan', 'submit', "projectplanID=$plan->id", $plan, 'button', 'start', '', 'iframe', true);
                        echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->planChange . " '><i class='icon-common-feedback disabled icon-feedback'></i></button>\n";
                    }
                } else {
                    common::printIcon('projectplan', 'submit', "projectplanID=$plan->id", $plan, 'button', 'start', '', 'iframe', true);
                    common::printIcon('projectplan', 'review', "projectplanID=$plan->id", $plan, 'button', 'glasses', '', 'iframe', true, '', $plan->reviewStage == 2 ? $this->lang->projectplan->involved : $this->lang->projectplan->review);
                }
                if ($plan->status == 'pass') {
                    common::printIcon('projectplan', 'exec', "projectplanID=$plan->id", $plan, 'button', 'run', '', 'iframe', true);
                } else {
                    common::printIcon('projectplan', 'edit', "projectplanID=$plan->id", $plan, 'button');
                }
                common::printIcon('projectplan', 'execEdit', "id=$plan->id", $plan, 'button', 'change', '', '', '', '', $lang->projectplan->execEdit);
                common::printIcon('projectplan', 'delete', "projectplanID=$plan->id", $plan, 'button', 'trash', 'hiddenwin');
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->opinion->category; ?></th>
                            <td><?php echo zget($lang->opinion->categoryList, $plan->category, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->status; ?></th>
                            <td><?php echo zget($lang->projectplan->statusList, $plan->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->insideStatus; ?></th>
                            <td><?php echo zget($lang->projectplan->insideStatusList, $plan->insideStatus, ''); ?><span
                                        class='c-actions text-left'><?php
                                    common::printIcon('projectplan', 'editStatus', "id=$plan->id", $plan, 'list', 'edit', '', 'iframe', true);
                                    ?>
                    </span></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->year; ?></th>
                            <td><?php echo $plan->year; ?></td>
                        </tr>
                        <tr <?php if($plan->secondLine != 1) echo 'class="hidden"' ?>>
                            <th><?php echo $lang->projectplan->secondLine; ?></th>
                            <td><?php echo zget($lang->projectplan->secondLineList, $plan->secondLine, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->isImportant; ?></th>
                            <td><?php echo zget($lang->projectplan->isImportantList, $plan->isImportant, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->isDelayPreYear; ?></th>
                            <td><?php echo zget($lang->projectplan->isDelayPreYearList, $plan->isDelayPreYear, ''); ?><span
                                        class='c-actions text-left'><?php
                                common::printIcon('projectplan', 'editDelayYear', "id=$plan->id", $plan, 'list', 'edit', '', 'iframe', true);
                                    ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->projectplan->aleadyRelationSlaveProject; ?></th>
                            <td colspan="3">
                                <?php
                                if($mainRelationInfo){
                                $slaveRelationArr = explode(",",$mainRelationInfo->slavePlanID);
                                $tempslaveProjectPlanStr = '';
                                foreach($slaveRelationArr as $slave){
                                    $tempname = zget($relationProjectplanList,$slave,'');
                                    if($tempname){
                                        $link = $this->createLink('projectplan', 'view',"planID=$slave");
                                        $htmla = html::a($link, $tempname, '');
                                        $tempslaveProjectPlanStr .= $htmla.'<br />';
                                    }

                                }
                                if($tempslaveProjectPlanStr){
                                    echo rtrim($tempslaveProjectPlanStr,',');
                                }else{
                                    echo $lang->projectplan->norelation;
                                }

                                }else{
                                    echo $lang->projectplan->norelation;
                                }

                                 ?>
                                <span
                                        class='c-actions text-left'><?php
                                    common::printIcon('projectplanmsrelation', 'maintenanceRelation', "planID=$plan->id", $plan, 'list', 'edit', '', 'iframe', true);
                                    ?></span>
                            </td>
                        </tr>


                        <tr>
                            <th><?php echo $lang->projectplan->ownerMainProject; ?></th>

                            <td colspan="3">
                                <?php

                                if($slaveRelationInfo) {

                                    $tempslaveProjectPlanStr = '';
                                    foreach ($slaveRelationInfo as $mainRelation) {
                                        $tempname = zget($relationProjectplanList, $mainRelation->mainPlanID, '') ;
                                        if ($tempname) {
                                            $link = $this->createLink('projectplan', 'view',"planID=$mainRelation->mainPlanID");
                                            $htmla = html::a($link, $tempname, '');
                                            $tempslaveProjectPlanStr .= $htmla. '<br />';
                                        }

                                    }
                                    if($tempslaveProjectPlanStr){
                                        echo rtrim($tempslaveProjectPlanStr, ',');
                                    }else{
                                        echo $lang->projectplan->norelation;
                                    }

                                }else{
                                    echo $lang->projectplan->norelation;
                                }

                                 ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->projectplan->planCode; ?></th>
                            <td colspan='3'><?php echo $plan->planCode; if($plan->oldPlanCode) echo "($plan->oldPlanCode)"; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->code; ?></th>
                            <td><?php echo $plan->code; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->mark; ?></th>
                            <td><?php echo $plan->mark; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->type; ?></th>
                            <td><?php echo zget($lang->projectplan->typeList, $plan->type, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->basis; ?></th>
                            <td colspan='3'><?php
                                $basisList = explode(',', str_replace(' ', '', $plan->basis));
                                foreach ($basisList as $a) {
                                    if (empty($a)) continue;
                                    echo zget($lang->projectplan->basisList, $a, '') . '<br>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->app; ?></th>
                            <td colspan='3'>
                                <?php
                                $appList = explode(',', str_replace(' ', '', $plan->app));
                                foreach ($appList as $a) {
                                    if (zget($apps, $a, '')) {
                                        echo html::a($this->createLink('application', 'view', "appID=$a"), zget($apps, $a, ''), '_blank') . "<br>";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php if ($plan->type == 1 || $plan->type == 2) { // 研发8项 ?>
                            <tr>
                                <th><?php echo $lang->projectplan->storyStatus; ?></th>
                                <td><?php echo zget($lang->projectplan->storyStatusList, $plan->storyStatus, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->dataEnterLake; ?></th>
                                <td><?php echo zget($lang->projectplan->dataEnterLakeList, $plan->dataEnterLake, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->basicUpgrade; ?></th>
                                <td><?php echo zget($lang->projectplan->basicUpgradeList, $plan->basicUpgrade, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->systemAssemble; ?></th>
                                <td><?php echo zget($lang->projectplan->systemAssembleList, $plan->systemAssemble, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->cloudComputing; ?></th>
                                <td><?php echo zget($lang->projectplan->cloudComputingList, $plan->cloudComputing, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->passwordChange; ?></th>
                                <td><?php echo zget($lang->projectplan->passwordChangeList, $plan->passwordChange, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->projectplan->structure; ?></th>
                                <td><?php echo zget($lang->projectplan->structureList, $plan->structure); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->projectplan->localize; ?></th>
                                <td><?php echo zget($lang->projectplan->localizeList, $plan->localize, '');

                                    ?></td>
                            </tr>
                        <?php } //研发8项 end ?>


                        <tr>
                            <th><?php echo $lang->projectplan->platformowner; ?></th>
                            <td>
                                <?php
                                $platformownerArr = explode(',', str_replace(' ', '', $plan->platformowner));
                                foreach ($platformownerArr as $platformowner) {
                                    if ($platformowner) echo ' ' . zget($lang->projectplan->platformownerList, $platformowner, '').'<br />';
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->projectplan->outsideProject; ?></th>
                            <td colspan='3'>
                                <?php
                                if (isset($plan->outsideProject)) {
                                    $ps = explode(',', str_replace(' ', '', $plan->outsideProject));

                                    foreach ($ps as $planID) {
                                        if (!empty(zget($outsideproject, $planID, ''))) echo html::a($this->createLink('outsideplan', 'view', "planID=$planID"), zget($outsideproject, $planID, ''), '_blank') . '<br>';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->outsideSubProject; ?></th>
                            <td colspan='3'>
                                <?php
                                foreach ($outsideSubProject as $outsideSubProjectItem) {
                                    if (isset($outsideSubProjectItem->subProjectName)) echo html::a($this->createLink('outsideplan', 'view', "appID=$outsideSubProjectItem->outsideProjectPlanID"), $outsideSubProjectItem->subProjectName, '_blank') . '<br>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->outsideTask; ?></th>
                            <td colspan='3'>
                                <?php
                                foreach ($outsideTask as $taskItem) {
                                    echo html::a($this->createLink('outsideplan', 'view', "appID=$taskItem->outsideProjectPlanID"), $taskItem->subTaskName, '_blank') . '<br>';
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->projectplan->begin; ?></th>
                            <td><?php echo $plan->begin == '0000-00-00' ? '' : $plan->begin; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->end; ?></th>
                            <td><?php echo $plan->end == '0000-00-00' ? '' : $plan->end; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->workload; ?></th>
                            <td><?php if ($plan->workload) echo $plan->workload . ' ' . $lang->projectplan->monthly; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->workloadBase . "({$lang->projectplan->monthly})"; ?></th>
                            <td colspan='3'><?php echo $plan->workloadBase . ' ' . $lang->projectplan->monthly; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->workloadChengdu . "({$lang->projectplan->monthly})"; ?></th>
                            <td colspan='3'><?php echo $plan->workloadChengdu . ' ' . $lang->projectplan->monthly; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->nextYearWorkloadBase . "({$lang->projectplan->monthly})"; ?></th>
                            <td colspan='3'><?php echo $plan->nextYearWorkloadBase . ' ' . $lang->projectplan->monthly; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->nextYearWorkloadChengdu . "({$lang->projectplan->monthly})"; ?></th>
                            <td colspan='3'><?php echo $plan->nextYearWorkloadChengdu . ' ' . $lang->projectplan->monthly; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->duration; ?></th>
                            <td><?php if ($plan->duration) echo $plan->duration . ' ' . $lang->projectplan->day; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->bearDept; ?></th>
                            <td colspan='3'><?php
                                $bearDepts = isset($plan->bearDept) ? explode(',', $plan->bearDept) : [];
                                foreach ($bearDepts as $dept) {
                                    echo zget($depts, $dept, '') . '<br />';
                                };
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->depts; ?></th>
                            <td colspan='3'>
                                <?php
                                if (isset($plan->depts)) {
                                    $planDepts = explode(',', str_replace(' ', '', $plan->depts));
                                    foreach ($planDepts as $deptID) {
                                        if ($deptID) echo ' ' . zget($depts, $deptID, ''). '<br />';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->reviewDate; ?></th>
                            <td colspan='3'><?php echo $plan->reviewDate === '0000-00-00' ? '' : $plan->reviewDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->owner; ?></th>
                            <td colspan='3'>
                                <?php
                                $owners = isset($plan->owner) ? explode(',', $plan->owner) : [];
                                foreach ($owners as $owner) {
                                    echo zget($users, $owner, '') . ' ';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->phone; ?></th>
                            <td colspan='3'><?php echo $plan->phone; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->createdBy; ?></th>
                            <td colspan='3'><?php echo zget($users, $plan->createdBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->projectplan->createdDate; ?></th>
                            <td colspan='3'><?php echo $plan->createdDate; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--<div class="cell">
            <div class="detail">
                <div class="detail-title"><?php /*echo $lang->projectplan->productsPlanPeriod; */?> </div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <?php
/*                        foreach ($productsRelated as $productRelated):*/?>
                            <tr>
                                <?php
/*                                if (empty($productRelated['productId'])) continue;

                                */?>
                                <td class='text-ellipsis'><?php /*echo html::a($this->createLink('product', 'view', 'id=' . $productRelated['productId']), $productRelated['productName'], '', "title={$productRelated['productName']}({$productRelated['realRelease']}~{$productRelated['realOnline']})");
                                    echo "({$productRelated['realRelease']}~{$productRelated['realOnline']})"; */?></td>

                            </tr>
                        <?php /*endforeach; */?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>-->
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->product; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <?php $productIndex = 0; ?>
                        <?php foreach ($products as $product): ?>
                            <?php if ($productIndex <= 5): ?>
                                <tr>
                                    <td class='text-ellipsis'><?php echo html::a($this->createLink('product', 'view', 'id=' . $product['productID']), $product['productName'], '', "title={$product['productName']}"); ?></td>
                                    <td class='text-ellipsis'><?php if ($product['planID']) echo html::a($this->createLink('productplan', 'view', 'id=' . $product['planID']), $product['planName'], '', "title={$product['planName']}"); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($productIndex == 6):; ?>
                                <tr>
                                    <td class='text-ellipsis'><a class="more-show" id='moreTipsproduct' onclick="more('product')">点击展开更多</a></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($productIndex >= 6):; ?>
                                <tr class='product-more hidden'>
                                    <td class='text-ellipsis'><?php echo html::a($this->createLink('product', 'view', 'id=' . $product['productID']), $product['productName'], '', "title={$product['productName']}"); ?></td>
                                    <td class='text-ellipsis'><?php if ($product['planID']) echo html::a($this->createLink('productplan', 'view', 'id=' . $product['planID']), $product['planName'], '', "title={$product['planName']}"); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php $productIndex++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->opinion.'(计划)'; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <?php $opinionIndex = 0; ?>
                        <?php foreach ($plan->opinionList as $id => $opinion): ?>
                            <?php if ($opinionIndex <= 5): ?>
                                <tr>
                                    <td class='text-ellipsis'>
                                        <?php if($opinion->sourceOpinion == 1):?>
                                            <?php echo html::a($this->createLink('opinion', 'view', 'id=' . $id).'#app=backlog', $opinion->name, '', "title=$opinion->name"); ?>
                                        <?php else:?>
                                            <?php echo html::a($this->createLink('opinioninside', 'view', 'id=' . $id).'#app=backlog', $opinion->name, '', "title=$opinion->name"); ?>
                                        <?php endif; ?>
                                        <!--<span
                                                class='c-actions text-left'><?php
/*                                            common::printIcon('projectplan', 'editPlanOpinion', "planID=$plan->id&opinionID=$opinion->id", $plan, 'list', 'trash', '', 'iframe', true);
                                            */?>
                                        </span>-->
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($opinionIndex == 6):; ?>
                                <tr>
                                    <td class='text-ellipsis'><a class="more-show" id='moreTipsopinion' onclick="more('opinion')">点击展开更多</a></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($opinionIndex >= 6):; ?>
                                <tr class='opinion-more hidden'>
                                    <td class='text-ellipsis'>
                                        <?php if($opinion->sourceOpinion == 1):?>
                                            <?php echo html::a($this->createLink('opinion', 'view', 'id=' . $id).'#app=backlog', $opinion->name, '', "title=$opinion->name"); ?>
                                        <?php else:?>
                                            <?php echo html::a($this->createLink('opinioninside', 'view', 'id=' . $id).'#app=backlog', $opinion->name, '', "title=$opinion->name"); ?>
                                        <?php endif; ?>
                                        <!--<span
                                                class='c-actions text-left'><?php
/*                                            common::printIcon('projectplan', 'editPlanOpinion', "planID=$plan->id&opinionID=$opinion->id", $plan, 'list', 'trash', '', 'iframe', true);
                                            */?>
                                        </span>-->
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php $opinionIndex++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->requirementList.'(计划)'; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <?php $requirementIndex = 0; ?>
                        <?php foreach ($plan->requirementList as $id => $requirement): ?>
                            <?php if ($requirementIndex <= 5): ?>
                                <tr>
                                    <td class='text-ellipsis'>
                                        <?php if($requirement->sourceRequirement == 1):?>
                                            <?php echo html::a($this->createLink('requirement', 'view', 'requirementID=' . $id).'#app=backlog', $requirement->name, '', "title=$requirement->name"); ?>
                                        <?php else:?>
                                            <?php echo html::a($this->createLink('requirementinside', 'view', 'requirementID=' . $id).'#app=backlog', $requirement->name, '', "title=$requirement->name"); ?>
                                        <?php endif; ?>
                                        <!--<span
                                                class='c-actions text-left'><?php
/*                                            common::printIcon('projectplan', 'editPlanRequirement', "planID=$plan->id&requirementID=$requirement->id", $plan, 'list', 'trash', '', 'iframe', true);
                                            */?>
                                        </span>-->
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($requirementIndex == 6):; ?>
                                <tr>
                                    <td class='text-ellipsis'><a class="more-show" id='moreTipsrequirement' onclick="more('requirement')">点击展开更多</a></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($requirementIndex >= 6):; ?>
                                <tr class='requirement-more hidden'>
                                    <td class='text-ellipsis'>
                                        <?php if($requirement->sourceRequirement == 1):?>
                                            <?php echo html::a($this->createLink('requirement', 'view', 'requirementID=' . $id).'#app=backlog', $requirement->name, '', "title=$requirement->name"); ?>
                                        <?php else:?>
                                            <?php echo html::a($this->createLink('requirementinside', 'view', 'requirementID=' . $id).'#app=backlog', $requirement->name, '', "title=$requirement->name"); ?>
                                        <?php endif; ?>
                                        <!--<span
                                                class='c-actions text-left'><?php
/*                                            common::printIcon('projectplan', 'editPlanRequirement', "planID=$plan->id&requirementID=$requirement->id", $plan, 'list', 'trash', '', 'iframe', true);
                                            */?>
                                        </span>-->
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php $requirementIndex++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->projectDemand.'(计划)'; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <?php $demandIndex = 0; ?>
                        <?php foreach ($plan->demandList as $id => $demand): ?>
                            <?php if ($demandIndex <= 5): ?>
                                <tr>
                                    <?php if($demand->sourceDemand == 1):?>
                                        <td class='text-ellipsis'><?php echo html::a($this->createLink('demand', 'view', 'id=' . $id), $demand->code, '', "title=$demand->title"); ?></td>
                                    <?php else:?>
                                        <td class='text-ellipsis'><?php echo html::a($this->createLink('demandinside', 'view', 'id=' . $id), $demand->code, '', "title=$demand->title"); ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                            <?php if ($demandIndex == 6):; ?>
                                <tr>
                                    <td class='text-ellipsis'><a class="more-show" id='moreTipsdemand' onclick="more('demand')">点击展开更多</a></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($demandIndex >= 6):; ?>
                                <tr class='demand-more hidden'>
                                    <?php if($demand->sourceDemand == 1):?>
                                        <td class='text-ellipsis'><?php echo html::a($this->createLink('demand', 'view', 'id=' . $id), $demand->code, '', "title=$demand->title"); ?></td>
                                    <?php else:?>
                                        <td class='text-ellipsis'><?php echo html::a($this->createLink('demandinside', 'view', 'id=' . $id), $demand->code, '', "title=$demand->title"); ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                            <?php $demandIndex++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->projectplan->problem.'(计划)'; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <?php $problemIndex = 0; ?>
                        <?php foreach ($plan->problemList as $id => $problem): ?>
                            <?php if ($problemIndex <= 5): ?>
                                <tr>
                                    <td class='text-ellipsis'><?php echo html::a($this->createLink('problem', 'view', 'id=' . $id), $problem->code, '', "title=$problem->code"); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($problemIndex == 6):; ?>
                                <tr>
                                    <td class='text-ellipsis'><a class="more-show" id='moreTipsproblem' onclick="more('problem')">点击展开更多</a></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($problemIndex >= 6):; ?>
                                <tr class='problem-more hidden'>
                                    <td class='text-ellipsis'><?php echo html::a($this->createLink('problem', 'view', 'id=' . $id), $problem->code, '', "title=$problem->code"); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php $problemIndex++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


<!--        --><?php //foreach ($relatedObject as $type => $objects):
//
//            ?>
<!--            <div class="cell">-->
<!--                <div class="detail">-->
<!--                    <div class="detail-title">--><?php
//
//                        echo $lang->projectplan->{$type}.'(实际)'; ?><!--</div>-->
<!--                    <div class='detail-content'>-->
<!--                        <table class='table table-data'>-->
<!--                            <tbody>-->
<!--                            --><?php //foreach ($objects as $id => $name): ?>
<!--                            --><?php //if(is_array($name)){
//                                ?>
<!--                                    <tr>-->
<!---->
<!--                                        <td class=''>--><?php //echo html::a($this->createLink($name['parent']['module'], 'view', 'objectID=' . $name['parent']['id']), $name['parent']['code'], '', "title={$name['parent']['code']}"); ?>
<!--                                            --><?php
//                                            if($name['sun']){
//                                                echo '(';
//                                                $tempcount = count($name['sun']) - 1;
//                                                foreach ($name['sun'] as $k=>$vv){
//                                                    if($tempcount != $k){
//                                                        echo html::a($this->createLink($vv['module'], 'view', 'objectID=' . $vv['id']), $vv['code'], '', "title={$vv['code']}"),'、';
//                                                    }else{
//                                                        echo html::a($this->createLink($vv['module'], 'view', 'objectID=' . $vv['id']), $vv['code'], '', "title={$vv['code']}");
//                                                    }
//
//                                                }
//                                                echo ')';
//                                            }
//
//                                            ?>
<!---->
<!---->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                        --><?php
//
//                               ?>
<!---->
<!---->
<!--                                --><?php
//
//                            }else{ ?>
<!--                                <tr>-->
<!--                                    --><?php //$objectModule = zget($config->projectplan->objectTables, $type); ?>
<!--                                    <td class='text-ellipsis'>--><?php //echo html::a($this->createLink($objectModule, 'view', 'objectID=' . $id), $name, '', "title=$name"); ?><!--</td>-->
<!--                                </tr>-->
<!--                                --><?php //} ?>
<!--                            --><?php //endforeach; ?>
<!--                            </tbody>-->
<!--                        </table>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        --><?php //endforeach; ?>
    </div>
</div>
<script>
    function more(type){
            var currentClass = $('#moreTips'+type).attr('class');
            if (currentClass == 'more-show') {
                $("."+type+'-more').removeClass('hidden');
                $('#moreTips'+type).text('点击收起更多');
                $('#moreTips'+type).attr('class', 'more-hidden');
            } else {
                $("."+type+'-more').addClass('hidden');
                $('#moreTips'+type).text('点击展开更多');
                $('#moreTips'+type).attr('class', 'more-show');
            }
    }

    function showajaxdiff(val){

        $.zui.modalTrigger.show({iframe:$(val).attr('href')+"?onlybody=yes",scrollInside:true,size:"fullscreen"});
    }

    function closeajaxdiff(){
        $.zui.modalTrigger.close();
    }

    function showAllChangeInfo(){
        let link = '<?php    echo helper::createLink("projectplan","ajaxGetAllChangeInfo","planID=".$plan->id)."?onlybody=yes";  ?>';
        $.zui.modalTrigger.show({iframe:link,scrollInside:true,size:"fullscreen"});
    }
</script>
<?php include '../../../common/view/footer.html.php'; ?>
