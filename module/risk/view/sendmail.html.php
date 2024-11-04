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
          <div style='padding:5px;'><?php echo  $mailConf->mailContent;?></div>
       </fieldset>
    </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->idAB;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $risk->id;?></td>
            </tr>
            <tr>
                <th style='width: 140px;border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->custommail->risk ;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5; text-align: left;'>
                    <?php
                    $detailsURL = zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('risk',  'view', "id=$risk->id", 'html');
                    echo html::a($detailsURL, $risk->name, '', 'style="color:blue;"');
                    ?>
                </td>
            </tr>
            <tr>
                <th style="width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;"><?php echo $this->lang->risk->strategy; ?></th>
                <td style="padding-left:7px;border:1px solid #e5e5e5;" class="textOVerThree" ><?php echo zget($this->lang->risk->strategyList,$risk->strategy,'');?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->risk->status;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->risk->statusList,$risk->status,'');?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->risk->rate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $risk->rate;?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->risk->pri;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->risk->priList,$risk->pri,'');?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->risk->category;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($this->lang->risk->categoryList,$risk->category,'');?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->risk->assignedTo;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$risk->assignedTo,'');?></td>
            </tr>
           <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->risk->frameworkUser;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo zget($users,$risk->frameworkUser,'');?></td>
            </tr>
            <tr>
                <th style='width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $this->lang->risk->createdDate;?></th>
                <td style='padding: 5px; border: 1px solid #e5e5e5;'><?php echo $risk->createdDate;?></td>
            </tr>


    </table>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
