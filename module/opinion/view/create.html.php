<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinion->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
<!--              <th>--><?php //echo $lang->opinion->synUnion;?><!--</th>-->
<!--              <td>--><?php //echo html::select('synUnion[]', $lang->opinion->synUnionList, '', "class='form-control chosen' multiple");?><!--</td>-->
              <th><?php echo $lang->opinion->sourceMode;?></th>
              <td><?php echo html::select('sourceMode', $sourceModeList, '', "class='form-control chosen'");?></td>

              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinion->category;?></span>
                      <?php echo html::select('category', $lang->opinion->categoryList, '', "class='form-control chosen'");?>
                  </div>
              </td>


          </tr>
          <tr>
              <th><?php echo $lang->opinion->union;?></th>
              <td><?php echo html::select('union[]', $lang->opinion->unionList, '', "class='form-control chosen'multiple");?></td>

              <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinion->sourceName;?></span>
                <?php echo html::input('sourceName', '', "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinion->contact;?></th>
              <td><?php echo html::input('contact', '', "class='form-control'");?></td>

              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinion->contactInfo;?></span>
                      <?php echo html::input('contactInfo', '', "class='form-control'");?>
                  </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinion->deadline;?></th>
              <td><?php echo html::input('deadline', '', "class='form-control form-date'");?></td>

              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinion->date;?></span>
                      <?php echo html::input('date', helper::today(), "class='form-control form-date'");?>
                  </div>
            </td>
          </tr>

          <tr>
            <th><?php echo $lang->opinion->receiveDate;?></th>
            <td><?php echo html::input('receiveDate', '', "class='form-control form-date'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinion->synUnion;?></span>
                  <?php echo html::select('synUnion[]', $lang->opinion->synUnionList, 1, "class='form-control chosen'");?>
              </div>
            </td>
          </tr>

          <tr>
            <th><?php echo $lang->opinion->name;?></th>
            <td colspan='2'><?php echo html::input('name', '', "class='form-control' maxlength='100'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->background;?></th>
            <td colspan='2' class="required"><?php echo html::textarea('background', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->overview;?></th>
            <td colspan='2' class="required"><?php echo html::textarea('overview', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->remark;?></th>
            <td colspan='2'><?php echo html::textarea('remark', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>          
          <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->assignedToManger;?></th>
            <td><?php echo html::select('assignedTo', $productManagerList, '', "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinion->mailto;?></span>
                  <?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple");?>
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
<?php include '../../common/view/footer.html.php';?>
