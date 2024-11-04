<script>
$(function()
{
    $(document).on('click', '.btn.addWhere', function()
    {
        var $nextWhere = $(this).closest('tr').next();
        $nextWhere.find('#field').chosen();
    })
})
</script>
