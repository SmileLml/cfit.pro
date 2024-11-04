<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
js::set('isDiskDeliveryList', html::select('isDiskDelivery',$this->lang->modify->isDiskDeliveryList,'',"class='form-control chosen'"));
js::set('checkSystemPass', $modify->checkSystemPass);
?>
<style>
    .reviewTip{color: red;}
</style>
<div id="mainContent" class="main-content fade">
    <?php if($modify->status == 'wait'):?>
        <div class="center-block">
            <?php if(!$res['result']):?>
                <h2 style="word-wrap: break-word;">
              <span class="reviewTip">
                <?php echo $res['message'];?>
              </span>
                </h2>
            <?php else:?>
                <div class="main-header">
                    <h2><?php echo $lang->modify->link;?></h2>
                </div>
                <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th><?php echo $lang->modify->release;?></th>
                            <td colspan="3"><?php echo html::select('release[]',$releases, '', "class='form-control chosen' required multiple");?></td>
                        </tr>
                        <tr class="hidden">
                           <!-- <th><?php /*echo $lang->modify->consumed;*/?></th>
                            <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>-->
                            <th><?php echo $lang->modify->isMediaChanged;?><i title="<?php echo $lang->modify->isMediaChangedTip;?>" class="icon icon-help"></i></th></th>
                            <td colspan="3"><?php echo html::select('isMediaChanged',$lang->modify->isMediaChangedList, '', "class='form-control chosen' required");?></td>
                        </tr>
                        <?php if($modify->reviewStage == 1):?>
                            <tr>
                                <th><?php echo $lang->modify->isNeedSystem;?></th>
                                <td>
                                    <div class='checkbox-primary'>
                                        <input id='isNeedSystem' name='isNeedSystem' value='1' type='checkbox' class='no-margin' />
                                        <label for='isNeedSystem'><?php echo $lang->modify->needSystem;?></label>
                                    </div>
                                </td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->modify->comment;?></th>
                            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                        </tr>
                        <tr>
                            <td class='form-actions text-center' colspan='3'>
                                <input type="hidden" name = "version" value="<?php echo $modify->version; ?>">
                                <?php echo html::submitButton() . html::backButton();?></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            <?php endif; ?>
        </div>
    <?php elseif($modify->status == 'cancel'):?>
        <div class="center-block">
      <?php if(!$res['result']):?>
              <h2 style="word-wrap: break-word;">
              <span class="reviewTip">
                <?php echo $res['message'];?>
              </span>
              </h2>
      <?php else:?>
    <div class="main-header">
      <h2><?php echo $lang->modify->cancelTitle;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->modify->result;?></th>
            <td><?php echo html::select('result', $modify->status == 'closing' ? $lang->modify->closeList : $lang->modify->confirmList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'>
                <!--保存初始审核节点-->
                <input type="hidden" name = "version" value="<?php echo $modify->version; ?>">
                <input type="hidden" name = "reviewStage" value="<?php echo $modify->reviewStage; ?>">

                <?php echo html::submitButton() . html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
   <?php endif; ?>
  </div>
    <?php else:?>
  <div class="center-block">
      <?php if(!$res['result']):?>
              <h2 style="word-wrap: break-word;">
              <span class="reviewTip">
                <?php echo $res['message'];?>
              </span>
              </h2>
      <?php else:?>
    <div class="main-header">
      <h2><?php echo $lang->modify->dealNode.$lang->modify->reviewNodeList[$modify->reviewStage];?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->modify->result;?></th>
            <td><?php echo html::select(
                    'result',
                    $modify->status == 'closing' ? $lang->modify->closeList : $lang->modify->confirmList,
                    '',
                    "class='form-control chosen' onchange=diskDelivery(this.value)"
                );?></td>
          </tr>
          <?php if($modify->reviewStage == 2 && strpos($modify->requiredReviewNode,'3')):?>
          <?php
              $hiddenClass = $modify->checkSystemPass ? 'hidden' : '';
              $checked = $modify->checkSystemPass ? 'no' : '';
              ?>
          <tr class="<?php echo $hiddenClass; ?>">
            <th><?php echo $lang->modify->isNeedSystem;?></th>
            <td>
              <?php echo html::radio('isNeedSystem', $lang->modify->isNeedSystemList, $checked);?>
            </td>
          </tr>
          <?php endif;?>
          <?php if($modify->reviewStage == 7 && strpos($modify->requiredReviewNode,'7')):?>
              <tr>
                  <th><?php echo $lang->modify->isDiskDelivery;?></th>
                  <td class="required">
                      <?php echo html::select(
                          'isDiskDelivery',
                          $lang->modify->isDiskDeliveryList,
                          '',
                          "class='form-control chosen'"
                      );?>
                  </td>
              </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->modify->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'>
                <!--保存初始审核节点-->
                <input type="hidden" name = "version" value="<?php echo $modify->version; ?>">
                <input type="hidden" name = "reviewStage" value="<?php echo $modify->reviewStage; ?>">

                <?php echo html::submitButton() . html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
   <?php endif; ?>
  </div>
    <?php endif; ?>
</div>
<script>
    function diskDelivery(val){
        if('pass' === val){
            $('#isDiskDelivery').parent().parent().removeClass('hidden');
        }else {
            $('#isDiskDelivery_chosen').remove();
            $('#isDiskDelivery').replaceWith(isDiskDeliveryList);
            $('#isDiskDelivery').chosen();
            $('#isDiskDelivery').parent().parent().addClass('hidden');
        }
        if('pass' === val && !checkSystemPass){
            $('input[name="isNeedSystem"]').parent().parent().parent().removeClass('hidden');
        }else {
            $('input:radio[value="no"]').prop('checked', true)
            $('input[name="isNeedSystem"]').parent().parent().parent().addClass('hidden');
        }
    }
</script>
<?php include '../../common/view/footer.html.php';?>
