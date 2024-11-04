<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<style>
    .infoName {
        width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;text-align: center;
    }
    .infoContent{
        padding: 5px; border: 1px solid #e5e5e5;
    }
</style>
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
            <div style='padding:5px;'><?php echo $mailConf->mailContent;?></div>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th class="infoName"><?php echo $this->lang->sectransfer->id ;?></th>
                <td class="infoContent"><?php echo $sectransfer->id?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->sectransfer->protransferDesc;?></th>
                <td class="infoContent"><?php echo $sectransfer->protransferDesc?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->sectransfer->status;?></th>
                <td class="infoContent"><?php echo zget($this->lang->sectransfer->statusListName,$sectransfer->status)?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->sectransfer->approver;?></th>
                <td class="infoContent"><?php echo zget($users,$sectransfer->approver);?></td>
            </tr>
        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
