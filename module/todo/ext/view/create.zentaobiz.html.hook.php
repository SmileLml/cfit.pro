<?php if(isset($feedbackID)):?>
<?php $inputHtml = html::hidden('feedback', (int)$feedbackID);?>
<?php 
$selectHtml  = '<tr><th>';
$selectHtml .= $lang->todo->assignTo;
$selectHtml .= '</th>';
$selectHtml .= '<td>';
$selectHtml .= html::select('assignedTo', $members, '', "class='form-control'");
$selectHtml .= '</td></tr>';
?>
<script language='Javascript'>
$(function()
{
    $('#dataform').children('table').find('tr:last').children('td:last').append(<?php echo json_encode($inputHtml)?>);
    $('#pri').closest('tr').after(<?php echo json_encode($selectHtml)?>);
    $('#assignedTo').chosen();
    $('#type').val('feedback');
})
</script>
<?php endif;?>
