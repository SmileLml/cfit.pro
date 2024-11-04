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
<?php if($problem->status != 'closed'||($problem->status == 'closed' &&($action->action == 'problemchange' || $action->action == 'problemreviewchange'))): ?>
    <tr>
      <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
          <tr>
            <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $problem->changeStatus == 'success' ? $this->lang->problem->delayMaile :$mailTitle;?></td>
          </tr>
        </table>
      </td>
    </tr>
<?php endif; ?>
<tr>
    <td style='padding: 10px; border: none;'>
       <fieldset style='border: 1px solid #e5e5e5'>
          <legend style='color: #114f8e'><?php echo $this->lang->custommail->tips;?></legend>
          <div style='padding:5px;'><?php echo ($problem->status != 'closed'||($problem->status == 'closed' &&($action->action == 'problemchange' || $action->action == 'problemreviewchange'))) ?  $mailConf->mailContent : $this->lang->problem->clostTip;?></div>
       </fieldset>
    </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
        <?php if($problem->status != 'closed'||($problem->status == 'closed' &&($action->action == 'problemchange' || $action->action == 'problemreviewchange'))): ?>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $problem->id;?></td>
            </tr>
            <tr>
                <th style='width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->$browseType . $this->lang->custommail->code;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5; text-align: left;'>
                    <?php
                    $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('problem',  'view', "id=$problem->id", 'html');
                    echo html::a($detailsURL, $problem->code, '', 'style="color:blue;"');
                    ?>
                </td>
            </tr>
            <tr>
                <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->custommail->summary; ?></th>
                <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $problem->abstract;?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $bestDate;?></td>
            </tr>
            <?php if (!empty($problem->IssueId) and $problem->ReviewStatus == 'externalsendback'): ?>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->reviewOpinion;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $problem->ReasonOfIssueRejecting;?></td>
            </tr>
            <?php endif;?>
            <?php if(isset($problem->delay) && $problem->delay == 'true'):?>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->originalResolutionDate;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo date('Y-m-d', strtotime($problem->originalResolutionDate));?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->delayResolutionDate;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo date('Y-m-d', strtotime($problem->delayResolutionDate));?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->delayReason;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $problem->delayReason;?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->delayUser;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$problem->delayUser,'');?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo sprintf($this->lang->problem->delayStatus,$this->lang->problem->delayName);?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo sprintf(zget($this->lang->problem->delayStatusList,$problem->delayStatus,''),$this->lang->problem->delayName);?></td>
                </tr>
            <?php endif;?>

            <?php if(isset($problem->change) && $problem->change == 'true'):?>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->changeOriginalResolutionDate;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo date('Y-m-d H:i:s', strtotime($problem->changeOriginalResolutionDate));?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->changeResolutionDate;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo date('Y-m-d H:i:s', strtotime($problem->changeResolutionDate));?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->changeReason;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $problem->changeReason;?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->changeCommunicate;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $problem->changeCommunicate;?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->baseChangeContent;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $problem->changeContent;?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->changeUser;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$problem->changeUser,'');?></td>
                </tr>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->changeStatus;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo sprintf(zget($this->lang->problem->delayStatusList,$problem->changeStatus,''),$this->lang->problem->changeName);?></td>
                </tr>
            <?php endif;?>

        <?php if($problem->status == 'returned'):?>
                <tr>
                    <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->problem->ReasonOfIssueRejecting;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $problem->ReasonOfIssueRejecting;?></td>
                </tr>
        <?php endif;?>
        <?php else: ?>
            <tr>
                <th style='width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->$browseType . $this->lang->custommail->code;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5; text-align: left;'>
                    <?php
                    $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('problem',  'view', "id=$problem->id", 'html');
                    echo html::a($detailsURL, $problem->code, '', 'style="color:blue;"');
                    ?>
                </td>
            </tr>
            <tr>
                <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->problem->abstract; ?></th>
                <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $problem->abstract;?></td>
            </tr>
            <tr>
                <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->custommail->closer; ?></th>
                <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo zget($users,$problem->closedBy);?></td>
            </tr>
            <tr>
                <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->custommail->closeTime; ?></th>
                <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo $problem->closedDate;?></td>
            </tr>
        <?php endif; ?>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
