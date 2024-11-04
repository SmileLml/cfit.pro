
$('.checkMeetingOwnerReview').click(function (){
    bootbox.confirm('请确认是否已经确认会议评审纪要？', function (result){
        if((result)){
           $('button[data-bb-handler="cancel"]').click();
           $('.checkMeetingOwnerReview').submit();
           return false;
        }
    });
    return false;
});