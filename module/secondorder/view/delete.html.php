<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $secondorder->code;?></span>
        <?php echo isonlybody() ? ("<span title='$secondorder->code'>" . $lang->secondorder->delete . '</span>') : html::a($this->createLink('secondorder', 'view', "secondorderID=$secondorder->id"), $secondorder->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->secondorder->delete;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form method='post' target='hiddenwin'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->secondorder->comment;?></th>
          <td colspan='2'><?php echo html::textarea('comment', '', "rows='8' class='form-control'required");?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'>
            <?php echo html::submitButton();?>
          </td>
        </tr>
      </table>
    </form>
    <hr class='small' />
    <div class='main'><?php include '../../common/view/action.html.php';?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
