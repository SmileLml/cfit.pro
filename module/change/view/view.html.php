<style>
    td p {margin-bottom: 0;}
    .table-fixed td{
        white-space: unset!important;
    }
</style>
<style class="dialog"></style>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php echo html::a(inlink('browse', "projectID=$change->project"), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $change->code?></span>
            <span class="text" title="">&nbsp;</span>
        </div>
    </div>
    <div class="btn-toolbar pull-right">
        <?php if(common::hasPriv('change', 'exportWord')) echo html::a($this->createLink('change', 'exportWord', "changeID=$change->id"), "<i class='icon-export'></i> {$lang->change->exportWord}", '', "class='btn btn-primary'");?>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->change->reason;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($change->reason) ? $change->reason : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->change->content;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($change->content) ? $change->content : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->change->effect;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($change->effect) ? $change->effect : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=change&objectID=$change->id");?>
        </div>
        <?php if(!empty($nodes) && $change->status != 'recall'){ ?>
            <div class="cell">
                <div class="detail">
                    <div class="clearfix">
                        <div class="detail-title pull-left"><?php echo $lang->change->reviewComment;?></div>
                        <div class="detail-title pull-right">
                            <?php
                            if(common::hasPriv('change', 'showHistoryNodes')) echo html::a($this->createLink('change', 'showHistoryNodes', 'id='.$change->id, '', true), $lang->change->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                            ?>
                        </div>
                    </div>
                    <div class="detail-content article-content">
                        <table class="table ops">
                            <tbody>
                            <tr>
                                <th class="w-180px"><?php echo $lang->change->reviewNode;?></th>
                                <td class="w-230px"><?php echo $lang->change->reviewer;?></td>
                                <td class="w-200px"><?php echo $lang->change->reviewResult;?></td>
                                <td class="w-400px"><?php echo $lang->change->reviewComment;?></td>
                                <td class="w-160px"><?php echo $lang->change->dealDate;?></td>
                            </tr>
                            <?php
                                foreach ($lang->change->reviewLevelNodeCodeList[$level] as $key => $reviewNode){
                                    $isSkipReviewerNode =  false;
                                    if(!isset($nodes[$reviewNode]) || !$nodes[$reviewNode]){
                                        $isSkipReviewerNode = true;
                                    }
                                    $currentNode = new stdClass();
                                    $currentNode->reviewers = [];
                                    $currentNode->status    = 'ignore';
                                    $currentNode->isShow    = 2;

                                    if(isset($nodes[$reviewNode])){
                                        $currentNode = $nodes[$reviewNode];
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
                                    if(!$reviewers){
                                        $reviewers = [];
                                    }

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
                                            <?php
                                            if($index == 0){
                                            ?>
                                                <th rowspan="<?php echo $rowspanCount;?>">
                                                    <?php echo $isShangHai && $reviewNode == 'deptLeader' ? $lang->change->reviewNodeCodeDescSh : zget($lang->change->reviewNodeCodeDescList,$reviewNode );?>
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
                                                        if($realReviewer->status != 'wait'){


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
                                                    <?php }
                                                    } ?>
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

                                            <th >
                                                <?php echo $isShangHai && $reviewNode == 'deptLeader' ? $lang->change->reviewNodeCodeDescSh : zget($lang->change->reviewNodeCodeDescList,$reviewNode );?>
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

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php include 'viewArchive.html.php';?>
        <?php include 'viewBaseLine.html.php';?>

        <?php if($change->files):?>
            <div class="cell">
                <?php echo $this->fetch('file', 'printFiles', array('files' => $change->files, 'fieldset' => 'true', 'object' => $change));?>
                <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=opinion&objectID=$change->id");?>
            </div>
        <?php endif;?>

        <div class="cell">
            <?php include '../../common/view/action.html.php';?>
        </div>
        <div class='main-actions' style="/*position: fixed;*/bottom: 10px; width: 1120px;">
            <div class="btn-toolbar">
                <?php common::printBack(inlink('browse', "projectID=$change->project"));?>
                <div class='divider'></div>
                <?php
                common::printIcon('change', 'edit', "changeID=$change->id", $change, 'button');
                common::printIcon('change', 'appoint', "changeID=$change->id", $change, 'list', 'hand-right', '', 'iframe', true);
                common::printIcon('change', 'review', "changeID=$change->id&version=$change->version&reviewStage=$change->reviewStage", $change, 'button', 'glasses', '', 'iframe', true);
                common::printIcon('change', 'run', "changeID=$change->id", $change, 'button', 'play', '', 'iframe', true);
                common::printIcon('change', 'delete', "changeID=$change->id", $change, 'button', 'trash', '', 'iframe', true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->change->basicInfo;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-150px'><?php echo $lang->change->level;?></th>
                            <td><?php echo zget($lang->change->levelList, $change->level, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->change->type;?></th>
                            <td><?php echo zget($lang->change->typeList, $change->type, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->change->category;?></th>
                            <td><?php echo zget($lang->change->categoryList, $change->category, '');?></td>
                        </tr>

                        <?php if($change->subCategory):?>
                            <tr>
                                <th><?php echo $lang->change->subCategory;?></th>
                                <td><?php echo zmget($lang->change->subCategoryList, $change->subCategory, '');?></td>
                            </tr>
                        <?php endif;?>

                        <tr>
                            <th><?php echo $lang->change->isInteriorPro;?></th>
                            <td><?php echo zget($lang->change->isInteriorProList, $change->isInteriorPro, '');?></td>
                        </tr>

                            <tr>
                                <th><?php echo $lang->change->isMasterPro;?></th>
                                <td><?php echo zget($lang->change->isMasterProList, $change->isMasterPro, '');?></td>
                            </tr>


                        <tr>
                            <th><?php echo $lang->change->isSlavePro;?></th>
                            <td><?php echo zget($lang->change->isSlaveProList, $change->isSlavePro, '');?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->change->status;?></th>
                            <td><?php echo zget($lang->change->statusList, $change->status, '');?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->change->project;?></th>
                            <td><?php foreach(explode(',', $change->project) as $project) echo '<p>' . zget($projects, $project, '') . '</p>'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->change->baseLineCondition;?></th>
                            <td><?php echo zget($condition, $change->baseLineCondition, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->change->createdBy;?></th>
                            <td><?php echo zget($users, $change->createdBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->change->createdDate;?></th>
                            <td><?php echo $change->createdDate;?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->change->mailUsers;?></th>
                            <td><?php echo zmget($users, $change->mailUsers);?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->consumedTitle;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->change->nodeUser;?></th>
<!--                            <td class='text-right'>--><?php //echo $lang->change->consumed;?><!--</td>-->
                            <td class='text-center'><?php echo $lang->change->before;?></td>
                            <td class='text-center'><?php echo $lang->change->after;?></td>
                        </tr>
                        <?php foreach($change->consumed as $c):?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->createdBy, '');?></th>
<!--                                <td class='text-right'>--><?php //echo $c->consumed . ' ' . $lang->hour;?><!--</td>-->
                                <td class='text-center'><?php echo zget($lang->change->statusList, $c->before, '-');?></td>
                                <td class='text-center'>
                                    <?php echo zget($lang->change->statusList, $c->after, '-');?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var scroll_height = document.body.scrollHeight;
    var window_height = window.innerHeight;
    if (scroll_height > window_height){
        var _top = 120;
        if (window_height <= 700){
            _top = 60
        }
        $(".dialog").append(".modal-dialog{top:"+_top+'px'+"!important}");
    }
</script>

<?php include '../../common/view/footer.html.php';?>
