<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="height: 550px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'></span>
        <span><?php echo $lang->review->editfiles;?></span>
      </h2>
    </div>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class='table table-form'>
                  <tr>
                      <th><?php echo $lang->review->filelist;?></th>

                      <td  colspan='2'>
                          <div class='detail'>
                              <div class='detail-content article-content'>
                                  <?php
                                  if($review->files){
                                      echo $this->fetch('file', 'printFiles', array('files' => $review->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                  }else{
                                      echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                  }
                                  ?>
                              </div>
                          </div>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->files;?></th>
                      <td colspan='2' class = 'required'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>

                  </tr>
                  <!--<tr>
                      <th><?php /*echo $lang->review->consumed;*/?></th>
                      <td  colspan='2'>

                  <?php /*echo html::input('consumed',  count($review->consumed) != 0 ? end($review->consumed)->consumed: '', "class='form-control'");*/?>

                          <?php /*echo html::input('consumed',   '', "class='form-control'");*/?>
                      </td>
                  </tr>-->
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
<?php include '../../common/view/footer.html.php';?>
