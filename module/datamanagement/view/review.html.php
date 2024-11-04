<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .inoneline{white-space: NOWRAP;}
    .destroyReasonDiv{width: 674px;height: 160px;word-wrap: break-word;overflow: auto;border: 1px solid #ccc;padding:8px;background-color: rgb(245,245,245);}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo (is_null($datamanagement->reviewStage) or $datamanagement->reviewStage== '') ? $lang->datamanagement->warm : $lang->datamanagement->reviewNodeList[$datamanagement->reviewStage]; ?></h2>
        </div>
        <?php if ($datamanagement->status == 'gainsuccess' and  $datamanagement->reviewStage == '1'): ?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->datamanagement->dataStatus; ?></th>
                    <td><?php echo zget($lang->datamanagement->statusList, $datamanagement->status, '');?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->delayReason; ?></th>
                    <td>
                        <div class="destroyReasonDiv">
                            <?php echo $extraObj->delayReason; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->useDeadline; ?></th>
                    <td><?php if ( $extraObj->delayDeadline == 'longterm'){
                            echo zget($lang->datamanagement->useDeadlineChoose, $extraObj->delayDeadline, '');
                        }else{
                            echo $extraObj->delayDeadline;}?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->result; ?></th>
                    <td class="required"><?php echo html::select('result', $lang->datamanagement->confirmList, '', "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->reviewOpinion;?></th>
                    <td colspan='2'><?php echo html::input('reviewOpinion', '', "class='form-control' required");?></td>
                </tr>
               <!-- <tr>
                    <th class='inoneline'><?php /*echo $lang->datamanagement->consumedInput;*/?></th>
                    <td><?php /*echo html::input('consumed', '', "class='form-control' required placeholder='小数点后保留1位小数'");*/?></td>
                </tr>-->
                <tr>
                    <th><?php echo $lang->datamanagement->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <!--保存初始审核节点-->
                        <input type="hidden" name = "changeVersion" value="<?php echo $datamanagement->changeVersion; ?>">
                        <input type="hidden" name = "reviewStage" value="<?php echo $datamanagement->reviewStage; ?>">
                        <?php echo html::submitButton($lang->datamanagement->save) . html::backButton(); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php elseif ($datamanagement->status == 'todestroy' and  $datamanagement->reviewStage == '2'): ?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->datamanagement->destroyReason; ?></th>
                        <td>
                            <div class="destroyReasonDiv">
                                <?php echo $extraObj->destroyReason; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->datamanagement->dataStatus; ?></th>
                        <td><?php echo zget($lang->datamanagement->statusList, $datamanagement->status, '');?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->datamanagement->result; ?></th>
                        <td class="required"><?php echo html::select('result', $lang->datamanagement->confirmList, '', "class='form-control chosen' onchange='rejectShow(this.value)'"); ?></td>
                    </tr>
                    <tr id='rejectTr' class='hidden'>
                        <th><?php echo $lang->datamanagement->rejectReason;?></th>
                        <td colspan='2'><?php echo html::input('rejectReason', '', "class='form-control' required");?></td>
                    </tr>
                    <tr id='passTr'>
                        <th><?php echo $lang->datamanagement->executorReviewer;?></th>
                        <td class='required'><?php echo html::select('executor', array(' '=>'请选择执行人') + $users, ' ',  "class='form-control chosen'");?></td>
                        <td class='required'><?php echo html::select('checker', array(' '=>'请选择复核人') + $users, ' ',  "class='form-control chosen'");?></td>
                    </tr>
                    <!-- <tr>
                        <th class='inoneline'><?php /*echo $lang->datamanagement->consumedInput;*/?></th>
                        <td><?php /*echo html::input('consumed', '', "class='form-control' required placeholder='小数点后保留1位小数'");*/?></td>
                    </tr>-->
                    <tr>
                        <th><?php echo $lang->datamanagement->comment;?></th>
                        <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'>
                            <!--保存初始审核节点-->
                            <input type="hidden" name = "changeVersion" value="<?php echo $datamanagement->changeVersion; ?>">
                            <input type="hidden" name = "reviewStage" value="<?php echo $datamanagement->reviewStage; ?>">
                            <?php echo html::submitButton($lang->datamanagement->save) . html::backButton(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        <?php else: ?>
            <div>
                <?php echo $lang->datamanagement->dealError; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    function rejectShow(isReject) {
        if (isReject == 'reject') {
            $('#rejectTr').removeClass('hidden');
            $('#passTr').addClass('hidden');
        } else {
            $('#rejectTr').addClass('hidden');
            $('#passTr').removeClass('hidden');
        }
    }

</script>

<?php include '../../common/view/footer.html.php'; ?>
