<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='changereview'>
    <table class="table table-form">
        <tbody>
            <tr>
                <th>
                    <?php echo $lang->change->isReject;?>
                    <i title="<?php echo $lang->change->isRejectTip;?>" class="icon icon-help"></i>
                </th>
                <td colspan='10'>
                    <?php echo html::radio('isReject', $lang->change->isRejectLabelList, '2');?>
                </td>
            </tr>
            <tr id='baselineTable1'  class="baselineRecord">
                <th><?php echo $lang->change->baseLineType;?></th>
                <td colspan='2'><?php echo html::select('baseLineType[]', $typelist, '', "class='form-control chosen'");?></td>
                <th><?php echo $lang->change->baseLinePath;?></th>
                <td colspan='7'>
                    <input type="text" name="baseLinePath[]" id="baseLinePath" value="" class="form-control " placeholder="<?php echo htmlspecialchars($lang->change->baseLinePathTip)?>" autocomplete="off">
                </td>
                <td  class="c-actions" colspan='1'>
                    <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>
                </td>
            </tr>
            <tr>
                <th class="baselineRecord"><?php echo $lang->change->baseLineTime;?></th>
                <td class="baselineRecord" colspan='10'><?php echo html::input('baseLineTime',  helper::today(), "class='form-date form-control' required");?></td>
                <!--<th><?php /*echo $lang->change->consumed;*/?></th>
                <td colspan='7'><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>-->
            </tr>
            <tr>
                <th><?php echo $lang->change->comment;?></th>
                <td colspan='10'><?php echo html::textarea('comment', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->change->commentTip)."'");?></td>
            </tr>
            <tr>
                <td class='form-actions text-center' colspan='11'>
                    <!--保存初始审核节点-->
                    <input type="hidden" name = "version" value="<?php echo $change->version; ?>">
                    <input type="hidden" name = "status" value="<?php echo $change->status; ?>">
                    <?php echo html::submitButton() . html::backButton();?>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<script>
    /**
     * 打基线是否退回
     */
    $('input:radio[name="isReject"]').change(function() {
        var isReject = $('input:radio[name="isReject"]:checked').val();
        setBaselineInfo(isReject);
    });


    /**
     *设置打基线信是否显示
     * @param isReject
     */
    function setBaselineInfo(isReject) {
        if(isReject == '1'){
            $('.baselineRecord').addClass('hidden');
        }else {
            $('.baselineRecord').removeClass('hidden');
        }
    }
</script>