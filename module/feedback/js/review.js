$(function()
{
    $('#result').change(function()
    {
        $.post(createLink('feedback', 'ajaxGetStatus', 'methodName=review'), {'result' : $(this).val()}, function(status)
        {
            $('#status').val(status).change();
        });
    });
})
