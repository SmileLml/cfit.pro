<?php $mailTitle = $mailConf->mailTitle;?>
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
      <?php echo $mailConf->mailContent;?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
      <tr>
        <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo 'LDAP账号';?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo '同步结果';?></th>
        <th style='width: 160px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo '同步时间';?></th>
      </tr>
      <tr>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo 'zhangsan';?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = 'javascript:void(0);';
        echo '同步新增用户成功';
        ?>
        </td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo '2022-10-24 10:44:44';?></td>
      </tr>
      <tr>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo 'yu li';?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = 'javascript:void(0);';
        echo 'yu li，因为用户名不合法，添加失败！，请修改用户名后再添加';
        ?>
        </td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo '2022-10-24 10:55:55';?></td>
      </tr>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
