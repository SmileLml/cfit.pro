<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $browseLink = $app->session->datamanagementList != false ? $app->session->datamanagementList : inlink('browse'); ?>
            <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $datamanagement->code; ?></span>
        </div>
    </div>
    <?php if (!isonlybody()): ?>
        <div class="btn-toolbar pull-right">
            <?php if (common::hasPriv('datamanagement', 'exportWord')) echo html::a($this->createLink('datamanagement', 'exportWord', "datamanagementID=$datamanagement->id"), "<i class='icon-export'></i> {$lang->datamanagement->exportWord}", '', "class='btn btn-primary'"); ?>
        </div>
    <?php endif; ?>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->datamanagement->desc; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($datamanagement->desc) ? html_entity_decode(str_replace("\n","<br/>",$datamanagement->desc)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->datamanagement->reason; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($datamanagement->reason) ? html_entity_decode(str_replace("\n","<br/>",$datamanagement->reason)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=datamanagement&objectID=$datamanagement->id"); ?>
        </div>
        <!-- 延期记录 -->
        <?php if (!empty($delayReview) and !empty((array)$delayReview)): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->datamanagement->delayReview; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($delayReview) and !empty((array)$delayReview)): ?>
                            <?php foreach ($delayReview as $item): ?>
                                <table class="table ops" style="table-layout:fixed">
                                    <tr>
                                        <th class="w-120px"><?php echo $lang->datamanagement->delayApplicant; ?></th>
                                        <td class="w-240px" style="font-weight:bold"><?php echo $lang->datamanagement->delayReason; ?></td>
                                        <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->delayDate; ?></td>
                                        <td class="w-150px" style="font-weight:bold"><?php echo $lang->datamanagement->comment; ?></td>
                                        <td class="w-140px" style="font-weight:bold"><?php echo $lang->datamanagement->operatDate; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="w-120px" style="background:#eee"><?php echo $item->delayApplicant ;?></td>
                                        <td class="w-240px"><?php echo $item->delayReason;?></td>
                                        <td class="w-180px"><?php echo $item->delayDeadline;?></td>
                                        <td class="w-150px"><?php echo $item->delayComment;?></td>
                                        <td class="w-140px"><?php echo $item->createdDate;?></td>
                                    </tr>
                                    <tr>
                                        <th class="w-120px"><?php echo $lang->datamanagement->delayReviewer; ?></th>
                                        <td class="w-240px" style="font-weight:bold"><?php echo $lang->datamanagement->result; ?></td>
                                        <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->reviewOpinion; ?></td>
                                        <td class="w-150px" style="font-weight:bold"><?php echo $lang->datamanagement->comment; ?></td>
                                        <td class="w-140px" style="font-weight:bold"><?php echo $lang->datamanagement->reviewDate; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="w-120px" style="background:#eee"><?php echo $item->reviewers ;?></td>
                                        <td class="w-240px"><?php echo $item->reviewResult;?></td>
                                        <td class="w-180px"><?php echo $item->reviewOpinion;?></td>
                                        <td class="w-150px"><?php echo $item->reviewComment;?></td>
                                        <td class="w-140px"><?php echo $item->reviewDate;?></td>
                                    </tr>
                                </table>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif;?>

        <!-- 销毁记录 -->
        <?php if (!empty($destroyReview) and !empty((array)$destroyReview)): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->datamanagement->destroyReview; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($destroyReview) and !empty((array)$destroyReview)): ?>
                            <?php foreach ($destroyReview as $item): ?>
                                <table class="table ops" style="table-layout:fixed">
                                    <tr>
                                        <th class="w-120px"><?php echo $lang->datamanagement->destroyApplicant; ?></th>
                                        <td class="w-240px" colspan="2" style="font-weight:bold"><?php echo $lang->datamanagement->destroyReason; ?></td>
                                        <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->comment; ?></td>
                                        <td class="w-140px" style="font-weight:bold"><?php echo $lang->datamanagement->operatDate; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="background:#eee"><?php echo $item->destroyApplicant ;?></td>
                                        <td colspan="2"><?php echo $item->destroyReason;?></td>
                                        <td><?php echo $item->destroyComment;?></td>
                                        <td><?php echo $item->createdDate;?></td>
                                    </tr>
                                    <tr>
                                        <th class="w-120px"><?php echo $lang->datamanagement->destroyReviewer; ?></th>
                                        <td class="w-120px" style="font-weight:bold"><?php echo $lang->datamanagement->result; ?></td>
                                        <td class="w-120px" style="font-weight:bold"><?php echo $lang->datamanagement->destroyOpinion; ?></td>
                                        <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->comment; ?></td>
                                        <td class="w-140px" style="font-weight:bold"><?php echo $lang->datamanagement->reviewDate; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="background:#eee"><?php echo $item->reviewers ;?></td>
                                        <td><?php echo $item->reviewResult;?></td>
                                        <td><?php echo $item->reviewOpinion;?></td>
                                        <td><?php echo $item->reviewComment;?></td>
                                        <td><?php echo $item->reviewDate;?></td>
                                    </tr>
                                </table>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif;?>

        <!-- 备案通知 -->
        <?php if (!empty($filingNoticeNodes) and !empty((array)$filingNoticeNodes)): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->datamanagement->filingNotice; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($filingNoticeNodes) and !empty((array)$filingNoticeNodes)): ?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-180px"><?php echo $lang->datamanagement->testDealUser; ?></th>
                                    <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->todoType; ?></td>
                                    <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->operatResult; ?></td>
                                    <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->comment; ?></td>
                                    <td class="w-180px" style="font-weight:bold"><?php echo $lang->datamanagement->operatDate; ?></td>
                                </tr>
                                <?php if (!empty($filingNoticeNodes->reviewedNode) and !empty($filingNoticeNodes->reviewedNode->dealUser)): ?>
                                    <tr>
                                        <td style="background:#eee"><?php echo !empty($filingNoticeNodes->reviewedNode->dealUser)?$filingNoticeNodes->reviewedNode->dealUser:'';?></td>
                                        <td><?php echo $lang->datamanagement->todoTypeList['reviewed'];?></td>
                                        <td><?php echo !empty($filingNoticeNodes->reviewedNode->dealResult)?$filingNoticeNodes->reviewedNode->dealResult:'' ;?></td>
                                        <td><?php echo !empty($filingNoticeNodes->reviewedNode->dealComment)?$filingNoticeNodes->reviewedNode->dealComment:'' ?></td>
                                        <td><?php echo !empty($filingNoticeNodes->reviewedNode->dealDate)?$filingNoticeNodes->reviewedNode->dealDate:'' ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($filingNoticeNodes->gainedNode) and !empty($filingNoticeNodes->gainedNode->dealUser)): ?>
                                    <tr>
                                        <td style="background:#eee"><?php echo !empty($filingNoticeNodes->gainedNode->dealUser)?$filingNoticeNodes->gainedNode->dealUser:'';?></td>
                                        <td><?php echo $lang->datamanagement->todoTypeList['gained'];?></td>
                                        <td><?php echo !empty($filingNoticeNodes->gainedNode->dealResult)?$filingNoticeNodes->gainedNode->dealResult:'' ;?></td>
                                        <td><?php echo !empty($filingNoticeNodes->gainedNode->dealComment)?$filingNoticeNodes->gainedNode->dealComment:'' ?></td>
                                        <td><?php echo !empty($filingNoticeNodes->gainedNode->dealDate)?$filingNoticeNodes->gainedNode->dealDate:'' ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($filingNoticeNodes->destroyedNode) and !empty($filingNoticeNodes->destroyedNode->dealUser)): ?>
                                    <tr>
                                        <td style="background:#eee"><?php echo !empty($filingNoticeNodes->destroyedNode->dealUser)?$filingNoticeNodes->destroyedNode->dealUser:'';?></td>
                                        <td><?php echo $lang->datamanagement->todoTypeList['destroyed'];?></td>
                                        <td><?php echo !empty($filingNoticeNodes->destroyedNode->dealResult)?$filingNoticeNodes->destroyedNode->dealResult:'' ;?></td>
                                        <td><?php echo !empty($filingNoticeNodes->destroyedNode->dealComment)?$filingNoticeNodes->destroyedNode->dealComment:'' ?></td>
                                        <td><?php echo !empty($filingNoticeNodes->destroyedNode->dealDate)?$filingNoticeNodes->destroyedNode->dealDate:'' ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif;?>


        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($browseLink); ?>
                <div class='divider'></div>
                <?php
                common::printIcon('datamanagement', 'readmessage', "datamanagementID=$datamanagement->id", $datamanagement, 'button', 'bullhorn','','iframe', true);
                common::printIcon('datamanagement', 'delay', "datamanagementID=$datamanagement->id", $datamanagement, 'button', 'time','','iframe', true);
                common::printIcon('datamanagement', 'review', "datamanagementID=$datamanagement->id&changeVersion=$datamanagement->changeVersion&reviewStage=$datamanagement->reviewStage", $datamanagement, 'button', 'glasses', '', 'iframe', true);
                common::printIcon('datamanagement', 'destroyexecution', "datamanagementID=$datamanagement->id", $datamanagement, 'button', 'play','','iframe', true);
                common::printIcon('datamanagement', 'destroy', "datamanagementId=$datamanagement->id", $datamanagement, 'button', 'close','','iframe', true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->datamanagement->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->datamanagement->type; ?></th>
                            <td><?php echo zget($lang->datamanagement->typeList, $datamanagement->type, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->isJk; ?></th>
                            <td><?php echo zget($lang->datamanagement->isJkList, $datamanagement->isJk, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->desensitizeType; ?></th>
                            <td><?php echo zget($lang->datamanagement->desensitizeTypeList, $datamanagement->desensitizeType, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->deadline; ?></th>
                            <td><?php echo $datamanagement->useDeadline; ?></td>
                        </tr>
                        <?php if($datamanagement->source == 'infoqz'): ?>
                            <tr>
                                <th><?php echo $lang->datamanagement->isDesensitize; ?></th>
                                <td><?php echo zget($lang->datamanagement->isDesensitizeList, $datamanagement->isDesensitize, ''); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?php echo $lang->datamanagement->infoCode; ?></th>
                            <td><?php echo html::a($this->createLink($datamanagement->source, 'view', 'id=' . $infoData->id, '', true), $infoData->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ?></td>

                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->source; ?></th>
                            <td><?php echo zget($lang->datamanagement->sourceList, $datamanagement->source, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->createdBy; ?></th>
                            <td><?php echo zget($users, $datamanagement->createdBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->createdDate; ?></th>
                            <td><?php echo $datamanagement->createdDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->delayedBy; ?></th>
                            <td><?php echo zget($users, $datamanagement->delayedBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->delayDeadline; ?></th>
                            <td><?php echo $datamanagement->delayDeadline; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->destroyedBy; ?></th>
                            <td><?php echo zget($users, $datamanagement->destroyedBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->destroyedDate; ?></th>
                            <td><?php echo $datamanagement->destroyedDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->reviewedBy; ?></th>
                            <td><?php echo zget($users, $datamanagement->reviewedBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->reviewedDate; ?></th>
                            <td><?php echo $datamanagement->reviewedDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->datamanagement->actualEndTime; ?></th>
                            <td><?php echo $datamanagement->actualEndTime; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->consumedTitle; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->datamanagement->nodeUser; ?></th>
                           <!-- <td class='text-right'><?php /*echo $lang->datamanagement->consumed; */?></td>-->
                            <td class='text-center'><?php echo $lang->datamanagement->before; ?></td>
                            <td class='text-center'><?php echo $lang->datamanagement->after; ?></td>
                        </tr>
                        <?php foreach ($datamanagement->consumed as $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                            <!--    <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour; */?></td>-->
                                <td class='text-center'><?php echo zget($lang->datamanagement->statusList, $c->before, '-'); ?></td>
                                <td class='text-center'><?php echo zget($lang->datamanagement->statusList, $c->after, '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
