<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $mailTitle;?></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->custommail->tips;?></legend>
            <div style='padding:5px;'><?php echo $mailConf->mailContent;?></div>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->templateId;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $templateDeptInfo->templateId;?></td>
            </tr>
            <tr >
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->type;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                    <?php echo zget($this->lang->residentsupport->typeList, $templateInfo->type);?> -
                    <?php echo zget($this->lang->residentsupport->subTypeList, $templateInfo->subType);?>
                </td>
            </tr>
            <?php
                if($action->action == 'modifyscheduling'):
                    $modifyInfo = $templateDeptInfo->modifyInfo;
                    $formatDutyUsersList = [];
                    $dayList = [];
                    if(isset($modifyInfo->formatDutyUsersList)){
                        $formatDutyUsersList = $modifyInfo->formatDutyUsersList;
                        $dayList = $modifyInfo->dayList;
                    }
                ?>
                <tr>
                    <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                        <?php echo $this->lang->residentsupport->dutyUser;?>
                    </th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                        <div class='detail'>
                            <div class="detail-content article-content ">
                                <table class='table ops  table-fixed scheduling-table'>
                                    <thead>
                                    <tr>
                                        <th class='w-120px'><?php echo $this->lang->residentsupport->dutyDate ;?></th>
                                        <th class='w-120px'><?php echo $this->lang->residentsupport->dutyGroupLeader;?></th>
                                        <th class='w-120px'><?php echo $this->lang->residentsupport->requireInfo;?></th>
                                        <th class='w-120px'><?php echo $this->lang->residentsupport->postTypeInfo;?></th>
                                        <th class='w-180px'><?php echo $this->lang->residentsupport->timeType ;?></th>
                                        <th class='w-180px'><?php echo  $this->lang->residentsupport->dutyTime ;?></th>
                                        <th class='w-120px'><?php echo  $this->lang->residentsupport->dutyUser ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(empty($formatDutyUsersList)):?>
                                        <tr>
                                            <th colspan="7" style="text-align: center;"><?php echo $this->lang->noData;?></th>
                                        </tr>
                                    <?php else:?>
                                        <?php
                                        foreach($formatDutyUsersList as $dayId => $currentDeptUerList):
                                            $dayUserCount = count($currentDeptUerList);
                                            $dayInfo = zget($dayList, $dayId);
                                            $dutyDate = $dayInfo->dutyDate;
                                            $dutyGroupLeader = $dayInfo->dutyGroupLeader;
                                            $deptFirstUerInfo =  $currentDeptUerList[0];
                                            ?>
                                            <tr>
                                                <th rowspan="<?php echo $dayUserCount;?>" style="vertical-align: middle;">
                                                    <?php echo $dutyDate; ?>
                                                </th>

                                                <th rowspan="<?php echo $dayUserCount;?>" style="vertical-align: middle;">
                                                    <?php echo $dutyGroupLeader ? zget($users, $dutyGroupLeader):''; ?>
                                                </th>
                                                <td title="<?php echo $deptFirstUerInfo->requireInfo; ?>">
                                                    <?php echo Helper::substr($deptFirstUerInfo->requireInfo, 10,'...'); ?>
                                                </td>
                                                <td><?php echo zget($this->lang->residentsupport->postType, $deptFirstUerInfo->postType); ?></td>
                                                <td><?php echo zget($this->lang->residentsupport->durationTypeList, $deptFirstUerInfo->timeType); ?></td>
                                                <td><?php echo $deptFirstUerInfo->startTime.'-'.$deptFirstUerInfo->endTime; ?></td>
                                                <td>
                                                    <?php echo zget($users, $deptFirstUerInfo->dutyUser);?>
                                                </td>
                                            </tr>
                                                <?php
                                                    if($dayUserCount > 1):
                                                        for($i = 1; $i < $dayUserCount; $i++):
                                                            $dutyUserInfo =  $currentDeptUerList[$i];
                                                ?>
                                                        <tr>
                                                            <td title="<?php echo $dutyUserInfo->requireInfo; ?>">
                                                                <?php echo Helper::substr($dutyUserInfo->requireInfo, 10,'...'); ?>
                                                            </td>
                                                            <td><?php echo zget($this->lang->residentsupport->postType, $dutyUserInfo->postType); ?></td>
                                                            <td><?php echo zget($this->lang->residentsupport->durationTypeList, $dutyUserInfo->timeType); ?></td>
                                                            <td><?php echo $dutyUserInfo->startTime.'-'.$dutyUserInfo->endTime; ?></td>
                                                            <td>
                                                                <?php echo zget($users, $dutyUserInfo->dutyUser);?>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    endfor;
                                                endif;
                                                ?>
                                        <?php
                                        endforeach;
                                        ?>
                                    <?php endif;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                        <?php echo $this->lang->residentsupport->dutyDept;?>
                    </th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                        <?php echo zget($deptInfo, 'name');?>
                    </td>
                </tr>
                <tr>
                    <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                        <?php echo $this->lang->residentsupport->processStatus;?>
                    </th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                        <?php echo zget($this->lang->residentsupport->temDeptStatusDescList, $templateDeptInfo->status) ;?>
                    </td>
                </tr>
            <?php else:?>
            <tr>
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->dutyDate;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                    <?php echo $templateInfo->startDate . '~' . $templateInfo->endDate;?>
                </td>
            </tr>

            <tr>
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->dutyDept;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                    <?php echo zget($deptInfo, 'name');?>
                </td>
            </tr>
            <tr>
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->processStatus;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                    <?php echo zget($this->lang->residentsupport->temDeptStatusDescList, $templateDeptInfo->status) ;?>
                </td>
            </tr>

            <tr>
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->dutyGroupLeader;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                    <?php if($templateDeptInfo->dutyGroupLeaderList):?>
                        <?php foreach ($templateDeptInfo->dutyGroupLeaderList as $dutyGroupLeaderInfo):?>
                            <?php echo $dutyGroupLeaderInfo->deptName;?>/<?php echo $dutyGroupLeaderInfo->realname;?><br/>
                        <?php endforeach;?>
                    <?php endif;?>

                </td>
            </tr>

            <tr>
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->dutyUser;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                    <?php if($templateDeptInfo->dutyUsers):?>
                        <?php foreach ($templateDeptInfo->dutyUsers as $dutyUser):?>
                            <?php echo zget($users, $dutyUser);?>&nbsp;
                        <?php endforeach;?>
                    <?php endif;?>
                </td>
            </tr>

            <?php endif;?>
            <tr>
                <th style='width: 60px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                    <?php echo $this->lang->residentsupport->logComment;?>
                </th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $action->comment;?></td>
            </tr>

        </table>
    </td>
</tr>

<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>