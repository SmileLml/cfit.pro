<?php
/**
 * The sendmail file of opinion module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     opinion
 * @version     $Id: sendmail.html.php 3717 2020-11-03 15:35:07Z  tianshujie@easycorp.ltd $
 * @link        https://www.zentao.net
 */
?>
<?php $mailTitle = $this->lang->opinion->common . ' #' . $opinion->id . ' ' . $opinion->name;?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
      <tr>
        <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'>
          <?php $color = empty($opinion->color) ? '#333' : $opinion->color;?>
          <?php echo html::a(zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('opinion', 'view', "opinionID=$opinion->id", 'html'), $mailTitle, '', "style='color: {$color}; text-decoration: underline;'");?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'><?php echo $this->lang->opinion->background;?></legend>
      <div style='padding: 5px;'><?php echo $opinion->background;?></div>
    </fieldset>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'><?php echo $this->lang->opinion->overview;?></legend>
      <div style='padding: 5px;'><?php echo $opinion->overview;?></div>
    </fieldset>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'><?php echo $this->lang->opinion->desc;?></legend>
      <div style='padding: 5px;'><?php echo $opinion->desc;?></div>
    </fieldset>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
