<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $implement->id ;?></span>
        <span class='label label-id'><?php echo $implement->name;?></span>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->implementionplan->delete;?></small>
        <?php endif;?>
      </h2>
    </div>
    <!--<form method='post' target='hiddenwin'>-->
      <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' >
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->implementionplan->comment;?></th>
          <td colspan='2' class="required"><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
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
