<?php if($subStatus):?>
<script>
$(function()
{
    if($('#subStatus').length == 0)
    {
        $('#submit').after("<input type='hidden' name='subStatus' id='subStatus' value='<?php echo $subStatus;?>' />");
    }
    else
    {
        $('#subStatus').val('<?php echo $subStatus;?>').trigger('chosen:updated');
    }
})
</script>
<?php endif;?>
