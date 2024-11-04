<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .input-group-addon{min-width: 150px;}
    .input-group{margin-bottom: 6px;}
    .checkbox-skipReview {width: 100px; margin-left: 5px;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $sectransfer->status == $lang->sectransfer->statusList['centerReject'] ? $lang->sectransfer->dealCenterReject : $lang->sectransfer->submit;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
            <?php if($sectransfer->status == $lang->sectransfer->statusList['centerReject'] ): ?>
                <tbody>
                <tr class="fields" style="height:60px;">
                    <th style="width:120px;"><?php echo $lang->sectransfer->rejectUser;?></th>
                    <td colspan='2'><?php echo $sectransfer->rejectUser;?></td>
                </tr>
                <tr class="fields" style="height:60px;">
                    <th style="width:120px;"><?php echo $lang->sectransfer->rejectReason;?></th>
                    <td colspan='2'><?php echo $sectransfer->rejectReason;?></td>
                </tr>
                <tr class="fields" style="height:60px;">
                    <th style="width:120px;"><?php echo $lang->sectransfer->examine.$lang->sectransfer->suggest;?></th>
                    <td><?php echo html::radio('suggestRadio', $lang->sectransfer->suggestList, '2', "onchange='toggleAcl(this.value, ".$sectransfer->jftype.")'");?></td>
                </tr>
                <tr id="stage" class='hidden'>
                    <th><?php echo $lang->sectransfer->transferStage;?></th>
                    <td colspan='1'><?php echo html::select('transitionPhase', $lang->sectransfer->transitionPhase, $sectransfer->transitionPhase, 'disabled class="form-control chosen"');?></td>
                </tr>
                <tr id="backReason" class='hidden'>
                    <th><?php echo $lang->sectransfer->backReason;?></th>
                    <td colspan='2'><?php echo html::textarea('reason', $sectransfer->reason, 'readonly class="form-control" required');?></td>
                </tr>
                <tr id="isLastTransfer" class='hidden'>
                    <th><?php echo $lang->sectransfer->isLastTransfer;?></th>
                    <td colspan='1'><?php echo html::radio('lastTransfer', $lang->sectransfer->orNotList, $sectransfer->lastTransfer, "disabled");?></td>
                    <td colspan='1'>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->sectransfer->transferNum;?></span>
                            <input type="number" name="transferNum" id="transferNum" value="<?php echo $sectransfer->transferNum; ?>" readonly = "readonly" class="form-control" autocomplete="off" style="border-radius: 0px 2px 2px 0px;">
                        </div>
                    </td>
                </tr>
                <tr id="backComment">
                    <th><?php echo $lang->sectransfer->comment;?></th>
                    <td colspan='2'>
                        <?php echo html::textarea('comment', '', "rows='8' class='form-control' required");?>
                    </td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            <?php else:?>
                <tbody>
                <tr class="nodes">
                    <th class='w-140px'><?php echo $lang->sectransfer->reviewers;?></th>
                    <td colspan='2'>
                        <?php
                        foreach($lang->sectransfer->reviewerListNum as $key => $nodeName):
                            ?>
                            <div class="table-row node-item node<?php echo $key;?>">
                                <div class='table-col reviewer-node-info-col'>
                                    <div class='input-group'>
                                        <span class='input-group-addon w-50px'><?php echo $nodeName;?></span>
                                    </div>
                                </div>
                                <div class="table-col c-actions">
                                    <div class='checkbox-primary checkbox-skipReview'>
                                        <?php if(in_array($key, $allowSkipReviewerNodes)):?>
                                            <?php echo html::checkbox("needReviewNode[$key]", $lang->sectransfer->needReview, true, ""); ?>
                                        <?php else:?>
                                            <?php echo html::checkbox("needReviewNode[$key]", $lang->sectransfer->needReview, true, "onClick='return false' style='cursor:not-allowed;' disabled"); ?>
                                            <!--隐藏传值-->
                                            <div class="hidden">
                                                <?php echo html::checkbox("needReviewNode[$key]", $lang->sectransfer->needReview, true, ""); ?>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->sectransfer->comment;?></th>
                    <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control' rows='5'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            <?php endif; ?>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
