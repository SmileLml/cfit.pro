<?php $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);?>
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
      <div style='padding:5px;'>
      <?php echo htmlspecialchars_decode($mailConf->mailContent); ?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
    <?php $this->app->loadLang('requirement'); ?>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
      <tr>
        <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->code;?></th>
        <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $requirement->code;?></td>
      </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->name;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $requirement->name;?></td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->acceptTime;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $requirement->acceptTime;?></td>
        </tr>
        <?php if($requirement->inOrout == 'in'):?>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->insideFeedback;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $requirement->feekBackEndTimeInside;?></td>
        </tr>
        <?php endif;?>
        <?php if($requirement->inOrout == 'out'):?>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->outsideFeedback;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $requirement->feekBackEndTimeOutSide;?></td>
        </tr>
        <?php endif;?>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo '当前' . $this->lang->requirement->status;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->requirement->statusList, $requirement->status, $requirement->status);?></td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->desc;?></th>
            <td style='padding: 20px 20px 20px 10px; border: 1px solid #e5e5e5;'><?php echo $requirement->desc;?></td>
        </tr>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
