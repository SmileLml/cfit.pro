<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="height: 350px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'></span>
        <span><?php echo $lang->review->projectswap;?></span>
      </h2>
    </div>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class='table table-form'>
                  <tr>
                      <th  ><?php echo $lang->review->projects;?></th>
                      <td colspan='2' class='required'>
                          <?php
                          echo html::select('project', $projectNames, '', "class='form-control chosen' ");?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->review->currentComment;?></th>
                      <td colspan='2' ><?php echo html::textarea('currentComment', '', "class='form-control'");?></td>
                  </tr>

                  <tr>
                      <td colspan='3' class='form-actions text-center'><?php echo html::submitButton() . html::backButton();?></td>
                  </tr>
              </table>
          </form>
  </div>

</div>
<?php include '../../../common/view/footer.html.php';?>
