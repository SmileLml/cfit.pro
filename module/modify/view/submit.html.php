<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .reviewTip{color: red;}
    .input-group-addon{min-width: 150px;}
    .input-group{margin-bottom: 6px;}
    .checkbox-skipReview {width: 120px; margin-left: 5px;}
    .msgTip{position: absolute;top:1px;right: -300px;color:red}
</style>
<div id="mainContent" class="main-content fade">
    <?php if(!empty($lockCode)):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo '关联需求条目'.$lockCode.'所属需求任务或意向正在变更，当前流程锁死，待变更流程结束后再进行后续操作。';?></h2>
    <?php else:?>
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->modify->submit;?></h2>
    </div>
    <form class="load-indicator main-form <?php if($linkType == 1){echo 'form-ajax';}?>"  method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <?php if($lastAction!==''and $modify->requiredReviewNode!=''):?>
            <tr class="nodes">
          <?php else:?>
            <tr class="nodes hidden">
          <?php endif;?>
            <th class='w-140px'><?php echo $lang->modify->reviewNodes;?></th>
            <td colspan='2'>
                <?php
                foreach($lang->modify->reviewerList as $key => $nodeName):
                    //历史数据
                    if(empty($nodesReviewers[$key]) or in_array($key, $lang->modify->skipNodes)){  //跳过产品经理审批节点
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
                                    $skipReview = $lang->modify->skipReview;
                                    if(in_array($key, [0, 1, 7])){
                                        $skipReview = $lang->modify->skipDeal;
                                    }
                                    ?>
                                  <?php if($lastAction==''or $modify->requiredReviewNode==''):?>
                                    <div class="hidden">
                                    <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, ""); ?>
                                    </div>
                                  <!-- 内部退回 -->
                                  <?php elseif($lastAction =='review'):?>
                                    <?php if($key<$unpassedKey&&$key!==2):?>
                                        <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, "class='skipNode' data-key=$key"); ?>
                                    <?php else:?>
                                        <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, "onClick='return false' style='cursor:not-allowed;' disabled"); ?>
                                        <!--隐藏传值-->
                                        <div class="hidden">
                                        <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, ""); ?>
                                        </div>
                                    <?php endif;?>
                                  <?php elseif($lastAction =='reject'):?>
                                    <!-- 外部退回 -->
                                    <?php if(!in_array($key, explode(',', $modify->requiredReviewNode))&&$key!==2||in_array($key,array(0,1,3))):?>
                                        <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, "class='skipNode' data-key=$key"); ?>
                                    <?php else:?>
                                        <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, "onClick='return false' style='cursor:not-allowed;' disabled"); ?>
                                        <!--隐藏传值-->
                                        <div class="hidden">
                                        <?php echo html::checkbox("skipReviewNode[$key]", $skipReview, true, ""); ?>
                                        </div>
                                    <?php endif;?>
                                  <?php endif?>
                                </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->modify->rejectComment;?></th>
            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control' rows='5'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?><input type="submit" class="btn_submit" value="" style="display:none"></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
    <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
    $("#submit").click(function () {
        $(this).attr("disabled",true)
        $(".btn_submit").click()
        setTimeout(function () {
            $("#submit").removeAttr('disabled');
        },2000)
    })
    var cmMsgTip = "<?php echo $lang->modify->cmMsgTip;?>"
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
