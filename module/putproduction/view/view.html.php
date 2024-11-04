<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php js::import($jsRoot . 'bpmn/bpmn-js-6.1.2.js'); ?>

<style>
    .review-opinion{
        width: 370px
    }
    #canvas {
        height: 420px;
        width: 1300px;
    }
    .bjs-powered-by{display: none;}
    .nodeSuccess .djs-visual > :nth-child(1){
        stroke: #52c41a !important;
        stroke-width: 3px;
    }
    .nodeProcessing .djs-visual > :nth-child(1){
        fill: #1b85ff !important;
        stroke-width: 3px;
    }
    .nodeProcessing .djs-visual > :nth-child(2){
        fill: #f6fff7 !important;
    }
</style>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php if (!isonlybody()): ?>
                <?php $browseLink = $app->session->putproductionList != false ? $app->session->putproductionList : inlink('browse'); ?>
                <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
                <div class="divider"></div>
            <?php endif; ?>
            <div class="page-title">
                <span class="label label-id"><?php echo $putproduction->code ?></span>
            </div>
        </div>
        <?php if (!isonlybody()): ?>
            <div class="btn-toolbar pull-right">
                <!--
                  <?php
                    if(common::hasPriv('putproduction', 'exportWord')) echo html::a($this->createLink('putproduction', 'exportWord', "putproductionID=$putproduction->id"), "<i class='icon-export'></i> {$lang->outwarddelivery->exportWord}", '', "class='btn btn-primary'");
                  ?>
                  -->
            </div>
        <?php endif; ?>
    </div>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">

            <div class="cell">
                <div class='tabs' id='tabsNav'>
                    <ul class='nav nav-tabs' id="changeUl">
                        <li id='putproductionFormLi' <?php if($type == 'putproductionForm')  echo "class='active'"?>><a href='#putproductionForm' data-toggle='tab'><?php echo  $lang->putproduction->formInfo;?></a></li>
                        <?php if(!empty($putproduction->workflowId)):?>
                            <li id='flowInfoLi' <?php if($type == 'flowInfo')   echo "class='active'"?>><a href='#flowInfo' data-toggle='tab'><?php echo  $lang->putproduction->flowImg;?></a></li>
                        <?php endif;?>

                    </ul>
                </div>
                <div class='tab-content'>
                    <div class="detail tab-pane <?php if($type == 'putproductionForm') echo 'active'?>" id="putproductionForm">

                        <div class="detail">
                            <div class="detail-title"></div>
                            <div class='detail-content'>
                                <table class='table table-data'>
                                    <tbody>
                                    <tr>
                                        <th class='w-160px'><?php echo $lang->putproduction->stage; ?></th>
                                        <td><?php echo zmget($lang->putproduction->stageList, $putproduction->stage, ''); ?></td>
                                        <th class='w-160px'><?php echo $lang->putproduction->property; ?></th>
                                        <td><?php echo zmget($lang->putproduction->propertyList, $putproduction->property, ''); ?></td>

                                    </tr>

                                    <tr>
                                        <th><?php echo $lang->putproduction->isReview; ?></th>
                                        <td><?php echo zget($lang->putproduction->isReviewList, $putproduction->isReview, ''); ?></td>
                                        <th><?php echo $lang->putproduction->level; ?></th>
                                        <td><?php echo zget($lang->putproduction->levelList, $putproduction->level, ''); ?></td>
                                    </tr>

                                        <?php if($putproduction->isIncludeSecondStage):?>
                                        <tr>
                                            <th><?php echo $lang->putproduction->isPutCentralCloud; ?></th>
                                            <td><?php echo zget($lang->putproduction->isPutCentralCloudList, $putproduction->isPutCentralCloud, ''); ?></td>
                                            <th><?php echo $lang->putproduction->dataCenter; ?></th>
                                            <td><?php echo zmget($lang->putproduction->dataCenterList, $putproduction->dataCenter, ''); ?></td>

                                        </tr>

                                        <tr>
                                            <th><?php echo $lang->putproduction->isBusinessCoopera; ?></th>
                                            <td><?php echo zget($lang->putproduction->isBusinessCooperaList, $putproduction->isBusinessCoopera, ''); ?></td>
                                            <th><?php echo $lang->putproduction->isBusinessAffect; ?></th>
                                            <td><?php echo zget($lang->putproduction->isBusinessAffectList, $putproduction->isBusinessAffect, ''); ?></td>
                                        </tr>
                                        <?php endif;?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->putproduction->desc; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($putproduction->desc) ?  html_entity_decode(str_replace("\n","<br/>",$putproduction->desc)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <?php if($putproduction->isReview == 2):?>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->putproduction->reviewComment; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($putproduction->reviewComment) ?  html_entity_decode(str_replace("\n","<br/>",$putproduction->reviewComment)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($putproduction->isIncludeSecondStage):?>
                            <?php if($putproduction->isBusinessCoopera == 2):?>
                                <div class="detail">
                                    <div class="detail-title"><?php echo $lang->putproduction->businessCooperaContent; ?></div>
                                    <div class="detail-content article-content">
                                        <?php echo !empty($putproduction->businessCooperaContent) ?  html_entity_decode(str_replace("\n","<br/>",$putproduction->businessCooperaContent)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                                    </div>
                                </div>
                            <?php endif;?>
                            <?php if($putproduction->isBusinessAffect == 2):?>
                            <div class="detail">
                                <div class="detail-title"><?php echo $lang->putproduction->businessAffect; ?></div>
                                <div class="detail-content article-content">
                                    <?php echo !empty($putproduction->businessAffect) ?  html_entity_decode(str_replace("\n","<br/>",$putproduction->businessAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                                </div>
                            </div>
                            <?php endif;?>

                        <?php endif;?>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->putproduction->remark; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($putproduction->remark) ?  html_entity_decode(str_replace("\n","<br/>",$putproduction->remark)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->putproduction->fileList; ?></div>
                            <div class="detail-content article-content">
                                <?php if(empty($putproduction->remoteFileList)):?>
                                    <div class='text-center text-muted'>
                                        <?php echo $lang->noData; ?>
                                    </div>
                                <?php else:?>

                                    <?php foreach (explode(',' , $putproduction->remoteFileList) as $value):
                                        $json = '{"str":"'.str_replace('#U', '\u',$value).'"}';
                                        $arr = json_decode($json,true);
                                        echo $arr['str'].'<br>';
                                        ?>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </div>
                        </div>

                    </div>

                    <div class="detail hidden <?php if($type == 'flowInfo') echo 'active'?>" id="flowInfo">
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->putproduction->currentStatus .': ' . zget($lang->putproduction->statusList, $putproduction->status);?></div>
                            <div class="detail-content article-content">
                                <div id="canvas">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!--处理意见-->
            <div class="cell">
                <div class="detail">
                    <div class="clearfix">
                        <div class="detail-title pull-left"><?php echo $lang->putproduction->reviewOpinion; ?></div>
                        <div class="detail-title pull-right">
                            <?php
                            if(common::hasPriv('putproduction', 'showHistoryNodes')) echo html::a($this->createLink('putproduction', 'showHistoryNodes', 'id='.$putproduction->id, '', true), $lang->putproduction->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                            ?>
                        </div>
                    </div>
                    <div class="detail-content article-content">
                        <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <th class="w-180px"><?php echo $lang->putproduction->reviewNode; ?></th>
                                <td class="w-180px"><?php echo $lang->putproduction->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->putproduction->dealResult; ?></td>
                                <td class="review-opinion"><?php echo $lang->putproduction->reviewOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->putproduction->reviewTime; ?></td>
                            </tr>

                            <?php
                                foreach ($nodes as $reviewNode):
                                    if(!isset($lang->putproduction->reviewNodeCodeNameList[$reviewNode['nodeName']])){
                                        continue;
                                    }
                                    $nodeCode = $reviewNode['nodeName'];
                                    $nodeName = zget($lang->putproduction->reviewNodeCodeNameList, $reviewNode['nodeName']);
                                    $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
                                    $reviewerUsers = zmget($users, $nodeDealUsers);
                                    if($reviewNode['nodeName'] == 'waitexternalreview'){
                                        if($reviewNode['result'] == 'pass'){
                                            $reviewNode['result'] = $putproduction->opResult;
                                        }
                                    }
                            ?>
                            <tr>
                                <th><?php echo $nodeName; ?></th>
                                <td title="<?php echo $reviewerUsers; ?>">
                                    <?php echo $reviewerUsers; ?>
                                </td>
                                <td>
                                    <?php
                                        if($putproduction->status == 'cancel' && $reviewNode['result'] == 'pending'){
                                            echo '';
                                        }else{
                                            if($nodeCode == $this->lang->putproduction->reviewNodeCodeList['waitdelivery']){
                                                echo zget($lang->putproduction->syncResultList, $reviewNode['result'], '');
                                            }else{
                                                echo zget($lang->putproduction->reviewResultList, $reviewNode['result'], '');
                                            }

                                        }
                                    ?>

                                    <?php if($reviewNode['dealUser']):?>
                                    （<?php echo zget($users, $reviewNode['dealUser']);?>）
                                    <?php endif;?>

                                </td>
                                <td> <?php echo $reviewNode['comment']; ?></td>
                                <td> <?php echo $reviewNode['dealDate']; ?></td>
                            </tr>
                            <?php endforeach;?>
                        </table>
                        <?php endif;?>
                    </div>
                </div>
            </div>

            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>

            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php common::printBack($browseLink); ?>
                    <div class='divider'></div>
                    <?php
                    common::printIcon('putproduction', 'edit', "putproductionID=$putproduction->id", $putproduction, 'button');
                    $submitClass = 'btn disabled';
                    if((common::hasPriv('putproduction', 'submit') && common::isDealUser($putproduction->dealUser, $app->user->account))|| $app->user->account == 'admin'){
                        $submitClass = 'btn';
                    }
                    common::printIcon('putproduction', 'submit', "putproductionID=$putproduction->id", $putproduction, 'button', 'play', '', 'iframe', true, " class = '{$submitClass}'");
                    common::printIcon('putproduction', 'assignment', "putproductionID=$putproduction->id", $putproduction, 'button', 'hand-right', '', 'iframe', true, '', $this->lang->putproduction->assignment);
                    common::printIcon('putproduction', 'review', "putproductionID=$putproduction->id&version=$putproduction->version&status=$putproduction->status", $putproduction, 'button', 'glasses', '', 'iframe', true);
                    common::printIcon('putproduction', 'copy', "putproductionID=$putproduction->id", $putproduction, 'button');
                    common::printIcon('putproduction', 'delete', "putproductionID=$putproduction->id", $putproduction, 'button', 'trash', '', 'iframe', true);
                    common::printIcon('putproduction', 'cancel', "putproductionID=$putproduction->id", $putproduction, 'button', 'cancel', '', 'iframe', true);
                    common::printIcon('putproduction', 'repush', "putproductionID=$putproduction->id", $putproduction, 'button', 'share', '', 'iframe', true);
                    ?>

                </div>
            </div>
        </div>
        <!-- 右侧基础信息 -->
        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->putproduction->baseinfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                                <tr>
                                    <th class="w-120px"><?php echo $lang->putproduction->status; ?></th>
                                    <td><?php echo zget($lang->putproduction->statusList, $putproduction->status, ''); ?></td>
                                </tr>

                                <tr>
                                    <th><?php echo $lang->putproduction->outsidePlanId; ?></th>
                                    <td><?php echo zget($outsideProjectList, $putproduction->outsidePlanId, ''); ?></td>
                                </tr>

                                <tr>
                                    <th><?php echo $lang->putproduction->inProjectIds; ?></th>
                                    <td><?php
                                        //echo $putproduction->inProjectIds ? zmget($inProjectList, $putproduction->inProjectIds, '<br/>') : '无';
                                        echo zmget($inProjectList, $putproduction->inProjectIds, '无','<br/>')
                                    ?></td>
                                </tr>

                                <tr>
                                    <th><?php echo $lang->putproduction->app; ?></th>
                                    <td><?php echo zmget($appList, $putproduction->app, ''); ?></td>
                                </tr>

                                <tr>
                                    <th><?php echo $lang->putproduction->productId; ?></th>
                                    <td><?php echo zmget($productList, $putproduction->productId, ''); ?></td>
                                </tr>

                                <tr>
                                    <th><?php echo $lang->putproduction->demandId; ?></th>
                                    <td><?php
                                        $demandIds = array_filter(explode(',', $putproduction->demandId));
                                        if(!empty($demandIds)):
                                            foreach ($demandIds as $demandId):
                                                echo html::a($this->createLink('demand', 'view', 'id=' . $demandId, '', true), zget($demandList, $demandId), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                            endforeach;
                                        endif;
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th><?php echo $lang->putproduction->createdBy; ?></th>
                                    <td><?php echo zget($users, $putproduction->createdBy, ''); ?></td>
                                </tr>

                                <tr>
                                    <th><?php echo $lang->putproduction->createdDate; ?></th>
                                    <td><?php echo $putproduction->createdDate; ?></td>
                                </tr>

                               <?php if($putproduction->firstStagePid):?>
                                <tr>
                                    <th><?php echo $lang->putproduction->firstStagePid; ?></th>
                                    <td>
                                        <?php
                                            echo html::a($this->createLink('putproduction', 'view', 'id=' . $putproduction->firstStagePid, '', true), zget($putproduction->firstStageInfo, 'code'), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'")
                                        ?>
                                    </td>
                                </tr>
                              <?php endif;?>

                            <?php if($putproduction->returnCount > 0): ?>
                                <tr>
                                    <th><?php echo $lang->putproduction->putproductionReturnCount; ?></th>
                                    <td>
                                        <?php echo $putproduction->returnCount; ?>
                                        <?php common::printIcon('putproduction', 'editReturnCount', "putproductionId=$putproduction->id", $putproduction, 'list', 'edit', '', 'iframe', true);?>
                                    </td>
                                </tr>
                            <?php endif;?>

                            <?php if($putproduction->dealUser):?>
                                <tr>
                                    <th><?php echo $lang->putproduction->dealUser; ?></th>
                                    <td><?php echo zmget($users, $putproduction->dealUser, ''); ?></td>
                                </tr>

                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php
                if($putproduction->releases):
                foreach($putproduction->releases as $release):
            ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->putproduction->releaseId; ?></div>
                        <div class='detail-content'>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class="w-120px"><?php echo $lang->release->name;?></th>
                                    <td><?php echo html::a($this->createLink('projectrelease', 'view', array('releaseID' => $release->id)), $release->name, '', 'data-app="project"');?></td>
                                </tr>

                                <tr>
                                    <th class="w-120px"><?php echo $lang->putproduction->path;?></th>
                                    <td><?php if($release->path) echo $release->path. $lang->api->sftpList['info'];?></td>
                                </tr>

                                <tr>
                                    <th class="w-120px"><?php echo $lang->file->common;?></th>
                                    <td>
                                        <div class='detail'>
                                            <div class='detail-content article-content'>
                                                <?php if($release->files):?>
                                                    <?php echo $this->fetch('file', 'printFiles', array('files' => $release->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false));?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
                endforeach;
                endif;
            ?>
           <?php if($putproduction->isOnlyFistStage):?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->putproduction->fileInfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class="w-120px"><?php echo $lang->putproduction->fileUrlRevision; ?></th>
                                <td><?php echo $putproduction->fileUrlRevision; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->putproduction->tempSftpPath; ?></th>
                                <td><?php
                                    $sftPathArray = json_decode($putproduction->sftpPath);
                                    if($sftPathArray):
                                        foreach ($sftPathArray as $value){
                                            echo $value."<br>";
                                        }
                                    endif;
                                    ?>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif;?>

            <?php if($putproduction->externalId):?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->putproduction->externalInfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class="w-180px"><?php echo $lang->putproduction->isOnLine; ?></th>
                                <td><?php echo zget($lang->putproduction->isOnLineList,$putproduction->isOnLine) ; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->putproduction->returnBy; ?></th>
                                <td><?php echo $putproduction->returnBy; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->putproduction->returnReason; ?></th>
                                <td><?php echo $putproduction->returnReason; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->putproduction->returnTel; ?></th>
                                <td><?php echo $putproduction->returnTel; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->putproduction->returnDate; ?></th>
                                <td><?php echo $putproduction->returnDate != '0000-00-00 00:00:00' ? $putproduction->returnDate : ''; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->putproduction->planStartTime; ?></th>
                                <td><?php echo $putproduction->planStartTime != '0000-00-00 00:00:00' ? $putproduction->planStartTime : ''; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->putproduction->planEndTime; ?></th>
                                <td><?php echo $putproduction->planEndTime!= '0000-00-00 00:00:00' ? $putproduction->planEndTime : ''; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->putproduction->realStartTime; ?></th>
                                <td><?php echo $putproduction->realStartTime != '0000-00-00 00:00:00' ? $putproduction->realStartTime : ''; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->putproduction->realEndTime; ?></th>
                                <td><?php echo $putproduction->realEndTime!= '0000-00-00 00:00:00' ? $putproduction->realEndTime : ''; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->putproduction->implementedBy; ?></th>
                                <td><?php echo $putproduction->implementedBy; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->putproduction->opResult; ?></th>
                                <td><?php echo zget($lang->putproduction->opResultList, $putproduction->opResult, ''); ?></td>
                            </tr>
                            <?php if($putproduction->opResult == 'fail'):?>
                            <tr>
                                <th><?php echo $lang->putproduction->opFailReason; ?></th>
                                <td><?php echo $putproduction->opFailReason; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->putproduction->appCauseFail; ?></th>
                                <td><?php echo $putproduction->appCauseFail; ?></td>
                            </tr>
                            <?php endif;?>
                            <?php if($putproduction->stage != '1'):?>
                                <tr>
                                    <th><?php echo $lang->putproduction->planUsedWindow; ?></th>
                                    <td><?php echo $putproduction->planUsedWindow; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->putproduction->planUsedWindowReason; ?></th>
                                    <td><?php echo $putproduction->planUsedWindowReason; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->putproduction->realUsedWindow; ?></th>
                                    <td><?php echo $putproduction->realUsedWindow; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->putproduction->realUsedWindowReason; ?></th>
                                    <td><?php echo $putproduction->realUsedWindowReason; ?></td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif;?>



            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->putproduction->statusTransition; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->consumed->nodeUser; ?></th>
                                <td class='text-center'><?php echo $lang->consumed->before; ?></td>
                                <td class='text-center'><?php echo $lang->consumed->after; ?></td>
                                <?php if(common::hasPriv('putproduction', 'workloadedit')): ?>
                                    <td class='text-center'><?php echo $lang->consumed->deal; ?></td>
                                <?php endif;?>
                            </tr>
                            <?php foreach ($putproduction->consumed as $val): ?>
                                <tr>
                                    <th class='w-100px'><?php echo zget($users, $val->account, ''); ?></th>
                                    <?php
                                    echo "<td class='text-center'>" . $val->extra . zget($lang->putproduction->statusList, $val->before, '-') . "</td>";
                                    echo "<td class='text-center'>" . $val->extra . zget($lang->putproduction->statusList, $val->after, '-') . "</td>";
                                    ?>
                                    <td class='c-actions text-center'>
                                        <?php if(in_array($val->after, $lang->putproduction->allowEditStatusStatusArray)) {
                                            common::printIcon('putproduction', 'workloadedit', "putproductionID={$putproduction->id}&consumedId={$val->id}", $putproduction, 'list', 'edit', '', 'iframe', true);
                                        }?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
  <?php
    js::set('status', $putproduction->status);
    js::set('endStatusList', $endStatusList); //结束状态
    js::set('bpmnXML', $flowImg);
    js::set('highLightedActivityIdList', isset($reviewFlowInfo->highLightedActivityIdList) ? $reviewFlowInfo->highLightedActivityIdList : []);
    js::set('runningActivityIdList', isset($reviewFlowInfo->runningActivityIdList) ? $reviewFlowInfo->runningActivityIdList : []);
  ?> <!--必须XMl格式-->
    <script>
        var ul = document.getElementById('changeUl');
        ul.addEventListener('click', function (e) {
            var target = e.target;
            switch (target.hash) {
                case '#putproductionForm':
                    $('#flowInfo').addClass('hidden');
                    $('#canvas').empty();
                    break;
                case '#flowInfo':
                    $('#flowInfo').removeClass('hidden');
                    if(bpmnXML){
                        //console.log(bpmnXML);
                        var bpmnJS = new BpmnJS({
                            container: '#canvas'
                        });

                        // import diagram
                        bpmnJS.importXML(bpmnXML, function(err) {
                            if (!err) {
                                //设置颜色的方法

                                const setNodeColor = function(ids, newBpmn, colorClass){
                                    const elementRegistry = newBpmn.get('elementRegistry');
                                    $.each(ids, function (index, item) {
                                        if (elementRegistry._elements[item]) {
                                            const element = elementRegistry._elements[item].gfx
                                            element.classList.add(colorClass)
                                        }
                                        // console.log(elementRegistry, element)
                                    });
                                };
                                var canvas = bpmnJS.get('canvas');
                                canvas.zoom('fit-viewport');
                                /*
                                if(highLightedActivityIdList.length > 0){
                                    setNodeColor(highLightedActivityIdList, bpmnJS, 'nodeSuccess');
                                }
                                */
                                if($.inArray(status, endStatusList) !== -1){ //结束标记
                                    runningActivityIdList = ['end'];
                                }else if(status == 'cancel'){
                                    runningActivityIdList = [];
                                }
                                if(runningActivityIdList.length > 0){
                                    setNodeColor(runningActivityIdList, bpmnJS, 'nodeProcessing');
                                }
                            } else {
                                return console.error('could not import BPMN 2.0 diagram', err);
                            }
                        });
                    }
                    break;
            }
        });
    </script>
<?php include '../../common/view/footer.html.php'; ?>