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
      <legend style='color: #114f8e'><?php echo $lang->custommail->tips;?></legend>
      <div style='padding:5px;'>
      <?php echo htmlspecialchars_decode($mailConf->mailContent);?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
      <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
<?php $this->app->loadLang('requestlog')?>
          <tr>
              <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->id ; ?></th>
              <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo 2;?></td>
          </tr>
          <tr>
              <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->code; ?></th>
              <td style="padding-left:7px;border:1px solid #e5e5e5;" ><?php
                  $res = json_decode($log->params);
                  if(isset($res->idFromJinke)) {echo $res->idFromJinke;}
                  elseif(isset($res->changeOrderId)) {echo $res->changeOrderId;}
                  elseif(isset($res->IssueId)) {echo $res->IssueId;}
                  else{echo $log->extra;}
                  ?></td>
          </tr>
          <tr>
              <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->objectType ; ?></th>
              <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo 'problem';?></td>
          </tr>
          <tr>
              <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->purpose ; ?></th>
              <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo '问题同步';?></td>
          </tr>
          <tr>
              <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->status ; ?></th>
              <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo '失败';?></td>
          </tr>
          <tr>
              <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->requestDate ; ?></th>
              <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo helper::now();?></td>
          </tr>

      </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
