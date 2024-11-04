<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $sectransferHistory = $app->session->sectransferHistory ? $app->session->sectransferHistory : inlink('browse') ?>
            <?php echo html::a($sectransferHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="label label-id"><?php echo $sectransfer->id ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->protransferDesc; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($sectransfer->protransferDesc) ? $sectransfer->protransferDesc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->reason; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($sectransfer->reason) ? str_replace("\n","<br/>", $sectransfer->reason) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->publish; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($sectransfer->publish) ? $sectransfer->publish : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->sectransferPublish; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($sectransfer->sftpPath) ? $sectransfer->sftpPath : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->fileList; ?></div>
                <div class="detail-content article-content">
                    <?php if(empty($sectransfer->remoteFileList)):
                            echo "<div class='text-center text-muted'> $lang->noData</div>";
                        else:
                            foreach (explode(',' , $sectransfer->remoteFileList) as $value):
                                $json = '{"str":"'.str_replace('#U', '\u',$value).'"}';
                                $arr = json_decode($json,true);
                                echo $arr['str'].'<br>';
                    endforeach; endif;?>
                </div>
            </div>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=sectransfer&objectID=$sectransfer->id"); ?>
            <?php if ($sectransfer->files): ?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->sectransfer->fileTitle; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                    <div class="detail-content">
                        <?php
                        foreach ($sectransfer->files as $key => $file) {
                            echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $sectransfer, 'canOperate' => $file->addedBy == $this->app->user->account));
                        }; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- 审核审批意见/处理意见 -->
        <div class="cell">
            <div class="detail">
                <div class="clearfix">
                    <div class="detail-title pull-left"><?php echo $lang->sectransfer->reviewOpinion; ?></div>
                    <div class="detail-title pull-right">
                        <?php
                        if(common::hasPriv('sectransfer', 'showHistoryNodes')) echo html::a($this->createLink('sectransfer', 'showHistoryNodes', 'id='.$sectransfer->id, '', true), $lang->sectransfer->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                        ?>
                    </div>
                </div>
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <td class="w-180px"><?php echo $lang->sectransfer->statusOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->sectransfer->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->sectransfer->reviewResult; ?></td>
                                <td style="width:370px;"><?php echo $lang->sectransfer->dealOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->sectransfer->reviewOpinionTime; ?></td>
                            </tr>
                            <?php
                            if((!empty($secondorder) && 'guestjx' == $secondorder->createdBy)
                                || ('1' == $sectransfer->jftype && $lang->sectransfer->cfjx == $sectransfer->externalRecipient)){
                                $lang->sectransfer->reviewNodeStatusList[7] = 'waitjx';
                            }
                            foreach ($lang->sectransfer->reviewNodeStatusList as $key => $reviewNode):
                                //$currentNode = $nodes[$key - 1];

                                $reviewerUserTitle = '';
                                $reviewerUsersShow = '';
                                $realReviewer = new stdClass();
                                $realReviewer->status = '';
                                $realReviewer->comment = '';
                                if (isset($nodes[$key - 1])) {
                                    $currentNode = $nodes[$key - 1];
                                    $reviewers = $currentNode->reviewers;
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
                                    }
                                }else{
                                    continue;
                                }

                               ?>
                                    <tr>
                                        <th><?php echo zget($lang->sectransfer->reviewNodeStatusLableList, $reviewNode); ?></th>
                                        <td title="<?php echo $reviewerUserTitle; ?>">
                                            <?php echo $reviewerUsersShow; ?>
                                        </td>
                                        <td><?php echo (
                                                ($sectransfer->status == 'waitApply' || $sectransfer->status == 'approveReject' || $sectransfer->status == 'externalReject')
                                                and $realReviewer->status == 'pending' and $reviewNode == 'waitCMApprove'
                                            ) ? '' : zget($lang->sectransfer->reviewStatusList, $realReviewer->status, ''); ?>
                                            <?php if (
                                                    $realReviewer->status == 'pass'
                                                    || $realReviewer->status == 'reject'
                                                    || $realReviewer->status == 'incorporate'
                                                    || $realReviewer->status == 'appoint'): ?>
                                                &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $realReviewer->comment ?>
                                        </td>
                                        <td><?php echo $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: ''?></td>
                                    </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>


        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($sectransferHistory); ?>
                <div class='divider'></div>
                <?php
                $param = "transferID=$sectransfer->id";
                common::hasPriv('sectransfer','edit') ? common::printIcon('sectransfer', 'edit', $param, $sectransfer, 'button') : '';
                if($sectransfer->status == 'centerReject'){
                    common::hasPriv('sectransfer','deal') ? common::printIcon('sectransfer', 'deal', "transferID=$sectransfer->id", $sectransfer,'button','play', '', 'iframe', true, "data-width=50%") : '';
                }else{
                    common::hasPriv('sectransfer','deal') ? common::printIcon('sectransfer', 'deal', "transferID=$sectransfer->id", $sectransfer,'button','play', 'hiddenwin', '') : '';
                }
                common::hasPriv('sectransfer','review') ? common::printIcon('sectransfer', 'review', $param, $sectransfer, 'button','glasses','', 'iframe',true, "data-width=50%") :'';
               if((($sectransfer->jftype == '1' && $sectransfer->externalRecipient != $this->lang->sectransfer->qszzx) || ($sectransfer->jftype == '2' && $secondorder->formType != $this->lang->sectransfer->external && empty($secondorder->externalCode))) && $sectransfer->status == 'alreadyEdliver'){
                    common::hasPriv('sectransfer','reject') ? common::printIcon('sectransfer', 'reject', "transferID=$sectransfer->id", $sectransfer, 'button','arrow-left','', 'iframe',true, "data-width=50%") : '';
                }
                common::hasPriv('sectransfer','delete') ? common::printIcon('sectransfer', 'delete', "transferID=$sectransfer->id", $sectransfer, 'button', 'trash', 'hiddenwin') : '';
                if($sectransfer->status == 'askCenterFailed'){
                    common::hasPriv('sectransfer','push') ? common::printIcon('sectransfer', 'push', "transferID=$sectransfer->id", $sectransfer, 'button', 'share', 'hiddenwin') : '';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th style="width:140px"><?php echo $lang->sectransfer->status; ?></th>
                            <td><?php echo zget($lang->sectransfer->statusListName, $sectransfer->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->approver; ?></th>
                            <?php
                            $dealUserTitle = '';
                            $dealUsersTitles = '';
                            if (!empty($sectransfer->approver)) {
                                foreach (explode(',', $sectransfer->approver) as $dealUser) {
                                    if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                                }
                            }
                            $dealUsersTitles = trim($dealUserTitle, ',');
                            ?>
                            <td title='<?php echo $dealUsersTitles; ?>'
                                class='text-ellipsis'><?php echo $dealUsersTitles; ?></td>
                        </tr>
                        <?php if($sectransfer->jftype == '1'):?>
                            <tr>
                                <th><?php echo $lang->sectransfer->inproject; ?></th>
                                <td><?php echo zget($inprojectList, $sectransfer->inproject); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->sectransfer->outproject; ?></th>
                                <td><?php echo zget($outprojectList, $sectransfer->outproject,''); ?></td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->sectransfer->app; ?></th>
                            <td><?php echo zget($apps, $sectransfer->app,''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->department; ?></th>
                            <td><?php echo zget($this->lang->application->teamList, $sectransfer->department,''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->jftype; ?></th>
                            <td><?php echo zget($lang->sectransfer->transferTypeList, $sectransfer->jftype,''); ?></td>
                        </tr>
                        <?php if($sectransfer->jftype == '2'):?>
                            <tr>
                                <th><?php echo $lang->sectransfer->subType; ?></th>
                                <td><?php echo zget($lang->sectransfer->transfersubTypeList, $sectransfer->subType,''); ?></td>
                            </tr>
                        <?php endif;?>
                        <?php if($sectransfer->jftype == '1'):?>
                            <tr>
                                <th><?php echo $lang->sectransfer->transferStage; ?></th>
                                <td><?php echo zget($lang->sectransfer->transitionPhase, $sectransfer->transitionPhase,''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->sectransfer->iscode; ?></th>
                                <td><?php echo zget($lang->sectransfer->oldOrNotList, $sectransfer->iscode,''); ?></td>
                            </tr>
                            <?php if($sectransfer->externalRecipient == $this->lang->sectransfer->qszzx): ?>
                            <tr>
                                <th><?php echo $lang->sectransfer->isLastTransfer; ?></th>
                                <td><?php echo zget($lang->sectransfer->orNotList, $sectransfer->lastTransfer,''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->sectransfer->transferNum; ?></th>
                                <td><?php echo $sectransfer->transferNum; ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th><?php echo $lang->sectransfer->recipient; ?></th>
                                <td><?php echo zget($this->lang->opinion->unionList, $sectransfer->externalRecipient,'');; ?></td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->sectransfer->externalContactEmail; ?></th>
                            <td><?php echo $sectransfer->externalContactEmail; ?></td>
                        </tr>
                        <?php if($sectransfer->jftype == '2'):?>
                            <tr>
                                <th><?php echo $lang->sectransfer->secondorderId; ?></th>
                                <td><?php echo $sectransfer->secondorderId == 0? '' : html::a($this->createLink('secondorder', 'view', 'id=' . $sectransfer->secondorderId, '', true), $secondorder->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->sectransfer->finallyHandOver; ?></th>
                                <td><?php echo zget($lang->sectransfer->finallyHandOverList ,$sectransfer->finallyHandOver,'') ?></td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->sectransfer->apply; ?></th>
                            <td><?php echo zget($users, $sectransfer->apply); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->dept; ?></th>
                            <td><?php echo zget($depts, $sectransfer->dept); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if($sectransfer->externalRecipient == '2' or ($sectransfer->jftype == '2' and !empty($secondorder->externalCode))):?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->feedbackInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th style="width:140px"><?php echo $lang->sectransfer->externalStatus; ?></th>
                            <td><?php echo zget($lang->sectransfer->externalStatusList, $sectransfer->externalStatus,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->rejectUser; ?></th>
                            <td><?php echo $sectransfer->rejectUser;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->rejectReason; ?></th>
                            <td><?php echo $sectransfer->rejectReason;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->externalTime; ?></th>
                            <td><?php echo $sectransfer->externalTime == '0000-00-00 00:00:00'?'':$sectransfer->externalTime;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->sectransfer->rejectNum; ?></th>
                            <td><?php echo $sectransfer->rejectNum;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->sectransfer->statusTransition; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->sectransfer->nodeUser; ?></th>
                            <td class='text-center'><?php echo $lang->sectransfer->before; ?></td>
                            <td class='text-center'><?php echo $lang->sectransfer->after; ?></td>
                            <td class='text-center'><?php echo $lang->sectransfer->deal; ?></td>
                        </tr>
                        <?php foreach ($consumed as $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                <?php
                                echo "<td class='text-center'>" . zget($lang->sectransfer->statusListName, $c->before, '-') . "</td>";
                                echo "<td class='text-center'>" . zget($lang->sectransfer->statusListName, $c->after, '-') . "</td>";?>
                                <td class='c-actions text-center'>
                                    <?php if(in_array($c->after, array_keys($lang->sectransfer->statusEditList))) {
                                        common::printIcon('sectransfer', 'workloadEdit', "sectransferID={$sectransfer->id}&consumedid={$c->id}", $sectransfer, 'list', 'edit', '', 'iframe', true);
                                        //common::printIcon('sectransfer', 'workloadDelete', "sectransferID=$sectransfer->id&consumedid={$c->id}", $sectransfer, 'list', 'trash', '', 'iframe', true);
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
<?php include '../../common/view/footer.html.php'; ?>
