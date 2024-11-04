<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
    <style>
        .mo-ellipsis{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
    </style>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php if (!isonlybody()): ?>
                <?php $browseLink = $app->session->outwarddeliveryList != false ? $app->session->outwarddeliveryList : inlink('browse'); ?>
                <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
                <div class="divider"></div>
            <?php endif; ?>
            <div class="page-title">
                <span class="label label-id"><?php echo $outwarddelivery->code ?></span>
            </div>
        </div>
        <?php if (!isonlybody()): ?>
            <div class="btn-toolbar pull-right">
                <!--    --><?php //if(common::hasPriv('outwarddelivery', 'exportWord')) echo html::a($this->createLink('outwarddelivery', 'exportWord', "outwarddeliveryID=$outwarddelivery->id"), "<i class='icon-export'></i> {$lang->outwarddelivery->exportWord}", '', "class='btn btn-primary'");?>
            </div>
        <?php endif; ?>
    </div>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">
            <?php if ($outwarddelivery->isNewTestingRequest): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->testingrequest->title; ?></div>
                        <div class='detail-content'>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class='w-120px'><?php echo $lang->testingrequest->testSummary; ?></th>
                                    <td><?php echo $testingrequest->testSummary; ?></td>                                    
                                    <th class='w-120px'><?php echo $lang->testingrequest->testTarget; ?></th>
                                    <td><?php echo $testingrequest->testTarget; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-120px'><?php echo $lang->testingrequest->acceptanceTestType; ?></th>
                                    <td><?php echo zget($lang->testingrequest->acceptanceTestTypeList, $testingrequest->acceptanceTestType); ?></td>
                                    <th class='w-120px'><?php echo $lang->testingrequest->currentStage; ?></th>
                                    <td><?php echo $testingrequest->currentStage; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->testingrequest->os; ?></th>
                                    <td><?php echo $testingrequest->os; ?></td>
                                    <th><?php echo $lang->testingrequest->db; ?></th>
                                    <td><?php echo $testingrequest->db; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->testingrequest->isCentralizedTest; ?></th>
                                    <td><?php echo zget($lang->testingrequest->isCentralizedTestList, $testingrequest->isCentralizedTest); ?></td>
                                </tr>
                                </tbody>
                            </table>

                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class='w-120px'><?php echo $lang->testingrequest->content; ?></th>
                                    <td><?php echo html_entity_decode(str_replace("\n","<br/>",$testingrequest->content)); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->testingrequest->env; ?></th>
                                    <td><?php echo html_entity_decode(str_replace("\n","<br/>",$testingrequest->env)); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->testingrequest->testReport; ?></th>
                                    <td>
                                        <div class='detail'>
                                            <div class='detail-content article-content'>
                                                <?php echo $this->fetch('file', 'printFiles', array('files' => $testingrequest->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false)); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($outwarddelivery->isNewProductEnroll): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->productenroll->title; ?></div>
                        <div class='detail-content'>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class='w-150px'><?php echo $lang->productenroll->productenrollDesc; ?></th>
                                    <td><?php echo $productenroll->productenrollDesc; ?></td>
                                    <th class='w-150px'><?php echo $lang->productenroll->isPlan; ?></th>
                                    <td><?php echo zget($lang->productenroll->isPlanList, $productenroll->isPlan, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->planProductName; ?></th>
                                    <td><?php echo $productenroll->planProductName; ?></td>
                                    <th><?php echo $lang->productenroll->softwareProductLine; ?></th>
                                    <td><?php echo zget($allLines, $productenroll->productLine, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->dynacommCn; ?></th>
                                    <td><?php echo $productenroll->dynacommCn; ?></td>
                                    <th><?php echo $lang->productenroll->dynacommEn; ?></th>
                                    <td><?php echo $productenroll->dynacommEn; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->versionNum; ?></th>
                                    <td><?php echo $productenroll->versionNum; ?></td>
                                    <th><?php echo $lang->productenroll->lastVersionNum; ?></th>
                                    <td><?php echo $productenroll->lastVersionNum; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->checkDepartment; ?></th>
                                    <td><?php echo zget($lang->productenroll->checkDepartmentList, $productenroll->checkDepartment, ''); ?></td>
                                    <th><?php echo $lang->productenroll->result; ?></th>
                                    <td><?php echo zget($lang->productenroll->resultList, $productenroll->result, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->installationNode; ?></th>
                                    <td><?php echo zget($lang->productenroll->installNodeList, $productenroll->installationNode, ''); ?></td>
                                    <th><?php echo $lang->productenroll->softwareProductPatch; ?></th>
                                    <td><?php echo zget($lang->productenroll->softwareProductPatchList, $productenroll->softwareProductPatch, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->softwareCopyrightRegistration; ?></th>
                                    <td><?php echo zget($lang->productenroll->softwareCopyrightRegistrationList, $productenroll->softwareCopyrightRegistration, ''); ?></td>
                                    <th><?php echo $lang->productenroll->platform; ?></th>
                                    <td><?php echo zget($lang->productenroll->appList, $productenroll->platform, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->planDistributionTime; ?></th>
                                    <td><?php echo $productenroll->planDistributionTime; ?></td>
                                    <th><?php echo $lang->productenroll->planUpTime; ?></th>
                                    <td><?php echo $productenroll->planUpTime; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->reasonFromJinke; ?></th>
                                    <td><?php echo $productenroll->reasonFromJinke; ?></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class='w-150px'><?php echo $lang->productenroll->introductionToFunctionsAndUses; ?></th>
<!--                                    展示ascii码的换行-->
<!--                                    <td>--><?php //echo str_replace(chr(10),'</br>',$productenroll->introductionToFunctionsAndUses); ?><!--</td>-->
                                    <td><?php echo html_entity_decode(str_replace("\n","<br/>",$productenroll->introductionToFunctionsAndUses)); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->remark; ?></th>
                                    <td><?php echo html_entity_decode(str_replace("\n","<br/>",$productenroll->remark)); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->productenroll->mediaInfo; ?></div>
                        <?php if ($productenroll->mediaInfo): ?>
                            <div class="detail-content article-content">
                                <table class="table ops">
                                    <tr>
                                        <th class="w-200px"><?php echo $lang->productenroll->num; ?></th>
                                        <td><?php echo $lang->productenroll->media; ?></td>
                                        <td><?php echo $lang->productenroll->mediaBytes; ?></td>
                                    </tr>
                                    <?php $num = 1;
                                    foreach ($productenroll->mediaInfo as $MB): ?>
                                        <tr>
                                            <th><?php echo $num; ?></th>
                                            <td>
                                                <?php echo $MB['name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $MB['bytes'];
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
            <?php endif; ?>

            <?php if ($outwarddelivery->isNewModifycncc): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->outwarddelivery->modifycncc . '<br/>'; ?></div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->modifyParam; ?></div>
                            <div class='detail-content'>
                                <table class='table table-data'>
                                    <tbody>
                                    <tr>
                                        <th class='w-150px'><?php echo $lang->modifycncc->applyUsercontact; ?></th>
                                        <td><?php echo $modifycncc->applyUsercontact; ?></td>
                                        <th class='w-150px'><?php echo $lang->modifycncc->level; ?></th>
                                        <td><?php echo zget($lang->modifycncc->levelList, $modifycncc->level, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->productRegistrationCode; ?></th>
                                        <td><?php echo $modifycncc->productRegistrationCode; ?></td>
                                        <th><?php echo $lang->modifycncc->node; ?></th>
                                        <td>
                                            <?php
                                            $changeNodes = [];
                                            foreach (explode(',', $modifycncc->node) as $node) {
                                                if (empty($node)) continue;
                                                $changeNodes [] = zget($lang->modifycncc->nodeList, $node, '');
                                            }
                                            echo implode(',', $changeNodes);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->operationType; ?></th>
                                        <td><?php echo zget($lang->modifycncc->operationTypeList, $modifycncc->operationType, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->app; ?></th>
                                        <td>
                                            <?php
                                            if ($modifycncc->app) {
                                                foreach ($modifycncc->appsInfo as $appID => $appInfo) {
                                                    $partitionMsg = $appInfo->name;
                                                    if (!empty($appInfo->partition[0])) {
                                                        $partitionMsg .= ' (';
                                                        foreach ($appInfo->partition as $partition) {
                                                            $partitionMsg .= $partition . ' 分区,';
                                                        }
                                                        $partitionMsg = trim($partitionMsg, ', ') . ' )';
                                                    }
                                                    echo $partitionMsg . '<br/>';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->isPayment; ?></th>
                                        <td>
                                            <?php echo $outwarddelivery->isPayment;?>
                                        </td>
                                        <th><?php echo $lang->modifycncc->team; ?></th>
                                        <td>
                                            <?php echo $outwarddelivery->team;?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->mode; ?></th>
                                        <td><?php echo zget($lang->modifycncc->modeList, $modifycncc->mode, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->classify; ?></th>
                                        <td><?php echo zget($lang->modifycncc->classifyList, $modifycncc->classify, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->changeSource; ?></th>
                                        <td><?php echo zget($lang->modifycncc->changeSourceList, $modifycncc->changeSource, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->changeStage; ?></th>
                                        <td><?php echo zget($lang->modifycncc->changeStageList, $modifycncc->changeStage, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->implementModality; ?></th>
                                        <td><?php echo zget($lang->modifycncc->implementModalityList, $modifycncc->implementModality, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->type; ?></th>
                                        <td><?php echo zget($lang->modifycncc->typeList, $modifycncc->type, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->changeForm;?></th>
                                        <td><?php echo zget($lang->modifycncc->changeFormList, $modifycncc->changeForm, '');?></td>
                                        <th><?php echo $lang->modifycncc->automationTools;?></th>
                                        <td><?php echo zget($lang->modifycncc->automationToolsList, $modifycncc->automationTools, '');?></td>
                                    </tr>
                                    <?php if($modifycncc->type == 1):?>
                                    <tr>
                                        <th><?php echo $lang->outwarddelivery->urgentSource; ?></th>
                                        <td><?php echo zget($lang->modifycncc->urgentSourceList, $modifycncc->urgentSource, ''); ?></td>
                                        <th><?php echo $lang->outwarddelivery->urgentReason; ?></th>
                                        <td><?php echo $modifycncc->urgentReason; ?></td>
                                    </tr>
                                    <?php endif;?>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->isBusinessCooperate; ?></th>
                                        <td><?php echo zget($lang->modifycncc->isBusinessCooperateList, $modifycncc->isBusinessCooperate, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->isBusinessJudge; ?></th>
                                        <td><?php echo zget($lang->modifycncc->isBusinessJudgeList, $modifycncc->isBusinessJudge, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->isBusinessAffect; ?></th>
                                        <td><?php echo zget($lang->modifycncc->isBusinessAffectList, $modifycncc->isBusinessAffect, ''); ?></td>
                                        <th><?php echo $lang->modifycncc->property; ?></th>
                                        <td><?php echo zget($lang->modifycncc->propertyList, $modifycncc->property, ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->isReview; ?></th>
                                        <td>
                                            <?php
                                            if(isset($lang->modifycncc->isReviewList[$modify->isReview])){
                                                echo $lang->modifycncc->isReviewList[$modify->isReview];
                                            }else{
                                                echo '';
                                            }
                                            ?>
                                        </td>
                                        <?php if($modifycncc->isReview == 1): ?>
                                            <th><?php echo $lang->modifycncc->reviewReport; ?></th>
                                            <td><?php foreach (explode(',',$modifycncc->reviewReport) as $value):?>
                                                <?php echo html::a($this->createLink('review', 'view', 'id=' . $modifycncc->reviewReport, '', true), zget($reviewReportList, $value, ''), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ; ?>
                                                <br>
                                                <?php endforeach;?>
                                            </td>

                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->backspaceExpectedStartTime; ?></th>
                                        <td><?php if (strtotime($modifycncc->backspaceExpectedStartTime) > 0) echo $modifycncc->backspaceExpectedStartTime; ?></td>
                                        <th><?php echo $lang->modifycncc->backspaceExpectedEndTime; ?></th>
                                        <td><?php if (strtotime($modifycncc->backspaceExpectedEndTime) > 0) echo $modifycncc->backspaceExpectedEndTime; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->planBegin; ?></th>
                                        <td><?php if (strtotime($modifycncc->planBegin) > 0) echo $modifycncc->planBegin; ?></td>
                                        <th><?php echo $lang->modifycncc->planEnd; ?></th>
                                        <td><?php if (strtotime($modifycncc->planEnd) > 0) echo $modifycncc->planEnd; ?></td>
                                    </tr>

                                    <tr>
                                        <th><?php echo $lang->modifycncc->changeRelation;?></th>
                                        <td colspan="3">
                                            <?php if(!empty($modifycncc) || count($modifycncc->relation)>0):?>
                                                <?php foreach($modifycncc->relation as $key => $line):?>
                                                    <?php if(common::hasPriv('modifycncc','view')):?>
                                                        <div title="<?php echo $lang->modifycncc->relateTypeList[$line[0]].'-'.$modifycnccList[$line[1]]?>" class="mo-ellipsis"><a href="<?php echo $this->createLink('modifycncc', 'view', 'id=' . $line[1])?>" style="color:#0c60e1"><?php echo $lang->modifycncc->relateTypeList[$line[0]].'-'.$modifycnccList[$line[1]]?></a></div>
                                                    <?php else:?>
                                                        <div class="mo-ellipsis" <?php echo $lang->modifycncc->relateTypeList[$line[0]].'-'.$modifycnccList[$line[1]]?>><?php echo $lang->modifycncc->relateTypeList[$line[0]].'-'.$modifycnccList[$line[1]]?></div>

                                                    <?php endif?>
                                                <?php endforeach;?>
                                            <?php endif;?>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (in_array($modifycncc->implementModality,[1,3,6])):?>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->outwarddelivery->aadsReason; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->aadsReason) ? $modifycncc->aadsReason : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <?php endif;?>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->desc; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->desc) ? $modifycncc->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->target; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->target) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->target)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->reason; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->reason) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->reason)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->changeContentAndMethod; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->changeContentAndMethod) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->changeContentAndMethod)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->step; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->step) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->step)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->techniqueCheck; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->techniqueCheck) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->techniqueCheck)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->test; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->test) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->test)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->applyReasonOutWindow; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->applyReasonOutWindow) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->applyReasonOutWindow)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->keyGuaranteePeriodApplyReason; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->keyGuaranteePeriodApplyReason) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->keyGuaranteePeriodApplyReason)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->checkList; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->checkList) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->checkList)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->cooperateDepNameList; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->cooperateDepNameList) ? zget($lang->modifycncc->cooperateDepNameListList, $modifycncc->cooperateDepNameList, '') : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->businessCooperateContent; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->businessCooperateContent) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->businessCooperateContent)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->judgeDep; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->judgeDep) ? zget($lang->modifycncc->judgeDepList, $modifycncc->judgeDep, '') : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->judgePlan; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->judgePlan) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->judgePlan)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->controlTableFile; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->controlTableFile) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->controlTableFile)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->controlTableSteps; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->controlTableSteps) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->controlTableSteps)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->feasibilityAnalysis; ?></div>
                            <div class="detail-content article-content">
                                <?php if (!empty($modifycncc->feasibilityAnalysis)) {
                                    $feasibilityAnalysisInfo = array();
                                    $feasibilityAnalysises = explode(',', $modifycncc->feasibilityAnalysis);
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
                            <div class="detail-title-light"><?php echo $lang->modifycncc->risk; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->risk) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->risk)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->riskAnalysisEmergencyHandle; ?></div>
                            <div class="detail-content article-content">
                                <table class="table ops">
                                    <tr>
                                        <th class="w-200px"><?php echo $lang->modifycncc->id; ?></th>
                                        <td><?php echo $lang->modifycncc->riskAnalysis; ?></td>
                                        <td><?php echo $lang->modifycncc->emergencyBackWay; ?></td>
                                    </tr>
                                    <?php if ($modifycncc->riskAnalysisEmergencyHandle): ?>
                                        <?php $num = 1;
                                        foreach ($modifycncc->riskAnalysisEmergencyHandle as $ER): ?>
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
                            <div class="detail-title-light"><?php echo $lang->modifycncc->effect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->effect) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->effect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->businessFunctionAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->businessFunctionAffect) ? html_entity_decode(str_replace("\n","<br/>", $modifycncc->businessFunctionAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->backupDataCenterChangeSyncDesc; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->backupDataCenterChangeSyncDesc) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->backupDataCenterChangeSyncDesc)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->emergencyManageAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->emergencyManageAffect) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->emergencyManageAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->changeImpactAnalysis; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->changeImpactAnalysis) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->changeImpactAnalysis)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->businessAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->businessAffect) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->businessAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->benchmarkVerificationType; ?></div>
                            <div class="detail-content article-content">
                                <?php echo zget($lang->modifycncc->benchmarkVerificationTypeList, $modifycncc->benchmarkVerificationType, ''); ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->verificationResults; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($modifycncc->verificationResults) ? html_entity_decode(str_replace("\n","<br/>",$modifycncc->verificationResults)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title-light"><?php echo $lang->modifycncc->resultTitle; ?></div>
                            <div class='detail-content'>
                                <table class='table table-data'>
                                    <tbody>
                                    <tr>
                                        <th class='w-150px'><?php echo $lang->modifycncc->feedBackId; ?></th>
                                        <td><?php echo $modifycncc->feedBackId; ?></td>
                                        <th class='w-150px'><?php echo $lang->modifycncc->operationName; ?></th>
                                        <td><?php echo $modifycncc->operationName; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->feedBackOperationType; ?></th>
                                        <td><?php echo $modifycncc->feedBackOperationType; ?></td>
                                        <th><?php echo $lang->modifycncc->depOddName; ?></th>
                                        <td><?php echo $modifycncc->depOddName; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->actualBegin; ?></th>
                                        <td><?php echo $modifycncc->actualBegin; ?></td>
                                        <th><?php echo $lang->modifycncc->actualEnd; ?></th>
                                        <td><?php echo $modifycncc->actualEnd; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->supply; ?></th>
                                        <td><?php echo $modifycncc->supply; ?></td>
                                        <th><?php echo $lang->modifycncc->changeNum; ?></th>
                                        <td><?php echo $modifycncc->changeNum; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->operationStaff; ?></th>
                                        <td><?php echo $modifycncc->operationStaff; ?></td>
                                        <th><?php echo $lang->modifycncc->executionResults; ?></th>
                                        <td><?php echo $modifycncc->executionResults; ?></td>
                                    </tr>

                                    <tr>
                                        <th><?php echo $lang->modifycncc->problemDescription; ?></th>
                                        <td><?php echo html_entity_decode(str_replace("\n","<br/>",$modifycncc->problemDescription)); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $lang->modifycncc->resolveMethod; ?></th>
                                        <td><?php echo html_entity_decode(str_replace("\n","<br/>",$modifycncc->resolveMethod)); ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->ROR; ?></div>
                    <?php if ($outwarddelivery->ROR): ?>
                        <div class="detail-content article-content">
                            <table class="table ops">
                                <tr>
                                    <th class="w-200px"><?php echo $lang->productenroll->num; ?></th>
                                    <td class="w-200px"><?php echo $lang->outwarddelivery->RORDate; ?></td>
                                    <td><?php echo $lang->outwarddelivery->ROR; ?></td>
                                </tr>
                                <?php $num = 1;
                                foreach ($outwarddelivery->ROR as $ROR): ?>
                                <?php if(!empty($ROR)):?>
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
                                <?php endif;?>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php else: ?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php endif; ?>
                </div>
            </div>


            <div class="cell">
                <div class="detail">
                    <div class="clearfix">
                        <div class="detail-title pull-left"><?php echo $lang->outwarddelivery->reviewOpinion; ?></div>
                        <div class="detail-title pull-right">
                            <?php
                            if(common::hasPriv('outwarddelivery', 'showHistoryNodes')) echo html::a($this->createLink('outwarddelivery', 'showHistoryNodes', 'id='.$outwarddelivery->id, '', true), $lang->outwarddelivery->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
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
                                <td style="width:370px;"><?php echo $lang->outwarddelivery->reviewOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewTime; ?></td>
                            </tr>
                            <?php
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
                                        <?php if('save' == $outwarddelivery->issubmit && in_array($outwarddelivery->status, ['reject','reviewfailed'])): ?>
                                        <?php elseif ($outwarddelivery->status != 'waitsubmitted'): ?>
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
                            <?php endforeach;
                            $outwarddelivery->reviewFailReason = json_decode($outwarddelivery->reviewFailReason, true);
                            $testingFlag = $productEnrollFlag = $modifycnccFlag = 'reject' == $outwarddelivery->status && !empty($outwarddelivery->reviewFailReason[$outwarddelivery->version]);
                            $testingCount = $productEnrollCount = $modifycnccCount = 0;
                            foreach ($outwarddelivery->reviewFailReason[$outwarddelivery->version] as $value){
                                foreach ($value as $nodeKey => $item){
                                    if($nodeKey == 1){
                                        $testingCount++;
                                    }
                                    if($nodeKey == 3){
                                        $productEnrollCount++;
                                    }
                                    if($nodeKey == 5){
                                        $modifycnccCount++;
                                    }
                                }
                            }
                            ?>
                            <?php if(!empty($outwarddelivery->reviewFailReason)):
                            $count = 1;
                            $testFlag = in_array($testingrequest->status, array('withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'));
                            foreach ($outwarddelivery->reviewFailReason[$outwarddelivery->version] as $key => $reasons):
//                                if($testFlag && $count == $testingCount){
//                                    continue;
//                                }
                                foreach ($reasons as $k => $reason):
                                    $testingFlag = $testFlag && ($k == 0 || $k == 1);//单子状态为已退回并且退回记录不为空，不追加外部节点
                                    if($k == 0 || $k == 1) :
                                        $count++;
                                    ?>
                                    <tr>
                                        <th><?php echo $lang->outwarddelivery->outerReviewNodeList[$reason['reviewNode']]; ?></th>
                                        <td><?php echo zget($users, $reason['reviewUser'], ','); ?></td>
                                        <td><?php echo $reason['reviewResult'] ?></td>
                                        <td><?php echo $reason['reviewFailReason'] ?></td>
                                        <td><?php echo $reason['reviewPushDate'] ?></td>
                                    </tr>
                                <?php endif; endforeach; endforeach; endif; ?>
                            <?php if ($outwarddelivery->isNewTestingRequest && !$testingFlag && (!$testFlag || $count == 1)): ?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->outerReviewNodeList['0']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestjk', ','); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (in_array($testingrequest->status, array('waitqingzong', 'qingzongsynfailed'))) {
                                            echo zget($lang->testingrequest->statusList, $testingrequest->status, '');
                                        } elseif (in_array($testingrequest->status, array('withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'))) {
                                            echo $lang->outwarddelivery->synSuccess;
                                        } else {
                                            echo '';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($testingrequest->pushStatus and !empty($TRlog) and !empty($TRlog->response) and $TRlog->response->message and in_array($testingrequest->status, array('withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing', 'qingzongsynfailed'))) {
                                            echo $TRlog->response->message;
                                        } elseif ($testingrequest->status == 'qingzongsynfailed') {
                                            echo $lang->outwarddelivery->synFail;
                                        } else {
                                            $TRlog->requestDate = '';
                                        }
                                        ?>
                                    </td>
                                    <td><?php if(!empty($TRlog) and !empty($TRlog->response))echo $TRlog->requestDate; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->outerReviewNodeList['1']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestcn', ','); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (in_array($testingrequest->status, array('withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'))) {
                                            echo zget($lang->testingrequest->statusList, $testingrequest->status);
                                        } else {
                                            echo '';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if(in_array($testingrequest->status, array('withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'))) {
                                            if($testingrequest->status == 'testingrequestreject'){
                                                echo "打回人：".$testingrequest->returnPerson."<br>"."审批意见：".$testingrequest->returnCase;
                                            }else{
                                                echo $testingrequest->returnCase;
                                            }
                                        }else{
                                            echo '';
                                        }
                                        ?>
                                    </td>
                                    <td><?php if(strtotime($testingrequest->returnDate) > 0 and in_array($testingrequest->status, array('withexternalapproval', 'testingrequestreject', 'testingrequestpass', 'testing'))) echo $testingrequest->returnDate; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if(!empty($outwarddelivery->reviewFailReason)):
                                $count = 1;
                                $enrollFlag = in_array($productenroll->status, array('withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'));
                                foreach ($outwarddelivery->reviewFailReason[$outwarddelivery->version] as $key => $reasons):
//                                    if($enrollFlag && $count == $productEnrollCount){
//                                        continue;
//                                    }
                                    foreach ($reasons as $k => $reason):
                                        $productEnrollFlag = $productEnrollFlag && ($k == 2 || $k == 3);//单子状态为已退回并且退回记录不为空，不追加外部节点
                                        if($k == 2 || $k == 3) :
                                            $count++;
                                            ?>
                                            <tr>
                                                <th><?php echo $lang->outwarddelivery->outerReviewNodeList[$reason['reviewNode']]; ?></th>
                                                <td><?php echo zget($users, $reason['reviewUser'], ','); ?></td>
                                                <td><?php echo $reason['reviewResult'] ?></td>
                                                <td><?php echo $reason['reviewFailReason'] ?></td>
                                                <td><?php echo $reason['reviewPushDate'] ?></td>
                                            </tr>
                                        <?php endif; endforeach; endforeach; endif; ?>
                            <?php if ($outwarddelivery->isNewProductEnroll && !$productEnrollFlag && (!$enrollFlag || $count == 1)): ?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->outerReviewNodeList['2']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestjk', ','); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (in_array($productenroll->status, array('waitqingzong', 'qingzongsynfailed'))) {
                                            echo zget($lang->productenroll->statusList, $productenroll->status, '');
                                        } elseif (in_array($productenroll->status, array('withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'))) {
                                            echo $lang->outwarddelivery->synSuccess;
                                        } else {
                                            echo '';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if ($productenroll->pushStatus and !empty($PElog) and !empty($PElog->response) and $PElog->response->message and in_array($productenroll->status, array('withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass','qingzongsynfailed'))) {
                                            echo $PElog->response->message;
                                        } elseif ($productenroll->status == 'qingzongsynfailed') {
                                            echo $lang->outwarddelivery->synFail;
                                        } else {
                                            $PElog->requestDate = '';
                                        } ?>
                                    </td>
                                    <td><?php if(!empty($PElog) and !empty($PElog->response))echo $PElog->requestDate; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->outerReviewNodeList['3']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestcn', ','); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (in_array($productenroll->status, array('withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'))) {
                                            echo zget($lang->productenroll->statusList, $productenroll->status);
                                        } else {
                                            echo '';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if(in_array($productenroll->status, array('withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'))){
                                            if($productenroll->status == 'productenrollreject'){
                                                echo "打回人：".$productenroll->returnPerson."<br>"."审批意见：".$productenroll->returnCase;
                                            }else{
                                                echo $productenroll->returnCase;
                                            }
                                        } ?>
                                    </td>
                                    <td><?php if(strtotime($productenroll->returnDate) > 0 and in_array($productenroll->status, array('withexternalapproval', 'emispass', 'giteepass', 'productenrollreject', 'productenrollpass'))) echo $productenroll->returnDate; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if(!empty($outwarddelivery->reviewFailReason)):
                                $count = 1;
                                $flag  = in_array($modifycncc->status, ['modifyfail','modifysuccesspart',
                                    'modifysuccess','modifyreject','psdlreview','centrepmreview','giteepass','giteeback'])
                                    || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus));
                                foreach ($outwarddelivery->reviewFailReason[$outwarddelivery->version] as $key => $reasons):
