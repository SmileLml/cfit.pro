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
<?php
$deptInfo  = $info->deptIds;
$deptIds = explode(',', $info->deptIds);
if(!empty($deptIds)){
    $tempData = [];
    foreach ($deptIds as $deptId){
        $deptName = trim(zget($deptList, $deptId), '/');
        $tempData[] = $deptName;
    }
    $deptInfo =  implode(',', $tempData);;
}?>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->code;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->code;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->startDate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->startDate;?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->endDate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->endDate;?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->area;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->localesupport->areaList, $info->area);?></td>
            </tr>


            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->appIds;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zmget($appList, $info->appIds);?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->deptIds;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $deptInfo;?></td>
            </tr>



            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->supportUsers;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zmget($users, $info->supportUsers);?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->manufacturer;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->manufacturer;?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->reason;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->reason;?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->remark;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->remark;?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->createdBy;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users, $info->createdBy, ''); ?></td>
            </tr>
            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->createdDept;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo trim(zget($deptList, $info->createdDept, ''), '/'); ?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->createdTime;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $info->createdTime;?></td>
            </tr>

            <tr>
                <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->status;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->localesupport->statusList, $info->status);?></td>
            </tr>

            <?php if($info->dealUsers):?>
                <tr>
                    <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->localesupport->dealUsers;?></th>
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zmget($users, $info->dealUsers);?></td>
                </tr>
            <?php endif;?>

        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
