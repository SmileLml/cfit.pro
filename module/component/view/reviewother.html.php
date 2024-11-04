<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2>三方组件：<?php echo $lang->component->reviewNodeList[$component->reviewStage]; ?></h2>
        </div>
        <?php if ($component->status == 'todepartreview' or $component->status == 'toteamreview'): ?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->component->result; ?></th>
                        <td class="required"><?php echo html::select('result', $lang->component->confirmList, '', "class='form-control chosen' onchange='rejectShow(this.value)'"); ?></td>
                    </tr>
                    <tr id='rejectReasonTr'>
                        <th><?php echo $lang->component->rejectReason; ?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('rejectReason', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->mailto; ?></th>
                        <td colspan='2'><?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->dealcomment; ?></th>
                        <td colspan='2'><?php echo html::textarea('dealcomment', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'>
                            <!--保存初始审核节点-->
                            <input type="hidden" name = "changeVersion" value="<?php echo $component->changeVersion; ?>">
                            <input type="hidden" name = "reviewStage" value="<?php echo $component->reviewStage; ?>">
                            <?php echo html::submitButton($lang->component->save) . html::backButton(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        <?php elseif ($component->status == 'toappoint'): ?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->component->result; ?></th>
                        <td class="required"><?php echo html::select('result', $lang->component->teamresultList, '', "class='form-control chosen' onchange='teamChoseShow(this.value)'"); ?></td>
                    </tr>
                    <?php if($component->type=='public'): ?>
                        <tr id="componentIdList">
                            <th><?php echo $lang->component->name;?></th>
                            <td class="required"><?php echo html::select('cid', $componentList,  $component->cid, "class='form-control chosen'"); ?></td>
                        </tr>
                    <?php endif;?>

                    <tr id='rejectReasonTr'>
                        <th><?php echo $lang->component->rejectReason; ?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('rejectReason', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr id='teamMemberTr'>
                        <th><?php echo $lang->component->teamMember; ?></th>
                        <td class="required"><?php echo html::select('teamMember[]', $users, "", "class='form-control chosen' multiple"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->mailto; ?></th>
                        <td colspan='2'><?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->dealcomment; ?></th>
                        <td colspan='2'><?php echo html::textarea('dealcomment', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'>
                            <!--保存初始审核节点-->
                            <input type="hidden" name = "changeVersion" value="<?php echo $component->changeVersion; ?>">
                            <input type="hidden" name = "reviewStage" value="<?php echo $component->reviewStage; ?>">
                            <?php echo html::submitButton($lang->component->save) . html::backButton(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        <?php elseif ($component->status == 'toarchitreview'):?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->component->result; ?></th>
                        <td class="required"><?php echo html::select('result', $lang->component->confirmList, $resultStatus, "class='form-control chosen' onchange='rejectShow2(this.value)'"); ?></td>
                    </tr>
                    <?php if($component->type=='public'): ?>
                        <tr id="componentIdList">
                            <th><?php echo $lang->component->name;?></th>
                            <td class="required"><?php echo html::select('cid', $componentList, $component->cid, "class='form-control chosen'"); ?></td>
                        </tr>
                    <?php endif;?>
                    <tr id='rejectReasonTr2'>
                        <th><?php echo $lang->component->rejectReason; ?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('rejectReason', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->mailto; ?></th>
                        <td colspan='2'><?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->dealcomment; ?></th>
                        <td colspan='2'><?php echo html::textarea('dealcomment', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'>
                            <!--保存初始审核节点-->
                            <input type="hidden" name = "changeVersion" value="<?php echo $component->changeVersion; ?>">
                            <input type="hidden" name = "reviewStage" value="<?php echo $component->reviewStage; ?>">
                            <?php echo html::submitButton('确认评审结论') . html::backButton(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        <?php elseif ($component->status == 'toarchitleaderreview'): ?>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->component->result; ?></th>
                        <td class="required"><?php echo html::select('result', $lang->component->teamresultList, $resultStatus, "class='form-control chosen' disabled"); ?></td>
                        <td class="hidden"><?php echo html::select('result', $lang->component->teamresultList, $resultStatus, "class='form-control chosen'"); ?></td>
                    </tr>
                    <?php if($component->type=='public' && $resultStatus=='incorporate'): ?>
                        <tr >
                            <th>组件名称</th>
                            <td class="required"><?php echo html::select('cid', $componentList, $component->cid, "class='form-control chosen' disabled "); ?></td>
                        </tr>
                    <?php endif;?>
                    <tr>
                        <th><?php echo $lang->component->mailto; ?></th>
                        <td colspan='2'><?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->dealcomment; ?></th>
                        <td colspan='2'><?php echo html::textarea('dealcomment', '', "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'>
                            <!--保存初始审核节点-->
                            <input type="hidden" name = "changeVersion" value="<?php echo $component->changeVersion; ?>">
                            <input type="hidden" name = "reviewStage" value="<?php echo $component->reviewStage; ?>">
                            <?php echo html::submitButton('确认评审结论') . html::backButton(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        <?php endif; ?>
    </div>
</div>
<script>
    function rejectShow(isReject) {
        if (isReject == 'reject') {
            $('#rejectReasonTr').removeClass('hidden');
        } else {
            $('#rejectReasonTr').addClass('hidden');
        }
    }

    function rejectShow2(isResult) {
        if (isResult == 'reject') {
            $('#rejectReasonTr2').removeClass('hidden');
            $('#componentIdList').addClass('hidden');
        } else if(isResult == 'incorporate'){
            $('#rejectReasonTr2').addClass('hidden');
            $('#componentIdList').removeClass('hidden');
        }else{
            $('#rejectReasonTr2').addClass('hidden');
            $('#componentIdList').addClass('hidden');
        }
    }

    function teamChoseShow(isResult) {
        if (isResult == 'reject') {
            $('#rejectReasonTr').removeClass('hidden');
            $('#teamMemberTr').addClass('hidden');
            $('#componentIdList').addClass('hidden');
        } else if (isResult == 'pass') {
            $('#rejectReasonTr').addClass('hidden');
            $('#teamMemberTr').addClass('hidden');
            $('#componentIdList').addClass('hidden');
        } else if (isResult == 'appoint') {
            $('#rejectReasonTr').addClass('hidden');
            $('#teamMemberTr').removeClass('hidden');
            $('#componentIdList').addClass('hidden');
        }else if(isResult == 'incorporate'){
            $('#rejectReasonTr').addClass('hidden');
            $('#teamMemberTr').addClass('hidden');
            $('#componentIdList').removeClass('hidden');
        }
    }

    $(function () {
        var resultStatus = $('#result').val();
        $('#teamMemberTr').addClass('hidden');
        $('#rejectReasonTr').addClass('hidden');
        $('#componentIdList').addClass('hidden');
        if(resultStatus == 'reject'){
            $('#rejectReasonTr2').removeClass('hidden');
        }else {
            $('#rejectReasonTr2').addClass('hidden');
        }

    });
</script>
<?php include '../../common/view/footer.html.php'; ?>