//                                    if($flag && $count == $modifycnccCount){
//                                        continue;
//                                    }
                                    foreach ($reasons as $k => $reason):
                                        $modifycnccFlag = $modifycnccFlag && ($k == 4 || $k == 5);//单子状态为已退回并且退回记录不为空，不追加外部节点
                                        if($k == 4 || $k == 5) :
                                            $count++;
                                            ?>
                                            <tr>
                                                <th><?php echo $lang->outwarddelivery->outerReviewNodeList[$reason['reviewNode']]; ?></th>
                                                <td><?php echo zget($users, $reason['reviewUser'], ','); ?></td>
                                                <td><?php echo $reason['reviewResult'] ?></td>
                                                <td><?php echo $reason['reviewFailReason'] ?></td>
                                                <td><?php echo $reason['reviewPushDate'] ?></td>
                                            </tr>
                            <?php endif; endforeach; endforeach; endif; ?>
                            <?php if ($outwarddelivery->isNewModifycncc && !$modifycnccFlag && (!$flag || $count == 1)): ?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->outerReviewNodeList['4']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestjk', ','); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (in_array($modifycncc->status, array('waitqingzong', 'qingzongsynfailed'))) {
                                            echo zget($lang->modifycncc->statusList, $modifycncc->status, '');
                                        } elseif (in_array($modifycncc->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'psdlreview', 'centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus))) {
                                            echo $lang->outwarddelivery->synSuccess;
                                        } else {
                                            echo '';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if ($modifycncc->pushStatus and !empty($MClog) and !empty($MClog->response) and $MClog->response->message and (in_array($modifycncc->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'psdlreview', 'centrepmreview','giteepass','giteeback','qingzongsynfailed')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus)))) {
                                            echo $MClog->response->message;
                                        } elseif ($modifycncc->status == 'qingzongsynfailed') {
                                            echo $lang->outwarddelivery->synFail;
                                        } else {
                                            $MClog->requestDate = '';
                                        } ?>
                                    </td>
                                    <td><?php if(!empty($MClog) and !empty($MClog->response))echo $MClog->requestDate; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->outerReviewNodeList['5']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestcn', ','); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (in_array($modifycncc->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'psdlreview', 'centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus))) {
                                            echo zget($lang->modifycncc->statusList, $modifycncc->status);
                                            if($modifycncc->status == 'modifyreject'){
                                                echo "（金信退回总中心，仅供参考）";
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (in_array($modifycncc->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject','psdlreview', 'centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus))) {
                                            if($modifycncc->status == 'giteeback'){
                                                echo "打回人：".$modifycncc->approverName."<br>审批意见：".$modifycncc->reasonCNCC;
                                            }else{
                                                echo $modifycncc->reasonCNCC;
                                            }
                                        } else {
                                            echo '';
                                        }
                                        ?>
                                    </td>
                                    <td><?php if (strtotime($modifycncc->feedbackDate) > 0 and (in_array($modifycncc->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject','psdlreview', 'centrepmreview','giteepass','giteeback')) || ($modifycncc->status == 'modifycancel' and !empty($modifycncc->changeStatus)))) echo $modifycncc->feedbackDate; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php else: ?>
                                <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>

            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php
                        if (!isonlybody()){
                            common::printBack($browseLink);
                        }
                    ?>
                    <div class='divider'></div>
                    <?php
                    common::printIcon('outwarddelivery', 'edit', "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'button');
                    common::printIcon('outwarddelivery', 'reject', "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'button', 'arrow-left', '', 'iframe', true, '', $this->lang->outwarddelivery->reject);
                    common::printIcon('outwarddelivery', 'review', "outwarddeliveryID=$outwarddelivery->id&version=$outwarddelivery->version&reviewStage=$outwarddelivery->reviewStage", $outwarddelivery, 'button', 'glasses', '', 'iframe', true);
                    if ($outwarddelivery->issubmit == 'save'){
                        $disabled = 'disabled';
                        if ($app->user->account == $outwarddelivery->createdBy or $app->user->account == 'admin'){
                            $disabled = '';
                        }
                        echo '<a href="javascript:void(0)" '.$disabled.'  class="btn" onclick="$.zui.messager.danger(\''.$lang->outwarddelivery->submitMsgTip.'\');" title="'.$lang->outwarddelivery->submit.'" data-app="second"><i class="icon-modify-submit icon-play"></i> <span class="text">'.$lang->outwarddelivery->submit.'</span></a>';
                    }else{
                        common::printIcon('outwarddelivery', 'submit', "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'button', 'play', '', 'iframe', true);
                    }
                    common::printIcon('outwarddelivery', 'delete', "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'button', 'trash', '', 'iframe', true);
                    common::printIcon('outwarddelivery', 'close', "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'button', 'cancel', '', 'iframe', true);
                    if((!empty($testingrequest) && ($testingrequest->pushStatus == -1 || $testingrequest->status == 'testingrequestreject') && $testingrequest->status != 'cancel') || (!empty($productenroll) && ($productenroll->pushStatus == -1 || $productenroll->status == 'giteeback' || $productenroll->status == 'productenrollreject' || $productenroll->status == 'testingrequestreject') && $productenroll->status != 'cancel')
                        || (!empty($modifycncc) && ($modifycncc->pushStatus == -1 || $modifycncc->status == 'giteeback') && $modifycncc->status != 'cancel')) {
                        common::printIcon('outwarddelivery', 'push',  "outwarddeliveryID=$outwarddelivery->id", $outwarddelivery, 'button', 'share', '', 'iframe', true);
                    }
                    if ($outwarddelivery->abnormalCode == '' && in_array($outwarddelivery->status,$lang->outwarddelivery->reissueArrayNew) && !(!isset($abnormalList[$modifycncc->id]) || $abnormalList[$modifycncc->id] == '') && $outwarddelivery->isNewModifycncc == 1){
                        common::printIcon('outwarddelivery', 'reissue', "modifyID=$outwarddelivery->id", $outwarddelivery, 'button', 'fold-all', '', '', false);
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->baseinfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class="w-120px"><?php echo $lang->outwarddelivery->outwardDeliveryDesc; ?></th>
                                <td><?php echo $outwarddelivery->outwardDeliveryDesc; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->status; ?></th>
                                <td><?php echo $outwarddelivery->closed == '1' ? $lang->outwarddelivery->labelList['closed']:zget($lang->outwarddelivery->statusList, $outwarddelivery->status, ''); ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->outwarddelivery->dealUser; ?></th>
                                <?php
                                if ($outwarddelivery->status == 'waitsubmitted' or $outwarddelivery->status == 'reject') {
                                    $outwarddelivery->dealUser = $outwarddelivery->createdBy;
                                } elseif ($outwarddelivery->status == 'withexternalapproval') {
                                    $outwarddelivery->dealUser = 'guestcn';
                                }
                                ?>
                                <?php
                                $currentReviewers = explode(',', $outwarddelivery->dealUser);
                                //所有审核人
                                $as = array();
                                foreach ($currentReviewers as $reviewer) {
                                    $as[] = zget($users, $reviewer);
                                }
                                $currentReviewers = implode(',', $as);
                                if($outwarddelivery->closed=='1'){
                                    $currentReviewers = '';
                                }
                                ?>
                                <td><?php echo trim($currentReviewers) . '<br/>'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->currentReview; ?></th>

                                <td><?php echo zget($lang->outwarddelivery->currentReviewList, $outwarddelivery->currentReview, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->deliveryType ?></th>
                                <td>
                                    <?php
                                    $deliveryType = array();
                                    if ($outwarddelivery->isNewTestingRequest) {
                                        $deliveryType[] = $lang->outwarddelivery->testingrequest;
                                    }
                                    if ($outwarddelivery->isNewProductEnroll) {
                                        $deliveryType[] = $lang->outwarddelivery->productenroll;
                                    }
                                    if ($outwarddelivery->isNewModifycncc) {
                                        $deliveryType[] = $lang->outwarddelivery->modifycncc;
                                    }
                                    echo implode('，', $deliveryType);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->childrenCode ?></th>
                                <td>
                                    <?php
                                    $childrenCode = array();
                                    if ($outwarddelivery->isNewTestingRequest) {
                                        $childrenCode[] = $lang->outwarddelivery->testingrequest . '-' . html::a($this->createLink('testingrequest', 'view', 'id=' . $outwarddelivery->testingRequestId, '', true), $testingrequest->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");
                                    }
                                    if ($outwarddelivery->isNewProductEnroll) {
                                        $childrenCode[] = $lang->outwarddelivery->productenroll . '-' . html::a($this->createLink('productenroll', 'view', 'id=' . $outwarddelivery->productEnrollId, '', true), $productenroll->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");
                                    }
                                    if ($outwarddelivery->isNewModifycncc) {
                                        $childrenCode[] = $lang->outwarddelivery->modifycncc . '-' . html::a($this->createLink('modifycncc', 'view', 'id=' . $outwarddelivery->modifycnccId, '', true), $modifycncc->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");
                                    }
                                    echo implode('<br/>', $childrenCode);
                                    ?>
                                </td>
                            </tr>
                            <?php if (!empty($abnormalOrder['newOrder'])):?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->newOrder;?></th>
                                    <td class='c-actions text-left'>
                                        <?php foreach ($abnormalOrder['newOrder'] as $newOrder) {
                                            if (common::hasPriv('modifycncc','view')){
                                                echo html::a($this->createLink('modifycncc', 'view', 'id=' . $newOrder->id, '', false),$newOrder->code).'<br/>';
                                            }else{
                                                echo $newOrder->code.'<br/>';
                                            }
                                        }?>

                                    </td>
                                </tr>
                            <?php endif;?>
                            <?php
                            if($outwarddelivery->isNewModifycncc == 1){?>
                                <?php if (common::hasPriv('outwarddelivery','editabnormalorder')):?>
                                    <tr>
                                        <th><?php echo $lang->outwarddelivery->associaitonOrder;?></th>
                                        <td class='c-actions text-left'>
                                            <?php if(isset($abnormalOrder['nowOrder']->code)):?>
                                                <?php if (common::hasPriv('modifycncc','view') && $abnormalOrder['nowOrder']->code != ''){
                                                    echo html::a($this->createLink('modifycncc', 'view', 'id=' . $abnormalOrder['nowOrder']->id, '', true),$abnormalOrder['nowOrder']->code,'', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");
                                                }else{
                                                    echo $abnormalOrder['nowOrder']->code;
                                                }?>
                                            <?php endif;?>
                                            <?php
                                            common::printIcon('outwarddelivery', 'editabnormalorder', "modifyId=$outwarddelivery->id", $outwarddelivery, 'list', 'edit', '', 'iframe', true);
                                            ?>
                                        </td>
                                    </tr>
                                <?php else:?>
                                    <?php if(isset($abnormalOrder['nowOrder']->code)):?>
                                        <tr>
                                        <th><?php echo $lang->outwarddelivery->associaitonOrder;?></th>
                                        <td class='c-actions text-left'>
                                                <?php if (common::hasPriv('modifycncc','view') && $abnormalOrder['nowOrder']->code != ''){
                                                    echo html::a($this->createLink('modifycncc', 'view', 'id=' . $abnormalOrder['nowOrder']->id, '', true),$abnormalOrder['nowOrder']->code,'', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");
                                                }else{
                                                    echo $abnormalOrder['nowOrder']->code;
                                                }?>
                                        </td>
                                    </tr>
                                    <?php endif;?>
                                <?php endif;?>

                            <?php }?>
                            <?php
                            if ($outwarddelivery->app) {
                                foreach ($outwarddelivery->appsInfo as $appID => $appInfo) {
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
                            if ($outwarddelivery->productId) {
                                foreach (explode(',', $outwarddelivery->productId) as $productID) {
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
                            <?php
                            if ($outwarddelivery->productLine) {
                                foreach (explode(',', $outwarddelivery->productLine) as $line) {
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
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->productCode; ?></th>
                                <td>
                                    <?php echo str_replace("\n","<br/>",  implode('<br/>', array_unique(explode(',',$outwarddelivery->productInfoCode)))); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->implementationForm; ?></th>
                                <td><?php echo zget($lang->outwarddelivery->implementationFormList, $outwarddelivery->implementationForm, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->projectPlanId; ?></th>
                                <td>
                                    <?php foreach (explode(',', $outwarddelivery->projectPlanId) as $project) {
                                        if ($project) {
                                            echo zget($projects, $project, '') . '<br/>';
                                        }
                                    } ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->CBPprojectId; ?></th>
                                <?php if ($outwarddelivery->CBPprojectId): ?>
                                    <td><?php echo $outwarddelivery->CBPInfo[0]->code . '（' . $outwarddelivery->CBPInfo[0]->name . '）'; ?></td>
                                <?php endif; ?>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->secondorderId; ?></th>
                                <td>
                                    <?php foreach (explode(',', $outwarddelivery->secondorderId) as $objectID): ?>
                                        <?php if ($objectID and $secondorder->$objectID['code']) {
                                            echo html::a($this->createLink('secondorder', 'view', 'id=' . $objectID, '', true), $secondorder->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        } ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->problemId; ?></th>
                                <td>
                                    <?php foreach (explode(',', $outwarddelivery->problemId) as $objectID): ?>
                                        <?php if ($objectID and $problem->$objectID['code']) {
                                            echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $problem->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        } ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->demandId; ?></th>
                                <td>
                                    <?php foreach (explode(',', $outwarddelivery->demandId) as $objectID): ?>
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
                                    <?php foreach (explode(',', $outwarddelivery->requirementId) as $objectID): ?>
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
                                <th><?php echo $lang->outwarddelivery->relatedOutwardDelivery ?></th>
                                <td>
                                    <?php
                                    if(!empty($relations) && !empty($relations['outwardDelivery'])){
                                        foreach ($relations['outwardDelivery'] as $object) {
                                            $outwarddeliveryMsg = html::a($this->createLink('outwarddelivery', 'view', 'id=' . $object['relationID'], '', true), $object['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '(';

                                            if ($object['children']['testingRequestId']) {
                                                $outwarddeliveryMsg .= html::a($this->createLink('testingrequest', 'view', 'id=' . $object['children']['testingRequestId'], '', true), $object['children']['testingRequestCode'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . ',';
                                            }
                                            if ($object['children']['productEnrollId']) {
                                                $outwarddeliveryMsg .= html::a($this->createLink('productenroll', 'view', 'id=' . $object['children']['productEnrollId'], '', true), $object['children']['productEnrollCode'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . ',';
                                            }
                                            if ($object['children']['modifycnccId']) {
                                                $outwarddeliveryMsg .= html::a($this->createLink('modifycncc', 'view', 'id=' . $object['children']['modifycnccId'], '', true), $object['children']['modifycnccCode'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . ',';
                                            }
                                            $outwarddeliveryMsg = trim($outwarddeliveryMsg, ',') . ')<br/>';
                                            echo $outwarddeliveryMsg;
                                        }
                                    }
                                     ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->relatedTestingRequest ?></th>
                                <td>
                                    <?php if ($outwarddelivery->isNewTestingRequest == 0 and $outwarddelivery->testingRequestId and $testingrequest->code): ?>
                                        <?php echo html::a($this->createLink('testingrequest', 'view', 'id=' . $outwarddelivery->testingRequestId, '', true), $testingrequest->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?>
                                        <br/>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->outwarddelivery->relatedProductEnroll ?></th>
                                <td>
                                    <?php if ($outwarddelivery->isNewProductEnroll == 0 and $outwarddelivery->productEnrollId and $productenroll->code): ?>
                                        <?php echo html::a($this->createLink('productenroll', 'view', 'id=' . $outwarddelivery->productEnrollId, '', true), $productenroll->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?>
                                        <br/>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->relatedModifycncc ?></th>
                                <td>
                                    <?php if ($outwarddelivery->isNewModifycncc == 0 and $outwarddelivery->modifycnccId and $modifycncc->code): ?>
                                        <?php echo html::a($this->createLink('modifycncc', 'view', 'id=' . $outwarddelivery->modifycnccId, '', true), $modifycncc->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?>
                                        <br/>
                                    <?php endif; ?>
                                </td>
                            </tr>


                            <?php if ($outwarddelivery->isNewTestingRequest): ?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->testingrequest . $lang->testingrequest->rejectTimes ?></th>
                                    <td class='c-actions text-left'><?php echo $testingrequest->returnTimes; ?><?php common::printIcon('testingrequest', 'editreturntimes', "outwardDeliveryId=$outwarddelivery->id", $outwarddelivery, 'list', 'edit', '', 'iframe', true);?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($outwarddelivery->isNewProductEnroll): ?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->productenroll . $lang->productenroll->rejectTimes ?></th>
                                    <td class='c-actions text-left'><?php echo $productenroll->returnTimes; ?><?php common::printIcon('productenroll', 'editreturntimes', "outwardDeliveryId=$outwarddelivery->id", $outwarddelivery, 'list', 'edit', '', 'iframe', true);?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($outwarddelivery->isNewModifycncc): ?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->modifycncc . $lang->modifycncc->rejectTimes ?></th>
                                    <td class='c-actions text-left'><?php echo $modifycncc->returnTimes; ?><?php common::printIcon('modifycncc', 'editreturntimes', "outwardDeliveryId=$outwarddelivery->id", $outwarddelivery, 'list', 'edit', '', 'iframe', true);?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->dealUserContact ?></th>
                                <td><?php echo $outwarddelivery->contactTel; ?></td>
                            </tr>
                            <?php if ($outwarddelivery->isNewModifycncc): ?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->manufacturer ?></th>
                                    <td><?php echo $outwarddelivery->manufacturer; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->manufacturerConnect ?></th>
                                    <td><?php echo $outwarddelivery->manufacturerConnect; ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->createdDepts ?></th>
                                <td><?php echo zget($depts, $outwarddelivery->createdDept, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->createdBy; ?></th>
                                <td><?php echo zget($users, $outwarddelivery->createdBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->createdDate; ?></th>
                                <td><?php if (strtotime($outwarddelivery->createdDate) > 0) echo $outwarddelivery->createdDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->editedBy; ?></th>
                                <td><?php echo zget($users, $outwarddelivery->editedBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->editedDate; ?></th>
                                <td><?php if (strtotime($outwarddelivery->editedDate) > 0) echo $outwarddelivery->editedDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->closedBy; ?></th>
                                <td><?php echo zget($users, $outwarddelivery->closedBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->closedDate; ?></th>
                                <td><?php if (strtotime($outwarddelivery->closedDate) > 0) echo $outwarddelivery->closedDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->closedReason; ?></th>
                                <td><?php echo zget($lang->outwarddelivery->closedReasonList, $outwarddelivery->closedReason); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->revertBy; ?></th>
                                <td><?php echo zget($users, $outwarddelivery->revertBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->revertReason; ?></th>
                                <td>
                                <?php
                                if($outwarddelivery->revertReason){
                                    $childTypeList = isset($this->lang->outwarddelivery->childTypeList) ? $this->lang->outwarddelivery->childTypeList['all'] : '[]';
                                    $childTypeList = json_decode($childTypeList, true);
                                    foreach(json_decode($outwarddelivery->revertReason) as $item){
                                        echo $item->RevertDate.' '.zget($lang->outwarddelivery->revertReasonList, $item->RevertReason, '');
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
                                    if($outwarddelivery->revertReason){
                                        $childTypeList = isset($this->lang->outwarddelivery->childTypeList) ? $this->lang->outwarddelivery->childTypeList['all'] : '[]';
                                        $childTypeList = json_decode($childTypeList, true);
                                        foreach(json_decode($outwarddelivery->revertReason) as $item){
                                            if (isset($item->RevertReasonChild) && $item->RevertReasonChild != ''){
                                                echo $item->RevertDate.' '.$childTypeList[$item->RevertReason][$item->RevertReasonChild];
                                            }
                                            echo '<br/>';
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php if(
                                (isset($this->lang->outwarddelivery->cancelLinkageUserList[$this->app->user->account]) || $this->app->user->account == 'admin')
                                && $outwarddelivery->isNewModifycncc == 1
                            ):?>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->demandCancelLinkage;?></th>
                                    <td>
                                        <?php echo zget($this->lang->outwarddelivery->cancelLinkageList,$outwarddelivery->demandCancelLinkage,'');?>
                                        <?php echo html::a($this->createLink('outwarddelivery', 'cancelLinkage', "outwarddeliveryId=$outwarddelivery->id&type=demandCancelLinkage", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->outwarddelivery->problemCancelLinkage;?></th>
                                    <td>
                                        <?php echo zget($this->lang->outwarddelivery->cancelLinkageList,$outwarddelivery->problemCancelLinkage,'');?>
                                        <?php echo html::a($this->createLink('outwarddelivery', 'cancelLinkage', "outwarddeliveryId=$outwarddelivery->id&type=problemCancelLinkage", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                    </td>
                                </tr>
                            <?php endif;?>
                            <tr class="hidden">
                                <th><?php echo $lang->modify->isMakeAmends;?></th>
                                <td><?php echo zget($lang->modify->isMakeAmendsList,$modifycncc->isMakeAmends,'')?></td>
                            </tr>
                            <?php if($modifycncc->isMakeAmends == 'yes'):?>
                                <tr>
                                    <th><?php echo $lang->modify->actualDeliveryTime;?></th>
                                    <td><?php echo $modifycncc->actualDeliveryTime;?></td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if ($outwarddelivery->isNewTestingRequest): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->outwarddelivery->testingrequest . '-' . $lang->outwarddelivery->outerReview; ?></div>
                        <div class='detail-content'>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class='w-120px'><?php echo $lang->testingrequest->giteeId; ?></th>
                                    <td><?php echo $testingrequest->giteeId; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-120px'><?php echo $lang->testingrequest->outerReviewResult; ?></th>
                                    <td><?php echo zget($lang->testingrequest->cardStatusList, $testingrequest->cardStatus, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->testingrequest->rejectBy; ?></th>
                                    <td><?php echo $testingrequest->returnPerson; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->testingrequest->rejectReason; ?></th>
                                    <td><?php echo $testingrequest->returnCase; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->testingrequest->rejectDate; ?></th>
                                    <td><?php if (strtotime($testingrequest->returnDate) > 0) echo $testingrequest->returnDate; ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($outwarddelivery->isNewProductEnroll): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->outwarddelivery->productenroll . '-' . $lang->outwarddelivery->outerReview; ?></div>
                        <div class='detail-content'>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class="w-120px"><?php echo $lang->productenroll->giteeId; ?></th>
                                    <td><?php echo $productenroll->giteeId; ?></td>
                                </tr>
                                <tr>
                                    <th class='w-120px'><?php echo $lang->productenroll->emisRegisterNumber; ?></th>
                                    <td><?php echo $productenroll->emisRegisterNumber; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->cardStatus; ?></th>
                                    <td><?php echo zget($lang->productenroll->cardStatusList, $productenroll->cardStatus, ''); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->rejectBy; ?></th>
                                    <td><?php echo $productenroll->returnPerson; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->rejectReason; ?></th>
                                    <td><?php echo $productenroll->returnCase; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->productenroll->rejectDate; ?></th>
                                    <td><?php if (strtotime($productenroll->returnDate) > 0) echo $productenroll->returnDate; ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($outwarddelivery->isNewModifycncc): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->outwarddelivery->modifycncc . '-' . $lang->outwarddelivery->outerReview; ?></div>
                        <div class='detail-content'>
                            <table class='table table-data'>
                                <tbody>
                                <tr>
                                    <th class="w-120px"><?php echo $lang->modifycncc->outsideNum; ?></th>
                                    <td><?php echo $modifycncc->giteeId; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-120px"><?php echo $lang->modifycncc->changeStatus; ?></th>
                                    <td><?php echo zget($lang->modifycncc->changeStatusList, $modifycncc->changeStatus, ''); ?></td>
                                </tr>
                                <?php if('modifycancel' != $modifycncc->status): ?>
                                <tr>
                                    <th><?php echo $lang->modifycncc->changeRemark; ?></th>
                                    <td><?php echo $modifycncc->changeRemark; ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th><?php echo $lang->modifycncc->actualBegin; ?></th>
                                    <td><?php echo $modifycncc->actualBegin; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modifycncc->actualEnd; ?></th>
                                    <td><?php echo $modifycncc->actualEnd; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modifycncc->reasonCNCC; ?></th>
                                    <td><?php echo $modifycncc->reasonCNCC; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->modifycncc->verificationReturnNum; ?></th>
                                    <td><?php echo $modifycncc->verificationReturnNum; ?></td>
                                </tr>
                                <?php if(!empty($modifycncc->returnLogArray)):?>
                                    <tr>
                                        <table class="table ops">
                                            <tr>
                                                <th class="w-120px"><?php echo $lang->modifycncc->returnTime; ?></th>
                                                <th class="w-120px"><?php echo $lang->modifycncc->returnNode; ?></th>
                                                <th class="w-120px"><?php echo $lang->modifycncc->returnPerson; ?></th>
                                                <th class="w-120px"><?php echo $lang->modifycncc->reasonCNCC; ?></th>
                                            </tr>
                                            <?php foreach ($modifycncc->returnLogArray as $key=>$value):?>
                                                <tr>
                                                    <td><?php echo $value->date;?></td>
                                                    <td><?php echo $value->node;?></td>
                                                    <td><?php echo $value->dealUser;?></td>
                                                    <td><?php echo $value->reason;?></td>
                                                </tr>
                                            <?php endforeach;?>
                                        </table>
                                    </tr>
                                <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($outwarddelivery->release): ?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->outwarddelivery->release; ?></div>
                        <div class='detail-content'>
                            <?php include '../../release/view/block.html.php'; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->leaveDefect; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-150px'><?php echo $lang->defect->idAB; ?></th>
                                <td class='text-right'><?php echo $lang->outwarddelivery->dealSuggest; ?></td>
                                <td class='text-right'><?php echo $lang->outwarddelivery->extFeedback; ?></td>
                            </tr>
                            <?php foreach ($leaveDefects as $leaveDefect): ?>
                                <tr>
                                    <th class='w-150px'><?php echo html::a($this->createLink('defect', 'view', 'id=' . $leaveDefect->id, '', true), $leaveDefect->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></th>
                                    <td class='text-right'><?php echo zget($lang->defect->dealSuggestList, $leaveDefect->dealSuggest); ?></td>
                                    <td class='text-right'><?php echo $leaveDefect->changeStatus; ?></td>
                                </tr>
                            <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->fixDefect; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-150px'><?php echo $lang->defect->idAB; ?></th>
                                <td class='text-right'><?php echo $lang->outwarddelivery->dealSuggest; ?></td>
                                <td class='text-right'><?php echo $lang->outwarddelivery->extFeedback; ?></td>
                            </tr>
                            <?php foreach ($fixDefects as $fixDefect): ?>
                                <tr>
                                    <th class='w-150px'><?php echo html::a($this->createLink('defect', 'view', 'id=' . $fixDefect->id, '', true), $fixDefect->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></th>
                                    <td class='text-right'><?php echo zget($lang->defect->dealSuggestList, $fixDefect->dealSuggest); ?></td>
                                    <td class='text-right'><?php echo $fixDefect->changeStatus; ?></td>
                                </tr>
                            <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->uatDefect; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-150px'><?php echo $lang->defect->idAB; ?></th>
                                <td class='text-right'><?php echo $lang->outwarddelivery->dealSuggest; ?></td>
                                <td class='text-right'><?php echo $lang->outwarddelivery->extFeedback; ?></td>
                            </tr>
                            <?php foreach ($uatDefects as $uatDefect): ?>
                                <tr>
                                    <th class='w-150px'><?php echo html::a($this->createLink('defect', 'view', 'id=' . $uatDefect->id, '', true), $uatDefect->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></th>
                                    <td class='text-right'><?php echo zget($lang->defect->dealSuggestList, $uatDefect->dealSuggest); ?></td>
                                    <td class='text-right'><?php echo $uatDefect->changeStatus; ?></td>
                                </tr>
                            <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->outwarddelivery->statusTransition; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->outwarddelivery->nodeUser; ?></th>
                               <!-- <td class='text-right'><?php /*echo $lang->outwarddelivery->consumed; */?></td>-->
                                <td class='text-center'><?php echo $lang->outwarddelivery->before; ?></td>
                                <td class='text-center'><?php echo $lang->outwarddelivery->after; ?></td>
                            </tr>
                            <?php foreach ($outwarddelivery->consumed as $c): ?>
                                <tr>
                                    <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                 <!--   <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour; */?></td>-->
                                    <?php
                                    echo "<td class='text-center'>" . $c->extra . zget($lang->outwarddelivery->statusList, $c->before, '-') . "</td>";
                                    echo "<td class='text-center'>" . $c->extra . zget($lang->outwarddelivery->statusList, $c->after, '-') . "</td>";
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
                    url: createLink('outwarddelivery', 'push', 'type=' + type + '&code=' + code),
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