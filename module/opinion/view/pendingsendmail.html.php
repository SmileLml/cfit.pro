<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<?php if($opinion->status != 'deleted' && $opinion->status != 'deleteout'):?>
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
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->id;?></td>
            </tr>
            <tr>
                <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->code; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->code;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->name; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->name;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->dealUser; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->dealUserCopy;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->status; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->opinion->statusList, $opinion->status);?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->startedDate ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->createdDate;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->union ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->unionCopy;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->date   ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->date;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->deadline ; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->deadline;?></td>
            </tr>
        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
<?php elseif($opinion->status == 'deleteout'):?>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $this->lang->opinion->deleteOutMaile;?></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding-top: 20px; border: none;'>
        <p style='font-size: 13px;font-weight: 500;padding-left:15px;'><?php echo $this->lang->opinion->deleteOutTip;?></p>
    </td>
</tr>

<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <fieldset style='border: 1px solid #e5e5e5'>
                    <legend style='color: #114f8e'><?php echo '';?></legend>
                    <div style='padding: 5px;'><?php echo helper::now().' 由清算总中心删除需求意向： <br />'.$opinion->code.' '.$opinion->name.'<br />详情请登录研发过程管理平台进行处理~';?></div>
                </fieldset>
            </tr>
        </table>
    </td>
</tr>
<?php else: ?>
<tr>
    <td>
        <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $this->lang->opinion->deleteMaile;?></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style='padding-top: 20px; border: none;'>
        <p style='font-size: 18px;font-weight: 500;padding-left:15px;'><?php echo $this->lang->opinion->tipmail;?></p>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->id;?></td>
            </tr>
            <tr>
                <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->code; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->code;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->dealUser; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->dealUserCopy;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->status; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->opinion->statusList, $opinion->status);?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->name; ?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->name;?></td>
            </tr>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->opinion->overview ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $opinion->overview;?></td>
            </tr>
        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
<?php endif;?>
