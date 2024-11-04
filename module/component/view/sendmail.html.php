<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<style>
    .infoName {
        width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;text-align: center;
    }
    .infoContent{
        padding: 5px; border: 1px solid #e5e5e5;
    }
</style>
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
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->creater;?></th>
            <td class="infoContent"><?php echo zget($users,$component->createdBy)?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->createrTime;?></th>
            <td class="infoContent"><?php echo $component->createdDate?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->createdDept;?></th>
            <td class="infoContent"><?php echo zget($depts,$component->createdDept)?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->componentType;?></th>
            <td class="infoContent"><?php echo zget($this->lang->component->type, $component->type);?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->application;?></th>
            <td class="infoContent"><?php echo zget($this->lang->component->applicationMethod, $component->applicationMethod);?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->name;?></th>
            <td style='padding: 5px; border: 1px solid #e5e5e5; text-align: left;'>
                <?php
                $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('component', 'view', "id=$component->id", 'html');
                echo html::a($detailsURL, $component->name, '', 'style="color:blue;"');
                ?>
            </td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->version; ?></th>
            <td class="infoContent"><?php echo $component->version;?></td>
        </tr>
        <tr>
          <th class="infoName"><?php echo $this->lang->component->developLanguage; ?></th>
          <td class="infoContent"><?php echo zget($this->lang->component->developLanguageList, $component->developLanguage, '');?></td>
        </tr>
        <tr>
          <th class="infoName"><?php echo $this->lang->component->project; ?></th>
          <td class="infoContent"><?php echo zget($projectPlanList,$component->projectId);?></td>
        </tr>
        <?php if($component->type=='thirdParty' && $component->applicationMethod=='new'):?>
            <tr>
                <th class="infoName">
                    <?php echo $this->lang->component->licenseType; ?>
                </th>
                <td class="infoContent"><?php echo $component->licenseType; ?></td>
            </tr>
        <?php endif;?>
        <?php if($component->type=='public' && $component->applicationMethod=='new'):?>
            <tr >
                <th class="infoName"><?php echo $this->lang->component->level; ?></th>
                <td class="infoContent"><?php echo zget($this->lang->component->levelList, $component->level); ?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo$this->lang->component->hasProfessionalReview; ?></th>
                <td class="infoContent"><?php echo zget($this->lang->component->professionalReviewResult, $component->hasProfessionalReview, ''); ?></td>
            </tr>
            <tr >
                <th class="infoName">
                    <?php echo $this->lang->component->maintainer; ?>
                </th>
                <td class="infoContent" ><?php echo zget($users,$component->maintainer);?></td>
            </tr>
        <?php endif;?>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->status;?></th>
            <td class="infoContent"><?php echo zget($this->lang->component->statusList,$component->status);?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->component->dealUser;?></th>
            <td class="infoContent"><?php echo $component->dealUser;?></td>
        </tr>
        <tr>
            <th class="infoName"><?php echo $this->lang->custommail->initiationTime;?></th>
            <td class="infoContent"><?php echo $bestDate;?></td>
        </tr>
    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
