<?php js::set('type', $type)?>
<?php js::set('orderBy', $orderBy)?>
<script>
$(function()
{
    $('#<?php echo $type?>').addClass('active');
    $('.reviewPass').attr('data-toggle', 'ajax');

    $("input[name^='leaveIDList']").remove();
    $('.checkbox-inline, .radio-inline').removeClass('checkbox-inline');

    var link = createLink('leave', 'export', "mode=all&orderBy=" + orderBy + "&type=" + type);
    $('#menuActions .btn-link').attr('href', link);
})
</script>
