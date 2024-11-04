<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->duty->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->duty->application;?></th>
            <td><?php echo html::select('application', $appList, $duty->application, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->type;?></th>
            <td><?php echo html::select('type', $lang->duty->typeList, $duty->type, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->importantTime;?></th>
            <td><?php echo html::radio("importantTime", $lang->duty->importantTimeList, $duty->importantTime);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->user;?></th>
            <td><?php echo html::select('user[]', $users, $duty->user, "class='form-control chosen' multiple");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->planDate;?></th>
            <td><?php echo html::input('planDate', $duty->planDate, "class='form-control form-date' ");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->actualUser;?></th>
            <td><?php echo html::select('actualUser[]', $users, $duty->actualUser, "class='form-control chosen' multiple");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->actualDate;?></th>
            <td><?php echo html::input('actualDate', $duty->actualDate, "class='form-control form-date'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->duty->desc;?></th>
            <td colspan='2'>
              <?php echo html::textarea('desc', htmlspecialchars($duty->desc), "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->duty->mailto;?></th>
              <td>
                  <div class="input-group">
                      <?php
                      echo html::select('mailto[]', $users, $duty->mailto, "multiple class='form-control chosen'");
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
