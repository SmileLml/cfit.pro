$(function (){
    if(status == 'waitFirstAssignDept'){ //指派初审部门
        setIsFirstReviewDef();
    } else if(status == 'waitFormalAssignReviewer'){ //评审主席指派正式审核人员
        var type = $('select[name="type"]').val();
        var grade = $('select[name="grade"]').val();
        setSelectReviewerList(type, reviewer);
        setSelectOwnerList(type, owner);
        //获得会议列表
        ajaxGetMeetingListRenew(type, reviewId);
        //评审方式
        setSelectGradeInfo(grade);
    }
});
//
/**
 * 设置默认是否初审
 */
function setIsFirstReviewDef(){
    var isFirstReviewVal = '';
    var isFirstReviewRadio = $('input[name="isFirstReview"]');
    for (var i = 0; i < isFirstReviewRadio.length; i++){
        if(isFirstReviewRadio[i].checked){
            isFirstReviewVal = isFirstReviewRadio[i].value;
        }
    }
    setIsFirstReview(isFirstReviewVal);
}


/**
 * 设置是否初审
 * @param isFirstReview
 */
function setIsFirstReview(isFirstReviewVal){
    if(isFirstReviewVal == 1){
        $('.firstReview0').addClass('hidden');
        $('.firstReview1').removeClass('hidden');
    }else if(isFirstReviewVal == 2){ //不初审
        $('.firstReview1').addClass('hidden');
        $('.firstReview0').removeClass('hidden');
        ajaxgetmailto(reviewId);
    }
}


/**
 * 评审主席指派正式评审人员页面
 */
/*$('#type').change(function() {
    var type = $(this).val();
    setSelectReviewerList(type);
    setSelectOwnerList(type);
    ajaxGetMeetingListRenew(type, reviewId);
});*/

/**
 * 根据不同评审类型展示不同的评审专员
 *
 * @param type
 */
function setSelectReviewerList(type, selectUser = ''){
    $.get(createLink('review', 'ajaxGetReviewer', "type=" + type + "&bearDept=" + bearDept + "&deptId=" + deptId+ "&selectUser=" + selectUser) , function(data)
    {
        $('#reviewer_chosen').remove();
        $('#reviewer').replaceWith(data);
        $('#reviewer').chosen();
    });
}

/**
 * 根据不同评审类型展示不同的评审主席
 * @param type
 */
function setSelectOwnerList(type, selectUser = ''){
    $.get(createLink('review', 'ajaxGetOwner', "type=" + type + "&bearDept=" + bearDept + "&deptId=" + deptId+ "&selectUser=" + selectUser), function(data) {
        $('#owner_chosen').remove();
        $('#owner').replaceWith(data);
        $('#owner').chosen();
    });
}


/**
 * 评审主席指派正式评审人员页面(不同评审方式，显示不一样)
 */
$('#nextStage').change(function() {
    var nextStage = $(this).val();
    setSelectGradeInfoRenew(nextStage);
    ajaxGetMeetingListRenew(type,'');
});

/**
 * 设置不同评审方式显示信息不一样
 *
 * @param nextStage
 */
function setSelectGradeInfoRenew(nextStage){
    var isSkipMeetingResult = 0;
    if(nextStage == 'meetingReview'){ //在线评审
        $('.gradeMeeting').removeClass('hidden');
        isSkipMeetingResult = 1;
    }else { //会议评审
        $('.gradeMeeting').addClass('hidden');
        isSkipMeetingResult = 2;
    }
    //会议排期信息
    var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
    setMeetingPlanTypeRenew(nextStage, meetingPlanType);
    //是否跳过会议评审结论
    $(":radio[name='isSkipMeetingResult'][value='"+isSkipMeetingResult+"']").prop("checked", "checked");
}

/**
 * 修改会议排期分类
 */
$('input:radio[name="meetingPlanType"]').change(function() {
    var nextStage = $('select[name="nextStage"]').val();
    var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
    setMeetingPlanTypeRenew(nextStage, meetingPlanType);
});


/**
 *会议排期信息(项目评审和指派正式评审专员中使用)
 *
 * @param nextStage
 * @param meetingPlanType
 */
function setMeetingPlanTypeRenew(nextStage, meetingPlanType){
    $('.meetingPlanType-1').addClass('hidden');
    $('.meetingPlanType-2').addClass('hidden');
    if(nextStage == 'meetingReview'){
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
function ajaxGetMeetingListRenew(reviewType, meetingCode = '0'){
    $.get(createLink('reviewmeeting', 'ajaxAllowBindMeetingList', "type=" + reviewType+ "&reviewID=" +  meetingCode+ "&isReNew=1"), function(data) {
        $('#meetingCode_chosen').remove();
        $('#meetingCode').replaceWith(data);
        $('#meetingCode').chosen();
    });
}

