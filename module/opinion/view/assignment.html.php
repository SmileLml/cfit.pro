<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <?php if($opinion->changeLock == 2):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:8px;"><?php echo $lang->opinion->changeIng;?></h2>
    <?php else:?>
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->opinion->assignment;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->opinion->assignTo;?></th>
            <td colspan='3'><?php echo html::select('dealUser', $productManagerList,'', "class='form-control chosen'");?></td>
          </tr>

          <tr>
            <th><?php echo $lang->opinion->comment;?></th>
            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($this->lang->opinion->submit) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
    <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
