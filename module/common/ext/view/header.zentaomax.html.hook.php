<script>
$(function()
{
    $('#globalBarLogo a').eq(1).attr('title', '<?php echo $lang->zentaoPMS . str_replace('max', $lang->maxName . ' ', $config->version);?>');
})
</script>
