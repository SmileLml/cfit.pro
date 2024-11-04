<?php if(isset($feedbackID)):?>
<?php $inputHtml = html::hidden('feedback', (int)$feedbackID);?>
<script language='Javascript'>
$(function()
{
    $('#dataform').children('table').find('tr:last').children('td:last').append(<?php echo json_encode($inputHtml)?>);
    $("#navbar .nav li[data-id=browse]").addClass('active');
})
</script>
<?php endif;?>
