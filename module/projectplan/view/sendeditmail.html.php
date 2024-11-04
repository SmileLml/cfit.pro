<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
      <tr>
        <td  style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $mailTitle;?></td>
      </tr>
    </table>
  </td>
</tr>
<?php if(isset($oldPlan)):?>
    <tr>
        <td style='padding: 10px; border: none;'>
            <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
    <tr>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>年度计划名称</th>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $plan->name;?></td>
    </tr>
    <tr>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>修改人</th>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $this->app->user->realname;?></td>
    </tr>
    <tr>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'>修改时间</th>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo date("Y-m-d",time());?></td>
    </tr>
    <tr>
        <td style='padding: 10px; border: none;' colspan="2">
            <fieldset style='border: 1px solid #e5e5e5'>
                <legend style='color: #114f8e'>修改内容</legend>
                <div style='padding:5px;'><?php echo $editmark;?></div>
            </fieldset>
        </td>
    </tr>
            </table>
        </td>
    </tr>

<?php endif; ?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
