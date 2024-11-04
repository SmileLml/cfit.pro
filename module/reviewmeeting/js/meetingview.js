$(document).ready(function(){
    $('#meetingreview').addClass("active");
});
$(document).ready(function(){
    var toolBarWidth = $(".main-col").width() - 20;
    $(".main-actions").width(toolBarWidth);
    $(window).scroll(function() {
        //if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
        var bL = window.innerHeight - document.getElementById('actionbox').getBoundingClientRect().bottom
        var obj = document.querySelector('.main-actions')
        //判断如果到底取消fixed
        if(bL - obj.clientHeight < 40){
            $(".m-reviewmeeting-meetingview").attr('class','m-reviewmeeting-meetingview main-actions-fixed');
        }else {
            $(".m-reviewmeeting-meetingview").attr('class','m-reviewmeeting-meetingview');
        }
        // if ($(document).scrollTop() >= 0) {
        //      $(".m-review-view").attr('class','m-review-view main-actions-fixed');
        //  }
    });
});