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
          <div style='padding:5px;'><?php echo  htmlspecialchars_decode($mailConf->mailContent) ;?></div>
       </fieldset>
    </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>

        <tr>
            <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->id ; ?></th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo $log->id;?></td>
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
            <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo zget($this->lang->requestlog->objectTypeList, $log->objectType, $log->objectType);?></td>
        </tr>
        <tr>
            <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->purpose ; ?></th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo zget($this->lang->requestlog->purposeList, $log->purpose, $log->purpose);?></td>
        </tr>
        <tr>
            <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->status ; ?></th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo zget($this->lang->requestlog->statusList, $log->status, $log->status);?></td>
        </tr>
        <tr>
            <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->requestlog->requestDate ; ?></th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;"  ><?php echo $log->requestDate;?></td>
        </tr>

    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
