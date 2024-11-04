<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>

    <style>
        .review-opinion {
            width: 370px
        }

        #canvas {
            height: 420px;
            width: 1300px;
        }

        .bjs-powered-by {
            display: none;
        }

        .nodeSuccess .djs-visual > :nth-child(1) {
            stroke: #52c41a !important;
            stroke-width: 3px;
        }

        .nodeProcessing .djs-visual > :nth-child(1) {
            fill: #1b85ff !important;
            stroke-width: 3px;
        }

        .nodeProcessing .djs-visual > :nth-child(2) {
            fill: #f6fff7 !important;
        }
        #taskTable a{color: #0c60e1}
    </style>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php if (!isonlybody()): ?>
                <?php $browseLink = $app->session->localeSupportList != false ? $app->session->creditList : inlink('browse'); ?>
                <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
                <div class="divider"></div>
            <?php endif; ?>
            <div class="page-title">
                <span class="label label-id"><?php echo $baseInfo->code; ?></span>
            </div>
        </div>
    </div>
<?php
$workTotal = 0;
$deptInfo = zmget($deptList, $baseInfo->deptIds, '');
$app = zmget($appList, $baseInfo->appIds, '');
$stype = zget($lang->localesupport->stypeList, $baseInfo->stype);
$supportUsersInfo = zmget($users, $baseInfo->supportUsers, '');
$areaInfo = zget($lang->localesupport->areaList, $baseInfo->area);
$mailto = zmget($users, $baseInfo->mailto, '');
$owndeptInfo = [];
$owndept = json_decode($baseInfo->owndept, true);
if(!empty($owndept)){
    foreach ($owndept as $appId => $val) {
        $appName = zget($appList, $appId);
        $team = zget($lang->application->teamList, $val);
        //$owndeptInfo[] = $appName . '：' . $team;
        $owndeptInfo[] = $team;
    }
}
$owndeptInfoStr = implode('，', $owndeptInfo);

$sjInfo = '';
$sjList = json_decode($baseInfo->sj, true);
//业务司局
if(!empty($sjList) && is_array($sjList)){
    $tempData = [];
    foreach ($sjList as $appId => $sj){
        $appName  = zget($appList, $appId);
        $fromUnit = zget($this->lang->application->fromUnitList, $sj);
        //$tempData[] = $appName .'：'.$fromUnit;
        $tempData[] = $fromUnit;
    }
    $sjInfo = implode("，", $tempData);
}

