<?php
/**
 * The mail file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     task
 * @version     $Id: sendmail.html.php 867 2010-06-17 09:32:58Z yuren_@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php $mailTitle = $mailConf['mailTitle'];?>
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
      <legend style='color: #114f8e'><?php echo '提示';?></legend>
      <div style='padding:5px;'>
      <?php echo $mailConf['mailContent'];?>
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
      <?php foreach($historyList as $history):?>
      <tr>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $history->ldapAccount;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = 'javascript:void(0);';
        echo zget($this->lang->ldap->syncResultList ,$history->result, '');
        ?>
        </td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $history->addTime;?></td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
