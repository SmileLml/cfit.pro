<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .reviewTip{color: red;}
</style>
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
      <h2><?php echo $lang->modifycncc->reviewNodeList[$modifycncc->reviewStage];?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->modifycncc->result;?></th>
            <td><?php echo html::select('result', $modifycncc->status == 'closing' ? $lang->modifycncc->closeList : $lang->modifycncc->confirmList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modifycncc->consumed;?></th>
            <td><?php echo html::input('consumed', '', "class='form-control'");?></td>
          </tr>
          <?php if($modifycncc->reviewStage == 2):?>
          <tr>
            <th><?php echo $lang->modifycncc->isNeedSystem;?></th>
            <td>
              <?php echo html::radio('isNeedSystem', $lang->modifycncc->isNeedSystemList, '');?>
            </td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->modifycncc->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'>
                <!--保存初始审核节点-->
                <input type="hidden" name = "version" value="<?php echo $modifycncc->version; ?>">
                <input type="hidden" name = "reviewStage" value="<?php echo $modifycncc->reviewStage; ?>">

                <?php echo html::submitButton() . html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
   <?php endif; ?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
