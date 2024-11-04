<table class='hidden'>
    <tbody id="baselineTable">
    <tr id='baselineTable' class="baselineRecord">
        <th><?php echo $lang->change->baseLineType;?></th>
        <td colspan='2'><?php echo html::select('baseLineType[]', $typelist, '', "class='form-control chosen' id='baseLineType0'");?></td>
        <th><?php echo $lang->change->baseLinePath;?></th>
        <td colspan='7'>
            <input type="text" name="baseLinePath[]" id="baseLinePath0" value="" class="form-control "  placeholder="<?php echo htmlspecialchars($lang->change->baseLinePathTip)?>" autocomplete="off">
        </td>
        </td>
        <td class="c-actions" colspan='1'>
            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>