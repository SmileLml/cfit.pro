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
                <?php echo $mailConf->mailContent;?>
            </div>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->productionchange->code;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $preproduction->code;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->productionchange->dealUser;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zmget($users, $preproduction->dealUser, ''); ?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->productionchange->status;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->productionchange->statusList, $preproduction->status);?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->productionchange->createdBy;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users, $preproduction->createdBy, ''); ?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->productionchange->createdDate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $preproduction->createdDate;?></td>
            </tr>

        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
