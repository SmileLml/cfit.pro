$(function()
{
    $('#PM').change(function()
    {
        var account = $(this).val();
        var link = createLink('projectplan', 'ajaxGetPMDept', 'account=' + account);
        $.post(link, function(data)
        {
            $('#dept').val(data);
            $('#dept').trigger('chosen:updated');
        })
    })
})
