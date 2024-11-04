<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='changereview'>
    <table class="table table-form">
        <tbody>
        <tr>
            <th>
                <?php echo $lang->review->isReject;?>
                <i title="<?php echo $lang->review->isRejectTip;?>" class="icon icon-help"></i>
            </th>
            <td colspan='10'>
                <?php echo html::radio('isReject', $lang->review->isRejectLabelList, '2');?>
            </td>
        </tr>

        <tr id='baselineTable1' class="baselineRecord">
            <th>
                <?php echo $lang->review->baseLineType;?>
            </th>
            <td colspan='2'><?php echo html::select('baseLineType[]', $typelist, '', "class='form-control chosen'");?></td>
            <th><?php echo $lang->review->baseLinePath;?></th>
            <td colspan='7'>
                <input type="text" name="baseLinePath[]" id="baseLinePath" value="" class="form-control" placeholder="<?php echo htmlspecialchars($lang->review->baseLinePathTip)?>" autocomplete="off">
            </td>
            <td class="c-actions">
                <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>
            </td>
        </tr>

        <tr>
            <th class="baselineRecord"><?php echo $lang->review->baseLineTime;?></th>
            <td class="baselineRecord" colspan='10'><?php echo html::input('baseLineTime',  date('Y-m-d H:i:s'), "class='form-datetime form-control' required");?></td>
           <!-- <th><?php /*echo $lang->review->consumed;*/?></th>
            <td colspan='7'><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>-->
        </tr>

        <tr>
            <th><?php echo $lang->review->comment;?></th>
            <td colspan='10'><?php echo html::textarea('comment', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->review->commentTip)."'");?></td>
        </tr>

        <tr>
            <td class='form-actions text-center' colspan='11'>
                <!--保存初始审核节点-->
                <input type="hidden" name = "version" value="<?php echo $review->version; ?>">
                <input type="hidden" name = "reviewStage" value="<?php echo $review->reviewStage; ?>">
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
            //$('#consumed').val('0');
        }else {
            $('.baselineRecord').removeClass('hidden');
            //$('#consumed').val('');
        }
    }
</script>