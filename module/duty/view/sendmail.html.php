<?php
/**
 * The sendmail file of duty module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     duty
 * @version     $Id: sendmail.html.php 3717 2020-11-03 15:35:07Z  tianshujie@easycorp.ltd $
 * @link        https://www.zentao.net
 */
?>
<?php $mailTitle = $this->lang->duty->common . ' #' . $duty->id . ' ';?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'>
                    <?php $color = empty($duty->color) ? '#333' : $duty->color;?>
                    <?php echo html::a(zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('duty', 'view', "processID=$duty->id", 'html'), $mailTitle, '', "style='color: {$color}; text-decoration: underline;'");?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->duty->user;?></legend>
            <div style='padding: 5px;'><?php $userName = ''; foreach(explode(',', $duty->user) as $account) $userName .= zget($users, $account, '') . ' ';?><?php echo $userName;?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->duty->type;?></legend>
            <div style='padding: 5px;'><?php echo zget($this->lang->duty->typeList, $duty->type, '');?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->duty->occurDate;?></legend>
            <div style='padding: 5px;'><?php echo $duty->occurDate;?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->duty->desc;?></legend>
            <div style='padding: 5px;'><?php echo $duty->desc;?></div>
        </fieldset>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>

