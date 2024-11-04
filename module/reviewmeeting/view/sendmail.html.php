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
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->dealUser;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php
                $dealUsers = array_unique(explode(',', $meetingInfo->dealUser));
                if(!empty($dealUsers)){
                    foreach($dealUsers as $dealUser) {
                        echo zget($users,$dealUser,'').'&nbsp;';
                    }
                }
                ?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $meetingInfo->id; ?></td>
        </tr>

      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->meetingCode;?></th>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $meetingInfo->meetingCode; ?></td>
      </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->reviewOwner;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users, $meetingInfo->owner); ?></td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->reviewer;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users, $meetingInfo->reviewer); ?></td>
        </tr>

        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>
                <?php echo $this->lang->reviewmeeting->senMailStatus;?>
            </th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php echo zget($this->lang->reviewmeeting->sendMailStatusLabelList, $meetingInfo->status,'');?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->meetingPlanExport;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
               <?php if($meetingInfo->meetingPlanExport):
                   $meetingPlanExport = explode(',', $meetingInfo->meetingPlanExport);
                   foreach ($meetingPlanExport as $planExport):
                       echo zget($users, $planExport,'').'&nbsp;';
                   endforeach;
               endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->meetingContent;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($reviewTitleList):
                    echo implode('，', $reviewTitleList);
                endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeet->createdDept;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($createdDeptList):
                    $count = count($createdDeptList);
                    $maxKey = $count - 1;
                    foreach ($createdDeptList as  $key => $val):
                        echo zget($deptMap, $val,'');
                        if($key < $maxKey){
                            echo '，';
                        }
                    endforeach;
                endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeet->createdBy;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($createdByList):
                    $count = count($createdByList);
                    $maxKey = $count - 1;
                    foreach ($createdByList as  $key => $val):
                        echo zget($users, $val,'');
                        if($key < $maxKey){
                            echo '，';
                        }
                    endforeach;
                endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->projectManager;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($projectPmList):
                    $count = count($projectPmList);
                    $maxKey = $count - 1;
                    foreach ($projectPmList as  $key => $val):
                        echo zget($users, $val,'');
                        if($key < $maxKey){
                            echo '，';
                        }
                    endforeach;
                endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->deptLeads;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($managerList):
                    $count = count($managerList);
                    $maxKey = $count - 1;
                    foreach ($managerList as  $key => $val):
                        echo zget($users, $val,'');
                        if($key < $maxKey){
                            echo '，';
                        }
                    endforeach;
                endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->project;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($projectNameList):
                    echo implode('，', $projectNameList);
                endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->projectType;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($projectTypeList):
                    $count = count($projectTypeList);
                    $maxKey = $count - 1;
                    foreach($projectBasisList as $key => $val) {
                        if(empty($val)) continue;
                        echo  zget($this->lang->projectplan->typeList, $val, '');
                        if($key < $maxKey){
                            echo '，';
                        }
                    }
                endif;?>
            </td>
        </tr>

        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->reviewmeeting->projectSource;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php if($projectBasisList):
                    $count = count($projectBasisList);
                    $maxKey = $count - 1;
                    foreach($projectBasisList as $key => $val) {
                        if(empty($val)) continue;
                        echo  zget($this->lang->projectplan->basisList, $val, '');
                        if($key < $maxKey){
                            echo '，';
                        }
                    }
                endif;?>
            </td>
        </tr>


     </table>
  </td>
</tr>

<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
