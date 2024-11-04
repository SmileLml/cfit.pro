<?php if($flow->buildin and ($action->action == 'exporttemplate' or $action->action == 'import' or ($action->module == 'feedback' and ($action->action == 'admin' or $action->action == 'adminview')))):?>
<script>
$(function()
{
    $('#extensionType option[value="override"]').remove();
})
</script>
<?php endif;?>
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
