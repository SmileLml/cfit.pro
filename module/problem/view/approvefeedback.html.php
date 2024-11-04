<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <?php if(!$res['result']):?>
      <div class="main-header">
          <h2>
          <span class="reviewTip">
            <?php echo $res['message'];?>
          </span>
          </h2>
      </div>
    <?php else:?>
    <div class="main-header">
      <h2><?php echo $lang->problem->reviewFeedback;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->problem->result;?></th>
            <td><?php echo html::select('result', $lang->problem->resultList, '', "class='form-control chosen'");?></td>
          </tr>
        <!--  <tr>
              <th><?php /*echo $lang->problem->consumed;*/?></th>
              <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
          </tr>-->
          <tr>
            <th><?php echo $lang->problem->reviewOpinion;?></th>
            <td colspan='2'><?php echo html::textarea('reviewOpinion', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
            <!--保存初始审核节点-->
            <input type="hidden" name = "version" value="<?php echo $problem->version; ?>">
            <input type="hidden" name = "reviewStage" value="<?php echo $problem->reviewStage; ?>">
          </tr>
        </tbody>
      </table>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
