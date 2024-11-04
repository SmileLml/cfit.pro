<?php if($isShowSafetyTest):?>
<tr>
    <th class='w-120px'><?php echo $lang->review->isSafetyTest;?></th>
    <td colspan='3'>
        <?php echo html::select('isSafetyTest', $lang->review->isSafetyTestList, $review->isSafetyTest, "class='form-control chosen' required");?>
    </td>
    <th class='w-150px'><?php echo $lang->review->isPerformanceTest;?></th>
    <td colspan='2'>
        <?php echo html::select('isPerformanceTest', $lang->review->isPerformanceTestList, $review->isPerformanceTest, "class='form-control chosen' required");?>
    </td>
    <td></td>
</tr>
<?php endif;?>

<?php if(empty($archiveList)):?>
    <tr class="archiveTrList" id='archiveTable1'>
        <th><?php echo $lang->review->archiveSvnUrl;?></th>
        <td colspan='4' class="required">
            <input type="text" name="svnUrl[]" id="svnUrl" value="" class="form-control svnUrl" placeholder="<?php echo htmlspecialchars($lang->review->archiveSvnUrlTip)?>">
        </td>

        <td colspan='2' class="required">
            <input type="text" name="svnVersion[]" id="svnVersion" value="" class="form-control svnVersion" placeholder="<?php echo htmlspecialchars($lang->review->archiveSvnVersion)?>" autocomplete="off">
        </td>
        <td class="c-actions">
            <a href="javascript:void(0)" onclick="addArchiveItem(this)" data-id='1' class="btn btn-link addArchiveBtn"><i class="icon-plus"></i></a>
        </td>
    </tr>
<?php else:?>
 <?php
    foreach ($archiveList as $key => $val):
        $keyTemp = $key + 1;
    ?>
        <tr class="archiveTrList" id='archiveTable1'>
            <th><?php echo $lang->review->archiveSvnUrl;?></th>
            <td colspan='4' class="required">
                <input type="text" name="svnUrl[]" id="svnUrl<?php echo $keyTemp; ?>" value="<?php echo $val->svnUrl?>" class="form-control svnUrl" placeholder="<?php echo htmlspecialchars($lang->review->archiveSvnUrlTip)?>">
            </td>

            <td colspan='2' class="required">
                <input type="text" name="svnVersion[]" id="svnVersion<?php echo $keyTemp; ?>" value="<?php echo $val->svnVersion?>" class="form-control svnVersion" placeholder="<?php echo htmlspecialchars($lang->review->archiveSvnVersion)?>" autocomplete="off">
            </td>
            <td class="c-actions">
                <a href="javascript:void(0)" onclick="addArchiveItem(this)" data-id='<?php echo $keyTemp; ?>' class="btn btn-link addArchiveBtn"><i class="icon-plus"></i></a>
                <?php if($key > 0):?>
                    <a href="javascript:void(0)" onclick="delArchiveItem(this)" data-id='<?php echo $keyTemp; ?>' id='codeClose<?php echo $keyTemp; ?>' class="btn btn-link delArchiveBtn"><i class="icon-close"></i></a>
                <?php endif;?>
            </td>
        </tr>

 <?php endforeach;?>
<?php endif;?>

<!--<tr>
    <th><?php /*echo $lang->review->consumed;*/?></th>
    <td colspan='6'><?php /*echo html::input('consumed', '', "class='form-control' required");*/?></td>
</tr>-->

<tr>
    <th><?php echo $lang->review->comment;?></th>
    <td colspan='6'><?php echo html::textarea('comment', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->review->archiveCommentTip)."'");?></td>
</tr>

<tr>
    <td class='form-actions text-center' colspan='8'>
        <!--保存初始审核节点-->
        <input type="hidden" name = "version" value="<?php echo $review->version; ?>">
        <input type="hidden" name = "reviewStage" value="<?php echo $review->reviewStage; ?>">
        <?php echo html::submitButton() . html::backButton();?>
    </td>
</tr>