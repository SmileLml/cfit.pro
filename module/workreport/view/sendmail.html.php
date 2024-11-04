<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<style>
    .textOVerThree {
        display: -webkit-box;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp:3;
        max-height: 9em;
        line-height: 3em;
        -webkit-box-orient: vertical
    }
</style>

<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->custommail->tips;?></legend>
            <div style='padding:5px;'><?php echo $mailConf->mailContent ;?></div>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <?php if($flag == 1): ?>
                <tr>
                 <!-- <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'></th>-->
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo sprintf($this->lang->workreport->weeklyReport,isset($total->allConsumed) ? round($total->allConsumed,2) : 0);?></td>
                </tr>
            <?php else: ?>
                <tr>
                  <!-- <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'></th>-->
                    <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo sprintf($this->lang->workreport->monthReport,$year,$month,isset($total->allConsumed) ? round($total->allConsumed,2) : 0);?></td>
                </tr>
            <?php endif; ?>
        </table>
    </td>
</tr>

