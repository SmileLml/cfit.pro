$(document).ready(function(){
    $.get(createLink('reviewmeeting', 'ajaxGetCounts'), function(data)
    {
        var obj = JSON.parse(data);
        $('#subNavbar ul>li a ').eq(0).html("会议列表"+ "<span class='label label-light label-badge'>"+obj.all+"</span>");
        $('#subNavbar ul>li a ').eq(1).html("会议日程"+"<span class='label label-light label-badge'>"+obj.suremeet+"</span>");
        $('#subNavbar ul>li a ').eq(2).html("未排会议"+"<span class='label label-light label-badge'>"+obj.wait+"</span>");
    });
});