?>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th><?php echo $lang->localesupport->startDate; ?></th>
                                <td><?php echo $baseInfo->startDate != '0000-00-00 00:00:00' ? $baseInfo->startDate: ''; ?></td>

                                <th><?php echo $lang->localesupport->endDate; ?></th>
                                <td><?php echo $baseInfo->endDate != '0000-00-00 00:00:00' ? $baseInfo->endDate: ''; ?></td>

                            </tr>

                            <tr>
                                <th><?php echo $lang->localesupport->area; ?></th>
                                <td><?php echo $areaInfo; ?></td>
                                <th><?php echo $lang->localesupport->stype  ?></th>
                                <td><?php echo $stype; ?></td>
                            </tr>

                            <?php if($baseInfo->area == '1'):?>
                                <tr>
                                    <th><?php echo $lang->localesupport->jxdepart; ?></th>
                                    <td><?php echo $baseInfo->jxdepart; ?></td>
                                    <th><?php echo $lang->localesupport->sysper; ?></th>
                                    <td><?php echo $baseInfo->sysper; ?></td>
                                </tr>
                            <?php endif;?>

                            <tr>
                                <th><?php echo $lang->localesupport->reason; ?></th>
                                <td><?php echo $baseInfo->reason; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->localesupport->remark; ?></th>
                                <td><?php echo $baseInfo->remark; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->localesupport->filelist;?></th>
                                <td colspan='3'>
                                    <?php if($baseInfo->files):?>
                                    <div class='detail' style="margin-left: 0px !important;">
                                        <div class='detail-left article-left'>
                                          <?php
                                                echo $this->fetch('file', 'printFiles', array('files' => $baseInfo->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => $isAllowEdit, 'isAjaxDel' => $isAllowEdit));
                                            ?>
                                        </div>
                                    </div>
                                <?php else:?>
                                    <?php  echo $lang->noData;?>
                                 <?php endif;?>
                                </td>

                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!--处理意见-->
            <div class="cell">
                <div class="detail">
                    <div class="clearfix">
                        <div class="detail-title pull-left"><?php echo $lang->localesupport->comment; ?></div>
                        <div class="detail-title pull-right">
                            <?php
                            if(common::hasPriv('localesupport', 'showHistoryNodes')) echo html::a($this->createLink('localesupport', 'showHistoryNodes', 'id='.$baseInfo->id, '', true), $lang->localesupport->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                            ?>
                        </div>
                    </div>
                    <div class="detail-content article-content">
                        <table class="table ops">
                            <tr>
                                <th class="w-150px"><?php echo $lang->localesupport->reviewNode; ?></th>
                                <td class="w-150px"><?php echo $lang->localesupport->reviewerDept; ?></td>
                                <td class="w-240px"><?php echo $lang->localesupport->reviewer; ?></td>
                                <td class="w-150px"><?php echo $lang->localesupport->dealResult; ?></td>
                                <td class="review-opinion"><?php echo $lang->localesupport->reviewOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->localesupport->reviewTime; ?></td>
                            </tr>
                            <?php if(empty($reviewList)):?>
                                <tr>
                                    <td colspan="6" style="text-align: center;"><?php echo $lang->noData;?></td>
                                </tr>
                            <?php else:?>

                                <?php
                                foreach ($reviewList as $reviewNode):
                                    if(!isset($lang->localesupport->reviewNodeNameList[$reviewNode->nodeCode])){
                                        continue;
                                    }
                                    $nodeCode = $reviewNode->nodeCode;
                                    $nodeName = zget($lang->localesupport->reviewNodeNameList, $nodeCode);
                                    $currentNodeReviewerList = $reviewNode->reviewerList;
                                    $deptIds = array_keys($currentNodeReviewerList);
                                    $deptId = $deptIds[0];
                                    $count = count($currentNodeReviewerList);
                                    $currentDeptReviewerInfo = $currentNodeReviewerList[$deptId];
                                    $deptName = trim(zget($deptList, $deptId), '/');
                                    $reviewers = implode(',', $currentDeptReviewerInfo['reviewers']);
                                    $realReviewInfo = isset($currentDeptReviewerInfo['realReviewInfo']) ? $currentDeptReviewerInfo['realReviewInfo']: new stdClass();
                                    $reviewerUsers = zmget($users, $reviewers);
                                    ?>
                                    <tr>
                                        <th rowspan="<?php echo $count;?>"><?php echo $nodeName; ?></th>
                                        <td title="<?php echo $deptName; ?>">
                                            <?php echo $deptName; ?>
                                        </td>
                                        <td title="<?php echo $reviewerUsers; ?>">
                                            <?php echo $reviewerUsers; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if(isset($realReviewInfo->reviewer)):
                                                ?> <?php echo  zget($this->lang->localesupport->dealResultList, $realReviewInfo->status); ?>（<?php echo zget($users, $realReviewInfo->reviewer);?>）
                                            <?php else:?>
                                                待处理
                                            <?php endif;?>

                                        </td>
                                        <td> <?php echo isset($realReviewInfo->comment)? $realReviewInfo->comment : ''; ?></td>
                                        <td> <?php echo isset($realReviewInfo->reviewTime)? $realReviewInfo->reviewTime : ''; ?></td>
                                    </tr>

                                    <?php if($count > 1):
                                    unset($currentNodeReviewerList[$deptId]);
                                    foreach ($currentNodeReviewerList as $deptId => $currentDeptReviewerInfo):
                                        $deptName = trim(zget($deptList, $deptId), '/');
                                        $reviewers = implode(',', $currentDeptReviewerInfo['reviewers']);
                                        $realReviewInfo = isset($currentDeptReviewerInfo['realReviewInfo']) ? $currentDeptReviewerInfo['realReviewInfo']: new stdClass();
                                        $reviewerUsers = zmget($users, $reviewers);

                                        ?>
                                        <tr>
                                            <td title="<?php echo $deptName; ?>">
                                                <?php echo $deptName; ?>
                                            </td>
                                            <td title="<?php echo $reviewerUsers; ?>">
                                                <?php echo $reviewerUsers; ?>
                                            </td>
                                            <td>
                                                <?php
                                                if(isset($realReviewInfo->reviewer)):
                                                    ?> <?php echo  zget($this->lang->localesupport->dealResultList, $realReviewInfo->status); ?>（<?php echo zget($users, $realReviewInfo->reviewer);?>）
                                                <?php else:?>
                                                    待处理
                                                <?php endif;?>

                                            </td>
                                            <td> <?php echo isset($realReviewInfo->comment)? $realReviewInfo->comment : ''; ?></td>
                                            <td> <?php echo isset($realReviewInfo->reviewTime)? $realReviewInfo->reviewTime : ''; ?></td>
                                        </tr>
                                    <?php endforeach;?>

                                <?php endif;?>

                                <?php endforeach;?>
                            <?php endif;?>
                        </table>

                    </div>
                </div>
            </div>

            <?php if($taskList):?>

                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->localesupport->task;  ?></div>
                        <div class='detail-content'>
                            <table class='table table-bordered' id="taskTable">
                                <thead>
                                <tr>
                                    <th class='w-80px'><?php echo $lang->localesupport->taskId;?></th>
                                    <th><?php echo $lang->localesupport->taskName;?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($taskList as $taskInfo):?>
                                    <tr>
                                        <td><?php echo $taskInfo->id;?></td>
                                        <td>
                                            <?php echo common::hasPriv('task', 'view') ? html::a($this->createLink('task','view', "taskId=$taskInfo->id"), $taskInfo->name) : $taskInfo->name;?>
                                        </td>
                                    </tr>

                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php if(isset($browseLink)):?>
                        <?php common::printBack($browseLink); ?>
                    <?php endif;?>

                    <div class='divider'></div>
                    <?php
                        common::printIcon('localesupport', 'edit', "localesupportId=$baseInfo->id", $baseInfo, 'list');
                        common::printIcon('localesupport', 'reportWork', "localesupportId=$baseInfo->id", $baseInfo, 'list', 'clock', '', 'iframe', true);
                        common::printIcon('localesupport', 'submit', "localesupportId=$baseInfo->id", $baseInfo, 'list', 'play', 'hiddenwin');
                        common::printIcon('localesupport', 'review', "localesupportId=$baseInfo->id", $baseInfo, 'list', 'glasses', '', 'iframe', true);
                        common::printIcon('localesupport', 'delete', "localesupportId=$baseInfo->id&confirm=no&source=view", $baseInfo, 'list', 'trash', 'hiddenwin');
                    ?>
                </div>
            </div>
        </div>

        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->localesupport->baseinfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>

                            <tr>
                                <th><?php echo $lang->localesupport->appIds  ?></th>
                                <td><?php echo $app ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->localesupport->owndept; ?></th>

                                <td><?php echo $owndeptInfoStr; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->localesupport->sj; ?></th>
                                <td><?php echo $sjInfo; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->localesupport->deptIds; ?></th>
                                <td><?php echo $deptInfo; ?></td>
                            </tr>


                            <tr>
                                <th><?php echo $lang->localesupport->supportUsers; ?></th>
                                <td><?php echo $supportUsersInfo; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->localesupport->manufacturer; ?></th>
                                <td><?php echo $baseInfo->manufacturer; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->localesupport->work; ?></th>
                                <td><?php echo zget($consumedList, $localesupportId, 0); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->localesupport->createdBy; ?></th>
                                <td><?php echo zget($users, $baseInfo->createdBy); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->localesupport->createdTime; ?></th>
                                <td><?php echo $baseInfo->createdTime ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->localesupport->status; ?></th>
                                <td><?php echo zget($lang->localesupport->statusList, $baseInfo->status);  ?></td>
                            </tr>
                            <!--
                            <tr>
                                <th><?php echo $lang->localesupport->mailto; ?></th>
                                <td><?php echo $mailto; ?></td>
                            </tr>
                            -->


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->localesupport->workreport;  ?></div>
                    <div class='detail-content'>
                        <table class='table table-bordered '>
                            <thead>
                            <tr>
                                <th><?php echo $lang->localesupport->supportUsers;?></th>
                                <th><?php echo $lang->localesupport->supportDate;?></th>
                                <th><?php echo $lang->localesupport->consumed ;?></th>
                                <th><?php echo $lang->localesupport->work ;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($baseInfo->workReportList)):
                                foreach ($baseInfo->workReportList as $k=>$work):
                                    foreach ($work as $k1=>$v):
                                        ?>
                                        <?php
                                        $workTotal+=$v->consumed;
                                        ?>
                                        <tr>
                                            <?php if($k1==0):?>
                                                <td rowspan="<?php echo count($work)?>"><?php echo zmget($users, $v->supportUser, ''); ?></td>
                                            <?php endif; ?>
                                            <td><?php echo $v->supportDate; ?></td>
                                            <td><?php echo $v->consumed; ?></td>
                                            <?php if($k1==0):?>
                                                <td rowspan="<?php echo count($work)?>"><?php echo array_sum(array_column((array)$work,'consumed')) ?></td>
                                            <?php endif; ?>
                                        </tr>

                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


<?php include '../../common/view/footer.html.php'; ?>