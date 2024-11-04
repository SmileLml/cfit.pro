<?php if($execution->type == 'stage'):?>
<?php
$html  = '<tr>';
$html .= '<th>';
$html .= $lang->task->design;
$html .= '</th>';
$html .= '<td>';
$html .= html::select('design', '', '', "class='form-control chosen'");
$html .= '</td>';
$html .= '<tr>';

js::set('designID', $task->design);
?>
<script>
$(function()
{
    $('#story').change(function()
    {
        var storyID = $(this).val();
        if(storyID)
        {   
            var link = createLink('story', 'ajaxGetDesign', 'storyID=' + storyID + '&design=' + designID);
            $.post(link, function(data)
            {   
                $('#design').replaceWith(data);
                $('#design_chosen').remove();
                $('#design').chosen();
            })  
        }
    });
    $('#story').closest('tr').after(<?php echo json_encode($html);?>);
    $('#design').chosen();
    $('#story').change();
})
</script>
<?php endif;?>
