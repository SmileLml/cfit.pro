$(function()
{
    $('.upgradeBtn').click(function()
    {
        //var link = createLink('workflow', 'upgrade', 'module=' + module + '&step=' + step + '&toVersion=' + $('#version').val() + '&mode=' + mode);
        var link = $(this).prop('href');
        if($('#version').val()) link += '&toVersion=' + $('#version').val();
        $('#triggerModal').load(link); 

        return false;
    });
})
