<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->reviewissue->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
      <table class="table table-form">
        <tbody>
          <tr>
            <th class="w-150px"><?php echo $lang->reviewissue->review;?></th>
            <td colspan='8'><?php echo
                html::input('review',  $review->title, 'class="form-control " disabled="disabled"');?></td>
            <td></td>
          </tr>
          <tr>
              <th class="w-150px"><?php echo $lang->reviewissue->title;?></th>
              <td colspan='8'><?php echo html::input('title', $issue->title, 'class="form-control"');?></td>
          </tr>
          <tr>
            <th class="w-150px"><?php echo $lang->reviewmeeting->desc;?></th>
            <td colspan='8'><?php echo html::textarea('desc', $issue->desc, "");?></td>
          </tr>
          <tr >
            <th class="w-150px"></th>
            <td colspan="8" class="form-actions text-center">
              <?php echo html::submitButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>

<?php include '../../common/view/footer.html.php'?>
