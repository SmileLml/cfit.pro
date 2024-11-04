<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $build->id;?></span>
        <?php echo isonlybody() ? ("<span title='$build->id'>" . $lang->build->back . '</span>') : html::a($this->createLink('build', 'view', "buildID=$build->id"), $build->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->build->back;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form method='post' target='hiddenwin'>
      <table class='table table-form'>
       <!-- <tr>
            <th class='w-140px'><?php /*echo $lang->build->consumed;*/?></th>
            <td colspan="2"><?php /*echo html::input('consumed', '', "class='form-control '");*/?></td>
        </tr>-->
        <tr>
          <th><?php echo $lang->build->desc;?></th>
          <td colspan='2'><?php echo html::textarea('comment', '', "rows='8' class='form-control' required");?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'>
            <?php echo html::submitButton();?>
          </td>
        </tr>
      </table>
    </form>
      <!--  <hr class='small' />
      <div class='main'><?php /*include '../../../common/view/action.html.php';*/?></div>-->
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
