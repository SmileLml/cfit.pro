<table class='hidden'>
    <tbody id="baselineTable">
    <tr id='baselineTable' class="baselineRecord">
        <th><?php echo $lang->review->baseLineType;?></th>
        <td colspan='2'><?php echo html::select('baseLineType[]', $typelist, '', "class='form-control chosen' id='baseLineType0'");?></td>
        <th><?php echo $lang->review->baseLinePath;?></th>
        <td colspan='7'><?php /*echo html::input('baseLinePath[]', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->change->baseLinePathTip)."'");*/?>
            <input type="text" name="baseLinePath[]" id="baseLinePath0" value="" class="form-control"  placeholder="<?php echo htmlspecialchars($lang->review->baseLinePathTip)?>" autocomplete="off">
        </td>
        </td>
        <td class="c-actions">
            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>