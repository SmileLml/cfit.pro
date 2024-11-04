<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
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
      <h2><?php echo $lang->outwarddelivery->submit;?></h2>
    </div>
    <form class="load-indicator main-form <?php if($linkType == 1){echo 'form-ajax';}?>" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
            <?php if($outwarddelivery->version !=1 and $outwarddelivery->modifyLevel == 1): ?>
            <tr class="nodes">
                <th class='w-140px'><?php echo $lang->outwarddelivery->reviewNodes;?></th>
                <td colspan='2'>
                    <?php
                    foreach($lang->outwarddelivery->reviewerList as $key => $nodeName):
                        //历史数据
                        if(empty($nodesReviewers[$key]) or in_array($key, $lang->outwarddelivery->skipNodes)){  //跳过产品经理审批节点
                            continue;
                        }
                        js::set('node_'.$key.'_reviewers', $nodesReviewers[$key] ? $nodesReviewers[$key]: '');
                        $hiddenClass = '3' == $key ? 'hidden' : '';
                        ?>

                        <div class="table-row node-item node<?php echo $key;?> <?php echo $hiddenClass?>">
                            <div class='table-col reviewer-node-info-col'>
                                <div class='input-group'>
                                    <span class='input-group-addon w-50px'><?php echo $nodeName;?></span>
                                    <!--                                        --><?php //echo html::select("nodes[$key][]", $reviewers[$key], $nodesReviewers[$key], "class='form-control chosen' required multiple");?>

                                </div>
                            </div>

                            <div class="table-col c-actions">

                                <div class='checkbox-primary checkbox-skipReview'>
                                    <?php
                                        $skipReview = $lang->outwarddelivery->skipReview;
                                        if(in_array($key, [0, 1, 7])){
                                            $skipReview = $lang->outwarddelivery->skipDeal;
                                        }
                                    ?>
                                    <?php if(in_array($key, $allowSkipReviewerNodes)):?>
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
            <?php else:?>
                <tr class="nodes hidden">
                    <th class='w-140px'><?php echo $lang->outwarddelivery->reviewNodes;?></th>
                    <td colspan='2'>
                        <?php
                        foreach($lang->outwarddelivery->reviewerList as $key => $nodeName):
                            //历史数据
                            if(empty($nodesReviewers[$key]) or in_array($key, $lang->outwarddelivery->skipNodes)){  //跳过产品经理审批节点
                                continue;
                            }
                            js::set('node_'.$key.'_reviewers', $nodesReviewers[$key] ? $nodesReviewers[$key]: '');
                            ?>

                            <div class="table-row node-item node<?php echo $key;?>">
                                <div class='table-col reviewer-node-info-col'>
                                    <div class='input-group'>
                                        <span class='input-group-addon w-50px'><?php echo $nodeName;?></span>
                                        <!--                                        --><?php //echo html::select("nodes[$key][]", $reviewers[$key], $nodesReviewers[$key], "class='form-control chosen' required multiple");?>

                                    </div>
                                </div>

                                <div class="table-col c-actions">

                                    <div class='checkbox-primary checkbox-skipReview'>

                                        <?php if(in_array($key, $allowSkipReviewerNodes)):?>
                                            <?php echo html::checkbox("skipReviewNode[$key]", $lang->outwarddelivery->skipReview, true, ""); ?>

                                        <?php else:?>
                                            <?php echo html::checkbox("skipReviewNode[$key]", $lang->outwarddelivery->skipReview, true, "onClick='return false' style='cursor:not-allowed;' disabled"); ?>
                                            <!--隐藏传值-->
                                            <div class="hidden">
                                                <?php echo html::checkbox("skipReviewNode[$key]", $lang->outwarddelivery->skipReview, true, ""); ?>
                                            </div>
                                        <?php endif;?>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach;?>
                    </td>
                </tr>
            <?php endif; ?>
         <!-- <tr>
            <th><?php /*echo $lang->outwarddelivery->consumed;*/?></th>
            <td><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
          </tr>-->
          <tr>
            <th><?php echo $lang->outwarddelivery->rejectComment;?></th>
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
<script>
    $("#submit").click(function () {
        $(this).attr("disabled",true)
        $(".btn_submit").click()
        setTimeout(function () {
            $("#submit").removeAttr('disabled');
        },2000)
    })
    var cmMsgTip = "<?php echo $lang->outwarddelivery->cmMsgTip;?>"
    $(".skipNode").change(function () {
        if ($(this).attr("data-key") == 0){
            if($(this).is(":checked")){
                $(".msgTip").remove()
            }else{
                $(this).parent().append("<div class='msgTip'>"+cmMsgTip+"</div>");
            }
        }
    })
    $(function () {
        var linkType = "<?php echo $linkType;?>";
        if (linkType == 2){
            setTimeout(function () {
                $("#consumed").attr("required",'true');
            },500)
        }
    })
</script>
<?php include '../../common/view/footer.html.php';?>
