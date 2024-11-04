$(function()
{
    //if(typeof mode === 'string')
    //{
    //    $('#subNavbar li[data-id=' + mode + ']').addClass('active');
    //    if(typeof rawMethod === 'string' && rawMethod == 'work') $('#subNavbar li[data-id=' + mode + '] a').append('<span class="label label-light label-badge">' + total + '</span>');
    //}
    var scp  = $('[data-id="changePassword"] a');
    var sign = config.requestType == 'GET' ? '&' : '?';
    scp.attr('href', scp.attr('href') + sign + 'onlybody=yes').modalTrigger({width:500, type:'iframe'});

    //地盘跳转详情页后返回需仍返回地盘地盘列表页
    $("tbody a").each(function () {
        var _href = $(this).attr("href");
        if(_href){
            var arr = _href.split("-");
            if ($.inArray("view",arr) != -1){
                _href = _href.replace(".html",'-workWaitList.html')
                $(this).attr("href",_href)
            }
        }
    })
});

if(typeof mode !== 'undefined'){
    //每5分钟执行一次
    setInterval(getWaitCount, 300000);
}

/**
 * 获得待办数量
 */
function getWaitCount() {
    var url = createLink('my', 'ajaxGetPendingSummary');
    var data = {};
    $.post(url, data, function(ret){
        //地盘二级菜单
        var isChange = false; //当前页面是否需要刷新
        for(var secondKey in ret) {
            if(secondKey != 'reviewObject'){
                var newTotal = ret[secondKey];
                var totalTag = ($('#subNavbar').find('li[data-id="'+secondKey+'"]')).children().children("span:eq(0)");
                var oldTotal = totalTag.html();
                if(newTotal != oldTotal){ //数量不一样
                    //初始化数据
                    totalTag.removeClass('red-pending');
                    var pendingClass = '';
                    if(newTotal > 0) {
                        pendingClass = 'red-pending';
                    }
                    totalTag.text(newTotal);
                    totalTag.addClass(pendingClass);
                    if(!isChange && (mode != 'audit') && (secondKey == mode)){ //重新加载二级菜单
                        isChange = true;
                    }
                }
            }
        }
        //是否需要刷新
        if(isChange){
            var webUrl = window.location.href;
            var tempWebUrl = getAsyncGetWaitCount(webUrl);
            window.location.href = tempWebUrl;
        }

        //待审批
        if(mode == 'audit'){
            //地盘三级菜单
            var res = ret['reviewObject'];
            var isChange = false; //当前页面是否需要刷新
            for(var newMode in res) {
                var newTotal = res[newMode];
                var totalTag = $('#audit' + newMode).children("span:eq(1)");
                var oldTotal = totalTag.text();
                if(newTotal != oldTotal){ //数量不一样
                    //初始化数据
                    totalTag.removeClass('red-pending');
                    var pendingClass = '';
                    if(newTotal > 0) {
                        pendingClass = 'red-pending';
                    }
                    totalTag.text(newTotal);
                    totalTag.addClass(pendingClass);
                    if(!isChange && (browseType == newMode)){ //重新加载三级菜单
                        isChange = true;
                    }
                }
            }
            //是否需要刷新
            if(isChange){
                var webUrl = window.location.href;
                var tempWebUrl = getAsyncGetWaitCount(webUrl);
                window.location.href = tempWebUrl;
            }
        }

    }, 'json');
}

/**
 * 获得设置异步刷新后数量不一致的跳转url
 *
 * @param webUrl
 * @returns {string|*}
 */
function getAsyncGetWaitCount(webUrl) {
    var tempUrl = webUrl.replace(".html",''); //去掉后缀
    var tempUrlArray = tempUrl.split("-");
    if(tempUrlArray[2] == undefined){
        tempUrlArray[2] = 'task';
    }
    if(tempUrlArray[3] == undefined){
        tempUrlArray[3] = 'assignedTo';
    }
    if(tempUrlArray[4] == undefined){ //排序
        tempUrlArray[4] = 'id_desc';
    }
    if(tempUrlArray[5] == undefined){ //总数量
        tempUrlArray[5] = '0';
    }
    if(tempUrlArray[6] == undefined){  //每页数量
        tempUrlArray[6] = '20';
    }
    if(tempUrlArray[7] == undefined){ //每页页数
        tempUrlArray[7] = '1';
    }
    if(tempUrlArray[8] == undefined){ //新传入的值是为了实现异步刷新数量但是不重置缓存
        tempUrlArray[8] = 'asyncGetWaitCount';
    }
    webUrl = tempUrlArray.join('-')+'.html';
    return webUrl;
}
