<?php $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);?>
<?php $this->app->loadLang('issue');//$this->app->company->name = $mailTitle;?>
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
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo 22;?></td>
        </tr>
        <tr>
            <th style='width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->issue ;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5; text-align: left;'>issue</td>
        </tr>
        <tr>
            <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->custommail->summary; ?></th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" >主题</td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->issue->pri;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>优先级</td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->issue->severity;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>严重程度</td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->issue->assignedTo;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>、指派给</td>
        </tr>
        <tr>
            <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->issue->createdDate;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>2023-04-01</td>
        </tr>
        </table>
    </td>
</tr>
<?php include '../../common/view/footer.html.php';?>