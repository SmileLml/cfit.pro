<?php $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables); ?>
<?php
//$this->app->company->name = $mailTitle;
?>
<?php include $this->app->getModuleRoot() .
    "common/view/mail.header.html.php"; ?>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $mailTitle; ?></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $lang->custommail
                    ->tips; ?></legend>
            <div style='padding:5px;'>
                <?php echo $mailConf->mailContent; ?>
            </div>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='display:none;width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->idAB; ?></th>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang
                        ->custommail->reviewName; ?></th>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang
                        ->custommail->closingDate; ?></th>
            </tr>
            <tr>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo 888; ?></td>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                    <?php
                    $link = "javascript:void(0);";
                    echo html::a($link, "code_示例评审标题", "", 'style="color:blue;"');
                    ?>
                </td>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo "2021-01-01"; ?></td>
            </tr>
        </table>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->startdept;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例id</td>
            </tr>

            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->creater;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审议题</td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审时间</td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->enddate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例参会人员</td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->status;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例无需参会人员</td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->dealUser;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审内容概述及评审要点</td>
            </tr>
        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . "common/view/mail.footer.html.php"; ?>
