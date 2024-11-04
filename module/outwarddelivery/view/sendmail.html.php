<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<style>
    .textOVerThree {
        word-break: break-all;
        display: -webkit-box;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp:3;
        max-height: 9em;
        line-height: 3em;
        -webkit-box-orient: vertical;
        white-space: normal;
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
        <tr >
            <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                <?php echo $this->lang->idAB; ?>
            </th>
            <td style="padding:7px;border:1px solid #e5e5e5;" >
                <?php echo $outwarddelivery->id;?>
            </td>
        </tr>
        <tr>
            <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                <?php echo $this->lang->custommail->$browseType . $this->lang->custommail->code;?>
            </th>
            <td style="padding:7px;border:1px solid #e5e5e5;" >
                <?php
                $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('outwarddelivery', 'view', "id=$outwarddelivery->id", 'html');
                echo html::a($detailsURL, $outwarddelivery->code, '', 'style="color:blue;"');
                ?>
            </td>
        </tr>

        <tr >
            <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                <?php echo $this->lang->custommail->summary; ?>
            </th>
            <td style="width:490px;padding:7px;border:1px solid #e5e5e5;" class="textOVerThree"><?php echo $outwarddelivery->outwardDeliveryDesc;?></td>
        </tr>

        <tr >
            <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                <?php echo $this->lang->custommail->flowStatus; ?>
            </th>
            <td style="padding:7px;border:1px solid #e5e5e5;" >
                <?php echo $outwarddelivery->statusDesc;?>
            </td>
        </tr>

        <tr >
            <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                <?php echo $this->lang->custommail->dealUser; ?>
            </th>
            <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $outwarddelivery->dealUsersStr;?></td>
        </tr>

        <tr >
            <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                <?php echo $this->lang->custommail->initiationTime;?>
            </th>
            <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $bestDate;?></td>
        </tr>

        <?php if($isShowExternalStatus):?>
            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->custommail->externalStatus; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $outwarddelivery->externalStatusDesc;?></td>
            </tr>

            <tr >
                <th style="width: 140px;padding:7px;background:#f5f5f5;border:1px solid #e5e5e5;">
                    <?php echo $this->lang->custommail->externalRejectReason; ?>
                </th>
                <td style="padding:7px;border:1px solid #e5e5e5;" ><?php echo $outwarddelivery->externalRejectReason;?></td>
            </tr>

        <?php endif;?>
    </table>
  </td>
</tr>

<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
