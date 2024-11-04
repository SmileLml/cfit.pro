<div class='detail'>
    <div class='detail-title'><?php echo $lang->review->basicInfo;?></div>

    <div class='detail-content'>
        <table class='table table-data' style="table-layout: fixed;">
            <tbody>
            <tr>
                <th class='w-120px'><?php echo $lang->review->status;?></th>
                <td><?php echo zget($lang->review->statusLabelList, $review->status,'');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->dealUser;?></th>
                <td>
                    <?php
                    $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
                    $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
                    $status = $review->status;
                    if(in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)){
                        $review->dealUser = $review->reviewers;
                    }
                    $dealUsers = $review->dealUser;
                    $dealUsersArray = explode(',', $dealUsers);
                    //待处理人
                    $dealUsers  = getArrayValuesByKeys($users, $dealUsersArray);
                    $dealUsersStr = implode(' ', $dealUsers);
                    ?>
                    <?php echo $dealUsersStr;?>
                </td>
            </tr>

            <tr>
                <th><?php echo $lang->review->deadDate;?></th>
                <td><?php
                    $endDate = date('Y-m-d', strtotime($review->endDate));
                    echo  $review->endDate != '0000-00-00 00:00:00' ? $endDate.'   ': '';
                    ?>
                    <?php
                    $reviewer = [];
                    if(isset($review->reviewer)){
                        $reviewer = explode(',', $review->reviewer);
                    }
                    $dealReviewflag = $this->loadModel('review')->isClickable($review, 'editenddate');
                    if($review->endDate != '0000-00-00 00:00:00' and $dealReviewflag ):?>
                    <span class='c-actions text-left'><?php
                        $editEndDateFlag = $this->loadModel('review')->isClickable($review, 'editEndDate');
                        common::printIcon('review', 'editEndDate', "id=$review->id", $review, 'list', 'edit', '', 'iframe', true,'data-position="50px"');
                        ?>
                    </span>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th><?php echo $lang->review->type;?></th>
                <td><?php echo zget($lang->review->typeList, $review->type, '');?>
                <span class='c-actions text-left'><?php
                    if(common::hasPriv('reviewmanage', 'editTypeandOwner') && in_array($review->type,$lang->review->changetypeArr)){
                        common::printIcon('reviewmanage', 'editTypeandOwner', "id=$review->id", $review, 'list','edit', '', 'iframe', true,'data-position="50px"');
                    }
                ?></span>
                </td>
            </tr>

            <tr>
                <th><?php echo $lang->review->isFirstReview;?></th>
                <td><?php echo zget($lang->review->isFirstReviewLabelList, $review->isFirstReview, '');?></td>
            </tr>

            <tr>
                <th><?php echo $lang->review->grade;?></th>
                <td><?php echo zget($lang->review->gradeList, $review->grade, '');?></td>
            </tr>

            <?php if($review->grade == 'meeting'):?>
                <tr>
                    <th><?php echo $lang->review->meetingPlanTime;?></th>
                    <td><?php echo $review->meetingPlanTime != '0000-00-00 00:00:00' ? $review->meetingPlanTime: '';?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->review->meetingCode;?></th>
                    <td><?php echo $review->meetingCode;?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->review->meetingRealTime;?></th>
                    <td><?php echo $review->meetingRealTime != '0000-00-00 00:00:00' ? $review->meetingRealTime: '';?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->review->meetingConsumedInfo;?></th>
                    <td><?php echo isset($review->meetingDetailInfo->consumed) && ($review->meetingDetailInfo->consumed > 0) ? $review->meetingDetailInfo->consumed.'H': '';?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <th><?php echo $lang->review->owner;?></th>
                <td>
                    <?php $owners = explode(',', str_replace(' ', '', $review->owner)); foreach($owners as $account) echo ' ' . zget($users, $account);?>
                    <span class='c-actions text-left'><?php
                        if(common::hasPriv('reviewmanage', 'editTypeandOwner') && in_array($review->type,$lang->review->changetypeArr)){
                            common::printIcon('reviewmanage', 'editTypeandOwner', "id=$review->id", $review, 'list','edit', '', 'iframe', true,'data-position="50px"');
                        }
                    ?></span>
                </td>
            </tr>

            <tr>
                <th><?php echo $lang->review->reviewer;?></th>
                <td>
                    <?php echo zget($users, $review->reviewer);?>
                </td>
            </tr>

            <tr>
                <th>
                    <?php echo $lang->review->expert;?>
                    <?php
                        $editInfoClass = $app->user->account == $review->reviewer ||  $app->user->account == 'admin' ? 'btn iframe' : 'btn iframe disabled'; //评审专员具有修改评审专员的权限;
                    ?>
                </th>
                <td>
                    <?php $experts = explode(',', str_replace(' ', '', $review->expert)); foreach($experts as $account) echo ' ' . zget($users, $account);?>
                    <?php
                        $title = sprintf($this->lang->review->editField, $this->lang->review->expert);
                        echo html::a(helper::createLink('reviewmanage', 'editUsersByField', "id=$review->id&field=expert", '', true), '<i class="icon-edit"></i>', '', "title='{$title}' class='{$editInfoClass}'");
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->review->reviewedBy;?></th>
                <td>
                    <?php $reviewedBy= explode(',', str_replace(' ', '', $review->reviewedBy)); foreach($reviewedBy as $account) echo ' ' . zget($outsideList1, $account);?>
                    <?php
                        $title = sprintf($this->lang->review->editField, $this->lang->review->reviewedBy);
                        echo html::a(helper::createLink('reviewmanage', 'editUsersByField', "id=$review->id&field=reviewedBy", '', true), '<i class="icon-edit"></i>', '', "title='{$title}' class='{$editInfoClass}'");
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->review->outside;?></th>
                <td>
                    <?php $outside = explode(',', str_replace(' ', '', $review->outside)); foreach($outside as $account) echo ' ' . zget($outsideList2, $account);?>
                    <?php
                        $title = sprintf($this->lang->review->editField, $this->lang->review->outside);
                        echo html::a(helper::createLink('reviewmanage', 'editUsersByField', "id=$review->id&field=outside", '', true), '<i class="icon-edit"></i>', '', "title='{$title}' class='{$editInfoClass}'");
                    ?>
                </td>
            </tr>

            <?php
                if($review->grade == 'meeting' && $review->type != 'cbp'):
                    $meetingDetailInfo = new stdClass();
                    if(isset($review->meetingDetailInfo) && ($review->meetingDetailInfo)){
                        $meetingDetailInfo = $review->meetingDetailInfo;
                    }
                ?>
                <tr>
                    <th><?php echo $lang->review->meetingPlanExport;?></th>
                    <td><?php $meetingPlanExport = explode(',', str_replace(' ', '', $review->meetingPlanExport)); foreach($meetingPlanExport as $account) echo ' ' . zget($users, $account);?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->review->realExport;?></th>
                    <td>
                        <?php if(isset($meetingDetailInfo->realExportVersion)):?>
                            <?php if($meetingDetailInfo->realExportVersion == 2):?>
                                <?php $realExport = explode(',', $meetingDetailInfo->realExport); foreach($realExport as $account) echo ' ' . zget($users, $account);?>
                            <?php else:?>
                                <?php echo  $meetingDetailInfo->realExport;?>
                            <?php endif;?>
                        <?php endif;?>
                    </td>
                </tr>

            <?php endif; ?>
            <tr>
                <th><?php echo $lang->review->relatedUsers;?></th>
                <td><?php $relatedUsers = explode(',', str_replace(' ', '', $review->relatedUsers)); foreach($relatedUsers as $account) echo ' ' . zget($users, $account);?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->qapre;?></th>
                <td><?php  echo ' ' . zget($users, $review->qa);?></td>
            </tr>

            <tr>
                <th>
                    <?php echo $lang->review->firstDept;?>

                </th>
                <td>
                    <?php echo $review->trialDept;?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->review->trialDeptLiasisonOfficer;?></th>
                <td><?php echo $review->trialDeptLiasisonOfficer;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->trialAdjudicatingOfficer;?></th>
                <td><?php echo $review->trialAdjudicatingOfficer;?></td>
            </tr>

            <tr>
                <th><?php echo $lang->review->trialJoinOfficer;?></th>
                <td><?php echo $review->trialJoinOfficer;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->cm;?></th>
                <td><?php  echo ' ' . zget($users, $review->qualityCm);?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->baseLineCondition;?></th>
                <td><?php  echo ' ' . zget($lang->review->condition, $review->baseLineCondition);?></td>
            </tr>
            <tr>
                <th><?php echo  $lang->review->preReviewDeadline;?></th>
                <td><?php  echo ''.$review->preReviewDeadline;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->firstReviewDeadline;?></th>
                <td><?php  echo  $review->firstReviewDeadline;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->deadline;?></th>
                <td><?php echo $review->deadline;?></td>
            </tr>

            <tr>
                <th><?php echo $lang->review->verifyDeadline;?></th>
                <td><?php echo $review->verifyDeadline != '0000-00-00'? $review->verifyDeadline:'';?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->editDeadline;?></th>
                <td><?php echo $review->editDeadline != '0000-00-00'? $review->editDeadline:'';?></td>
            </tr>

            <tr>
                <th class='w-100px'><?php echo $lang->review->projectType;?></th>
                <td><?php echo zget($lang->projectplan->typeList, $review->projectType,'');?></td>
            </tr>
            <tr>
                <th class='w-100px'><?php echo $lang->review->isImportant;?></th>
                <td><?php echo zget($lang->review->isImportantList, $review->isImportant,'');?></td>
            </tr>
            <tr>
                <th><?php echo  $lang->review->mainRelationInfo;?></th>
                <td ><?php  echo $review->mainRelationInfo ?  str_replace(',',"<br/>", $review->mainRelationInfo) : $lang->review->noRelationRecord;?>
                </td>
            </tr>
            <tr>
                <th><?php echo  $lang->review->slaveRelationInfo;?></th>
                <td ><?php echo $review->slaveRelationInfo ?  str_replace(',',"<br/>", $review->slaveRelationInfo): $lang->review->noRelationRecord;?>
                </td>
            </tr>

            <?php if($review->isSafetyTest > 1):?>
                <tr>
                    <th><?php echo  $lang->review->isSafetyTest;?></th>
                    <td ><?php echo zget($lang->review->isSafetyTestList, $review->isSafetyTest);?>
                    </td>
                </tr>
            <?php endif;?>

            <?php if($review->isPerformanceTest > 1):?>
                <tr>
                    <th><?php echo  $lang->review->isPerformanceTest;?></th>
                    <td ><?php echo zget($lang->review->isPerformanceTestList, $review->isPerformanceTest);?>
                    </td>
                </tr>
            <?php endif;?>

            <tr>
               <th class='w-100px'><?php echo $lang->review->createdDept;?></th>
                <td><?php echo zget($deptMap,$review->createdDept,'')?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->editBy;?></th>
                <td><?php  echo ' ' . zget($users, $review->editBy);?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->editDate;?></th>
                <td><?php  echo $review->editDate;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->closeBy;?></th>
                <td><?php echo ' ' . zget($users, $review->closePerson);?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->closeTime;?></th>
                <td><?php echo $review->closeTime;?></td>
            </tr>

            <tr>
                <th><?php echo $lang->review->createdBy;?></th>
                <td><?php echo zget($users, $review->createdBy);?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->createdDate;?></th>
                <td><?php echo $review->createdDate;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->suspendBy;?></th>
                <td><?php echo zget($users, $review->suspendBy);?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->suspendTime;?></th>
                <td><?php echo $review->suspendTime != '0000-00-00 00:00:00' ? $review->suspendTime: '';?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->suspendReason;?></th>
                <td style="word-wrap:break-word ;word-break:break-all;"><?php echo $review->suspendReason;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->renewBy;?></th>
                <td><?php echo zget($users, $review->renewBy);?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->renewTime;?></th>
                <td><?php echo $review->renewTime != '0000-00-00 00:00:00' ? $review->renewTime: '';?></td>
            </tr>
            <tr>
                <th><?php echo $lang->review->renewReason;?></th>
                <td style="word-wrap:break-word; word-break:break-all;"><?php echo $review->renewReason;?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>