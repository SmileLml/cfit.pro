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
      <legend style='color: #114f8e'><?php echo $lang->custommail->tips;?></legend>
      <div style='padding:5px;'>
      <?php echo htmlspecialchars_decode($mailConf->mailContent); ?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
    <?php $this->app->loadLang('problem'); ?>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
      <tr>
        <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->problem->code;?></th>
        <td style='padding: 10px; border: 1px solid #e5e5e5;'>CFIT-Q-20230609-01</td>
      </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->problem->abstract;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'>这是问题摘要</td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->problem->createdDate;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'>2023-07-01 00:00:00</td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->problem->outsideFeedbackDate;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'>2023-07-01 00:00:00</td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->problem->status;?></th>
            <td style='padding: 10px; border: 1px solid #e5e5e5;'>开发中</td>
        </tr>
        <tr>
            <th style='width: 150px;text-align: center; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->problem->desc;?></th>
            <td style='padding: 20px; border: 1px solid #e5e5e5;'>AAAAAAAAAAA</td>
        </tr>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
