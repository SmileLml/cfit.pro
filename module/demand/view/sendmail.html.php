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
<?php if($demand->status != 'deleted'):?>
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
            <div style='padding:5px;'><?php echo $mailContentConfig;?></div>
        </fieldset>
    </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->id;?></td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->code;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->code;?></td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->dealUser;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->newDealUser;?></td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->status;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->statusCN;?></td>
        </tr>
        <tr>
            <th style="width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->demand->title; ?></th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $demand->title;?></td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $bestDate;?></td>
        </tr>
        <?php if($demand->delay == 'true'):?>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->originalResolutionDate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo date('Y-m-d', strtotime($demand->originalResolutionDate));?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->delayResolutionDate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo date('Y-m-d', strtotime($demand->delayResolutionDate));?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->delayReason;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->delayReason;?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->delayEmailStatus;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->demand->delayStatusList,$demand->delayStatus,'');?></td>
            </tr>
        <?php endif;?>

    </table>
  </td>
</tr>
<?php else:?>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $this->lang->demand->deleteMaile;?></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding-top: 20px; border: none;'>
        <p style='font-size: 18px;font-weight: 500;padding-left:15px;'><?php echo $this->lang->demand->tipmail;?></p>
    </td>
</tr>

<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->id;?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->code;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->code;?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->dealUser;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->newDealUser;?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->status;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $demand->statusCN;?></td>
            </tr>
            <tr>
                <th style="width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->demand->title; ?></th>
                <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $demand->title;?></td>
            </tr>
            <tr>
                <th style="width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->demand->desc; ?></th>
                <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $demand->desc;?></td>
            </tr>
<!--            <tr>-->
<!--                <th style="width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;">--><?php //echo $this->lang->demand->thisRemarks; ?><!--</th>-->
<!--                <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" >--><?php //echo $demand->thisRemarks;?><!--</td>-->
<!--            </tr>-->

        </table>
    </td>
</tr>
<?php endif;?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
