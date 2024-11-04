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
<?php $mailTitle = $this->lang->requirement->common . ' #' . $requirement->id . ' ' . $requirement->name; ?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php'; ?>
<?php if($requirement->status != 'deleted' && $requirement->status != 'deleteout'):?>
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
                    <?php if($requirement->sourceRequirement == 1):?>
                    <?php
                    $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('requirement', 'view', "id=$requirement->id", 'html');
                    echo html::a($detailsURL, $mailTitle, '', 'style="color:blue;"');
                    ?>
                    <?php endif;?>
                    <?php if($requirement->sourceRequirement == 2):?>
                    <?php
                    $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('requirementinside', 'view', "id=$requirement->id", 'html');
                    echo html::a($detailsURL, $mailTitle, '', 'style="color:blue;"');
                    ?>
                    <?php endif;?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->id; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->id; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->code; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->code; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->opinionName; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->opinionName; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->name; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->name; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->pending; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->pending; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->status; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->statusChn; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->startDate; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->createdDate; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->union; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->union; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->date; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->date; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->endDate; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->deadLine; ?></div>
        </fieldset>
        <?php if (!empty($requirement->feedbackStatus) and $requirement->feedbackStatus != '' and $requirement->status != 'topublish'): ?>
            <fieldset style='border: 1px solid #e5e5e5'>
                <legend style='color: #114f8e'><?php echo $this->lang->requirement->feedbackStatus; ?></legend>
                <div style='padding: 5px;'><?php echo $requirement->feedbackStatusChn; ?></div>
            </fieldset>
        <?php endif;?>
        <?php if (!empty($requirement->feedbackStatus) and $requirement->feedbackStatus != '' and ($requirement->feedbackStatus == 'feedbacksuccess' or $requirement->feedbackStatus == 'feedbackfail')) : ?>
            <fieldset style='border: 1px solid #e5e5e5'>
                <legend style='color: #114f8e'><?php echo $this->lang->requirement->extApproveComm; ?></legend>
                <div style='padding: 5px;'><?php echo $requirement->reviewComments; ?></div>
            </fieldset>
        <?php endif;?>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php'; ?>
<?php elseif($requirement->status == 'deleteout'):?>

    <tr>
        <td>
            <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $this->lang->requirement->deleteOutMaile;?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style='padding-top: 20px; border: none;'>
            <p style='font-size: 13px;font-weight: 500;padding-left:15px;'><?php echo $this->lang->requirement->deleteOutTip;?></p>
        </td>
    </tr>

    <tr>
        <td style='padding: 10px; border: none;'>
            <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
                <tr>
                    <fieldset style='border: 1px solid #e5e5e5'>
                        <legend style='color: #114f8e'><?php echo '';?></legend>
                        <div style='padding: 5px;'><?php echo helper::now().' 由清算总中心删除需求任务： <br />'.$requirement->code.' '.$requirement->name.'<br />详情请登录研发过程管理平台进行处理~';?></div>
                    </fieldset>
                </tr>
            </table>
        </td>
    </tr>

<?php else: ?>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->deleteMaile;?></legend>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->idAB; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->id; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->code; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->code; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->pending; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->pending; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->status; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->statusChn; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->name; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->name; ?></div>
        </fieldset>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->requirement->desc; ?></legend>
            <div style='padding: 5px;'><?php echo $requirement->desc; ?></div>
        </fieldset>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php'; ?>
<?php endif;?>

