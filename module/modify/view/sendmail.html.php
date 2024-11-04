<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<style>
    .textOVerThree {
        display: -webkit-box;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp:3;
        max-height: 9em;
        line-height: 3em;
        -webkit-box-orient: vertical
    }
</style>
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
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $modify->id;?></td>
        </tr>
        <tr>
            <th style='width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->$browseType . $this->lang->custommail->code;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5; text-align: left;'>
                <?php
                $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('modify', 'view', "id=$modify->id", 'html');
                echo html::a($detailsURL, $modify->code, '', 'style="color:blue;"');
                ?>
            </td>
        </tr>
        <tr>
            <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->custommail->summary; ?></th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $modify->desc;?></td>
        </tr>
        <tr>
          <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->custommail->flowStatus; ?></th>
          <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo zget($this->lang->modify->statusList, $modify->status);?></td>
        </tr>
        <tr>
          <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->custommail->dealUser; ?></th>
          <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $modify->dealUser;?></td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $bestDate;?></td>
        </tr>
        <?php if($isupdatestatus == 1):?>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->result; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo zget($this->lang->modify->changeStatusList, $modify->changeStatus, ''); ?></td>
            </tr>
<!--        外部单号-->
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->externalCode; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->externalCode; ?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->reviewComment; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->returnReason;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->changeRemark; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->changeRemark;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->actualBegin; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->realStartTime;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->actualEnd; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->realEndTime;?></td>
            </tr>
        <?php endif;?>
        <?php if($isupdateapprove == 1):?>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->result; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo zget($this->lang->modify->changeStatusList, $modify->changeStatus, ''); ?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->implementers; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->implementers;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->implementDepartment; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->implementDepartment;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->implementStartTime; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->implementStartTime;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->implementEndTime; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->implementEndTime;?></td>
            </tr>
        <?php endif;?>
        <?php if($isfeedback):?>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->feedBackId; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->feedbackId;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->operationName; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->operateName;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->feedBackOperationType; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->operateType;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->feedbackresult; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->implementResult;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->actualBegin; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->startTime;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->actualEnd; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->endTime;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->depOddName; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->code;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->supply; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->supportUserName;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->changeNum; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->externalCode;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->operationStaff; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->operateUserName;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->problemDescription; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->issueDesc;?></td>
            </tr>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->modify->resolveMethod; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $modify->resolveMethod;?></td>
            </tr>

        <?php endif;?>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
