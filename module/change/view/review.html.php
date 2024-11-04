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
      <?php elseif(in_array($app->user->account,explode(',',$change->appiontUsers))):?>
          <div class='main-header'>
              <h2>
                  <span class='label label-id'><?php echo $change->code;?></span>
                  <small><?php echo $lang->change->HandledByFeedbackOperator;?></small>
              </h2>
          </div>

          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class="table table-form">
                  <tbody>
                  <tr class="hidden">
                      <th><?php echo $lang->change->result;?></th>
                      <td><?php echo html::select('result',  $lang->change->confirmList, 'pass', "class='form-control chosen'");?></td>
                  </tr>


                  <tr>
                      <th><?php echo $lang->change->comment;?></th>
                      <td colspan='2'><?php echo html::textarea('comment', isset($appointUser[$app->user->account]) ? $appointUser[$app->user->account]->comment: '', "class='form-control'");?></td>
                  </tr>
                  <tr>
                      <td class='form-actions text-center' colspan='3'>
                          <!--保存初始审核节点-->
                          <input type="hidden" name = "version" value="<?php echo $change->version; ?>">
                          <input type="hidden" name = "status" value="<?php echo $change->status; ?>">
                          <?php echo html::submitButton() . html::backButton();?>
                      </td>
                  </tr>
                  </tbody>
              </table>
          </form>
      <?php else:?>
          <div class='main-header'>
              <h2>
                  <span class='label label-id'><?php echo $change->code;?></span>
                  <small><?php echo $lang->arrow . zget($lang->change->reviewNodeCodeDescList, $nodeCode);?></small>
              </h2>
          </div>
          <?php if($change->status == $lang->change->statusArray['archive']):?>
                <?php include 'reviewArchive.html.php';?>
          <?php elseif($change->status == $lang->change->statusArray['gmsuccess']): ?>
                <?php include 'reviewBaseLine.html.php';?>
          <?php else:?>
              <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class="table table-form">
                <tbody>
                  <tr>
                    <th><?php echo $lang->change->result;?></th>
                    <td><?php echo html::select('result', $change->status == 'closing' ? $lang->change->closeList : $lang->change->confirmList, '', "class='form-control chosen'");?></td>
                  </tr>
                  <!--<tr>
                    <th><?php /*echo $lang->change->consumed;*/?></th>
                    <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
                  </tr>-->
                  <?php if(($change->status == $lang->change->statusArray['cmconfirmed']) && ($change->level == 2)):?>
                  <tr class="hidden">
                    <th><?php echo $lang->change->isNeedLeader;?></th>
                    <td>
                      <?php echo html::radio('isNeedLeader', $lang->change->isNeedLeaderList, '');?>
                      <input type="hidden" name="isleader" id ='isleader' value>
                    </td>
                  </tr>
                  <?php endif;?>
                  <tr >
                    <th><?php echo $lang->change->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                  </tr>
                  <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <!--保存初始审核节点-->
                        <input type="hidden" name = "version" value="<?php echo $change->version; ?>">
                        <input type="hidden" name = "status" value="<?php echo $change->status; ?>">
                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                  </tr>
                <?php
                if($appointUser){

                    foreach ($appointUser as $point){


                    ?>
                    <tr>
                        <th><?php echo zget($users,$point->reviewer);?></th>
                        <td colspan='2'><?php echo $point->comment;?></td>
                    </tr>


                    <?php
                    }
                }

                ?>
                </tbody>
              </table>
            </form>
          <?php endif;?>
      <?php endif;?>
  </div>
</div>

<?php if($change->status == $lang->change->statusArray['archive']):?>
    <?php include 'reviewArchiveHidden.html.php';?>
<?php elseif($change->status == $lang->change->statusArray['gmsuccess']):?>
    <?php include 'reviewBaseLineHidden.html.php';?>
<?php endif;?>
<?php
js::set('status',$change->status);
js::set('cm', $lang->change->statusArray['cmconfirmed']);
js::set('level',$change->level );
?>

<?php include '../../common/view/footer.html.php';?>