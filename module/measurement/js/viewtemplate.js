$().ready(function()
{
    $('#submit').click(function()
    {
        $('#reportForm').submit();
    });

    $('fieldset [name^=program]').prop('readonly', true);
});
