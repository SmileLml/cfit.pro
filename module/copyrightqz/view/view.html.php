<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
    <style>
        .inoneline{white-space: NOWRAP;}
        .table-width tbody>tr>th{
            width: 100px!important;
        }
    </style>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php if(!isonlybody()):?>
                <?php $copyrightqzHistory = $app->session->copyrightqzHistory? $app->session->copyrightqzHistory: inlink('browse')?>
                <?php echo html::a($copyrightqzHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
                <div class="divider"></div>
            <?php endif;?>
            <div class="page-title">
                <span class="label label-id"><?php echo $copyrightqz->code ?></span>
            </div>
        </div>
        <?php if (!isonlybody()): ?>
            <div class="btn-toolbar pull-right">
                <?php if (common::hasPriv('copyrightqz', 'exportviewexcel')) echo html::a($this->createLink('copyrightqz', 'exportviewexcel', "copyrightqzId=$copyrightqz->id"), "<i class='icon-export'></i> {$lang->copyrightqz->exportviewexcel}", '', "class='btn btn-primary'"); ?>
            </div>
        <?php endif; ?>
    </div>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->softwareType; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($softwareType) ? $softwareType : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->devHardwareEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->devHardwareEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->devHardwareEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->opsHardwareEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->opsHardwareEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->opsHardwareEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->devOS; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->devOS) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->devOS)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->devEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->devEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->devEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->operatingPlatform; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->operatingPlatform) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->operatingPlatform)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->operationSupportEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->operationSupportEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->operationSupportEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->devLanguage; ?></div>
                    <div class="detail-content article-content">
