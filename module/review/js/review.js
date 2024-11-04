$(function (){
    if(status == 'waitFormalOwnerReview'){
        //初始化获得会议列表
        ajaxGetMeetingList(type, reviewId);
    }

    $('#result').change(function (){
        var resultVal = $(this).val();
        if($.inArray(status, allowAssignVerifyersStatusList) >= 0) { //评审主席设置评审结论
            //初始化评审页面
            initReviewFormalOwnerReview();
            if(resultVal == 'passNeedEdit'){
                $('.verifyReviewers').removeClass('hidden');
            }else {
                $('.verifyReviewers').addClass('hidden');
                //确定线上评审结论且是会议评审
                if(status == 'waitFormalOwnerReview' && resultVal == 'meeting'){
                    //设置会议评审信息
                    setSelectGradeInfo(resultVal);
                }
            }
        }
    });
});



/**
 * 设置不同评审方式显示信息不一样
 *
 * @param gradeType
 */
function setSelectGradeInfo(gradeType){
    if(gradeType == 'meeting'){ //会议评审
        $('.gradeMeeting').removeClass('hidden');

    }else { //在线评审
        $('.gradeMeeting').addClass('hidden');
    }
    //会议排期信息
    var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
    setMeetingPlanType(gradeType, meetingPlanType);
}

/**
 * 修改会议排期分类
 */
$('input:radio[name="meetingPlanType"]').change(function() {
    //var grade = $('select[name="grade"]').val();
    var grade = 'meeting';
    var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
    setMeetingPlanType(grade, meetingPlanType);
});

var relevantIndex = 1;
//添加
function addRelevantItem(obj)
{
    var relevantObj  = $('#baselineTable');
    var relevantHtml = relevantObj.clone();
    relevantIndex++;

    relevantHtml.find('#codePlus0').attr({'id':'codePlus' + relevantIndex, 'data-id': relevantIndex});
    relevantHtml.find('#codeClose0').attr({'id':'codeClose' + relevantIndex, 'data-id': relevantIndex});

    relevantHtml.find('#baseLineType0').attr({'id':'baseLineType' + relevantIndex});
    relevantHtml.find('#baseLinePath0').attr({'id':'baseLinePath' + relevantIndex});
    relevantHtml.find('#baselineTable').attr({'id':'baselineTable' + relevantIndex});

    var objIndex = $(obj).attr('data-id');
    $('#baselineTable' + objIndex).after(relevantHtml.html());

    $("#baseLineType0_chosen").remove();
    $('#baseLineType' + relevantIndex).attr('class','form-control chosen');
    $('#baseLineType' + relevantIndex).chosen();


}
//刪除
function delRelevantItem(obj)
{
    var objIndex = $(obj).attr('data-id');
    $('#baselineTable' + objIndex).remove();
}

//是否委派他人验证
function setIsAppointOther(isAppointOther){
    if(isAppointOther == 1){ //委派
        $('.appointVerify').removeClass('hidden');
        $('.verifying').addClass('hidden');
    }else { //不委派
        $('.verifying').removeClass('hidden');
        $('.appointVerify').addClass('hidden');
    }
}

/**
 *会议排期信息(项目评审和指派正式评审专员中使用)
 *
 * @param gradeType
 * @param meetingPlanType
 */
function setMeetingPlanType(gradeType, meetingPlanType){
    $('.meetingPlanType-1').addClass('hidden');
    $('.meetingPlanType-2').addClass('hidden');
    if(gradeType == 'meeting'){
        if(meetingPlanType == 1){
            $('.meetingPlanType-2').addClass('hidden');
            $('.meetingPlanType-1').removeClass('hidden');
        }else if (meetingPlanType == 2){
            $('.meetingPlanType-1').addClass('hidden');
            $('.meetingPlanType-2').removeClass('hidden');
        }
    }
}

/**
 *获得会议列表
 *
 * @param reviewType
 */
function ajaxGetMeetingList(reviewType, meetingCode = ''){
    $.get(createLink('reviewmeeting', 'ajaxAllowBindMeetingList', "type=" + reviewType+ "&reviewID=" +  meetingCode), function(data) {
        $('#meetingCode_chosen').remove();
        $('#meetingCode').replaceWith(data);
        $('#meetingCode').chosen();
    });
}

/**
 * 初始化评审主席线上评审结论
 *
 */
function initReviewFormalOwnerReview() {
    $('.gradeMeeting').addClass('hidden');
    $('.meetingPlanType-1').addClass('hidden');
    $('.meetingPlanType-2').addClass('hidden');
    $('.verifyReviewers').addClass('hidden');
}
