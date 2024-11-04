<?php if($flow->buildin):?>
<script>
$(function()
{
    $('#type option[value="batch"]').remove();
    $('#show option[value="dropdownlist"]').remove();
    $('#show').val('direct');
})
</script>
<?php endif;?>
