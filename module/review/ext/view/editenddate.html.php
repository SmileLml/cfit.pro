<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="height: 350px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $review->id;?></span>
        <span><?php echo $review->title;?></span>

        <small><?php echo $lang->arrow .$lang->review->editEndDate;?></small>
      </h2>
    </div>
      <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class='table table-form'>
              <tr>
                  <th  class='w-150px'><?php echo $lang->review->editEndDate;?></th>
                  <td colspan='2'><?php
                      $endDate = date('Y-m-d', strtotime($review->endDate));
                      echo html::input('endDate', $endDate, "class='form-date form-control' ");?></td>

              </tr>
              <tr>
                  <td class='text-center' colspan='3'>
                      <?php echo html::submitButton();?>
                  </td>
              </tr>
          </table>
      </form>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
