<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .reviewTip{color: red;}
    .input-group-addon{min-width: 150px;}
    .input-group{margin-bottom: 6px;}
    .checkbox-skipReview {width: 120px; margin-left: 5px;}
    .msgTip{position: absolute;top:1px;right: -310px;color:red}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
      <?php if(!$res):?>
      <div class="main-header">
          <h2>
              <span class="reviewTip">
                <?php echo $lang->info->rejectError;?>
              </span>
          </h2>
      </div>
      <?php else:?>
        <div class="main-header">
          <h2>
              <?php echo $lang->info->reject;?>
          </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
            <tbody>
              <tr>
                  <th><?php echo $lang->info->revertReason;?></th>
                  <td>
                      <?php echo html::select("revertReason", $lang->info->revertReasonList, '', "class='form-control chosen' required");?>
                  </td>
                  <th><?php echo $lang->info->revertReasonChild;?></th>
                  <td>
                      <?php echo html::select("revertReasonChild", [], '', "class='form-control chosen'");?>
                  </td>
              </tr>
              <th class='w-140px'><?php echo $lang->info->reviewNodes;?></th>
                  <td>
                      <?php
                      foreach($lang->info->reviewerList as $key => $nodeName):
                          //历史数据
                          if(empty($nodesReviewers[$key]) or in_array($key, $lang->info->skipNodes)){  //跳过产品经理审批节点
                              continue;
                          }
                          js::set('node_'.$key.'_reviewers', $nodesReviewers[$key] ? $nodesReviewers[$key]: '');
                          $hiddenClass = '3' == $key ? 'hidden' : '';
                          ?>

                          <div class="table-row node-item node<?php echo $key;?> <?php echo $hiddenClass; ?>">
                              <div class='table-col reviewer-node-info-col'>
                                  <div class='input-group'>
                                      <span class='input-group-addon w-50px'><?php echo $nodeName;?></span>
                                  </div>
                              </div>

                              <div class="table-col c-actions">

                                      <div class='checkbox-primary checkbox-skipReview'>
                                          <?php
                                          $skipReview = $lang->info->skipReview;
                                          if(in_array($key, [0, 1, 7])){
                                              $skipReview = $lang->info->skipDeal;
                                          }
                                          ?>
                                          <?php if(in_array($key, $lang->info->allowSkipReviewerNodes)):?>
                                              <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, "class='skipNode' data-key=$key"); ?>

                                          <?php else:?>
                                              <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, "onClick='return false' style='cursor:not-allowed;' disabled"); ?>
                                              <!--隐藏传值-->
                                              <div class="hidden">
                                              <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, ""); ?>
                                              </div>
                                          <?php endif;?>
                                      </div>

                              </div>
                          </div>
                      <?php endforeach;?>
                  </td>
                </tr>
              <!-- <tr>
                <th><?php /*echo $lang->info->consumed;*/?></th>
                <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
              </tr>-->
              <tr>
                <th><?php echo $lang->info->rejectComment;?></th>
                <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control' rows='5' required");?></td>
              </tr>
              <tr>
                <td class='form-actions text-center' colspan='3'>
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
<script>
    $('#revertReason').change(function()
    {
        var type = $(this).val();
        $.get(createLink('outwarddelivery', 'ajaxGetChildType', 'type=' + type+'&module=info'), function(data)
        {
            $('#revertReasonChild_chosen').remove();
            $('#revertReasonChild').replaceWith(data);
            $('#revertReasonChild').chosen();
        });
    });
    var cmMsgTip = "<?php echo $lang->info->cmMsgTip;?>"
    $(".skipNode").change(function () {
        if ($(this).attr("data-key") == 0){
            if($(this).is(":checked")){
                $(".msgTip").remove()
            }else{
                $(this).parent().append("<div class='msgTip'>"+cmMsgTip+"</div>");
            }
        }
    })
</script>