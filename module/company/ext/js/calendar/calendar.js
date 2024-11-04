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
    var $showdata = $('#showdata');
    $showdata.load($showdata.data('url'), function()
    {
        if($.fn.datatable)
        {
            $showdata.find('table.datatable').datatable({fixedLeftWidth: 200, scrollPos: 'out', customizable: false, sortable: false, mergeRows: true,
                ready:function(e){
                    setTimeout(function() {fixScroll(); }, 100);
                    setTimeout(function() {
                        $('.bar').css('left', '0px');
                        $('.datatable-wrapper .table-datatable').css('left', '0px')
                    }, 100);
                }
            });
        }
    });

    var dateVal = $('#featurebar ul.nav li #date').val();
    $('#date').focus(function(){$(this).val('').datetimepicker('update');}).blur(function(){$(this).val(dateVal)});
    if($('#showAll').prop('checked'))
    {
        $('.side #user').attr('disabled', true);
        $('.side #user').trigger("chosen:updated");
    }
    $('#showAll').change(function()
    {
        if($(this).prop('checked'))
        {
            $('.side #user').attr('disabled', true);
        }
        else
        {
            $('.side #user').removeAttr('disabled');
        }
        $('.side #user').trigger("chosen:updated");
    });
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

function fixScroll()
{
    var $scrollwrapper = $('div.datatable').first().find('.scroll-wrapper:first');
    if($scrollwrapper.size() == 0)return;

    var $tfoot       = $('div.datatable').first().find('table tfoot:last');
    var scrollOffset = $scrollwrapper.offset().top + $scrollwrapper.find('.scroll-slide').height();
    if($tfoot.size() > 0) scrollOffset += $tfoot.height();
    if($('div.datatable.head-fixed').size() == 0) scrollOffset -= '29';
    var windowH = $(window).height();
    var bottom  = $tfoot.hasClass('fixedTfootAction') ? 15 + $tfoot.height() : 15;
    if(typeof(ssoRedirect) != "undefined") bottom = 15;
    if(scrollOffset > windowH + $(window).scrollTop()) $scrollwrapper.css({'position': 'fixed', 'bottom': bottom + 'px'});
    $(window).scroll(function()
    {
          newBottom = $tfoot.hasClass('fixedTfootAction') ? 15 + $tfoot.height() : 15;
          if(typeof(ssoRedirect) != "undefined") newBottom = 15;
          if(scrollOffset <= windowH + $(window).scrollTop())
          {
              $scrollwrapper.css({'position':'relative', 'bottom': '0px'});
          }
          else if($scrollwrapper.css('position') != 'fixed' || bottom != newBottom)
          {
              $scrollwrapper.css({'position': 'fixed', 'bottom': newBottom + 'px'});
              bottom = newBottom;
          }
    });
}