<!--                        --><?php
//                        $devLanguageItem = '';
//                        $devLanguageItems = '';
//                        if (!empty($copyrightqz->devLanguage)) {
//                            foreach (explode(',', $copyrightqz->devLanguage) as $devLanguage) {
//                                if (!empty($devLanguage)) $devLanguageItem .= zget($lang->copyrightqz->devLanguageList, $devLanguage, $devLanguage) . ',';
//                            }
//                        }
//                        $devLanguageItems = trim($devLanguageItem, ',');
//                        ?>
                        <?php echo !empty($devLanguage) ? $devLanguage: "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->devPurpose; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->devPurpose) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->devPurpose)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->industryOriented; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->industryOriented) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->industryOriented)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->mainFunction; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->mainFunction) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->mainFunction)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->techFeatureType; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($techFeatureType) ? $techFeatureType : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->techFeature; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->techFeature) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->techFeature)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->others; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyrightqz->others) ? html_entity_decode(str_replace("\n","<br/>",$copyrightqz->others)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->copyrightqz->attachments; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                        <div class="detail-content">
                            <?php if ($copyrightqz->files):
                                foreach ($copyrightqz->files as $key => $file) {
                                    echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $copyrightqz, 'canOperate' => ($file->addedBy == $this->app->user->account && ($copyrightqz->status=='tosubmit' || $copyrightqz->status=='feedbackFailed'))));
                                }
                                else:
                                echo "<div class='detail-content text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            <?php endif;?>
                        </div>
                    </div>
                <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=copyrightqz&objectID=$copyrightqz->id"); ?>
            </div>
            <!-- 审核审批意见/处理意见 -->
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->reviewTitle; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($nodes) && (!($copyrightqz->status =='tosubmit'))):?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-180px"><?php echo $lang->copyrightqz->reviewNode; ?></th>
                                    <td class="w-180px"><?php echo $lang->copyrightqz->reviewer; ?></td>
                                    <td class="w-180px"><?php echo $lang->copyrightqz->reviewResult; ?></td>
                                    <td><?php echo $lang->copyrightqz->reviewOpinion; ?></td>
                                    <td class="w-180px"><?php echo $lang->copyrightqz->reviewOpinionTime; ?></td>
                                </tr>
                                <?php foreach ($lang->copyrightqz->reviewNodeList as $key => $reviewNode):
                                    $currentKey = $key-1;
                                    $reviewerUserTitle = '';
                                    $reviewerUsersShow = '';
                                    $realReviewer = new stdClass();
                                    $realReviewer->status = '';
                                    $realReviewer->comment = '';
                                    $realReviewer->reviewTime = '';
                                    if (isset($nodes[$currentKey])) {
                                        $currentNode = $nodes[$currentKey];
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
                                            if (!empty($realReviewer->extra)){
                                                $realReviewer->extra = json_decode($realReviewer->extra);
                                            }
                                        }
                                    } ?>
                                    <tr>
                                        <th><?php echo zget($lang->copyrightqz->statusList, $reviewNode) ;?></th>
                                        <td title="<?php echo $reviewerUserTitle; ?>">
                                            <?php echo $reviewerUsersShow; ?></td>
                                        <td>
                                            <?php echo zget($lang->copyrightqz->confirmResultList, $realReviewer->status, ''); ?>
                                            <?php
                                            if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                                ?>
                                                &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                            <?php endif; ?>
                                        </td>
                                        <td><?php if ($realReviewer->status =='reject'){
                                                echo "不通过原因：".$realReviewer->extra->rejectReason.(!empty($realReviewer->comment) ? "<br>本次操作备注：".$realReviewer->comment : "");
                                            }else{
                                                echo $realReviewer->comment;
                                            }?></td>
                                        <td><?php echo $realReviewer->reviewTime;?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <th><?php echo $lang->copyrightqz->outsideReviewNodeList['1']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestjk', ''); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($copyrightqz->synStatus =='1') {
                                            echo $lang->copyrightqz->synSuccess;
                                        } elseif ($copyrightqz->synStatus =='2') {
                                            echo $lang->copyrightqz->synFail;
                                        } else {
                                            echo $lang->copyrightqz->confirmResultList['wait'];
                                        } ?>
                                    </td>
                                    <td><?php echo ($copyrightqz->synStatus =='2'?$copyrightqz->synFailedReason : ''); ?></td>
                                    <td><?php echo $copyrightqz->synDate; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->copyrightqz->outsideReviewNodeList['2']; ?></th>
                                    <td>
                                        <?php echo zget($users, 'guestcn', ','); ?>
                                    </td>
                                    <td>
                                        <?php if ($copyrightqz->outsideReviewResult == 'pass' || $copyrightqz->outsideReviewResult == 'reject'):?>
                                            <?php echo zget($lang->copyrightqz->outsideReviewResultList, $copyrightqz->outsideReviewResult, ''); ?>
        &nbsp;                                  （<?php echo $copyrightqz->approverName; ?>）
                                        <?php elseif($copyrightqz->synStatus=='1'):?>
                                                <?php echo $lang->copyrightqz->confirmResultList['pending'];?>
                                        <?php else:?>
                                            <?php echo $lang->copyrightqz->confirmResultList['wait']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $copyrightqz->reason;?>
                                    </td>
                                    <td><?php echo $copyrightqz->outsideReviewTime; ?></td>
                                </tr>
                            </table>
                        <?php else:?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                        <?php endif;?>
                    </div>
                </div>
            </div>

            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>

            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php common::printBack($copyrightqzHistory);?>
                    <div class='divider'></div>
                    <?php
                    common::printIcon('copyrightqz', 'edit', "copyrightqzId=$copyrightqz->id", $copyrightqz, 'button');
                    common::printIcon('copyrightqz', 'review', "copyrightqzId=$copyrightqz->id&changeVersion=$copyrightqz->changeVersion&reviewStage=$copyrightqz->reviewStage", $copyrightqz, 'list', 'glasses', '', 'iframe', true);
                    common::printIcon('copyrightqz', 'reject', "copyrightqzId=$copyrightqz->id", $copyrightqz, 'list', 'left-circle','','iframe', true);
                    common::printIcon('copyrightqz', 'delete', "copyrightqzId=$copyrightqz->id", $copyrightqz, 'list', 'trash','','iframe', true);
                    if($copyrightqz->status == 'synFailed') common::printIcon('copyrightqz', 'handlepush',  "copyrightqzID=$copyrightqz->id", $copyrightqz, 'button', 'share','','iframe', true);
                    ?>
                </div>
            </div>
        </div>
        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->basicInfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data table-width'>
                            <tbody>
                            <tr>
                                <th class="inoneline"><?php echo $lang->copyrightqz->code; ?></th>
                                <td><?php echo $copyrightqz->code; ?></td>
                            </tr>
                            <tr>
                                <th class="inoneline"><?php echo $lang->copyrightqz->emisCode; ?></th>
                                <td><?php echo $copyrightqz->emisCode; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->applicant; ?></th>
                                <td><?php echo zget($users,$copyrightqz->applicant); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->applicantDept; ?></th>
                                <td><?php echo zget($depts,$copyrightqz->applicantDept); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->productenrollCode ; ?></th>
                                <td><?php echo $copyrightqz->productenrollDeleted=='0' ? html::a($this->createLink('productenroll', 'view', 'productenrollID=' . $copyrightqz->productenrollId, '', true), $copyrightqz->productenrollCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"): $lang->copyrightqz->productenrollDeleted  ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->fullname; ?></th>
                                <td><?php echo $copyrightqz->fullname;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->shortName; ?></th>
                                <td><?php echo $copyrightqz->shortName;?></td>
                            </tr>
                            <tr>
                                <th class="inoneline"><?php echo $lang->copyrightqz->version; ?></th>
                                <td><?php echo $copyrightqz->version;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->system; ?></th>
                                <td><?php echo zget($lang->copyrightqz->systemList,$copyrightqz->system)?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->descType; ?></th>
                                <td><?php echo zget($lang->copyrightqz->descTypeList,$copyrightqz->descType);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->description; ?></th>
                                <td><?php echo $copyrightqz->description;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->devFinishedTime; ?></th>
                                <td><?php  echo substr($copyrightqz->devFinishedTime,0, 10);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->publishStatus; ?></th>
                                <td><?php echo zget($lang->copyrightqz->publishStatusList,$copyrightqz->publishStatus);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->firstPublicTime; ?></th>
                                <td><?php echo substr($copyrightqz->firstPublicTime,0, 10); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->firstPublicCountry; ?></th>
                                <td><?php echo zget($lang->copyrightqz->firstPublicCountryList,$copyrightqz->firstPublicCountry);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->firstPublicPlace; ?></th>
                                <td><?php echo $copyrightqz->firstPublicPlace;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->devMode; ?></th>
                                <td><?php echo zget($lang->copyrightqz->devModeList,$copyrightqz->devMode);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->rightObtainMethod; ?></th>
                                <td><?php echo zget($lang->copyrightqz->rightObtainMethodList,$copyrightqz->rightObtainMethod);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->isRegister; ?></th>
                                <td><?php echo zget($lang->copyrightqz->isRegisterList,$copyrightqz->isRegister);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->oriRegisNum; ?></th>
                                <td><?php echo $copyrightqz->oriRegisNum;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->isOriRegisNumChanged; ?></th>
                                <td><?php echo zget($lang->copyrightqz->isOriRegisNumChangedList,$copyrightqz->isOriRegisNumChanged);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->proveNum; ?></th>
                                <td><?php echo $copyrightqz->proveNum;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->rightRange; ?></th>
                                <td><?php echo zget($lang->copyrightqz->rightRangeList,$copyrightqz->rightRange);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->sourceProgramAmount; ?></th>
                                <td><?php echo $copyrightqz->sourceProgramAmount;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->identityMaterial; ?></th>
                                <td><?php echo $identityMaterial; ?></td>
                            </tr>
                            <?php if (strstr($copyrightqz->identityMaterial,'99')):?>
                                <tr>
                                    <th><?php echo $lang->copyrightqz->generalDeposit; ?></th>
                                    <td><?php echo $generalDeposit;?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->copyrightqz->generalDepositType; ?></th>
                                    <td><?php echo $copyrightqz->generalDepositType;?></td>
                                </tr>
                            <?php endif;?>
                            <?php if  (strstr($copyrightqz->identityMaterial,'1')):?>
                                <tr>
                                    <th><?php echo $lang->copyrightqz->exceptionalDeposit; ?></th>
                                    <td><?php echo $exceptionalDeposit;?></td>
                                </tr>
                                <?php if (strstr($copyrightqz->exceptionalDeposit,'99')): ?>
                                <tr>
                                    <th><?php echo $lang->copyrightqz->pageNum; ?></th>
                                    <td><?php echo $copyrightqz->pageNum;?></td>
                                </tr>
                                <?php endif;?>
                             <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->outsideReview; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data table-width'>
                            <tbody>
                            <tr>
                                <th><?php echo $lang->copyrightqz->outsideReviewResult; ?></th>
                                <td><?php echo zget($lang->copyrightqz->outsideReviewResultList,$copyrightqz->outsideReviewResult); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->outsideReviewer; ?></th>
                                <td><?php echo $copyrightqz->approverName;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->outsideReviewOpinion; ?></th>
                                <td><?php echo $copyrightqz->reason;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyrightqz->outsideReviewTime; ?></th>
                                <td><?php echo $copyrightqz->outsideReviewTime;?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyrightqz->statusTransition;?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-90px text-center'><?php echo $lang->copyrightqz->nodeUser;?></th>
                              <!--  <td class='text-center inoneline'><?php /*echo $lang->copyrightqz->consumed;*/?></td>-->
                                <td class='text-center'><?php echo $lang->copyrightqz->before;?></td>
                                <td class='text-center'><?php echo $lang->copyrightqz->after;?></td>
                            </tr>
                            <?php foreach($consumed as $c):?>
                                <tr>
                                    <th class='w-90px text-center'><?php echo zget($users, $c->account, '');?></th>
                                 <!--   <td class='text-center'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                                    <?php
                                    echo "<td class='text-center'>".zget($lang->copyrightqz->statusList, $c->before, '-')."</td>";
                                    echo "<td class='text-center'>".zget($lang->copyrightqz->statusList, $c->after, '-')."</td>";
                                    ?>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include '../../common/view/footer.html.php'; ?>