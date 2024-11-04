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
                <?php $copyrightHistory = $app->session->copyrightHistory? $app->session->copyrightHistory: inlink('browse')?>
                <?php echo html::a($copyrightHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
                <div class="divider"></div>
            <?php endif;?>
            <div class="page-title">
                <span class="label label-id"><?php echo $copyright->code ?></span>
            </div>
        </div>
        <?php if (!isonlybody()): ?>
            <div class="btn-toolbar pull-right">
                <?php if (common::hasPriv('copyright', 'exportviewexcel')) echo html::a($this->createLink('copyright', 'exportviewexcel', "copyrightId=$copyright->id"), "<i class='icon-export'></i> {$lang->copyright->exportviewexcel}", '', "class='btn btn-primary'"); ?>
            </div>
        <?php endif; ?>
    </div>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->softwareType; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($softwareType) ? $softwareType : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->devHardwareEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->devHardwareEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyright->devHardwareEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->opsHardwareEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->opsHardwareEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyright->opsHardwareEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->devOS; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->devOS) ? html_entity_decode(str_replace("\n","<br/>",$copyright->devOS)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->devEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->devEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyright->devEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->operatingPlatform; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->operatingPlatform) ? html_entity_decode(str_replace("\n","<br/>",$copyright->operatingPlatform)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->operationSupportEnv; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->operationSupportEnv) ? html_entity_decode(str_replace("\n","<br/>",$copyright->operationSupportEnv)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->devLanguage; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($devLanguage) ? $devLanguage: "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->devPurpose; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->devPurpose) ? html_entity_decode(str_replace("\n","<br/>",$copyright->devPurpose)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->industryOriented; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->industryOriented) ? html_entity_decode(str_replace("\n","<br/>",$copyright->industryOriented)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->mainFunction; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->mainFunction) ? html_entity_decode(str_replace("\n","<br/>",$copyright->mainFunction)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->techFeatureType; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($techFeatureType) ? $techFeatureType : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->techFeature; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->techFeature) ? html_entity_decode(str_replace("\n","<br/>",$copyright->techFeature)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->others; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($copyright->others) ? html_entity_decode(str_replace("\n","<br/>",$copyright->others)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->copyright->attachments; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                        <div class="detail-content">
                            <?php if ($copyright->files):
                                foreach ($copyright->files as $key => $file) {
                                    echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $copyright, 'canOperate' => ($file->addedBy == $this->app->user->account && ($copyright->status=='tosubmit' || $copyright->status=='feedbackFailed'))));
                                }
                                else:
                                echo "<div class='detail-content text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            <?php endif;?>
                        </div>
                    </div>
                <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=copyright&objectID=$copyright->id"); ?>
            </div>
            <!-- 审核审批意见/处理意见 -->
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->reviewTitle; ?></div>
                    <div class="detail-content article-content">
                        <?php if (!empty($nodes)):?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-180px"><?php echo $lang->copyright->reviewNode; ?></th>
                                    <td class="w-180px"><?php echo $lang->copyright->reviewer; ?></td>
                                    <td class="w-180px"><?php echo $lang->copyright->reviewResult; ?></td>
                                    <td><?php echo $lang->copyright->reviewOpinion; ?></td>
                                    <td class="w-180px"><?php echo $lang->copyright->reviewOpinionTime; ?></td>
                                </tr>
                                <?php foreach ($lang->copyright->reviewNodeList as $key => $reviewNode):
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
                                        <th><?php echo zget($lang->copyright->statusList, $reviewNode) ;?></th>
                                        <td title="<?php echo $reviewerUserTitle; ?>">
                                            <?php echo $reviewerUsersShow; ?></td>
                                        <td>
                                            <?php echo $copyright->status != 'tosubmit' ? zget($lang->copyright->confirmResultList, $realReviewer->status, ''):''; ?>
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
                    <?php common::printBack($copyrightHistory);?>
                    <div class='divider'></div>
                    <?php
                    common::printIcon('copyright', 'edit', "copyrightId=$copyright->id", $copyright, 'button');
                    common::printIcon('copyright', 'review', "copyrightId=$copyright->id&changeVersion=$copyright->changeVersion&reviewStage=$copyright->reviewStage", $copyright, 'list', 'glasses', '', 'iframe', true);
                    common::printIcon('copyright', 'return', "", $copyright, 'list', 'left-circle','','iframe', true);
                    common::printIcon('copyright', 'delete', "copyrightId=$copyright->id", $copyright, 'list', 'trash','','iframe', true);
                    if($copyright->status == 'synFailed') common::printIcon('copyright', 'handlepush',  "copyrightID=$copyright->id", $copyright, 'button', 'share');
                    ?>
                </div>
            </div>
        </div>
        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->copyright->basicInfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data table-width'>
                            <tbody>
                            <tr>
                                <th class="inoneline"><?php echo $lang->copyright->code; ?></th>
                                <td><?php echo $copyright->code; ?></td>
                            </tr>
                            <tr>
                                <th class="inoneline"><?php echo $lang->copyright->modifyCode; ?></th>
                                <td><?php echo html::a($this->createLink('modify','view','modifyID='.$copyright->modifyId,'',true),$copyright->modifyCode,'',"data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></td>
                            </tr>
                            
                            <tr>
                                <th class="inoneline"><?php echo $lang->copyright->buildDept; ?></th>
                                <td><?php echo zget($buildDepts,$copyright->buildDept); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->createdBy; ?></th>
                                <td><?php echo zget($users,$copyright->createdBy); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->createdDept; ?></th>
                                <td><?php echo zget($depts,$copyright->createdDept); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->fullname; ?></th>
                                <td><?php echo $copyright->fullname;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->shortName; ?></th>
                                <td><?php echo $copyright->shortName;?></td>
                            </tr>
                            <tr>
                                <th class="inoneline"><?php echo $lang->copyright->version; ?></th>
                                <td><?php echo $copyright->version;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->system; ?></th>
                                <td><?php echo zget($systemList,$copyright->system,'')?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->descType; ?></th>
                                <td><?php echo zget($lang->copyright->descTypeList,$copyright->descType);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->description; ?></th>
                                <td><?php echo $copyright->description;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->devFinishedTime; ?></th>
                                <td><?php echo substr($copyright->devFinishedTime,0,10);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->publishStatus; ?></th>
                                <td><?php echo zget($lang->copyright->publishStatusList,$copyright->publishStatus);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->firstPublicTime; ?></th>
                                <td><?php echo substr($copyright->firstPublicTime,0,10);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->firstPublicCountry; ?></th>
                                <td><?php echo zget($lang->copyright->firstPublicCountryList,$copyright->firstPublicCountry);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->firstPublicPlace; ?></th>
                                <td><?php echo $copyright->firstPublicPlace;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->devMode; ?></th>
                                <td><?php echo zget($lang->copyright->devModeList,$copyright->devMode);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->rightObtainMethod; ?></th>
                                <td><?php echo zget($lang->copyright->rightObtainMethodList,$copyright->rightObtainMethod);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->isRegister; ?></th>
                                <td><?php echo zget($lang->copyright->isRegisterList,$copyright->isRegister);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->oriRegisNum; ?></th>
                                <td><?php echo $copyright->oriRegisNum;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->isOriRegisNumChanged; ?></th>
                                <td><?php echo zget($lang->copyright->isOriRegisNumChangedList,$copyright->isOriRegisNumChanged);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->proveNum; ?></th>
                                <td><?php echo $copyright->proveNum;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->rightRange; ?></th>
                                <td><?php echo zget($lang->copyright->rightRangeList,$copyright->rightRange);?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->sourceProgramAmount; ?></th>
                                <td><?php echo $copyright->sourceProgramAmount;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->copyright->identityMaterial; ?></th>
                                <td><?php echo $identityMaterial; ?></td>
                            </tr>
                            <?php if (strstr($copyright->identityMaterial,'99')):?>
                                <tr>
                                    <th><?php echo $lang->copyright->generalDeposit; ?></th>
                                    <td><?php echo $generalDeposit;?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->copyright->generalDepositType; ?></th>
                                    <td><?php echo $copyright->generalDepositType;?></td>
                                </tr>
                            <?php endif;?>
                            <?php if  (strstr($copyright->identityMaterial,'1')):?>
                                <tr>
                                    <th><?php echo $lang->copyright->exceptionalDeposit; ?></th>
                                    <td><?php echo $exceptionalDeposit;?></td>
                                </tr>
                                <?php if (strstr($copyright->exceptionalDeposit,'99')): ?>
                                <tr>
                                    <th><?php echo $lang->copyright->pageNum; ?></th>
                                    <td><?php echo $copyright->pageNum;?></td>
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
                    <div class="detail-title"><?php echo $lang->copyright->statusTransition;?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-90px text-center'><?php echo $lang->copyright->nodeUser;?></th>
                              <!--  <td class='text-center inoneline'><?php /*echo $lang->copyright->consumed;*/?></td>-->
                                <td class='text-center'><?php echo $lang->copyright->before;?></td>
                                <td class='text-center'><?php echo $lang->copyright->after;?></td>
                            </tr>
                            <?php foreach($consumed as $c):?>
                                <tr>
                                    <th class='w-90px text-center'><?php echo zget($users, $c->account, '');?></th>
                                  <!--  <td class='text-center'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                                    <?php
                                    echo "<td class='text-center'>".zget($lang->copyright->statusList, $c->before, '-')."</td>";
                                    echo "<td class='text-center'>".zget($lang->copyright->statusList, $c->after, '-')."</td>";
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