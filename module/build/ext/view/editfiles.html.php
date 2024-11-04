<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="height: 350px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'></span>
        <span><?php echo $lang->build->editfiles;?></span>
      </h2>
    </div>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class='table table-form'>
                  <tr>
                      <th><?php echo $lang->build->fileList;?></th>

                      <td  colspan='2'>
                          <div class='detail'>
                              <div class='detail-content article-content'>
                                  <?php
                                  if($build->files){
                                      echo $this->fetch('file', 'printFiles', array('files' => $build->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
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
                      <td colspan='2' class = 'required'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85&filesName=verifyFiles');?></td>
                      <td class='muted'>
                          <div class="fileOverSize"><span > <?php echo sprintf($lang->review->fileOverSize, $this->config->review->fileSize->fileSize);?></span></div>
                      </td>
                  </tr>
                  <tr>
                      <td colspan='4' class='form-actions text-center'><?php echo html::submitButton() . html::backButton();?></td>
                  </tr>
              </table>
          </form>
  </div>

</div>
<?php include '../../../common/view/footer.html.php';?>
