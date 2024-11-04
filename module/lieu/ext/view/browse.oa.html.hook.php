<script>
$(function()
{
    $('#<?php echo $type?>').addClass('active');
    $('.side .has-active-item').addClass('open');
    $('.reviewPass').attr('data-toggle', 'ajax');

    $("input[name^='lieuIDList']").remove();
    $('.checkbox-inline, .radio-inline').removeClass('checkbox-inline');
})
</script>
