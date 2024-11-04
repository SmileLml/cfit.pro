<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $requirement->code;?></span>
        <?php echo isonlybody() ? ("<span title='$requirement->code'>" . $lang->requirement->secureStatus . '</span>') : html::a($this->createLink('requirement', 'view', "requirementID=$requirement->id"), $requirement->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->requirement->secureStatus;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post'>
      <table class='table table-form'>
          <tr>
              <th><?php echo $lang->requirement->unlockSeparate;?></th>
              <td >
                  <?php echo html::radio('secureStatusLinkage', $this->lang->requirement->unlockSeparateList,1);?>
              </td>
          </tr>
          <th><?php echo $lang->requirement->comment;?></th>
          <td ><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center form-actions'>
            <?php echo html::submitButton($this->lang->requirement->submitBtn);?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
