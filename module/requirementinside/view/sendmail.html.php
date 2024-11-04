<?php
/**
 * The sendmail file of requirement module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     requirement
 * @version     $Id: sendmail.html.php 3717 2020-11-03 15:35:07Z  tianshujie@easycorp.ltd $
 * @link        https://www.zentao.net
 */
?>
<?php $mailTitle = $this->lang->requirementinside->common . ' #' . $requirement->id . ' ' . $requirement->name; ?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php'; ?>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->custommail->tips;?></legend>
            <div style='padding:5px;'><?php echo $mailConf->mailContent;?></div>
        </fieldset>
    </td>
</tr>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'>
                    <?php
                    $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('requirementinside', 'view', "id=$requirement->id", 'html');
                    echo html::a($detailsURL, $mailTitle, '', 'style="color:blue;"');
                    ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->id; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->id; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->code; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->code; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->opinionName; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->opinionName; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->name; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->name; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->pending; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->pending; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->status; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->statusChn; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->startDate; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->createdDate; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->union; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->union; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->date; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->date; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirementinside->endDate; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->deadLine; ?></div>
        </fieldset>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php'; ?>
