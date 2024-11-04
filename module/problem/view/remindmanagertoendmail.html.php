<?php include $this->app->getModuleRoot() . 'common/view/mail.list.header.html.php';?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' width='900' style='border: none; border-collapse: collapse;'>
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
      <?php echo $this->lang->problem->remindToEndMailContent; ?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
    <?php $this->app->loadLang('problem'); ?>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' class='table table-bordered'>
        <thead>
          <tr>
              <th style='width: 100px;'><?php echo  $this->lang->problem->acceptUser; ?></th>
              <th style='width: 150px;'><?php echo  $this->lang->problem->code; ?></th>
              <th style='width: 150px;'><?php echo  $this->lang->problem->statusName; ?></th>
              <th style='width: 350px;'><?php echo  $this->lang->problem->abstractName;?></th>
              <th style='width: 150px;'><?php echo  $this->lang->problem->PlannedTimeOfChange;?></th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach ($data as $problem):
            ?>
            <tr>
                <td title='<?php echo $problem->acceptUserName; ?>'><?php echo $problem->acceptUserName; ?></td>
                <td title='<?php echo $problem->code; ?>'><?php echo $problem->code; ?></td>
                <td title='<?php echo $problem->status; ?>'><?php echo $problem->status; ?></td>
                <td title='<?php echo $problem->abstract; ?>'><?php echo $problem->abstract; ?></td>
                <td title='<?php echo $problem->PlannedTimeOfChange; ?>'><?php echo $problem->PlannedTimeOfChange; ?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
