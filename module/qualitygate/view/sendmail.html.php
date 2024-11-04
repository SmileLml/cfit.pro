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
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->code;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->code;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->projectName;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->projectName;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->productName;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->productName; ?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->productCode;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->productCode;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->productVersion;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->productVersionTitle;?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->buildName;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->buildName;?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->buildStatus;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->build->statusListNew, $info->buildStatus);?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->status;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->qualitygate->statusList, $info->status);?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->createdBy;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users, $info->createdBy);?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->qualitygate->createdTime;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->createdTime;?></td>
            </tr>
        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
