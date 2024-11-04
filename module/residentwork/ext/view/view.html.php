<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<?php $browseLink = inlink('browse');?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php echo html::a($this->session->residentworkList, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $info->id;?></span>
            <span class="text"><?php echo $info->dutyDate;?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->isEmergency; ?></div>
                <div class="detail-content article-content">
                    <?php echo isset($info->isEmergency) ? zget($lang->residentwork->importantTimeList,$info->isEmergency) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <?php if(isset($info->isEmergency) &&  $info->isEmergency== 1):?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->emergencyRemark; ?></div>
                <div class="detail-content article-content" style="white-space: pre-line">
                    <?php echo $info->remark ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->desc; ?></div>
                <div class="detail-content article-content" style="white-space: pre-line">
                    <?php echo $info->logs ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->warnLogs; ?></div>
                <div class="detail-content article-content" style="white-space: pre-line">
                    <?php echo $info->warnLogs ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->residentwork->analysis; ?></div>
                <div class="detail-content article-content" style="white-space: pre-line">
                    <?php echo $info->analysis ?? "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>

        <?php if(!empty($info->files)): ?>
        <div class="cell">
            <div class='detail'>
                <div class='detail-content article-content'>
                    <?php
                        echo $this->fetch('file', 'printFiles', array('files' => $info->files, 'fieldset' => 'true'));
                    ?>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="cell"><?php include '../../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($this->session->residentworkList); ?>
                <div class='divider'></div>
                <?php
                $templateDayInfo = new stdClass();
                $templateDayInfo->id = $info->id;
                $templateDayInfo->templateId = $info->templateId;
                $templateDayInfo->dutyDate = $info->dutyDate;
                //变更排班
                $dutyDate = str_replace("-",',',$info->dutyDate);
//                common::printIcon('residentsupport', 'modifyScheduling', "dayId=$residentInfo->id", $templateDayInfo, 'list', 'calendar', '');
                common::printIcon('residentwork', 'editlog', "workId=$info->id", $info, 'list', 'edit', '');

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
                            <td><?php echo $info->createdDate ?? ''; ?></td>
                        </tr>
                        <tr>
                            <th class='w-120px'><?php echo $lang->residentwork->fillInCreated; ?></th>
                            <td><?php echo zget($users,$info->createdBy ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->dutyPlace; ?></th>
                            <td><?php echo zget($lang->residentsupport->areaList,$info->area ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->actualLeader; ?></th>
                            <?php
                                $actualLeader = '';
                                $leader = zget($users,$info->groupLeader ?? '');
                                if(!empty($leader)){
                                    foreach ($info->details as $detail) {
                                        if ($detail->realDutyuser  == $info->groupLeader){
                                            $leaderDeptId = $detail->realDutyuserDept;
                                        }
                                    }
                                    $dept = str_replace('/','',$depts[$leaderDeptId]);
                                    $actualLeader = $dept.'/'.$leader;
                                }
                            ?>
                            <td><?php echo $actualLeader; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->actualUser; ?></th>
                            <?php
                            if(isset($info->details)){
                                $realDutyUser = "";
                                foreach ($info->details as $detail) {
                                    $deptId = $detail->realDutyuserDept;
                                    $realDutyUser .= str_replace('/','',$depts[$deptId]).'/'.zget($users,$detail->realDutyuser).'&nbsp;&nbsp;';
                                }
                            }else{
                                $realDutyUser = '';
                            }
                            ?>


                            <td><?php echo $realDutyUser; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->dateType; ?></th>
                            <td><?php echo zget($lang->residentsupport->dateTypeList,$info->dateType ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->pushTitle; ?></th>
                            <td>
                                <?php if ($info->pushStatus != ''){
                                    echo $lang->residentwork->logPushStatusArray[$info->pushStatus];
                                }else{
                                    echo '暂未推送';
                                }?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->residentwork->logSource?></th>
                            <td>
                                <?php if ($info->dayId == 0){
                                    echo "用户创建";
                                }else{
                                    echo "排班计划";
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
                        <td><?php echo zget($lang->residentsupport->typeList, $info->type ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class='w-120px'><?php echo $lang->residentwork->dutyDate; ?></th>
                        <td><?php echo $dutyDate = date('Y年m月d日',strtotime($info->dutyDate));?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->dutyDept; ?></th>
                        <?php
                        foreach ($info->details as $detail) {
                            $deptId = $detail->realDutyuserDept;
                            $dutyUserDeptArr[] = $depts[$deptId];
                        }
                        ?>
                        <td><?php echo implode('，',array_unique($dutyUserDeptArr)); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->residentwork->dutyGroupLeader; ?></th>
                        <?php
                        $actualLeader = '';
                        $leader = zget($users,$info->groupLeader ?? '');
                        if(!empty($leader)){
                            foreach ($info->details as $detail) {
                                if ($detail->realDutyuser  == $info->groupLeader){
                                    $leaderDeptId = $detail->realDutyuserDept;
                                }
                            }
                            $dept = str_replace('/','',$depts[$leaderDeptId]);
                            $actualLeader = $dept.'/'.$leader;
                        }
                        ?>
                        <td><?php echo $actualLeader; ?></td>
                    </tr>
                    <tr>

                        <th><?php echo $lang->residentwork->dutyUser; ?></th>
                        <td><?php echo $realDutyUser; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
</div>
<?php include '../../../common/view/footer.html.php'; ?>
