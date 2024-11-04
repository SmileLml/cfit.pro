<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
js::set('checkSystemPass', $outwarddelivery->checkSystemPass);
?>
<style>
    .reviewTip{color: red;}
</style>
<div id="mainContent" class="main-content fade" style="height: 400px">
    <?php if(!empty($lockCode)):?>
        <h2 style="color:red;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo '关联需求条目$'.$lockCode.'$所属需求任务或意向正在变更，当前流程锁死，待变更流程结束后再进行后续操作。';?></h2>
    <?php else:?>
    <?php if($outwarddelivery->status == 'wait'):?>
        <div class="center-block">
            <?php if(!$res['result']):?>
                <h2 style="word-wrap: break-word;">
              <span class="reviewTip">
                <?php echo $res['message'];?>
              </span>
                </h2>
            <?php else:?>
                <div class="main-header">
                    <h2><?php echo $lang->outwarddelivery->link;?></h2>
                </div>
                <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                    <table class="table table-form">
                        <tbody>
                        <tr>

                            <th><?php echo $lang->outwarddelivery->release;?></th>
                            <td colspan="3"><?php echo html::select('release[]',$releases, '', "class='form-control chosen' required $multiple");?></td>
                        </tr>
                        <tr class="hidden">
                           <!-- <th><?php /*echo $lang->outwarddelivery->consumed;*/?></th>
                            <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>-->
                            <th><?php echo $lang->outwarddelivery->isMediaChanged;?><i title="<?php echo $lang->outwarddelivery->isMediaChangedTip;?>" class="icon icon-help"></i></th></th>
                            <td colspan="3"><?php echo html::select('isMediaChanged',$lang->outwarddelivery->isMediaChangedList, '', "class='form-control chosen' required");?></td>
                        </tr>
                        <?php if($outwarddelivery->reviewStage == 1):?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->isNeedSystem;?></th>
                                <td>
                                    <div class='checkbox-primary'>
                                        <input id='isNeedSystem' name='isNeedSystem' value='1' type='checkbox' class='no-margin' />
                                        <label for='isNeedSystem'><?php echo $lang->outwarddelivery->needSystem;?></label>
                                    </div>
                                </td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->outwarddelivery->comment;?></th>
                            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");
                            //校验审核阶段
                            echo html::hidden('formId',$outwarddelivery->id.$outwarddelivery->reviewStage.$outwarddelivery->version.$outwarddelivery->dealUser);
                            ?>

                            </td>
                        </tr>
                        <tr>
                            <td class='form-actions text-center' colspan='3'>
                                <input type="hidden" name = "version" value="<?php echo $outwarddelivery->version; ?>">
                                <?php echo html::submitButton() . html::backButton();?></td>
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
      <h2><?php echo $lang->outwarddelivery->dealNode.$lang->outwarddelivery->reviewNodeList[$outwarddelivery->reviewStage];?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->outwarddelivery->result;?></th>
            <td><?php echo html::select('result', $outwarddelivery->status == 'closing' ? $lang->outwarddelivery->closeList : $lang->outwarddelivery->confirmList, '', "class='form-control chosen' onchange = resultChange(this.value)");?></td>
          </tr>
         <!-- <tr>
            <th><?php /*echo $lang->outwarddelivery->consumed;*/?></th>
            <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
          </tr>-->
          <?php if($outwarddelivery->reviewStage == 2 and $outwarddelivery->isNewModifycncc == 1 and ((in_array(3,explode(',',$outwarddelivery->requiredReviewNode)) == 1 and $outwarddelivery->isOutsideReject == '1') or $outwarddelivery->isOutsideReject == '0')):?>
              <?php
              $hiddenClass = $outwarddelivery->checkSystemPass ? 'hidden' : '';
              $checked = $outwarddelivery->checkSystemPass ? 'no' : '';
              ?>
          <tr class="<?php echo $hiddenClass; ?>">
            <th><?php echo $lang->outwarddelivery->isNeedSystem;?></th>
            <td>
              <?php echo html::radio('isNeedSystem', $lang->outwarddelivery->isNeedSystemList, $checked);?>
            </td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->outwarddelivery->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'>
                <!--保存初始审核节点-->
                <input type="hidden" name = "version" value="<?php echo $outwarddelivery->version; ?>">
                <input type="hidden" name = "reviewStage" value="<?php echo $outwarddelivery->reviewStage; ?>">

                <?php echo html::submitButton() . html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
   <?php endif; ?>
  </div>
<?php endif; ?>
    <?php endif;?>
</div>
<script>
    function resultChange(val){
        if('pass' === val && !checkSystemPass){
            $('input[name="isNeedSystem"]').parent().parent().parent().removeClass('hidden');
        }else {
            $('input:radio[value="no"]').prop('checked', true)
            $('input[name="isNeedSystem"]').parent().parent().parent().addClass('hidden');
        }
    }
</script>
<?php include '../../common/view/footer.html.php';?>
