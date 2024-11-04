$(document).ready(function()
{
    $('#mainNavbar li').removeClass('active');

    if(type == 'table') $('#mainNavbar > .container > nav > .navbar-nav > li').find('a[href*=browseDB]').parent('li').addClass('active');
    if(type == 'flow')  $('#mainNavbar > .container > nav > .navbar-nav > li').find('a[href*=browseFlow]').parent('li').addClass('active');

    if(type == 'flow' && status) $('#menu > .container > .nav > li').removeClass('active').find('a[href*=browseFlow][href*=' + status + ']').parent().addClass('active');
 
    $(document).on('change', '#app', function()
    {
        if($(this).val()) $('select#positionModule').load(createLink('workflow', 'ajaxGetAppMenus', 'app=' + $(this).val() + '&exclude=' + currentModule));
    });

    $('.flow-toggle').click(function()
    {
        var obj = $(this).find('i');
        if(obj.hasClass('icon-plus'))
        {
            obj.parents('tr').next('tr').show();
            obj.removeClass('icon-plus').addClass('icon-minus');
        }
        else if(obj.hasClass('icon-minus'))
        {
            obj.parents('tr').next('tr').hide();
            obj.removeClass('icon-minus').addClass('icon-plus');
        }
        return false;
    });

    $('a.mode-toggle').click(function()
    {
        var mode = $(this).data('mode');
        $('a.mode-toggle').removeClass('active');
        $(this).addClass('active');
        $('#cardMode, #listMode').hide();
        $('#' + mode + 'Mode').show();
        $('#cardMode').next().toggle(mode == 'card');
        $.cookie('flowViewType', mode, {path: "/"});
    })

    var type = $.cookie('flowViewType');
    if(typeof(type) == 'undefined' || type == '') type = 'card';
    $('#menuActions a[data-mode=' + type +']').click();

    $('.footerbar a').click(function()
    {
    });

    $(document).on('click', '.deactivater', function()
    {
        if(confirm(confirmToDeactivate)) 
        {
            $.getJSON($(this).attr('href'), function(data)
            {
                if(data.result == 'fail') alert(data.message);

                return location.reload();
            })
        }
        return false;
    })

    $(document).on('click', '.activater', function()
    {
        var reload = $(this);
        $.getJSON(reload.attr('href'), function(data)
        {
            if(data.result == 'fail') alert(data.message);
            return location.reload();
        });

        return false;
    })
});
