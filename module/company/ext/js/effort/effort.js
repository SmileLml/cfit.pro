function loadProductExecutions(productID)
{
    link = createLink('product', 'ajaxGetExecutions', 'productID=' + productID + '&executionID=' + $('#executionIdBox #execution').val());
    $('#executionIdBox .chosen-container').remove();
    $('#executionIdBox select').remove();
    $.get(link, function(data)
    {
        $('#executionIdBox').append(data).find('select').chosen();
    });
}
function loadDeptUsers(deptID)
{
    link = createLink('dept', 'ajaxGetUsers', 'dept=' + deptID + '&user=' + $('#userBox #user').val());
    $('#userBox .chosen-container').remove();
    $('#userBox select').remove();
    $.get(link, function(data)
    {
        $('#userBox').append(data).find('select').chosen();
    })
}
function loadExecutionRelated(){}

$(function()
{
    if($('#effortList thead th.w-work').width() < 150) $('#effortList thead th.w-work').width(150);
    flushWidth('#sidebar .detail');
});

function flushWidth(obj)
{
    var maxWidth = 0;
    $(obj).find('.input-group').each(function()
    {
        var $groupAddon = $(this).find('.input-group-addon:first');
        if($groupAddon.length > 0)
        {
            var width = $(this).find('.input-group-addon:first').outerWidth();
            if(width > maxWidth) maxWidth = width;
        }
    });
    $(obj).find('.input-group').each(function()
    {
        var $groupAddon = $(this).find('.input-group-addon:first');
        var padding     = 1;
        if($groupAddon.length > 0)
        {
            while($groupAddon.outerWidth() < maxWidth)
            {
                $groupAddon.css('padding-right', padding + 'px').css('padding-left', padding + 'px');
                padding++;
            }
        }
    });
}
