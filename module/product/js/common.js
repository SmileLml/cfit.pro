$(document).ready(function()
{
    if(window.noProject) $('#aclprivate').parents('.radio').remove();
})

function setWhite(acl)
{
    acl != 'open' ? $('#whitelistBox').removeClass('hidden') : $('#whitelistBox').addClass('hidden');
}


var newRow;
var rowNum = 0;
// tongyanqi 2022-04-25
function addItem(obj)
{
    if(rowNum >= 50) { alert("最多加10个产品编号"); return; }
    rowNum++;
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.input-product-code').val('');
    $next.find('.form-datetime').val('');
    $next.find('.input-product-comment').val('');
    $next.find('.form-datetime').datetimepicker('yyyy-MM-dd HH:mm:ss');
}

function removeItem(obj,id)
{
    $row = $(obj).closest('.table-row');
    var value = $row.find('.input-product-code').val();
    var value1 = value.replace(/-/g,'');
    var value2 = value.replace(/ /g,'');
    $.post(createLink('product', 'ajaxIsNew'), {value:value2,id:id}, function (data) {

        if(data == 1&&$(obj).closest('td').find('.table-row').size() != 1){
            if(!confirm('该产品编号可能已被使用，确认要删除吗？')){
                return false;
            }
        }else if($(obj).closest('td').find('.table-row').size() == 1){
            if(!confirm('至少保留一个产品编号')){
                return false;
            }
        }
        if($(obj).closest('td').find('.table-row').size() == 1) return false;
        $(obj).closest('.table-row').remove();
        rowNum--;

    });

}

function computeWorkDays()
{
    var begin = $('#begin').val();
    var end   = $('#end').val();
    if(!end || !begin) return false;

    var days = computeDaysDelta(begin, end);
    if(days < 0) return false;
    $('#duration').val(days);
}

function convertStringToDate(dateString)
{
    dateString = dateString.split('-');
    return new Date(dateString[0], dateString[1] - 1, dateString[2]);
}

function computeDaysDelta(date1, date2)
{
    date1 = convertStringToDate(date1);
    date2 = convertStringToDate(date2);
    delta = (date2 - date1) / (1000 * 60 * 60 * 24) + 1;

    weekEnds = 0;
    for(i = 0; i < delta; i++)
    {
        if((weekend == 2 && date1.getDay() == 6) || date1.getDay() == 0) weekEnds ++;
        date1 = date1.valueOf();
        date1 += 1000 * 60 * 60 * 24;
        date1 = new Date(date1);
    }
    return delta - weekEnds;
}

