var newRow;
var rowNum = 0;
function addItem(obj)
{
    if(rowNum >= 50) { alert("最多加50条介质信息"); return; }
    rowNum++;
    if(newRow){
        $row.after(newRow.clone());
    } else {
        newRow = $row = $(obj).closest('.table-row');
        $row.after($row.clone());
    }
    $next = $row.next();
    $next.find('.productCol #product_chosen').remove();
    $next.find('.productCol select').val('0').chosen();
    $next.find('.form-date').datepicker();
}

function removeItem(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) return false;
    $(obj).closest('.table-row').remove();
    rowNum--;
}

function loadRelease(obj)
{
    var $releaseCol = $(obj).closest('.table-row').find('.releaseCol .input-group');
    $.get(createLink('release', 'ajaxGetByProduct', "product=" + $(obj).val()), function(data)
    {
        if(!data) data = '<select id="release" name="release[]" class="form-control chosen"></select>';
        $releaseCol.find('select').replaceWith(data);
        $releaseCol.find('#release_chosen').remove();
        $releaseCol.find('select').attr('name', 'release[]').chosen();
    });
}

function refresh()
{
    var selfClose = $.cookie('selfClose');
    $.cookie('selfClose', 0, {expires:config.cookieLife, path:config.webRoot});
    if(selfClose == 1)
    {
        $('#kanbanWrapper').wrap("<div id='tempDIV'></div>");
        $('#tempDIV').load(location.href + ' #kanbanWrapper', function()
        {
            $('#kanbanWrapper').unwrap();
            initBoards()
            $(".kanbanFrame").modalTrigger({type: 'iframe', width: '80%', afterShow:function(){ $('#ajaxModal').data('cancel-reload', true)}, afterHidden: function(){refresh()}});
        });
    }
}

$(function()
{
    $('[data-id="create"] a').modalTrigger({type: 'ajax', width: '1200'});
});

