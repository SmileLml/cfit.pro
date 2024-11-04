<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .reviewTip{color: red;}
    .input-group-addon{min-width: 150px;}
    .input-group{margin-bottom: 6px;}
    .checkbox-skipReview {width: 100px; margin-left: 5px;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
      <?php if(!$res):?>
      <div class="main-header">
          <h2>
              <span class="reviewTip">
                <?php echo $lang->copyrightqz->rejectError;?>
              </span>
          </h2>
      </div>
      <?php else:?>
        <div class="main-header">
          <h2>
              <?php echo $lang->copyrightqz->reject;?>
          </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
            <tbody>
              <tr>
                <th class="w-120px"><?php echo $lang->copyrightqz->statuslable;?></th>
                <td colspan='2'><?php echo zget($lang->copyrightqz->statusList,$item->status);?></td>
              </tr>
             <!-- <tr>
                <th><?php /*echo $lang->copyrightqz->consumed;*/?></th>
                <td colspan='2'><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
              </tr>-->
              <tr>
                <th><?php echo $lang->copyrightqz->comment;?></th>
                <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control' rows='5'");?></td>
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
