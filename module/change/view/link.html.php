<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->change->link;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->change->release;?></th>
            <td colspan="2"><?php echo html::select('release[]', $releases, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <!--<tr>
            <th><?php /*echo $lang->change->consumed;*/?></th>
            <td colspan="2"><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>
          </tr>-->
          <?php if($change->reviewStage == 1):?>
          <tr>
            <th><?php echo $lang->change->isNeedSystem;?></th>
            <td>
              <div class='checkbox-primary'>
                <input id='isNeedSystem' name='isNeedSystem' value='1' type='checkbox' class='no-margin' />
                <label for='isNeedSystem'><?php echo $lang->change->needSystem;?></label>
              </div>
            </td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->change->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
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
