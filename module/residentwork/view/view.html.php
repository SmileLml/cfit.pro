<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php $browseLink = inlink('browse');?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php echo html::a($this->session->residentworkList, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $residentInfo->id;?></span>
            <span class="text"><?php echo $residentInfo->dutyDate;?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->isEmergency; ?></div>
                <div class="detail-content article-content">
                    <?php echo isset($residentInfo->workInfo->isEmergency) ? zget($lang->residentwork->importantTimeList,$residentInfo->workInfo->isEmergency) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <?php if(isset($residentInfo->workInfo->isEmergency) &&  $residentInfo->workInfo->isEmergency== 1):?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->emergencyRemark; ?></div>
                <div class="detail-content article-content">
                    <?php echo $residentInfo->workInfo->remark ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->desc; ?></div>
                <div class="detail-content article-content">
                    <?php echo $residentInfo->workInfo->logs ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->warnLogs; ?></div>
                <div class="detail-content article-content">
                    <?php echo $residentInfo->workInfo->warnLogs ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->analysis; ?></div>
                <div class="detail-content article-content">
                    <?php echo $residentInfo->workInfo->analysis ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>

        <?php if(!empty($residentInfo->workInfo->files)): ?>
        <div class="cell">
            <div class='detail'>
                <div class='detail-content article-content'>
                    <?php
                        echo $this->fetch('file', 'printFiles', array('files' => $residentInfo->workInfo->files, 'fieldset' => 'true'));
                    ?>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($this->session->residentworkList); ?>
                <div class='divider'></div>
                <?php
                $templateDayInfo = new stdClass();
                $templateDayInfo->id = $residentInfo->id;
                $templateDayInfo->templateId = $residentInfo->templateId;
                $templateDayInfo->dutyDate = $residentInfo->dutyDate;
                //变更排班
                $dutyDate = str_replace("-",',',$residentInfo->dutyDate);
                common::printIcon('residentsupport', 'modifyScheduling', "dayId=$residentInfo->id", $templateDayInfo, 'list', 'calendar', '');
                common::printIcon('residentwork', 'recordDutyLog', "dutyDate=$dutyDate&dayId=$residentInfo->id", $templateDayInfo, 'list', 'edit', '');

                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->workLog; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->residentwork->fillInDate; ?></th>
                            <td><?php echo $residentInfo->workInfo->createdDate ?? ''; ?></td>
                        </tr>
                        <tr>
                            <th class='w-120px'><?php echo $lang->residentwork->fillInCreated; ?></th>
                            <td><?php echo zget($users,$residentInfo->workInfo->createdBy ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->dutyPlace; ?></th>
                            <td><?php echo zget($lang->residentsupport->areaList,$residentInfo->workInfo->area ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->actualLeader; ?></th>
                            <?php
                                $actualLeader = '';
                                $leader = zget($users,$residentInfo->workInfo->groupLeader ?? '');
                                if(!empty($leader)){
                                    $leaderDeptId = $residentInfo->workInfo->realLeaderDeptId;
                                    $dept = str_replace('/','',$depts[$leaderDeptId]);
                                    $actualLeader = $dept.'/'.$leader;
                                }
                            ?>
                            <td><?php echo $actualLeader; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->actualUser; ?></th>
                            <?php
                            if(isset($residentInfo->workInfo->workDetail)){
                                $realDutyUser = '';
                                $userArr = [];
                                $deptArr = [];
                                $buildUserInfo = [];
                                foreach ($residentInfo->workInfo->workDetail as $duty){
                                    $userArr[] = $duty->realDutyuser;
                                    $deptArr[] = $duty->realDutyuserDept;
                                }
                                $realDutyUserDeptList = getArrayValuesByKeys($depts, $deptArr);
                                $realDutyUserList = getArrayValuesByKeys($users, $userArr);

                                foreach ($userArr as $key => $value){
                                    $buildUserInfo[] = str_replace('/','',$realDutyUserDeptList[$key]).'/'.$users[$value];
                                }
                                $realDutyUser = implode(',', $buildUserInfo);
                            }else{
                                $realDutyUser = '';
                            }
                            ?>


                            <td><?php echo $realDutyUser; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->dateType; ?></th>
                            <td><?php echo zget($lang->residentsupport->dateTypeList,$residentInfo->workInfo->dateType ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->pushTitle; ?></th>
                            <td>
                                <?php if ($residentInfo->workInfo->pushStatus != ''){
                                    echo $lang->residentwork->logPushStatusArray[$residentInfo->workInfo->pushStatus];
                                }else{
                                    echo '暂未推送';
                                }?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
            <div class="detail-title"><?php echo $lang->residentwork->basicInfo; ?></div>
            <div class='detail-content'>
                <table class='table table-data'>
                    <tbody>
                        <th class='w-120px'><?php echo $lang->residentwork->type; ?></th>
                        <td><?php echo zget($lang->residentsupport->typeList, $residentInfo->templateInfo->type ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class='w-120px'><?php echo $lang->residentwork->dutyDate; ?></th>
                        <td><?php echo $dutyDate = date('Y年m月d日',strtotime($residentInfo->dutyDate));?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->requireInfo; ?></th>
                        <?php
                        $requireInfo = '';
                        $requireInfoArr = explode(',',$residentInfo->detailInfo->requireInfo);
                        $requireInfo .= implode(',', array_unique($requireInfoArr));
                        ?>
                        <td><?php echo $requireInfo; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->postTypeInfo; ?></th>
                        <?php
                        $postType = '';
                        $postTypeArr = explode(',',$residentInfo->detailInfo->postType);
                        $postTypeList = getArrayValuesByKeys($lang->residentsupport->postType, $postTypeArr);
                        $postType .= implode(',', array_unique($postTypeList));
                        ?>
                        <td><?php echo $postType; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->timeType; ?></th>
                        <?php
                        $timeType = '';
                        $timeTypeArr = explode(',',$residentInfo->detailInfo->timeType);
                        $timeTypeList = getArrayValuesByKeys($lang->residentsupport->durationTypeList, $timeTypeArr);
                        $timeType .= implode(',', array_unique($timeTypeList));
                        ?>
                        <td><?php echo $timeType; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->dutyTime; ?></th>
                        <?php
                        $timeSlot = '';
                        $timeSlotArr = explode(',',$residentInfo->detailInfo->timeSlot);
                        $timeSlot .= implode(',', array_unique($timeSlotArr));
                        ?>
                        <td><?php echo $timeSlot; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->dutyDept; ?></th>
                        <?php
                        $dutyUserDept = '';
                        $userDeptArr = explode(',',$residentInfo->detailInfo->dutyUserDept);
                        $dutyUserDeptList = getArrayValuesByKeys($depts, $userDeptArr);
                        $dutyUserDept .= implode(',', array_unique($dutyUserDeptList));
                        ?>
                        <td><?php echo $dutyUserDept; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->dutyGroupLeader; ?></th>
                        <?php
                        $dutyGroupLeader = '';
                        $leader = zget($users,$residentInfo->dutyGroupLeader ?? '');
                        if(!empty($leader)){
                            $leaderDeptId = $residentInfo->dutyGroupLeaderDept;
                            $dept = str_replace('/','',$depts[$leaderDeptId]);
                            $dutyGroupLeader = $dept.'/'.$leader;
                        }
                        ?>
                        <td><?php echo $dutyGroupLeader; ?></td>
                    </tr>
                    <tr>
                        <?php
                        $dutyUser = '';
                        $buildUserInfo = [];
                        $userArr = explode(',',$residentInfo->detailInfo->dutyUser);
                        $dutyUserList = getArrayValuesByKeys($users, $userArr);
                        foreach ($userArr as $key => $value){
                            $buildUserInfo[] = str_replace('/','',$dutyUserDeptList[$key]).'/'.$users[$value];
                        }
                        $dutyUser = implode(',', $buildUserInfo);
                        ?>
                        <th><?php echo $lang->residentwork->dutyUser; ?></th>
                        <td><?php echo $dutyUser; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
