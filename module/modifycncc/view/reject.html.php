<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .reviewTip{color: red;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
      <?php if(!$res):?>
      <div class="main-header">
          <h2>
              <span class="reviewTip">
                <?php echo $lang->modifycncc->rejectError;?>
              </span>
          </h2>
      </div>
      <?php else:?>
        <div class="main-header">
          <h2>
              <?php echo $lang->modifycncc->reject;?>
          </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
            <tbody>
            <!--
              <tr>
                <th><?php echo $lang->modifycncc->consumed;?></th>
                <td><?php echo html::input('consumed', '', "class='form-control'");?></td>
              </tr>
              -->

              <tr>
                <th><?php echo $lang->modifycncc->rejectComment;?></th>
                <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control' rows='5' required");?></td>
              </tr>
              <tr>
                <td class='form-actions text-center' colspan='3'>
                    <?php echo html::submitButton() . html::backButton();?>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
