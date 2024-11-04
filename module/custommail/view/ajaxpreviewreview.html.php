<?php $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables); ?>
<?php
//$this->app->company->name = $mailTitle;
?>
<?php include $this->app->getModuleRoot() .
    "common/view/mail.header.html.php"; ?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
      <tr>
        <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo $mailTitle; ?></td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'><?php echo $lang->custommail
          ->tips; ?></legend>
      <div style='padding:5px;'>
    <?php echo $mailConf->mailContent; ?>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='display:none;width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->idAB; ?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang
            ->custommail->reviewName; ?></th>
        <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang
            ->custommail->closingDate; ?></th>
      </tr>
      <tr>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo 888; ?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = "javascript:void(0);";
        echo html::a($link, "code_示例评审标题", "", 'style="color:blue;"');
        ?>
        </td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo "2021-01-01"; ?></td>
      </tr>
    </table>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
        <tr>
            <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->startdept;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例发起部门</td>
        </tr>

        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->creater;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例发起人</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->initiationTime;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例发起日期</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->enddate;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例截止日期</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->status;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例当前状态</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->dealUser;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例待处理人</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->id;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审编号</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->title;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审标题</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->type;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审类型</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->object;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审对象</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->grade;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审方式</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->qapre;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例QA预审</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->reviewer;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审专员</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->owner;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审会主席</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->expert;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审专家</td>

        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->reviewedBy;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例评审参与人员</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->outside;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例外部人员</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialDept;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例初审部门</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialDeptLiasisonOfficer;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例初审接口人</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialAdjudicatingOfficer;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例初审主审人员</td>
        </tr>
        <tr>
            <th style='width: 100px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->review->trialJoinOfficer;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5;'>示例初审参与人员</td>
        </tr>
        <tr style="line-height:32px;">
            <th style="padding-left:7px;background:#f5f5f5;width:120px;border:1px solid #e5e5e5;">
                <?php echo $lang->custommail->remak; ?>
            </th>
            <td style="padding-left:7px;border:1px solid #e5e5e5;" >备注示例</td>
        </tr>

    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . "common/view/mail.footer.html.php"; ?>
