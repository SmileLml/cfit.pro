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
      <?php echo  $mailConf->mailContent; ?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
    <?php $this->app->loadLang('defect'); ?>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' class='table table-bordered'>
        <thead>
          <tr>
              <th style='width: 150px;'><?php echo  $this->lang->defect->code; ?></th>
              <th style='width: 200px;'><?php echo  $this->lang->defect->title;?></th>
              <th style='width: 100px;'><?php echo  $this->lang->defect->status; ?></th>
              <th style='width: 100px;'><?php echo  $this->lang->defect->nextUser; ?></th>
              <th style='width: 150px;'><?php echo  $this->lang->defect->source; ?></th>
              <th style='width: 100px;'><?php echo  $this->lang->defect->createdBy; ?></th>
              <th style='width: 140px;'><?php echo  $this->lang->defect->createdDate;?></th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach ($data as $defect):
                    $status    = zget($this->lang->defect->statusList, $defect->status);
                    $dealUser  = zget($users, $defect->dealUser);
                    $createdBy = zget($users, $defect->createdBy);
                    $source    = zget($this->lang->defect->sourceList, $defect->source);
            ?>
            <tr>
                <td title='<?php echo $defect->code; ?>'><?php echo $defect->code; ?></td>
                <td title='<?php echo $defect->title; ?>'><?php echo $defect->title; ?></td>
                <td title='<?php echo $status; ?>'><?php echo $status; ?></td>
                <td title='<?php echo $dealUser; ?>'><?php echo $dealUser; ?></td>
                <td title='<?php echo $source; ?>'><?php echo $source; ?></td>
                <td title='<?php echo $createdBy; ?>'><?php echo $createdBy; ?></td>
                <td title='<?php echo $defect->createdDate; ?>'><?php echo $defect->createdDate; ?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
