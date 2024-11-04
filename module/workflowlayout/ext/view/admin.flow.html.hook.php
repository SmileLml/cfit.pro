<?php if($action->buildin and $action->action == 'view'):?>
<script>
$(function()
{
    $('.form-actions a[href*=block]').remove();
})
</script>
<?php endif;?>
