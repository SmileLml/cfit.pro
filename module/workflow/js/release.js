$(function()
{
    $.setAjaxForm('#releaseForm', function(response)
    {
        if(response.result == 'success')
        {
            if(response.entries)
            {
                entries = JSON.parse(response.entries);
                $.refreshDesktop(entries, true);
            }

            setTimeout(function(){location.href = response.locate;}, 1200);
        }
    });

    $('#app').change(function()
    {
        if($(this).val()) $('select#positionModule').load(createLink('workflow', 'ajaxGetAppMenus', 'app=' + $(this).val() + '&exclude=' + currentModule));
    });

    $('#createApp').change(function()
    {
        var checked = $(this).prop('checked');
    
        $('#app').toggle(!checked);
        $('.newApp').toggle(checked);
        $('#releaseForm table tbody tr').eq(1).toggle(!checked);
    
        $(this).parents('td').find('.text-error').remove();
        $('#name, #code').css({'margin-bottom': 0, 'border-color': ''});
    });
})
