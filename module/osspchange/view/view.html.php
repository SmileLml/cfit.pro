<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $osspchangeHistory = $app->session->osspchangeHistory ? $app->session->osspchangeHistory : inlink('browse') ?>
            <?php echo html::a($osspchangeHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="label label-id"><?php echo $osspchange->id ?></span>
            <span class="text"><?php echo $this->lang->osspchange->code.'：'.$osspchange->code; ?></span>

        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->title; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($osspchange->title) ? $osspchange->title : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->background; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($osspchange->background) ? $osspchange->background : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->content; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($osspchange->content) ? $osspchange->content : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <?php if ($osspchange->files): ?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->osspchange->filelist; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                    <div class="detail-content">
                        <?php
                        foreach ($osspchange->files as $key => $file) {
                            echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $osspchange, 'canOperate' => $file->addedBy == $this->app->user->account));
                        }; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->advise; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($osspchange->advise) ? $osspchange->advise : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->reviewResult; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($result) ? $result : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->changeNotice; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($osspchange->changeNotice) ? zget($this->lang->osspchange->changeNoticeList,$osspchange->changeNotice) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->fileInfo; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($osspchange->fileInfo) ? $osspchange->fileInfo : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <!-- 审核审批意见/处理意见 -->
        <div class="cell">
            <div class="detail">
                <div class="clearfix">
                    <div class="detail-title pull-left"><?php echo $lang->osspchange->reviewList; ?></div>
                    <div class="detail-title pull-right">
                        <?php
                        if(common::hasPriv('osspchange', 'showHistoryNodes')) echo html::a($this->createLink('osspchange', 'showHistoryNodes', 'id='.$osspchange->id, '', true), $lang->osspchange->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                        ?>
                    </div>
                </div>
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <td class="w-180px"><?php echo $lang->osspchange->statusOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->osspchange->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->osspchange->reviewResult; ?></td>
                                <td style="width:370px;"><?php echo $lang->osspchange->dealOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->osspchange->reviewOpinionTime; ?></td>
                            </tr>
                            <?php foreach ($nodes as $currentNode):
                                $reviewerUserTitle = '';
                                $reviewerUsersShow = '';
                                $realReviewer = new stdClass();
                                $realReviewer->status = '';
                                $realReviewer->comment = '';
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
                                    if($realReviewer->extra){
                                        $currentExtra = json_decode($realReviewer->extra, true);
                                        $reviewInfo = $currentExtra['reviewInfo'];
                                    }
                                }
                                ?>
                                <tr>
                                    <th><?php echo zget($lang->osspchange->reviewNameList, $currentNode->nodeCode); ?></th>
                                    <td title="<?php echo $reviewerUserTitle; ?>">
                                        <?php echo $reviewerUsersShow; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($currentNode->status == $lang->osspchange->pendingStatus){
                                            $result = $lang->osspchange->pending;
                                        }elseif($currentNode->status == $lang->osspchange->ignoreStatus){
                                            $result = $lang->osspchange->ignore;
                                        }elseif($currentNode->nodeCode == $lang->osspchange->statusList['waitConfirm']){
                                            $result = zget($resultList, $reviewInfo, '');
                                        }elseif($currentNode->nodeCode == $lang->osspchange->statusList['waitDeptApprove']){
                                            $result = zget($systemManagerList, $reviewInfo, '');
                                        }elseif($currentNode->nodeCode == $lang->osspchange->statusList['waitQMDApprove']){
                                            $result = zget($QMDmanagerList, $reviewInfo, '');
                                        }elseif($currentNode->nodeCode == $lang->osspchange->statusList['waitMaxLeaderApprove']){
                                            $result = zget($maxLeaderList, $reviewInfo, '');
                                        }elseif($currentNode->nodeCode == $lang->osspchange->statusList['waitClosed']){
                                            $result = zget($closedList, $reviewInfo, '');
                                        }
                                        echo $result;
                                        $reviewInfo = '';
                                        ?>
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
                <?php common::printBack($osspchangeHistory); ?>
                <div class='divider'></div>
                <?php
                $param = "transferID=$osspchange->id";
                common::hasPriv('osspchange','edit') ? common::printIcon('osspchange', 'edit', $param, $osspchange, 'list') : '';
                //                    common::hasPriv('osspchange','submit') ? common::printIcon('osspchange', 'submit', "osspchangeID=$osspchange->id", $osspchange,'list','play', 'hiddenwin', '') : '';
                common::hasPriv('osspchange','confirm') ? common::printIcon('osspchange', 'confirm', $param, $osspchange, 'list','play','', 'iframe',true, "data-width=80%") :'';
                common::hasPriv('osspchange','review') ? common::printIcon('osspchange', 'review', $param, $osspchange, 'list','glasses','', 'iframe',true, "data-width=50%") :'';
                common::hasPriv('osspchange','close') ? common::printIcon('osspchange', 'close', $param, $osspchange, 'list','off','', 'iframe',true, "data-width=80%") : '';
                common::hasPriv('osspchange','delete') ? common::printIcon('osspchange', 'delete', "osspchangeID=$osspchange->id", $osspchange, 'list', 'trash', 'hiddenwin') : '';
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th style="width:140px"><?php echo $lang->osspchange->status; ?></th>
                            <td><?php echo zget($lang->osspchange->statusNameList, $osspchange->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->dealuser; ?></th>
                            <td title='<?php echo zmget($users, $osspchange->dealuser); ?>'
                                class='text-ellipsis'><?php echo zmget($users, $osspchange->dealuser); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->proposer; ?></th>
                            <td><?php echo zget($users, $osspchange->proposer); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->systemProcess; ?></th>
                            <td><?php echo zget($lang->osspchange->systemProcessList, $osspchange->systemProcess,''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->systemVersion; ?></th>
                            <td><?php echo zget($lang->osspchange->systemVersionList, $osspchange->systemVersion,''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->systemDept; ?></th>
                            <td><?php echo zget($depts, $osspchange->systemDept,''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->systemManager; ?></th>
                            <td><?php echo zget($users, $osspchange->systemManager,''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->QMDmanager; ?></th>
                            <td><?php echo zget($users, $osspchange->QMDmanager,''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->osspchange->notifyPerson; ?></th>
                            <td><?php echo zmget($users, $osspchange->notifyPerson); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->osspchange->statusTransition; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->osspchange->nodeUser; ?></th>
                            <td class='text-center'><?php echo $lang->osspchange->before; ?></td>
                            <td class='text-center'><?php echo $lang->osspchange->after; ?></td>
                        </tr>
                        <?php foreach ($consumed as $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                <?php
                                echo "<td class='text-center'>" . zget($lang->osspchange->statusNameList, $c->before, '-') . "</td>";
                                echo "<td class='text-center'>" . zget($lang->osspchange->statusNameList, $c->after, '-') . "</td>";?>
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
