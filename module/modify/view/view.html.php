<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
.review-opinion{
    width: 370px
}
</style>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php if (!isonlybody()): ?>
                <?php $browseLink = $app->session->modifyList != false ? $app->session->modifyList : inlink('browse'); ?>
                <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
                <div class="divider"></div>
            <?php endif; ?>
            <div class="page-title">
                <span class="label label-id"><?php echo $modify->code ?></span>
            </div>
        </div>
        <?php if (!isonlybody()): ?>
            <div class="btn-toolbar pull-right">
                  <?php 
                    if(common::hasPriv('modify', 'exportWord')) echo html::a($this->createLink('modify', 'exportWord', "modifyID=$modify->id"), "<i class='icon-export'></i> {$lang->outwarddelivery->exportWord}", '', "class='btn btn-primary'");
                  ?>
            </div>
        <?php endif; ?>
    </div>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">

                <div class="cell">
                    <div class="detail">
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modify->subTitle->params; ?></div>
                            <div class='detail-content'>
                                <table class='table table-data'>
                                    <tbody>
                                    <tr>
                                        <th class='w-160px'><?php echo $lang->modifycncc->applyUsercontact; ?></th>
                                        <td><?php echo $modify->applyUsercontact; ?></td>
                                        <th class='w-140px'><?php echo $lang->modifycncc->level; ?></th>
                                        <td><?php echo zget($lang->modifycncc->levelList, $modify->level, ''); ?></td>
                                    </tr>
                                    <?php if($isSecond): ?>
                                        <tr>
                                            <th class='w-150px'><?php echo $lang->modify->jxLevel; ?></th>
                                            <td class='c-actions text-left'><?php echo zget($lang->modify->levelJxList, $modify->jxLevel, ''); ?>
                                                <?php
                                                if($isReview){
                                                    //需求收集2646
                                                    //common::printIcon('modify', 'editLevel', "modifyId=$modify->id", $modify, 'list', 'edit', '', 'iframe', true);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <!-- <th><?php echo $lang->modifycncc->productRegistrationCode; ?></th>
                                        <td><?php echo $modify->productRegistrationCode; ?></td> -->
                                        <th><?php echo $lang->modifycncc->node; ?></th>
                                        <td>
                                            <?php
                                            $changeNodes = [];
                                            foreach (explode(',', $modify->node) as $node) {
                                                if (empty($node)) continue;
                                                $changeNodes [] = zget($lang->modify->nodeList, $node, '');
                                            }
                                            echo implode(',', $changeNodes);
                                            ?>
                                        </td>
                                        <!-- <th><?php echo $lang->modifycncc->operationType; ?></th>
                                        <td><?php echo zget($lang->modifycncc->operationTypeList, $modify->operationType, ''); ?></td> -->
                                        <th><?php echo $lang->modify->isReview; ?></th>
                                        <td>
                                            <?php
                                                if(isset($lang->modify->isReviewList[$modify->isReview])){
                                                    echo $lang->modify->isReviewList[$modify->isReview];
                                                }else{
                                                    echo '';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php if($modify->isReview==2): ?>
                                    <tr>
                                        <!-- <th><?php echo $lang->modify->isReviewPass; ?></th>
                                        <td><?php echo zget($lang->modify->isReviewPassList,$modify->isReviewPass); ?></td> -->
                                        <th><?php echo $lang->modify->reviewReport; ?></th>
                                        <td colspan="3"><?php foreach (explode(',',$modify->reviewReport) as $value):?>
                                                <?php echo html::a($this->createLink('review', 'view', 'id=' . $value, '', true), zget($reviewReportList, $value, ''), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ; ?>
                                                <br>
                                            <?php endforeach;?>
                                        </td>
                                    </tr>

                                    <?php endif;?>
                                    <!-- <?php
                                    if ($modify->app) {
                                        foreach ($modify->appsInfo as $appID => $appInfo) {
                                            $apps[] = $appInfo->name;
                                            $isPayments[] = zget($lang->application->isPaymentList, $appInfo->isPayment, '');
                                            $teams[] = zget($lang->application->teamList, $appInfo->team, '');
                                        }
                                    }
                                    ?> -->
                                    <!-- <tr>
                                        <th><?php echo $lang->outwarddelivery->app; ?></th>
                                        <td>
                                            <?php echo trim(implode('<br/>', array_unique($apps)), ','); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->isPayment; ?></th>
                                        <td>
                                        <?php echo trim(implode('<br/>', array_unique($isPayments)), ','); ?>
                                        </td>
                                        <th><?php echo $lang->modifycncc->team; ?></th>
                                        <td>
                                        <?php echo trim(implode('<br/>', array_unique($teams)), ','); ?>
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <th><?php echo $lang->modifycncc->mode; ?></th>
                                        <td><?php echo zget($lang->modifycncc->modeList, $modify->mode, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->classify; ?></th>
                                        <td><?php echo zget($lang->modifycncc->classifyList, $modify->classify, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->changeSource; ?></th>
                                        <td><?php echo zget($lang->modifycncc->changeSourceList, $modify->changeSource, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->changeStage; ?></th>
                                        <td><?php echo zget($lang->modifycncc->changeStageList, $modify->changeStage, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modify->outsidePlanId; ?></th>
                                        <td><?php echo zget($outsideProjectList, $modify->outsidePlanId, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->implementModality; ?></th>
                                        <td><?php echo zget($lang->modifycncc->implementModalityList, $modify->implementModality, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->type; ?></th>
                                        <td><?php echo zget($lang->modifycncc->typeList, $modify->type, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->isBusinessCooperate; ?></th>
                                        <td><?php echo zget($lang->modifycncc->isBusinessCooperateList, $modify->isBusinessCooperate, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->isBusinessJudge; ?></th>
                                        <td><?php echo zget($lang->modifycncc->isBusinessJudgeList, $modify->isBusinessJudge, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->isBusinessAffect; ?></th>
                                        <td><?php echo zget($lang->modifycncc->isBusinessAffectList, $modify->isBusinessAffect, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->property; ?></th>
                                        <td><?php echo zget($lang->modifycncc->propertyList, $modify->property, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->backspaceExpectedStartTime; ?></th>
                                        <td><?php if (strtotime($modify->backspaceExpectedStartTime) > 0) echo $modify->backspaceExpectedStartTime; ?></td>
                                        <th><?php echo $lang->modifycncc->backspaceExpectedEndTime; ?></th>
                                        <td><?php if (strtotime($modify->backspaceExpectedEndTime) > 0) echo $modify->backspaceExpectedEndTime; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->planBegin; ?></th>
                                        <td><?php if (strtotime($modify->planBegin) > 0) echo $modify->planBegin; ?></td>
                                        <th><?php echo $lang->modifycncc->planEnd; ?></th>
                                        <td><?php if (strtotime($modify->planEnd) > 0) echo $modify->planEnd; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modify->involveDatabase; ?></th>
                                        <td><?php echo zget($lang->modify->materialIsReviewList,$modify->involveDatabase,''); ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->desc; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->desc) ? $modify->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modify->target; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->target) ? html_entity_decode(str_replace("\n","<br/>",$modify->target)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->reason; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->reason) ? html_entity_decode(str_replace("\n","<br/>",$modify->reason)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->step; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->step) ? html_entity_decode(str_replace("\n","<br/>",$modify->step)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->techniqueCheck; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->techniqueCheck) ? html_entity_decode(str_replace("\n","<br/>",$modify->techniqueCheck)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->changeContentAndMethod; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->changeContentAndMethod) ? html_entity_decode(str_replace("\n","<br/>",$modify->changeContentAndMethod)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->checkList; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->checkList) ? html_entity_decode(str_replace("\n","<br/>",$modify->checkList)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modify->preChange; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->preChange) ? html_entity_decode(str_replace("\n","<br/>",$modify->preChange)) : "<div class='text-center text-muted'>" . $lang->modify->noChange . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modify->postChange; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->postChange) ? html_entity_decode(str_replace("\n","<br/>",$modify->postChange)) : "<div class='text-center text-muted'>" . $lang->modify->noChange . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modify->synImplement; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->synImplement) ? html_entity_decode(str_replace("\n","<br/>",$modify->synImplement)) : "<div class='text-center text-muted'>" . $lang->modify->noChange . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modify->pilotChange; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->pilotChange) ? html_entity_decode(str_replace("\n","<br/>",$modify->pilotChange)) : "<div class='text-center text-muted'>" . $lang->modify->noChange . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modify->promotionChange; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->promotionChange) ? html_entity_decode(str_replace("\n","<br/>",$modify->promotionChange)) : "<div class='text-center text-muted'>" . $lang->modify->noChange . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->cooperateDepNameList; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->cooperateDepNameList) ? zget($lang->modify->cooperateDepNameListList, $modify->cooperateDepNameList, '') : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->businessCooperateContent; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->businessCooperateContent) ? html_entity_decode(str_replace("\n","<br/>",$modify->businessCooperateContent)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->judgeDep; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->judgeDep) ? zget($lang->modify->judgeDepList, $modify->judgeDep, '') : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->judgePlan; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->judgePlan) ? html_entity_decode(str_replace("\n","<br/>",$modify->judgePlan)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->controlTableFile; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->controlTableFile) ? html_entity_decode(str_replace("\n","<br/>",$modify->controlTableFile)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->controlTableSteps; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->controlTableSteps) ? html_entity_decode(str_replace("\n","<br/>",$modify->controlTableSteps)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->feasibilityAnalysis; ?></div>
                            <div class="detail-content article-content">
                                <?php if (!empty($modify->feasibilityAnalysis)) {
                                    $feasibilityAnalysisInfo = array();
                                    $feasibilityAnalysises = explode(',', $modify->feasibilityAnalysis);
                                    foreach ($feasibilityAnalysises as $feasibilityAnalysis) {
                                        $feasibilityAnalysisInfo[] = zget($lang->modifycncc->feasibilityAnalysisList, $feasibilityAnalysis, '');
                                    }
                                    echo trim(implode(',', $feasibilityAnalysisInfo), ',');
                                } else {
                                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->risk; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->risk) ? html_entity_decode(str_replace("\n","<br/>",$modify->risk)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->riskAnalysisEmergencyHandle; ?></div>
                            <div class="detail-content article-content">
                                <table class="table ops">
                                    <tr>
                                        <th class="w-200px"><?php echo $lang->modifycncc->id; ?></th>
                                        <td><?php echo $lang->modifycncc->riskAnalysis; ?></td>
                                        <td><?php echo $lang->modifycncc->emergencyBackWay; ?></td>
                                    </tr>
                                    <?php if ($modify->riskAnalysisEmergencyHandle): ?>
                                        <?php $num = 1;
                                        foreach ($modify->riskAnalysisEmergencyHandle as $ER): ?>
                                            <tr>
                                                <th><?php echo $num; ?></th>
                                                <td>
                                                    <?php echo $ER->riskAnalysis; ?>
                                                </td>
                                                <td>
                                                    <?php echo $ER->emergencyBackWay;
                                                    $num = $num + 1 ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->effect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->effect) ? html_entity_decode(str_replace("\n","<br/>",$modify->effect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->businessFunctionAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->businessFunctionAffect) ? html_entity_decode(str_replace("\n","<br/>", $modify->businessFunctionAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->backupDataCenterChangeSyncDesc; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->backupDataCenterChangeSyncDesc) ? html_entity_decode(str_replace("\n","<br/>",$modify->backupDataCenterChangeSyncDesc)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->emergencyManageAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->emergencyManageAffect) ? html_entity_decode(str_replace("\n","<br/>",$modify->emergencyManageAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->businessAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modify->businessAffect) ? html_entity_decode(str_replace("\n","<br/>",$modify->businessAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <?php if($modify->status == 'modifyfail' or $modify->status == 'modifyrollback' or $modify->status == 'modifycancel' or $modify->status == 'modifyreject' or $modify->status == 'modifyerror'): ?>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->modifycncc->resultTitle; ?></div>
                            <div class='detail-content'>
                                <table class='table table-data'>
                                    <tbody>
                                    <tr>
                                        <th class='w-150px'><?php echo $lang->modifycncc->feedBackId; ?></th>
                                        <td><?php echo $modify->feedbackId; ?></td>
                                        <th class='w-150px'><?php echo $lang->modifycncc->operationName; ?></th>
                                        <td><?php echo $modify->operateName; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->feedBackOperationType; ?></th>
                                        <td><?php echo $modify->operateType; ?></td>
                                        <th><?php echo $lang->modifycncc->depOddName; ?></th>
                                        <td><?php echo $modify->code; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->actualBegin; ?></th>
                                        <td>
                                            <?php
                                            if ($modify->realStartTime != '0000-00-00 00:00:00'){
                                                echo $modify->realStartTime;
                                            }

                                            ?>
                                        </td>
                                        <th><?php echo $lang->modifycncc->actualEnd; ?></th>
                                        <td>
                                            <?php
                                                if ($modify->realEndTime != '0000-00-00 00:00:00'){
                                                    echo $modify->realEndTime;
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->supply; ?></th>
                                        <td><?php echo $modify->supportUserName; ?></td>
                                        <th><?php echo $lang->modifycncc->changeNum; ?></th>
                                        <td><?php echo $modify->externalCode; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->operationStaff; ?></th>
                                        <td><?php echo $modify->operateUserName; ?></td>
                                        <th><?php echo $lang->modifycncc->executionResults; ?></th>
                                        <td><?php echo $modify->implementResult; ?></td>
                                    </tr>
                                    <!--        <tr>-->
                                    <!--          <th>--><?php //echo $lang->modifycncc->result;?><!--</th>-->
                                    <!--          <td>-->
                                    <?php //echo zget($lang->modifycncc->resultList,$modify->result);?><!--</td>-->
                                    <!--          <th>--><?php //echo $lang->modifycncc->internalSupply;?><!--</th>-->
                                    <!--          --><?php //if(!empty($modify->internalSupply)){
                                    //            $supplyInfo=array();
                                    //            $internalSupplys=explode(',',$modify->internalSupply);
                                    //            foreach ($internalSupplys as $internalSupply){
                                    //              $supplyInfo[]=zget($users,$internalSupply);
                                    //            }
                                    //          }?>
                                    <!--          <td>--><?php //echo implode(',',$supplyInfo);?><!--</td>-->
                                    <!--        </tr>-->
                                    <tr>
                                        <th><?php echo $lang->modifycncc->problemDescription; ?></th>
                                        <td><?php echo html_entity_decode(str_replace("\n","<br/>",$modify->issueDesc)); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->resolveMethod; ?></th>
                                        <td><?php echo html_entity_decode(str_replace("\n","<br/>",$modify->resolveMethod)); ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                </div>

            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->ROR; ?></div>
                    <?php if ($modify->ROR): ?>
                        <div class="detail-content article-content">
                            <table class="table ops">
                                <tr>
                                    <th class="w-200px"><?php echo $lang->productenroll->num; ?></th>
                                    <td class="w-200px"><?php echo $lang->outwarddelivery->RORDate; ?></td>
                                    <td><?php echo $lang->outwarddelivery->ROR; ?></td>
                                </tr>
                                <?php $num = 1;
                                foreach ($modify->ROR as $ROR): ?>
                                    <tr>
                                        <th><?php echo $num; ?></th>
                                        <td>
                                            <?php echo $ROR['RORDate']; ?>
                                        </td>
                                        <td>
                                            <?php echo $ROR['RORContent'];
                                            $num = $num + 1 ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php else: ?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if($testingrequest): ?>
              <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->modify->testReport;?></div>
                    <div class="detail-content article-content">
                        <?php echo $this->fetch('file', 'printFiles', array('files' => $testingrequest->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false));?>
                    </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- 审核审批意见 -->
            <div class="cell">
                <div class="detail">
                    <div class="clearfix">
                        <div class="detail-title pull-left"><?php echo $lang->outwarddelivery->reviewOpinion; ?></div>
                        <div class="detail-title pull-right">
                            <?php
                            if(common::hasPriv('modify', 'showHistoryNodes')) echo html::a($this->createLink('modify', 'showHistoryNodes', 'id='.$modify->id, '', true), $lang->modify->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                            ?>
                        </div>
                    </div>

                    <div class="detail-content article-content">
                        <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <th class="w-180px"><?php echo $lang->outwarddelivery->reviewNode; ?></th>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewResult; ?></td>
                                <td class="review-opinion"><?php echo $lang->outwarddelivery->reviewOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewTime; ?></td>
                            </tr>
                            <?php
                            if ($modify->level == 2):
                                unset($lang->outwarddelivery->reviewNodeList[6]);
                            elseif ($modify->level == 3):
                                unset($lang->outwarddelivery->reviewNodeList[5]);
                                unset($lang->outwarddelivery->reviewNodeList[6]);
                            endif;
                            //循环数据
                            if ($modify->createdDate > "2024-04-02 23:59:59"){
                                unset($this->lang->modify->reviewNodeList[3]);
                            }
                            foreach ($lang->modify->reviewNodeList as $key => $reviewNode):
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
                                    $reviewersArrayNew = $this->loadModel('common')->getAuthorizer('modify', implode(',', $reviewersArray), $lang->modify->reviewBeforeStatusList[$key], $lang->modify->authorizeStatusList);
                                    $reviewersArray = explode(',', $reviewersArrayNew);
                                    $userCount = count($reviewersArray);
                                    if ($userCount > 0) {
                                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                        $reviewerUserTitle = implode(',', $reviewerUsers);
                                        $subCount = 10;
                                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                        //获得实际审核人
                                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                        $extra = json_decode($realReviewer->extra);
                                        if(!empty($extra->reviewerList)){
                                            $reviewersArray = $extra->reviewerList;
                                            $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                            $reviewerUserTitle = implode(',', $reviewerUsers);
                                            $subCount = 10;
                                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                        }
                                    }
                                }
                                if ( in_array($key, $lang->outwarddelivery->skipNodes) and (! in_array($realReviewer->status,['pass','reject']))) { continue; }
                                ?>

                                <tr>
                                    <th><?php echo $reviewNode; ?></th>
                                    <td title="<?php echo $reviewerUserTitle; ?>">
                                        <?php echo $reviewerUsersShow; ?>
                                    </td>
                                    <td>
                                        <?php if('save' == $modify->issubmit && in_array($modify->status, ['reject','reviewfailed'])): ?>
                                        <?php elseif ($modify->status != 'waitsubmitted'): ?>
                                            <?php
                                            if($realReviewer->status == 'ignore'){
                                                if($key == 3 or $key == 7){
                                                    echo '无需处理';
                                                    $ignoreComment = 3 == $key ? implode('',array_unique(array_column((array)$reviewers, 'comment'))) : '';
                                                    $realReviewer->comment = $ignoreComment;
                                                }else{
                                                    echo '本次跳过';
                                                    $realReviewer->comment = '已通过';
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
                                                    echo zget($users, $extra->proxy, '')."处理";
                                                    echo "【".zget($users, $realReviewer->reviewer)."授权】";
                                                }else{
                                                    echo zget($users, $realReviewer->reviewer, '');
                                                }?>）
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $realReviewer->comment; ?></td>
                                    <td><?php echo $realReviewer->reviewTime; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php $modify->reviewFailReason = json_decode($modify->reviewFailReason, true);
                            $continueStatus = ['modifyreject','modifycancel','modifysuccess','modifysuccesspart','modifyrollback','modifyfail','modifyerror'];
                            $count = count($modify->reviewFailReason[$modify->version]);
                            foreach ($modify->reviewFailReason[$modify->version] as $key => $reasons):
//                                if(in_array($modify->status, $continueStatus) && $key == $count - 1){
//                                    continue;
//                                }
                            foreach ($reasons as $reason): ?>
                                <tr>
                                    <th><?php echo $lang->modify->outerReviewNodeList[$reason['reviewNode']]; ?></th>
                                    <td><?php echo zget($users, $reason['reviewUser'], ','); ?></td>
                                    <?php if(!($modify->lastStatus == 'waitqingzong' && $modify->cancelStatus)): ?>
                                        <td><?php echo $reason['reviewResult'] ?></td>
                                        <td><?php echo $reason['reviewFailReason'] ?></td>
                                        <td><?php echo $reason['reviewPushDate'] ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; endforeach; ?>
                            <?php if(
                                (strtotime($modify->createdDate) >= strtotime('2022-09-30') or strtotime($modify->editedDate) >= strtotime('2022-09-30'))
                                and !('reject' == $modify->status && !empty($modify->reviewFailReason[$modify->version]))
                                and !(in_array($modify->status, $continueStatus) && !empty($modify->reviewFailReason[$modify->version]))
                            ): ?>
                                <?php if($modify->isDiskDelivery==0): ?>
                                <tr>
                                    <th><?php echo $lang->modify->outerReviewNodeList['4']; ?></th>
                                    <td><?php echo zget($users, 'guestjk', ','); ?></td>
                                    <?php if(!($modify->lastStatus == 'waitqingzong' && $modify->cancelStatus)): ?>
                                    <td>
                                        <?php
                                        if (in_array($modify->status, array('waitqingzong', 'jxsynfailed','waitImplement'))) {
                                            echo zget($lang->modify->statusList, $modify->status=='waitImplement'?'jxsynfailed':$modify->status, '');
                                        } elseif (in_array($modify->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)) {
                                            echo $lang->modify->synSuccess;
                                        } else {
                                            echo '';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (in_array($modify->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)) {
                                            echo '生产变更单同步成功';
                                        } elseif (in_array($modify->status, array('waitqingzong', 'jxsynfailed','waitImplement'))) {
                                            echo $modify->pushFailReason;
                                        }  ?>
                                    </td>
                                    <td><?php if($modify->pushDate != '0000-00-00 00:00:00' and in_array($modify->status, array('waitqingzong', 'jxsynfailed','withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)){echo $modify->pushDate; }?></td>
                                    <?php endif; ?>
                                </tr>                                
                                <tr>
                                    <th><?php echo $lang->modify->outerReviewNodeList['5']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestjx', ','); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (in_array($modify->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)) {
                                            echo zget($lang->modify->statusList, $modify->status);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (in_array($modify->status, array('modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)) {
                                            if($modify->status == 'modifyreject'){
                                                echo "打回人：".$modify->approverName."<br>审批意见：".$modify->returnReason;
                                            }else{
                                                echo $modify->returnReason;
                                            }
                                        }   else {
                                            echo '';
                                        }
                                        ?>
                                    </td>
                                    <td><?php if (strtotime($modify->changeDate) > 0 and in_array($modify->status, array('modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)) echo $modify->changeDate; ?></td>
                                </tr>
                                <?php endif;?>
                            <?php endif; ?>
                            <?php else: ?>
                                <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 取消变更审批意见 -->
            <!-- <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->modify->cancelReviewResult; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($modify->cancelStatus)): ?>
                        <table class="table ops">
                            <tr>
                                <th class="w-180px"><?php echo $lang->outwarddelivery->reviewNode; ?></th>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->modify->reviewResultAna; ?></td>
                                <td><?php echo $lang->outwarddelivery->reviewComment; ?></td>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewTime; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->cancelreviewNodeList['0']; ?></th>
                                <td>
                                    <?php
                                    $as = array();
                                    foreach(explode(',',trim($modify->cancelReviewer,',')) as $dealUser){
                                        $as[] = zget($users, $dealUser);
                                    }
                                    echo implode(',',$as); ?>
                                </td>
                                <td>
                                    <?php
                                    if($modify->cancelStatus == 'canceltojx' || $modify->cancelStatus == 'canceled'){
                                            echo '通过';
                                        } elseif($modify->cancelStatus == 'cancelback') {
                                            echo '不通过';

                                        }
                                    ?>
                                </td>
                                <td><?php echo $modify->cancelComment; ?></td>
                                <td><?php echo $modify->cancelReviewDate; ?></td>
                            </tr>
                            <?php if($modify->externalId && $modify->externalCode):?>
                            <tr>
                                <th><?php echo $lang->modify->outerReviewNodeList['4']; ?></th>
                                <td>
                                    <?php echo zget($users, 'guestjk', ','); ?>
                                </td>
                                    <td>
                                        <?php
                                        if (in_array($modify->status, array('canceltojx'))) {
                                            echo '待同步金信';
                                        } elseif (in_array($modify->status, array('jxsyncancelfailed','canceled'))) {
                                            echo zget($lang->modify->statusList, $modify->status, '');
                                        } else{
                                            echo '';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (in_array($modify->status, array('canceled'))) {
                                            echo '取消生产变更单同步成功';
                                        } elseif ($modify->status == 'jxsyncancelfailed') {
                                            echo $modify->cancelPushFailReason;
                                        }  ?>
                                    </td>
                                    <td><?php if($modify->cancelPushDate != '0000-00-00 00:00:00'){echo $modify->cancelPushDate; }?></td>
                            </tr>
                            <?php endif;?>
                            <?php else: ?>
                                <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div> -->

            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>

            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php common::printBack($browseLink); ?>
                    <div class='divider'></div>
                    <?php
                    common::printIcon('modify', 'edit', "modifyID=$modify->id", $modify, 'button');
                    common::printIcon('modify', 'reject', "modifyID=$modify->id", $modify, 'button', 'arrow-left', '', 'iframe', true, '', $this->lang->outwarddelivery->reject);
                    common::printIcon('modify', 'review', "modifyID=$modify->id&version=$modify->version&reviewStage=$modify->reviewStage", $modify, 'button', 'glasses', '', 'iframe', true);
                    if ($modify->issubmit == 'save'){
                        $disabled = 'disabled';
                        if ($app->user->account == $modify->createdBy or $app->user->account == 'admin'){
                            $disabled = '';
                        }
                        echo '<a href="javascript:void(0)" '.$disabled.'  class="btn" onclick="$.zui.messager.danger(\''.$lang->modify->submitMsgTip.'\');" title="'.$lang->modify->submit.'" data-app="second"><i class="icon-modify-submit icon-play"></i> <span class="text">'.$lang->modify->submit.'</span></a>';
                    }else{
                        common::printIcon('modify', 'submit', "modifyID=$modify->id", $modify, 'button', 'play', '', 'iframe', true);
                    }
                    common::printIcon('modify', 'delete', "modifyID=$modify->id", $modify, 'button', 'trash', '', 'iframe', true);
                    /*common::printIcon('modify', 'close', "modifyID=$modify->id", $modify, 'button', 'off', '', 'iframe', true);*/
                    if($modify->status == 'jxsynfailed' || $modify->status == 'modifyreject'
                    ) common::printIcon('modify', 'push',  "modifyID=$modify->id", $modify, 'button', 'share', '', 'iframe', true);

                    if($modify->status == 'productsuccess' || $modify->status == 'waitImplement'
                    ) common::printIcon('modify', 'run', "modifyID=$modify->id", $modify, 'button', 'play', '', 'iframe', true);

                    if($modify->status == 'closing'
                    ) common::printIcon('modify', 'closeold', "modifyID=$modify->id", $modify, 'button', 'off', '', 'iframe', true);
                    if ($modify->abnormalCode == '' && in_array($modify->status,$lang->modify->reissueArrayNew) && !(!isset($abnormalList[$modify->id]) || $abnormalList[$modify->id] == '')){
                        common::printIcon('modify', 'reissue', "modifyID=$modify->id", $modify, 'button', 'fold-all', '', '', false);
                    }
                    common::printIcon('modify', 'close', "modifyID=$modify->id", $modify, 'button', 'cancel', '', 'iframe', true);
                    ?>

                </div>
            </div>
        </div>
        <!-- 右侧基础信息 -->
        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->baseinfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <!-- <tr>
                                <th class="w-120px"><?php echo $lang->outwarddelivery->outwardDeliveryDesc; ?></th>
                                <td><?php echo $modify->outwardDeliveryDesc; ?></td>
                            </tr> -->
                            <tr>
                                <th class="w-120px"><?php echo $lang->outwarddelivery->status; ?></th>
                                <td><?php echo $modify->closed == '1' ? $lang->modify->labelList['closed']:zget($lang->modify->statusList, $modify->status, ''); ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->outwarddelivery->dealUser; ?></th>
                                <?php
                                if ($modify->status == 'waitsubmitted') {
                                    $modify->dealUser = $modify->createdBy;
                                } elseif ($modify->status == 'withexternalapproval') {
                                    $modify->dealUser = 'guestjx';
                                }
                                ?>
                                <?php
                                $currentReviewers = explode(',', $modify->dealUser);
                                //所有审核人
                                $as = array();
                                foreach ($currentReviewers as $reviewer) {
                                    $as[] = zget($users, $reviewer);
                                }
                                $currentReviewers = implode(',', $as);
                                ?>
                                <td><?php echo trim($currentReviewers) . '<br/>'; ?></td>
                            </tr>

                            <?php
                            if ($modify->app) {
                                foreach ($modify->appsInfo as $appID => $appInfo) {
                                    $apps[] = $appInfo->name;
                                    $isPayments[] = zget($lang->application->isPaymentList, $appInfo->isPayment, '');
                                    $teams[] = zget($lang->application->teamList, $appInfo->team, '');
                                }
                            }
                            ?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->app; ?></th>
                                <td>
                                    <?php echo trim(implode('<br/>', array_unique($apps)), ','); ?>
                                </td>
                            </tr>
                            <!-- 材料是否评审 -->
                            <?php if(in_array(1,explode(',',$modify->app))):?>
                            <tr>
                                <th><?php echo $lang->modify->materialIsReview; ?></th>
                                <td>
                                    <?php echo zget($lang->modify->materialIsReviewList,$modify->materialIsReview,''); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->materialReviewUser; ?></th>
                                <td>
                                    <?php echo zget($users,$modify->materialReviewUser,''); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->materialReviewResult; ?></th>
                                <td>
                                    <?php echo $modify->materialReviewResult; ?>
                                </td>
                            </tr>
                            <?php endif;?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->isPayment; ?></th>
                                <td>
                                    <?php echo trim(implode('<br/>', array_unique($isPayments)), ','); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->team; ?></th>
                                <td>
                                    <?php echo trim(implode('<br/>', array_unique($teams)), ','); ?>
                                </td>
                            </tr>

                            <?php
                            $productName = array();
                            if ($modify->productId) {
                                foreach (explode(',', $modify->productId) as $productID) {
                                    if ($productID) {
                                        $productCode[] = $allProductCodes[$productID];
                                        $productName[] = $allProductNames[$productID];
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->productName; ?></th>
                                <td>
                                    <?php echo trim(implode('<br/>', array_unique($productName)), '<br/>'); ?>
                                </td>
                            </tr>
                            <!-- <?php
                            if ($modify->productLine) {
                                foreach (explode(',', $modify->productLine) as $line) {
                                    if ($line) {
                                        $productLine[] = $allLines[$line];
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->productLine; ?></th>
                                <td>
                                    <?php echo trim(implode('<br/>', array_unique($productLine)), ','); ?>
                                </td>
                            </tr> -->
                            <tr>
                                <th><?php echo $lang->outwarddelivery->productCode; ?></th>
                                <td>
                                    <?php echo str_replace("\n","<br/>", implode('<br/>', explode(',', $modify->productInfoCode))); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->implementationForm; ?></th>
                                <td><?php echo zget($lang->outwarddelivery->implementationFormList, $modify->implementationForm, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->projectPlanId; ?></th>
                                <td>
                                    <?php foreach (explode(',', $modify->projectPlanId) as $project) {
                                        if ($project) {
                                            echo zget($projects, $project, '') . '<br/>';
                                        }
                                    } ?>
                                </td>
                            </tr>
<!--                            <tr>-->
<!--                                <th>--><?php //echo $lang->outwarddelivery->CBPprojectId; ?><!--</th>-->
<!--                                <td>--><?php //if ($modify->CBPprojectId){
//                                    foreach ($modify->CBPInfo as $CBPInfo) {
//                                        echo $CBPInfo->code . '（' . $CBPInfo->name . '）'. '<br/>';
//                                    }
//                                }
//                                    ?>
<!--                                    </td>-->
<!--                            </tr>-->
                            <tr>
                                <th><?php echo $lang->modify->secondorderId;?></th>
                                <td>
                                    <?php foreach (explode(',', $modify->secondorderId) as $secondorderId): ?>
                                        <?php if ($secondorderId and $secondorder->$secondorderId['code']) {
                                            echo html::a($this->createLink('secondorder', 'view', 'id=' . $secondorderId, '', true), $secondorder->$secondorderId['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        } ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->problemId; ?></th>
                                <td>
                                    <?php foreach (explode(',', $modify->problemId) as $objectID): ?>
                                        <?php if ($objectID and $problem->$objectID['code']) {
                                            echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $problem->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        } ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->demandId;?></th>
                                <td>
                                    <?php foreach (explode(',', $modify->demandId) as $objectID): ?>
                                        <?php if ($objectID and $demand->$objectID['code']) {
                                            if($demand->$objectID['sourceDemand'] == 1){
                                                echo html::a($this->createLink('demand', 'view', 'id=' . $objectID, '', true), $demand->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                            }else{
                                                echo html::a($this->createLink('demandinside', 'view', 'id=' . $objectID, '', true), $demand->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                            }
                                        } ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->requirementId ?></th>
                                <td>
                                    <?php foreach (explode(',', $modify->requirementId) as $objectID): ?>
                                        <?php if ($objectID and $requirement->$objectID['code']) {
                                            if($requirement->$objectID['sourceRequirement'] == 1){
                                                echo html::a($this->createLink('requirement', 'view', 'id=' . $objectID, '', true), $requirement->$objectID['code'], '', "data-toggle='modal' data-type ='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                            }else{
                                                echo html::a($this->createLink('requirementinside', 'view', 'id=' . $objectID, '', true), $requirement->$objectID['code'], '', "data-toggle='modal' data-type ='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                            }
                                        } ?>
                                    <?php endforeach;
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->outwarddelivery->relatedTestingRequest ?></th>
                                <td>
                                    <?php if ($modify->testingRequestId and $testingrequest->code): ?>
                                        <?php echo html::a($this->createLink('testingrequest', 'view', 'id=' . $modify->testingRequestId, '', true), $testingrequest->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?>
                                        <br/>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- <tr>
                                <th><?php echo $lang->outwarddelivery->relatedProductEnroll ?></th>
                                <td>
                                    <?php if ($modify->productenrollId and $productenroll->code): ?>
                                        <?php echo html::a($this->createLink('productenroll', 'view', 'id=' . $modify->productenrollId, '', true), $productenroll->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?>
                                        <br/>
                                    <?php endif; ?>
                                </td>
                            </tr> -->

                            <tr>
                                <th><?php echo $lang->outwarddelivery->modifycncc . $lang->modifycncc->rejectTimes ?></th>
                                <td class='c-actions text-left'><?php echo $modify->returnTime; ?><?php common::printIcon('modify', 'editreturntimes', "modifyId=$modify->id", $modify, 'list', 'edit', '', 'iframe', true);?></td>
                            </tr>
                            <?php if (!empty($abnormalOrder['newOrder'])):?>
                                <tr>
                                    <th><?php echo $lang->modify->newOrder;?></th>
                                    <td class='c-actions text-left'>
                                        <?php foreach ($abnormalOrder['newOrder'] as $newOrder) {
                                            if (common::hasPriv('modify','view')){
                                                echo html::a($this->createLink('modify', 'view', 'id=' . $newOrder->id, '', false),$newOrder->code).'<br/>';
                                            }else{
                                                echo $newOrder->code.'<br/>';
                                            }
                                        }?>
                                    </td>
                                </tr>
                            <?php endif;?>
                            <?php if (common::hasPriv('modify','editabnormalorder')):?>
                                <tr>
                                    <th><?php echo $lang->modify->associaitonOrder;?></th>
                                    <td class='c-actions text-left'>
                                        <?php if (isset($abnormalOrder['nowOrder']->code) && $abnormalOrder['nowOrder']->code != ''){?>
                                            <?php if (common::hasPriv('modify','view')){
                                                echo html::a($this->createLink('modify', 'view', 'id=' . $abnormalOrder['nowOrder']->id, '', false),$abnormalOrder['nowOrder']->code);
                                            }else{
                                                echo $abnormalOrder['nowOrder']->code;
                                            }?>
                                        <?php }?>

                                        <?php common::printIcon('modify', 'editabnormalorder', "modifyId=$modify->id", $modify, 'list', 'edit', '', 'iframe', true);?>
                                    </td>
                                </tr>
                            <?php else:?>
                                <?php if (isset($abnormalOrder['nowOrder']->id) && $abnormalOrder['nowOrder']->id != ''):?>
                                    <tr>
                                        <th><?php echo $lang->modify->associaitonOrder;?></th>
                                        <td class='c-actions text-left'>
                                            <?php if (common::hasPriv('modify','view') && $abnormalOrder['nowOrder']->code != ''){
                                                echo html::a($this->createLink('modify', 'view', 'id=' . $abnormalOrder['nowOrder']->id, '', false),$abnormalOrder['nowOrder']->code);
                                            }else{
                                                echo $abnormalOrder['nowOrder']->code;
                                            }?>
                                        </td>
                                    </tr>
                                <?php endif;?>
                            <?php endif;?>

<!--                            <tr>-->
<!--                                <th>--><?php //echo $lang->outwarddelivery->dealUserContact ?><!--</th>-->
<!--                                <td>--><?php //echo $modify->contactTel; ?><!--</td>-->
<!--                            </tr>-->
                            <tr>
                                <th><?php echo $lang->outwarddelivery->createdDepts ?></th>
                                <td><?php echo zget($depts, $modify->createdDept, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->createdBy; ?></th>
                                <td><?php echo zget($users, $modify->createdBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->createdDate; ?></th>
                                <td><?php if (strtotime($modify->createdDate) > 0) echo $modify->createdDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->editedBy; ?></th>
                                <td><?php echo zget($users, $modify->editedBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->editedDate; ?></th>
                                <td><?php if (strtotime($modify->editedDate) > 0) echo $modify->editedDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->closedBy; ?></th>
                                <td><?php echo zget($users, $modify->closedBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->closedDate; ?></th>
                                <td><?php if (strtotime($modify->closedDate) > 0) echo $modify->closedDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->closedReason; ?></th>
                                <td><?php echo zget($lang->outwarddelivery->closedReasonList, $modify->closeReason); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->revertBy; ?></th>
                                <td><?php echo zget($users, $modify->revertBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->revertReason; ?></th>
                                <td>
                                <?php
                                if($modify->revertReason){
                                    foreach(json_decode($modify->revertReason) as $item){
                                        echo $item->RevertDate.' '.zget($lang->modify->revertReasonList, $item->RevertReason, '');
                                        echo '<br/>';
                                    }
                                }
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->revertReasonChild; ?></th>
                                <td>
                                    <?php
                                    if($modify->revertReason){
                                        $childTypeList = isset($this->lang->modify->childTypeList) ? $this->lang->modify->childTypeList['all'] : '[]';
                                        $childTypeList = json_decode($childTypeList, true);
                                        foreach(json_decode($modify->revertReason) as $item){
                                            if (isset($item->RevertReasonChild) && $item->RevertReasonChild != ''){
                                                echo $item->RevertDate.' '.$childTypeList[$item->RevertReason][$item->RevertReasonChild];
                                            }
                                            echo '<br/>';
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->isDiskDelivery?></th>
                                <td class='c-actions text-left'>
                                    <?php echo zget($lang->modify->isDiskDeliveryList,$modify->isDiskDelivery) ?>
                                    <?php if($isDiskDeliveryable) common::printIcon('modify', 'isDiskDelivery', "modifyId=$modify->id", $modify, 'list', 'edit', '', 'iframe', true);?>
                                </td>
                            </tr>
                            <?php if(isset($this->lang->modify->cancelLinkageUserList[$this->app->user->account]) || $this->app->user->account == 'admin'):?>
                                <tr>
                                    <th><?php echo $lang->modify->demandCancelLinkage;?></th>
                                    <td>
                                        <?php echo zget($this->lang->modify->cancelLinkageList,$modify->demandCancelLinkage,'');?>
                                        <?php echo html::a($this->createLink('modify', 'cancelLinkage', "modifyId=$modify->id&type=demandCancelLinkage", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modify->problemCancelLinkage;?></th>
                                    <td>
                                        <?php echo zget($this->lang->modify->cancelLinkageList,$modify->problemCancelLinkage,'');?>
                                        <?php echo html::a($this->createLink('modify', 'cancelLinkage', "modifyId=$modify->id&type=problemCancelLinkage", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                    </td>
                                </tr>
                            <?php endif;?>
                            <tr class="hidden">
                                <th><?php echo $lang->modify->isMakeAmends;?></th>
                                <td><?php echo zget($lang->modify->isMakeAmendsList,$modify->isMakeAmends,'')?></td>
                            </tr>
                            <?php if($modify->isMakeAmends == 'yes'):?>
                                <tr>
                                    <th><?php echo $lang->modify->actualDeliveryTime;?></th>
                                    <td><?php echo $modify->actualDeliveryTime;?></td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <?php if(true): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->modify->runResultMsg; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class="w-120px"><?php echo $lang->modify->implementers; ?></th>
                                <td><?php echo $modify->implementers; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->implementDepartment; ?></th>
                                <td><?php echo $modify->implementDepartment; ?></td>
                            </tr>
                            <?php if('modifycancel' != $modify->status): ?>
                            <tr>
                                <th><?php echo $lang->modifycncc->changeRemark; ?></th>
                                <td><?php echo $modify->changeRemark; ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th><?php echo $lang->modifycncc->actualBegin; ?></th>
                                <td>
                                    <?php
                                        if ($modify->realStartTime != '0000-00-00 00:00:00'){
                                            echo $modify->realStartTime;
                                        }

                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->actualEnd; ?></th>
                                <td>
                                    <?php
                                        if ($modify->realEndTime != '0000-00-00 00:00:00'){
                                            echo $modify->realEndTime;
                                        }
                                    ?>
                                </td>
                            </tr>
                            <!-- <tr>
                                <th><?php echo $lang->modify->returnjx; ?></th>
                                <td style="overflow: hidden;"><?php echo $modify->returnReason; ?></td>
                            </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->modify->outerReviewMsg; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class="w-120px"><?php echo $lang->modifycncc->changeStatus; ?></th>
                                <td><?php echo zget($lang->modify->changeStatusList, $modify->changeStatus); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->implementers; ?></th>
                                <td><?php echo $modify->implementers; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->implementDepartment; ?></th>
                                <td><?php echo $modify->implementDepartment; ?></td>
                            </tr>
                            <?php if('modifycancel' != $modify->status): ?>
                            <tr>
                                <th><?php echo $lang->modifycncc->changeRemark; ?></th>
                                <td><?php echo $modify->changeRemark; ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th><?php echo $lang->modifycncc->actualBegin; ?></th>
                                <td>
                                    <?php
                                    if ($modify->realStartTime != '0000-00-00 00:00:00'){
                                        echo $modify->realStartTime;
                                    }

                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modifycncc->actualEnd; ?></th>
                                <td>
                                    <?php
                                    if ($modify->realEndTime != '0000-00-00 00:00:00'){
                                        echo $modify->realEndTime;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->modify->returnjx; ?></th>
                                <td style="overflow: hidden;"><?php echo $modify->returnReason; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php foreach($modify->releases as $release):?>
              <div class="cell">
                <div class="detail">
                  <div class="detail-title"><?php echo $lang->modify->release;?></div>
                  <div class='detail-content'>
                    <table class='table table-data'>
                      <tbody>
                        <tr>
                          <th class='w-100px'><?php echo $lang->release->name;?></th>
                          <td><?php echo html::a($this->createLink('projectrelease', 'view', array('releaseID' => $release->id)), $release->name, '', 'data-app="project"');?></td>
                        </tr>

                        <tr>
                          <th class='w-100px'><?php echo $lang->release->path;?></th>
                          <td><?php if($release->path) echo $release->path. $lang->api->sftpList['info'];?></td>
                        </tr>

                        <tr>
                          <th><?php echo $lang->file->common;?></th>
                          <td>
                            <div class='detail'>
                              <div class='detail-content article-content'>
                                <?php echo $this->fetch('file', 'printFiles', array('files' => $release->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false));?>
                              </div>
                            </div>
                          </td>
                        </tr>

                      </tbody>
                    </table>
                  </div>
                </div>
              </div> 
            <?php endforeach;?>
            <?php if($modify->cancelStatus): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->modify->cancelMsg; ?></div>
                        <div class='detail-content'>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class="w-120px"><?php echo $lang->modify->cancelStatus; ?></th>
                                    <td><?php echo zget($lang->modify->statusList, $modify->cancelStatus, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modify->dealUser; ?></th>
                                    <?php
                                    $currentReviewers = explode(',', $modify->cancelReviewer);
                                    //所有审核人
                                    $as = array();
                                    foreach ($currentReviewers as $reviewer) {
                                        $as[] = zget($users, $reviewer);
                                    }
                                    $currentReviewers = implode(',', $as);
                                    ?>
                                    <td><?php echo trim($currentReviewers) . '<br/>'; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modify->cancelReason; ?></th>
                                    <td><?php echo $modify->cancelReason; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modify->canceledBy; ?></th>
                                    <td><?php echo zget($users, $modify->canceledBy); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modify->canceledDate; ?></th>
                                    <td><?php echo $modify->canceledDate; ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->statusTransition; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->outwarddelivery->nodeUser; ?></th>
                             <!--   <td class='text-right'><?php /*echo $lang->outwarddelivery->consumed; */?></td>-->
                                <td class='text-center'><?php echo $lang->outwarddelivery->before; ?></td>
                                <td class='text-center'><?php echo $lang->outwarddelivery->after; ?></td>
                            </tr>
                            <?php foreach ($modify->consumed as $c): ?>
                                <tr>
                                    <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                  <!--  <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour; */?></td>-->
                                    <?php
                                    echo "<td class='text-center'>" . $c->extra . zget($lang->modify->statusList, $c->before, '-') . "</td>";
                                    echo "<td class='text-center'>" . $c->extra . zget($lang->modify->statusList, $c->after, '-') . "</td>";
                                    ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        function push(code, type) {
            $.ajax(
                {
                    url: createLink('modify', 'push', 'type=' + type + '&code=' + code),
                    dataType: "html",
                    async: false,
                    // data: {productID: productID, roadMapID: roadMapID},
                    type: 'get',
                    success: function (data) {
                        // $("#roadMap" + roadMapID).html(data);
                        // $("#" + roadMapID).chosen();
                        alert('设置推送成功');
                    }
                })

        }

    </script>
<?php include '../../common/view/footer.html.php'; ?>