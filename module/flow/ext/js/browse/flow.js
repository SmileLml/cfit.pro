$(function()
{
    if(moduleNavigator == 'primary')
    {
        $('#mainHeader #navbar li.active').removeClass('active');
        $('#mainHeader #navbar li[data-id="browse' + label + '"]').addClass('active');
        $('#pageActions .btn-toolbar .iframe').modalTrigger();
    }

    /* Add title for table td. */
    $('.main-table .table tbody tr').each(function()
    {
        $(this).find('td').each(function()
        {
              $(this).attr('title', $(this).text());
        });
    });

    $('table tr td .dropdown .dropdown-menu').closest('td').css('overflow', 'visible');
    $('table tr').each(function(){$(this).find('td:last').removeAttr('title');});
    $('.main-table .table tbody tr').each(function()
    {
        var $aTag = $(this).find('td').eq(0).find('a');
        if($aTag.length > 0) $aTag.prop('outerHTML', $aTag.html());
    });
});
