<?php $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);?>
<?php //$this->app->company->name = $mailTitle;?>
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
<tr>
  <td style='padding: 10px; border: none;'>
      <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
          <tr>
              <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $build->id?></td>
          </tr>
          <tr>
              <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->nameCode;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $build->name?></td>
          </tr>
          <tr>
              <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->flowStatus;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->build->statusList,$build->status,'')?></td>
          </tr>
          <tr>
              <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->dealUser;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zmget($users,$build->dealuser,'')?></td>
          </tr>
          <tr>
              <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->project;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($application,$build->app,'无')?></td>
          </tr>
          <tr>
              <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->product;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($product,$build->product,'无')?></td>
          </tr>
          <tr>
              <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->version;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($version,$build->version,'')?></td>
          </tr>
          <tr>
              <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->createTime;?></th>
              <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $build->createdDate?></td>
          </tr>

          <?php if($build->status == 'waitdeptmanager'):?>
              <tr>
                  <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->build->specialPassReason;?></th>
                  <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $build->specialPassReason;?></td>
              </tr>
          <?php endif;?>
      </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
