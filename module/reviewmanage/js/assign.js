/**
 * 选择部门时，抄送人同步负责人多
 * @param deptId
 */
function ajaxGetManage(deptId){

    var arrays =[];
    $('[name="depts[]"]:checked').each(function (){
        arrays.push($(this).val());
    })

    $.get(createLink('reviewmanage', 'ajaxgetmanagers', "deptId=" + arrays), function(data)
    {
        $('#mailto_chosen').remove();
        $('#mailto').replaceWith(data);
        $('#mailto').chosen();
        $('#mailtoLabel').remove();
    });
}
$('#type').change(function()
{
    var Type = $(this).val();
    //部门评审时,将外部专家隐去----需求收集1284
    if(Type=='dept'){
        $('#reviewedBy').closest('tr').addClass('hidden');
        $('#outside').closest('tr').addClass('hidden');
    }else {
        $('#reviewedBy').removeClass('hidden');
        $('#outside').removeClass('hidden');
    }
    setSelectReviewerList(Type);
    setSelectOwnerList();
    ajaxGetMeetingList(Type, reviewId);

});

/**
 评审主席变化时，抄送人跟着一起变化
 * @param reviewId
 */
function ajaxgetmailto(reviewId){
    var  reviewId =$(".label-id")[0].textContent;
    $.get(createLink('reviewmanage', 'ajaxgetmailto', "reviewId=" + reviewId), function(data)
    {
        $('#mailto_chosen').remove();
        $('#mailto').replaceWith(data);
        $('#mailto').chosen();
        $('#mailtoLabel').remove();
        $('#mailto').nextAll()[1] .remove();
    });
}

$(function (){
    if(status == 'waitFirstAssignDept'){ //指派初审部门
        setIsFirstReviewDef();
    } else if(status == 'waitFormalAssignReviewer'){ //评审主席指派正式审核人员
        var type = $('select[name="type"]').val();
        var grade = $('select[name="grade"]').val();
        setSelectReviewerList(type, reviewer);
        //setSelectOwnerList();
        //获得会议列表
        ajaxGetMeetingList(type, reviewId);
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
 * 根据不同评审类型展示不同的评审专员
 *
 * @param type
 */
function setSelectReviewerList(type, selectUser = ''){
    $.get(createLink('review', 'ajaxGetReviewer', "type=" + type  + "&bearDept=" + bearDept + "&deptId=" + deptId+ "&selectUser=" + selectUser) , function(data)
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
function setSelectOwnerList(){
    var type  = $('select[name="type"]').val();
    var grade = $('select[name="grade"]').val();
    var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
    var selectUser = '';
    if((grade == 'meeting') && (meetingPlanType == 1 || meetingPlanType == 4)){ //会议评审且是选择已有会议
        var meetingCode = $('select[name="meetingCode"]').val();
        if(meetingCode){
            selectUser = meetingOwnerList[meetingCode];
        }
    }
    var params = "type=" + type  + "&bearDept=" + bearDept + "&deptId=" + deptId;
    if(selectUser){
        params += "&selectUser=" + selectUser;
    }

    $.get(createLink('review', 'ajaxGetOwner', params), function(data) {
        $('#owner_chosen').remove();
        $('#owner').replaceWith(data);
        $('#owner').chosen();
    });
}

/**
 * 评审主席指派正式评审人员页面(不同评审方式，显示不一样)
 */
$('#grade').change(function() {
    var grade = $(this).val();
    var type  = $('#reviewType').val();
    setSelectGradeInfo(grade);
    if(type != 'manage' && role == 'cto') {
        setSelectOwnerList();
    }
});

/**
 * 绑定会议的修改
 */
$('#meetingCode').change(function() {
    setSelectOwnerList();
});

/**
 * 设置不同评审方式显示信息不一样
 *
 * @param gradeType
 */
function setSelectGradeInfo(gradeType){
    var isSkipMeetingResult = 0;
    if(gradeType == 'meeting'){ //在线评审
        $('.gradeMeeting').removeClass('hidden');
        isSkipMeetingResult = 1;
    }else { //会议评审
        $('.gradeMeeting').addClass('hidden');
        isSkipMeetingResult = 2;
    }
    //会议排期信息
    var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
    setMeetingPlanType(gradeType, meetingPlanType);
    //是否跳过会议评审结论
    $(":radio[name='isSkipMeetingResult'][value='"+isSkipMeetingResult+"']").prop("checked", "checked");
}

/**
 * 修改会议排期分类
 */
$('input:radio[name="meetingPlanType"]').change(function() {
    var grade = $('select[name="grade"]').val();
    var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
    setMeetingPlanType(grade, meetingPlanType);
    if(role == 'cto') {
        setSelectOwnerList(); //设置评审主席
    }
});


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
        if( meetingPlanType == 1 ||meetingPlanType == 4 ){
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
function ajaxGetMeetingList(reviewType, reviewID = ''){
    $.get(createLink('reviewmeeting', 'ajaxAllowBindMeetingList', "type=" + reviewType+ "&reviewID=" +  reviewID), function(data) {
        $('#meetingCode_chosen').remove();
        $('#meetingCode').replaceWith(data);
        $('#meetingCode').chosen();
    });
}







