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
<?php /*if($plan->status == 'yearpass' && $plan->changeStatus !== 'pending'):*/?><!--

<?php /*elseif ($plan->status == 'yearreject'):*/?>

<?php /*else:*/?>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php /*echo $this->lang->custommail->tips;*/?></legend>
            <div style='padding:5px;'><?php /*echo $mailConf->mailContent;*/?></div>
        </fieldset>
    </td>
</tr>
--><?php /*endif;*/?>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->created;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$plan->submitedBy,'');?></td>
        </tr>
        <?php if(!isset($planchange)): ?>
            <tr>
                <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->createTime;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $plan->createdDate;?></td>
            </tr>
        <?php endif; ?>

        <?php if(isset($planchange) && $planchange): ?>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->changeTime;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $planchange->createdDate;?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->insideName;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $plan->name;?></td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->type;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->projectplan->typeList,$plan->type,'');?></td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->source;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php
                $basisInfo = array_unique(explode(',', $plan->basis));
                if(!empty($basisInfo)){
                    foreach($basisInfo as $basis) {
                        echo zget($this->lang->projectplan->basisList,$basis,'').'&nbsp;';
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->dept;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php
                $deptInfo = array_unique(explode(',', $plan->bearDept));
                if(!empty($deptInfo)){
                    foreach($deptInfo as $dept) {
                        echo zget($depts,$dept,'').'&nbsp;';
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->owner;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>
                <?php
                $ownerInfo = array_unique(explode(',', $plan->owner));
                if(!empty($ownerInfo)){
                    foreach($ownerInfo as $owner) {
                        echo zget($users,$owner,'').'&nbsp;';
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->startTime;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $plan->begin;?></td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->endTime;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $plan->end;?></td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->isImport;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->projectplan->isImportantList,$plan->isImportant,'');?></td>
        </tr>
        <?php if(isset($planchange) && $planchange): ?>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->projectplan->mail->changeContent;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $planchange->planRemark;?></td>
        </tr>
        <?php endif; ?>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
