<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinion->edit;?><span style='opacity: 0.5;font-size: 12px;font-weight: normal;'><?php echo empty($opinion->demandCode)?'':$lang->opinion->subTitle;?></span></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
            <tr>
                <th><?php echo $lang->opinion->sourceMode;?></th>
                <td>
                    <?php echo html::select('sourceMode', $sourceModeList, $opinion->sourceMode, "class='form-control chosen'");?></td>

                <td>
                    <div class='input-group'>
                        <span class='input-group-addon'><?php echo $lang->opinion->category;?></span>
                        <?php echo html::select('category', $lang->opinion->categoryList, $opinion->category, "class='form-control chosen'");?>
                    </div>
                </td>
            </tr>
          <tr>
              <th><?php echo $lang->opinion->union;?></th>
              <td><?php echo html::select('union[]', $lang->opinion->unionList, $opinion->union, "class='form-control chosen'multiple");?></td>
              <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinion->sourceName;?></span>
                <?php echo html::input('sourceName', $opinion->sourceName, "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinion->contact;?></th>
              <td><?php echo html::input('contact', $opinion->contact, "class='form-control'");?></td>

              <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinion->date;?></span>
                <?php echo html::input('date', $opinion->date, "class='form-control form-date'");?>
              </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinion->deadline;?></th>
              <td>
                  <?php if($opinion->createdBy != 'guestcn' && in_array($opinion->status,['underchange','subdivided','pass'])):?>
                      <?php echo html::input('deadline', $opinion->deadline, "class='form-control' readonly");?>
                  <?php else:?>
                      <?php echo html::input('deadline', $opinion->deadline, "class='form-control form-date'");?>
                  <?php endif;?>
              </td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinion->contactInfo;?></span>
                      <?php echo html::input('contactInfo', $opinion->contactInfo, "class='form-control'");?>
                  </div>
              </td>

          </tr>

          <tr>
              <th><?php echo $lang->opinion->receiveDate;?></th>
              <td><?php echo html::input('receiveDate', $opinion->receiveDate, "class='form-control form-date'");?></td>
              <?php if($opinion->createdBy != 'guestcn'):?>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinion->synUnion;?></span>
                      <?php echo html::select('synUnion[]', $lang->opinion->synUnionList, $opinion->synUnion, "class='form-control chosen'");?>
                  </div>
              </td>
              <?php endif;?>
          </tr>
            <!-- 迭代三十去掉是否属于(外部)项目/任务范围 -->
<!--          --><?php //if($opinion->createdBy == 'guestcn'):?>
<!--          <tr>-->
<!--            <th>--><?php //echo $lang->opinion->isOutsideProject;?><!--</th>-->
<!--            <td class="required">--><?php //echo html::select('isOutsideProject', $lang->opinion->isOutsideProjectList, $opinion->isOutsideProject, "class='form-control chosen'");?><!--</td>-->
<!--          </tr>-->
<!--          --><?php //endif;?>
          <tr>
            <th><?php echo $lang->opinion->name;?></th>
            <td colspan='2'><?php echo html::input('name', $opinion->name, "class='form-control' maxlength='100'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->background;?></th>
            <td colspan='2' class="background_edit" <?php if($opinion->createdBy != 'guestcn'):?> class="required"<?php endif;?>><?php echo html::textarea('background', $opinion->background, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->overview;?></th>
            <td colspan='2' class="desc_edit" <?php if($opinion->createdBy != 'guestcn'):?> class="required"<?php endif;?>><?php echo html::textarea('overview', $opinion->overview, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->remark;?></th>
            <td colspan='2'><?php echo html::textarea('remark', $opinion->remark, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->opinion->filelist;?></th>
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
            <?php if($opinion->createdBy != 'guestcn' && !in_array($opinion->status,['underchange','subdivided','pass'])):?>
                <tr>
                    <th><?php echo $lang->files;?></th>
                    <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                </tr>
            <?php endif;?>
          <tr>
            <th><?php echo $assign = $opinion->createdBy != 'guestcn' ? $lang->opinion->assignedToManger : $lang->opinion->assignedTo;?></th>
            <td><?php echo html::select('assignedTo', $users, $opinion->assignedTo, "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinion->mailto;?></span>
                  <?php echo html::select('mailto[]', $users, $opinion->mailto, "class='form-control chosen' multiple");?>
              </div>
            </td>
          </tr>

          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->opinion->submit) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('prohibitEditing', json_encode($config->opinion->prohibitEditing));?>
<?php js::set('demandCode', empty($opinion->demandCode) ? 0 : 1);?>
<?php js::set('deadline', empty($opinion->deadline) ? false : true);?>
<?php js::set('category', empty($opinion->category) ? false : true);?>
<?php js::set('fieldReadonly', $readonly);?>
<script>
//需求概述默认只读处理

if(fieldReadonly)
{
    $(function()
    {
        $('#name').attr('readonly','readonly');
        var $overview = window.editor['overview'];
        var $background = window.editor['background'];
        $overview.readonly(true);
        $background.readonly(true);
        var desc_iframeDom = $('.desc_edit iframe')[0].contentWindow.document.getElementsByClassName('article-content')[0]
        desc_iframeDom.style.background = '#f5f5f5';
        desc_iframeDom.style.cursor = 'not-allowed';
        var background_iframeDom1 = $('.background_edit iframe')[0].contentWindow.document.getElementsByClassName('article-content')[0]
        background_iframeDom1.style.background = '#f5f5f5';
        background_iframeDom1.style.cursor = 'not-allowed';

    });
}

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
