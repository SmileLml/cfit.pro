<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinioninside->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
<!--              <th>--><?php //echo $lang->opinioninside->synUnion;?><!--</th>-->
<!--              <td>--><?php //echo html::select('synUnion[]', $lang->opinioninside->synUnionList, '', "class='form-control chosen' multiple");?><!--</td>-->
              <th><?php echo $lang->opinioninside->sourceMode;?></th>
              <td><?php echo html::select('sourceMode', $sourceModeList, '', "class='form-control chosen'");?></td>

              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinioninside->category;?></span>
                      <?php echo html::select('category', $lang->opinion->categoryList, '', "class='form-control chosen'");?>
                  </div>
              </td>


          </tr>
          <tr>
              <th><?php echo $lang->opinioninside->union;?></th>
              <td><?php echo html::select('union[]', $lang->opinion->unionList, '', "class='form-control chosen'multiple");?></td>

              <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinioninside->sourceName;?></span>
                <?php echo html::input('sourceName', '', "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinioninside->contact;?></th>
              <td><?php echo html::input('contact', '', "class='form-control'");?></td>

              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinioninside->contactInfo;?></span>
                      <?php echo html::input('contactInfo', '', "class='form-control'");?>
                  </div>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->opinioninside->deadline;?></th>
              <td><?php echo html::input('deadline', '', "class='form-control form-date'");?></td>

              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->opinioninside->date;?></span>
                      <?php echo html::input('date', helper::today(), "class='form-control form-date'");?>
                  </div>
            </td>
          </tr>

          <tr>
            <th><?php echo $lang->opinioninside->receiveDate;?></th>
            <td><?php echo html::input('receiveDate', '', "class='form-control form-date'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinioninside->synUnion;?></span>
                  <?php echo html::select('synUnion[]', $lang->opinion->synUnionList, 1, "class='form-control chosen'");?>
              </div>
            </td>
          </tr>

          <tr>
            <th><?php echo $lang->opinioninside->name;?></th>
            <td colspan='2'><?php echo html::input('name', '', "class='form-control' maxlength='100'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinioninside->background;?></th>
            <td colspan='2' class="required"><?php echo html::textarea('background', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinioninside->overview;?></th>
            <td colspan='2' class="required"><?php echo html::textarea('overview', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinioninside->remark;?></th>
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
            <th><?php echo $lang->opinioninside->assignedToManger;?></th>
            <td><?php echo html::select('assignedTo', $users, '', "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->opinioninside->mailto;?></span>
                  <?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple");?>
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
<?php include '../../common/view/footer.html.php';?>
