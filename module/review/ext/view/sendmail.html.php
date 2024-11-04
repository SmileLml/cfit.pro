<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
      <tr>
        <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'>
            <?php echo $mailTitle;?>
        </td>
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
<?php if($isAutoSendMail == 1):?>
   <tr>
        <td style='padding: 10px; border: none;'>
            <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
                <thead>
                <tr>
                    <th style='width: 80px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->reviewID;?></th>
                    <th style='width: 80px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->createdDept;?></th>
                    <th style='width: 80px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->title;?></th>
                    <th style='width: 80px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->deadDate;?></th>
                    <th style='width: 80px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->type;?></th>
                    <th style='width: 80px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->status;?></th>
                    <th style='width: 80px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->dealUser;?></th>
                    <?php if($isAutoDealTime):?>
                        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                            <?php echo $this->lang->review->autoDealTime;?>
                        </th>
                    <?php endif;?>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($reviewListSend)):?>
                <?php
                    $currentDay = helper::today();
                    foreach($reviewListSend as $review):
                        $endDate = '';
                        if($review->endDate != '0000-00-00 00:00:00'){
                            $endDate = date('Y-m-d', strtotime($review->endDate));
                        }

                ?>
                    <tr>
                        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $review->id;?></td>
                        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
                            <?php if(($review->createdDept)) echo zget($deptMap,$review->createdDept,'');?>
                        </td>
                        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
                            <?php echo $review->title;?>
                        </td>

                        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
                            <?php echo $endDate;?>
                        </td>

                        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
                            <?php  echo zget($this->lang->review->typeList, $review->type, '');?>
                        </td>
                        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
                            <?php  echo zget($this->lang->review->statusReviewList, $review->status,'');?>
                        </td>
                        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
                            <?php
                            $dealUsers = $review->dealUser;
                            $dealUsersArray = explode(',', $dealUsers);
                            //待处理人
                            $dealUsers  = getArrayValuesByKeys($users, $dealUsersArray);
                            $dealUsersStr = implode(' ', $dealUsers);
                            ?>
                            <?php echo $dealUsersStr;?>
                        </td>

                          <?php if($isAutoDealTime):?>
                          <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'>
                              <?php  echo (isset($review->autoDealTime) && ($review->autoDealTime != '0000-00-00 00:00:00'))? $review->autoDealTime: '';?>
                          </td>
                          <?php endif;?>

                    </tr>
                <?php endforeach;?>
                <?php endif;?>
                </tbody>
            </table>
        </td>
    </tr>
<?php else:?>
   <tr>
        <td style='padding: 10px; border: none;'>
            <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
                <tr>
                    <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->startdept;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php if(($review->createdDept)) echo zget($deptMap,$review->createdDept,'')?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->creater;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$review->createdBy,'');?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $review->createdDate;?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->deadDate;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php
                        $endDate = date('Y-m-d', strtotime($review->endDate));
                        echo  $review->endDate != '0000-00-00 00:00:00' ? $endDate: '';?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->status;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->review->statusLabelList,$review->status,'');?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->dealUser;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                        <?php
                        $dealUsers = array_unique(explode(',', $review->dealUser));
                        if(!empty($dealUsers)){
                            foreach($dealUsers as $dealUser) {
                                echo zget($users,$dealUser,'').'&nbsp;';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php if($projectPlan):?>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->projectName;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $projectPlan->name;?></td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->projectType;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->projectplan->typeList, $projectPlan->type, '');?></td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->projectSource;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php
                            $basisList = explode(',', str_replace(' ', '', $projectPlan->basis));
                            foreach($basisList as $a) {
                                if(empty($a)) continue;
                                echo  zget($this->lang->projectplan->basisList, $a, '') . '<br>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->id;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $review->id;?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->title;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $review->title;?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->type;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->review->typeList,$review->type,'');?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->object;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php foreach($review->objects as $object){echo zget($this->lang->review->objectList,$object,'');};?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->grade;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->review->gradeList,$review->grade,'');?></td>
                </tr>
                <?php if($review->grade =='meeting'):?>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->meetingPlanTime;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php
                            if($review->meetingPlanTime != "0000-00-00 00:00:00"){
                                echo $review->meetingPlanTime;
                            }else{
                                echo  '';
                            }
                            ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->qapre;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$review->qa,'');?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->reviewer;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$review->reviewer,'');?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->owner;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$review->owner,'');?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->expert;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php
                        $experts = explode(',',trim($review->expert,','));

                        foreach($experts as $expert) {
                            if($review->type=='pro'){
                                $deptName = $this->loadModel('user')->getUserDeptName($expert)->deptName;
                                if(!empty($deptName)){
                                    echo $deptName."/".ltrim(zget($users,$expert,''),'').'&nbsp;';
                                }else{
                                    echo ltrim(zget($users,$expert,''),'').'&nbsp;';
                                }
                            }else{
                                echo ltrim(zget($users,$expert,''),'').'&nbsp;';
                            }

                        };?></td>

                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->reviewedBy;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php $reviewedBy = explode(',',$review->reviewedBy); foreach($reviewedBy as $reviewedBy) {
                            if(!empty($reviewedBy)){
                                $companyId =  $this->loadModel('user')->getUserDeptName($reviewedBy)->company;
                                $companyName = zget($companies,$companyId);
                                if(!empty($companyName)){
                                    echo $companyName."/".zget($users, $reviewedBy).'&nbsp;';
                                }else{
                                    echo zget($users, $reviewedBy).'&nbsp;';
                                }

                            }
                        };?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->outside;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php $outside = explode(',',$review->outside); foreach($outside as $outside) {
                            if(!empty($outside)){
                                $companyId =  $this->loadModel('user')->getUserDeptName($outside)->company;
                                $companyName = zget($companies,$companyId);
                                if(!empty($companyName)){
                                    echo $companyName."/".zget($outsideList2, $outside,'').'&nbsp;';
                                }else{
                                    echo zget($outsideList2, $outside,'').'&nbsp;';
                                }

                            }
                        };?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->relatedUsers;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php $relatedUsers = explode(',',$review->relatedUsers); foreach($relatedUsers as $relatedUsers) {echo ltrim(zget($users,$relatedUsers,''),'').'&nbsp;';};?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialDept;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $review->trialDept;?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialDeptLiasisonOfficer;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $review->trialDeptLiasisonOfficer;?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialAdjudicatingOfficer;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $review->trialAdjudicatingOfficer;?></td>
                </tr>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialJoinOfficer;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $review->trialJoinOfficer;?></td>
                </tr>
            </table>
        </td>
    </tr>
<?php endif;?>

<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
