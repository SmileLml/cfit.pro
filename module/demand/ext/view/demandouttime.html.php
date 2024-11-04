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
    <?php $this->app->loadLang('demand'); ?>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
      <tr>
        <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->code;?></th>
        <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $demand->code;?></td>
      </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->title;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $demand->title;?></td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->end;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo $demand->end;?></td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo '当前' . $this->lang->demand->status;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->demand->statusList, $demand->status, $demand->status);?></td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->demand->desc;?></th>
            <td style='padding: 20px 20px 20px 10px; border: 1px solid #e5e5e5;'><?php echo $demand->desc;?></td>
        </tr>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
