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
            <legend style='color: #114f8e'><?php echo $this->lang->residentwork->desc;?></legend>
            <div style='padding:5px;'>    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->dutyPlace;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $area[$work->area]?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->actualLeader;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $users[$work->groupLeader]?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->actualUser;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php
                            if(!empty($realDutyuser)){
                                foreach($realDutyuser as $user) {
                                    echo zget($users,$user,'').'&nbsp;';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentsupport->dateType;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $dateTypeList[$work->dateType]?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->isEmergency;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php
                            if ($work->isEmergency == 1){
                                echo "是";
                                $remark = $work->remark;
                            }else{
                                echo "否";
                                $remark = "";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->emergencyRemark;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $remark;?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->desc;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $work->logs;?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->warnLogs;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $work->warnLogs;?>
                        </td>
                    </tr>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->analysis;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $work->analysis;?>
                        </td>
                    </tr>
                    <?php if ($work->type == 1){?>
                    <tr>
                        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->residentwork->logPushTitle;?></th>
                        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                            <?php echo $this->lang->residentwork->logPushStatusArray[$work->pushStatus];?>
                        </td>
                    </tr>
                    <?php }?>
                    <tr>
                        <td colspan="2" style="padding: 5px;background-color: #fff0d5">
                            <span style="display: inline-block;width:10px;height: 10px;border-radius: 50%;background-color: #f1a325"></span>
                            <span>&nbsp;&nbsp;
                                <?php if ($work->editedBy != ''){
                                    echo $work->editedDate."，由<b>".$users[$work->editedBy]."</b>编辑";
                                }else{
                                    echo $work->createdDate."，由<b>".$users[$work->createdBy]."</b>创建";

                                }
                                ?>
                </span>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
    </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
  </td>
</tr>

<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
