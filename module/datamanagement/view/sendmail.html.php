<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<style>
    .infoName {
        width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;text-align: center;
    }
    .infoContent{
        padding: 5px; border: 1px solid #e5e5e5;
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
<?php if($mailType=='remind'):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
        <tr>
            <th class="infoName"><?php echo $this->lang->datamanagement->code;?></th>
            <td class="infoContent"><?php echo $datamanagement->code?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->datamanagement->desc;?></th>
            <td class="infoContent"><?php echo $datamanagement->desc?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->datamanagement->useDeadline;?></th>
            <td class="infoContent"><?php echo $datamanagement->useDeadline ?></td>
        </tr>
    </table>
  </td>
</tr>
<?php elseif($mailType=='destroyed'):?>
    <tr>
        <td style='padding: 10px; border: none;'>
            <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->code;?></th>
                    <td class="infoContent"><?php echo $datamanagement->code?></td>
                </tr>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->desc;?></th>
                    <td class="infoContent"><?php echo $datamanagement->desc?></td>
                </tr>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->destroyedReason;?></th>
                    <td class="infoContent"><?php echo $datamanagement->destroyedReason ?></td>
                </tr>
            </table>
        </td>
    </tr>
<?php elseif($mailType=='destroyreviewing'):?>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th class="infoName"><?php echo $this->lang->datamanagement->code;?></th>
                <td class="infoContent"><?php echo $datamanagement->code?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->datamanagement->desc;?></th>
                <td class="infoContent"><?php echo $datamanagement->desc?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->datamanagement->destroyedReason;?></th>
                <td class="infoContent"><?php echo $datamanagement->destroyedReason ?></td>
            </tr>
        </table>
    </td>
</tr>
<?php elseif($mailType=='delayed' or $mailType=='delay'):?>
    <tr>
        <td style='padding: 10px; border: none;'>
            <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->code;?></th>
                    <td class="infoContent"><?php echo $datamanagement->code?></td>
                </tr>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->desc;?></th>
                    <td class="infoContent"><?php echo $datamanagement->desc?></td>
                </tr>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->delayReason;?></th>
                    <td class="infoContent"><?php echo $datamanagement->delayReason ?></td>
                </tr>
                <?php if($mailType=='delayed'): ?>
                    <tr>
                        <th class="infoName"><?php echo $this->lang->datamanagement->reviewOpinion;?></th>
                        <td class="infoContent"><?php echo $datamanagement->reviewOpinion ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </td>
    </tr>
<?php elseif($mailType=='syncstatus'):?>
<tr>
    <td style='padding: 10px; border: none;'>
        <?php echo $datamanagement->message;?>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th class="infoName"><?php echo $this->lang->datamanagement->code;?></th>
                <td class="infoContent"><?php echo $datamanagement->code?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->datamanagement->desc;?></th>
                <td class="infoContent"><?php echo $datamanagement->desc?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->datamanagement->reason;?></th>
                <td class="infoContent"><?php echo $datamanagement->reason ?></td>
            </tr>
            <?php if($datamanagement->status=='destroyed'):?>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->destroyedReason;?></th>
                    <td class="infoContent"><?php echo $datamanagement->destroyedReason ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </td>
</tr>
<?php elseif($mailType=='destroy' or $mailType=='destroyreviewed'):?>
    <tr>
        <td style='padding: 10px; border: none;'>
            <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->code;?></th>
                    <td class="infoContent"><?php echo $datamanagement->code?></td>
                </tr>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->desc;?></th>
                    <td class="infoContent"><?php echo $datamanagement->desc?></td>
                </tr>
                <tr>
                    <th class="infoName"><?php echo $this->lang->datamanagement->destroyedReason;?></th>
                    <td class="infoContent"><?php echo $datamanagement->destroyedReason ?></td>
                </tr>
                <?php if($mailType=='destroyreviewed' and $datamanagement->isResult == 'reject'):?>
                    <tr>
                        <th class="infoName"><?php echo $this->lang->datamanagement->rejectReason;?></th>
                        <td class="infoContent"><?php echo $datamanagement->rejectReason ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </td>
    </tr>
<?php endif;?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
