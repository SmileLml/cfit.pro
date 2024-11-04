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
      <?php echo $this->lang->demand->remindToEndMailContent; ?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
    <?php $this->app->loadLang('demand'); ?>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' class='table table-bordered'>
        <thead>
          <tr>
              <th style='width: 150px;'><?php echo  $this->lang->demand->demandAcceptUser; ?></th>
              <th style='width: 110px;'><?php echo  $this->lang->demand->code; ?></th>
              <th style='width: 100px;'><?php echo  $this->lang->demand->demandStatus; ?></th>
              <th style='width: 100px;'><?php echo  $this->lang->demand->title;?></th>
              <th style='width: 120px;'><?php echo  $this->lang->demand->requirementCode;?></th>
              <th style='width: 100px;'><?php echo  $this->lang->demand->requirementName;?></th>
              <th style='width: 160px;'><?php echo  $this->lang->demand->requirementPlanEnd;?></th>
              <th style='width: 100px;'><?php echo  $this->lang->demand->requirementStatus;?></th>
          </tr>
        </thead>

        <tbody>
            <?php
                foreach ($data as $demand):
            ?>
            <tr>

                <td title='<?php echo $demand->acceptUserName; ?>'><?php echo $demand->acceptUserName; ?></td>
                <td title='<?php echo $demand->code; ?>'><?php echo $demand->code; ?></td>
                <td title='<?php echo $demand->demandStatus; ?>'><?php echo $demand->demandStatus; ?></td>
                <td title='<?php echo $demand->title; ?>'><?php echo $demand->title;?></td>
                <td title='<?php echo $demand->requirementCode; ?>'><?php echo $demand->requirementCode; ?></td>
                <td title='<?php echo $demand->requirementName; ?>'><?php echo $demand->requirementName; ?></td>
                <td title='<?php echo $demand->requirementPlanEnd; ?>'><?php echo $demand->requirementPlanEnd; ?></td>
                <td title='<?php echo $demand->requirementStatus; ?>'><?php echo $demand->requirementStatus; ?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
