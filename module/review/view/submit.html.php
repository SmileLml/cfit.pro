<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $review->id;?></span>
        <span><?php echo $review->title;?></span>

        <small><?php echo $lang->arrow . $lang->review->submit;?></small>
      </h2>
    </div>
    <form method='post' enctype='multipart/form-data' target='hiddenwin'>
      <table class='table table-form'>
        <tr>
          <th class='w-100px'><?php echo $lang->review->owner;?></th>
          <td class='w-p45-f'><?php echo html::select('owner[]', $users, $review->owner, "class='form-control chosen' multiple");?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->review->expert;?></th>
          <td><?php echo html::select('expert[]', $users, $review->expert, "class='form-control chosen' multiple");?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->review->reviewedBy;?></th>
          <td><?php echo html::select('reviewedBy[]', $users, $review->reviewedBy, "class='form-control chosen' multiple");?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->review->reviewer;?></th>
          <td><?php echo html::select('reviewer', $users, $review->reviewer, "class='form-control chosen'");?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->comment;?></th>
          <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
        </tr>
        <tr>
          <td class='text-center' colspan='3'><?php echo html::submitButton();?></td>
        </tr>
      </table>
    </form>
    <hr class='small' />
    <div class='main'><?php include '../../common/view/action.html.php';?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
