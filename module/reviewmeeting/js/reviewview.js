$(document).ready(function(){
    var toolBarWidth = $(".main-col").width() - 20;
    $(".main-actions").width(toolBarWidth);
    $(window).scroll(function() {
        //if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
        var bL = window.innerHeight - document.getElementById('actionbox').getBoundingClientRect().bottom
        var obj = document.querySelector('.main-actions')
        //判断如果到底取消fixed
        if(bL - obj.clientHeight < 40){
            $(".m-reviewmeeting-reviewview").attr('class','m-reviewmeeting-reviewview main-actions-fixed');
        }else {
            $(".m-reviewmeeting-reviewview").attr('class','m-reviewmeeting-reviewview');
        }
        // if ($(document).scrollTop() >= 0) {
        //      $(".m-review-view").attr('class','m-review-view main-actions-fixed');
        //  }
    });
});

/**
 * 撤回评审 需再次确认
 * @returns {boolean}
 */
function recall()
{
    // if(confirm("确认要撤回评审吗?",'确认窗口')){
    if(confirm("是否确定撤回，一旦撤回将从特定流程重新开始",'确认窗口')){
        return true;
    }else{
        return false;
    }
}

/**
 * 关闭评审 需再次确认
 * @returns {boolean}
 */
function reviewClose(id,count)
{
    if(count > 0){
        if(confirm("存在N个问题未处理，确定要关闭评审吗？",'确认窗口')){
            $.zui.modalTrigger.show({iframe:createLink("reviewmanage","close","reviewID="+id)+"?onlybody=yes"});
            $('#reviewClose'+id).click();
        }
    }else{
        $.zui.modalTrigger.show({iframe:createLink("reviewmanage","close","reviewID="+id)+"?onlybody=yes"});
        $('#reviewClose'+id).click();
    }
}



