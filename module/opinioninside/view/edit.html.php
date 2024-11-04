<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinioninside->edit;?><span style='opacity: 0.5;font-size: 12px;font-weight: normal;'><?php echo empty($opinion->demandCode)?'':$lang->opinioninside->subTitle;?></span></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
            <tr>
                <th><?php echo $lang->opinioninside->sourceMode;?></th>
                <td>
                    <?php echo html::select('sourceMode', $sourceModeList, $opinion->sourceMode, "class='form-control chosen'");?></td>

                <td>
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->opinioninside->category;?></span>
                        <?php echo html::select('category', $lang->opinion->categoryList, $opinion->category, "class='form-control chosen'");?>
                    </div>
                </td>
            </tr>
          <tr>
              <th><?php echo $lang->opinioninside->union;?></th>
              <td><?php echo html::select('union[]', $lang->opinion->unionList, $opinion->union, "class='form-control chosen'multiple");?></td>
              <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinioninside->sourceName;?></span>
                <?php echo html::input('sourceName', $opinion->sourceName, "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinioninside->contact;?></th>
              <td><?php echo html::input('contact', $opinion->contact, "class='form-control'");?></td>

              <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinioninside->date;?></span>
                <?php echo html::input('date', $opinion->date, "class='form-control form-date'");?>
              </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinioninside->deadline;?></th>
              <td><?php echo html::input('deadline', $opinion->deadline, "class='form-control form-date'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinioninside->contactInfo;?></span>
                      <?php echo html::input('contactInfo', $opinion->contactInfo, "class='form-control'");?>
                  </div>
              </td>

          </tr>

          <tr>
              <th><?php echo $lang->opinioninside->receiveDate;?></th>
              <td><?php echo html::input('receiveDate', $opinion->receiveDate, "class='form-control form-date'");?></td>

              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinioninside->synUnion;?></span>
                      <?php echo html::select('synUnion[]', $lang->opinion->synUnionList, $opinion->synUnion, "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>

          <tr>
            <th><?php echo $lang->opinioninside->name;?></th>
            <td colspan='2'><?php echo html::input('name', $opinion->name, "class='form-control' maxlength='100'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinioninside->background;?></th>
            <td colspan='2' <?php if($opinion->createdBy != 'guestcn'):?> class="required"<?php endif;?>><?php echo html::textarea('background', $opinion->background, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinioninside->overview;?></th>
            <td colspan='2' <?php if($opinion->createdBy != 'guestcn'):?> class="required"<?php endif;?>><?php echo html::textarea('overview', $opinion->overview, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinioninside->remark;?></th>
            <td colspan='2'><?php echo html::textarea('remark', $opinion->remark, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->opinioninside->filelist;?></th>
              <td>
                  <div class='detail'>
                      <div class='detail-content article-content'>
                          <?php
                          if($opinion->files){
                              echo $this->fetch('file', 'printFiles', array('files' => $opinion->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                          }else{
                              echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                          }
                          ?>
                      </div>
                  </div>
              </td>
          </tr>
          <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>
          <tr>
            <th><?php echo $assign = $opinion->createdBy != 'guestcn' ? $lang->opinioninside->assignedToManger : $lang->opinioninside->assignedTo;?></th>
            <td><?php echo html::select('assignedTo', $users, $opinion->assignedTo, "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinioninside->mailto;?></span>
                  <?php echo html::select('mailto[]', $users, $opinion->mailto, "class='form-control chosen' multiple");?>
              </div>
            </td>
          </tr>

          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->opinioninside->submit) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('prohibitEditing', json_encode($config->opinioninside->prohibitEditing));?>
<?php js::set('demandCode', empty($opinion->demandCode) ? 0 : 1);?>
<?php js::set('deadline', empty($opinion->deadline) ? false : true);?>
<?php js::set('category', empty($opinion->category) ? false : true);?>
<script>
if(demandCode)
{
    var prohibitEditing = eval('(' + prohibitEditing + ')');
    for(var i in prohibitEditing)
    {
      // if(prohibitEditing[i] == 'deadline'){ //期望完成日期存在就disabled输入框
      //   if(deadline) $('#' + prohibitEditing[i]).attr('disabled', 'disabled');
      // }else if(prohibitEditing[i] == 'category'){
      //   if(category) $('#' + prohibitEditing[i]).attr('disabled', 'disabled');
      // }else if(prohibitEditing[i] == 'assignedTo'){
      //   if(assignedTo) $('#' + prohibitEditing[i]).attr('disabled', 'disabled');
      // }else{
        $('#' + prohibitEditing[i]).attr('disabled', 'disabled');
      // }
    }

    $(function()
    {
        var $background = editor['background'];
        $background.readonly(true);
    });
}
</script>
<?php include '../../common/view/footer.html.php';?>
