<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->outwarddelivery->close;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <?php if($closeEnable == true):?>
              <tbody>
              <tr>
                  <th></th>
                  <td style="color:#FF0000" colspan='3'><?php echo $lang->outwarddelivery->rejectNotice?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->outwarddelivery->rejectComment;?></th>
                  <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control' rows='5' required");?></td>
              </tr>
              <tr>
                  <td class='form-actions text-center' colspan='4'><?php echo html::submitButton() . html::backButton();?></td>
              </tr>
              </tbody>
          <?php else:?>
              <tbody>
              <tr>
                  <th></th>
                  <td style="color:#FF0000" colspan='3'><?php echo $closeNotice; ?></td>
              </tr>
              <tr>
                  <td class='form-actions text-center' colspan='4'><?php echo html::backButton();?></td>
              </tr>
              </tbody>
          <?php endif;?>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>