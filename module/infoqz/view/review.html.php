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
          <h2>
              <?php echo $lang->infoqz->dealNode . $lang->infoqz->reviewNodeList[$info->reviewStage];?>
          </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
            <tbody>
              <tr>
                <th><?php echo $lang->infoqz->result;?></th>
                <td><?php echo html::select('result', $info->status == 'closing' ? $lang->infoqz->closeList : $lang->infoqz->confirmList, '', "class='form-control chosen'");?></td>
              </tr>
              <?php if($info->type == 'business'):?>
                  <?php if($info->reviewStage == 2): ?>
                  <tr class="hidden">
                    <th><?php echo $lang->infoqz->isNeedSystem;?></th>
                    <td>
                      <?php echo html::radio('isNeedSystem', $lang->infoqz->isNeedSystemList, 'no');?>
                    </td>
                  </tr>
                  <?php endif;?>
              <?php endif;?>

              <tr>
                <th><?php echo $lang->infoqz->comment;?></th>
                <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
              </tr>
              <tr>
                <td class='form-actions text-center' colspan='3'>
                    <!--保存初始状态-->
                    <input type="hidden" name = "version" value="<?php echo $info->version; ?>">
                    <input type="hidden" name = "reviewStage" value="<?php echo $info->reviewStage; ?>">
                    
                    <?php echo html::submitButton() . html::backButton();?>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>