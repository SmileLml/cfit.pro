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
            <div style='padding:5px;'><?php echo $mailConf->mailContent;?></div>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->id;?></td>
            </tr>
            <tr>
                <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->code; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->code;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->opinionName; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->nameByOpinion;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->entriesName; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->name;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->pending; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->dealUserCopy;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->status;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->requirement->statusList, $requirement->status);?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->startDate ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->createdDate;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->union ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->union;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->date   ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->dateByOpinion;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->deadLine; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->dateByOpinion;?></td>
            </tr>
            <?php if($requirement->entriesCode  and $requirement->status != 'topublish'):?>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->feedbackStatus  ;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->feedbackStatus;?></td>
                </tr>
            <?php endif;?>
            <?php if($requirement->entriesCode  and $requirement->reviewComments):?>
                <tr>
                    <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->requirement->extApproveComm  ;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $requirement->reviewComments;?></td>
                </tr>
            <?php endif;?>
        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
