$(document).ready(function()
{
    $('#verificationTable #type').change(function()
    {
        $('.sqlTR').toggle($(this).val() == 'sql');
        $('.dataTR').toggle($(this).val() == 'data');
    });

    $('#verificationTable #type').change();
    $('#verificationTable [name*=paramType]').change();
})
