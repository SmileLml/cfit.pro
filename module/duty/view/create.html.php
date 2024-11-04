<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->duty->create;?></h2>
      <?php if(isonlybody()) echo '<button id="closeModal" type="button" class="btn btn-link pull-right" data-dismiss="modal"><i class="icon icon-close"></i></button>';?>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->duty->application;?></th>
            <td><?php echo html::select('application', $appList, '', "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->type;?></th>
            <td><?php echo html::select('type', $lang->duty->typeList, '', "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->importantTime;?></th>
            <td><?php echo html::radio("importantTime", $lang->duty->importantTimeList);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->user;?></th>
            <td><?php echo html::select('user[]', $users, '', "class='form-control chosen' multiple");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->planDate;?></th>
            <td><?php echo html::input('planDate', $date, "class='form-control form-date'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->actualUser;?></th>
            <td><?php echo html::select('actualUser[]', $users, '', "class='form-control chosen' multiple");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->actualDate;?></th>
            <td><?php echo html::input('actualDate', $date, "class='form-control form-date'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->desc;?></th>
            <td colspan='2'>
              <?php echo html::textarea('desc', '', "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->duty->mailto;?></th>
              <td>
                  <div class="input-group">
                      <?php
                      echo html::select('mailto[]', $users, '', "multiple class='form-control chosen'");
                      echo $this->fetch('my', 'buildContactLists');
                      ?>
                  </div>
              </td>
          </tr>
          <tr>
          <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
