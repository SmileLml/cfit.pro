
/**
 * 选择日期
 */
$('#startDate').change(function () {
    start = $('#startDate').val();

});

$('#endDate').change(function () {
    end = $('#endDate').val();
});

/**
 * 创建报工
 */
function createWork() {
    var originIndex = 0;
    supportUserIndex++;
    var currentRow = $('#workReportLineDemo').children(':first-child').clone();
    currentRow.find('#addWorkItem' + originIndex).attr({'data-id': supportUserIndex, 'id':'addWorkItem' + supportUserIndex});
    currentRow.find('#supportUserTd' + originIndex).attr({'id':'supportUserTd' + supportUserIndex});
    currentRow.find('#supportUser' + originIndex + '_chosen').remove();
    currentRow.find('#supportUser' + originIndex).attr({'id':'supportUser' + supportUserIndex,'data-index':supportUserIndex});

    currentRow.find("input[name*='supportDate[]']").val('');
    currentRow.find("input[name*='consumed[]']").val('');
    currentRow.find("input[name*='workReportId[]']").val('');
    $('#createWorkTr').attr('class','hidden'); //隐藏添加按钮
    $('#supportUserTBody').append(currentRow);
    $('#supportUser' + supportUserIndex).attr('class','form-control chosen supportUserSelect');
    $('#supportUser' + supportUserIndex).chosen();
    $('#supportUser' + supportUserIndex).val('').trigger("chosen:updated");
    setDefaultDateTime();
    sortOrderLine();
}
/**
 * 增加报工
 *
 * @param obj
 */
function addWork(obj){
    //var originIndex = $(obj).attr('data-id');
    var originIndex = 0;
    supportUserIndex++;
    var currentRow = $('#workReportLineDemo').children(':first-child').clone();
    currentRow.find('#addWorkItem' + originIndex).attr({'data-id': supportUserIndex, 'id':'addWorkItem' + supportUserIndex});
    currentRow.find('#supportUserTd' + originIndex).attr({'id':'supportUserTd' + supportUserIndex});
    currentRow.find('#supportUser' + originIndex + '_chosen').remove();
    currentRow.find('#supportUser' + originIndex).attr({'id':'supportUser' + supportUserIndex,'data-index':supportUserIndex});

    currentRow.find("input[name*='supportDate[]']").val('');
    currentRow.find("input[name*='consumed[]']").val('');
    currentRow.find("input[name*='workReportId[]']").val('');

    $(obj).parent().parent().parent().after(currentRow);
    $('#supportUser' + supportUserIndex).attr('class','form-control chosen supportUserSelect');
    $('#supportUser' + supportUserIndex).chosen();
    $('#supportUser' + supportUserIndex).val('').trigger("chosen:updated");
    setDefaultDateTime();
    sortOrderLine();
}

/**
 * 删除记录
 *
 * @param obj
 */
function delWork(obj) {
    var currentRow = $(obj).parent().parent().parent();
    var count = $("#supportUserTBody select[name*='supportUser[]']").length;
    // if(count > 1) {
    //     currentRow.remove();
    // }else if(count == 1){
    //     $("#supportUserTBody select[name*='supportUser[]']").val('').trigger("chosen:updated");
    //     $("#supportUserTBody input[name*='supportDate[]']").val('');
    //     $("#supportUserTBody input[name*='consumed[]']").val('');
    // }
    currentRow.remove();
    if(count == 1){ //删除后没有
        $('#createWorkTr').removeClass('hidden');//隐藏添加按钮
    }else if(count > 1){
        sortOrderLine();
    }
}

/**
 * 排序
 */
function sortOrderLine() {
    var keyIndex = 0;
    $('#supportUserTBody').children('tr').each(function (index) {
        if($(this).attr('id') != 'createWorkTr'){
            keyIndex++;
            $(this).attr('id', 'supportUserInfo_' + keyIndex);
            $(this).children(':first-child').text(keyIndex);
        }
    });
}

/**
 * 设置时间
 */
function setDefaultDateTime() {
    if(typeof start === 'undefined' || start == ''|| start == '0000-00-00 00:00:00' || start == '0000-00-00 00:00'){
        start = $('#startDate').val();
    }
    if(typeof end == 'undefined'|| end == ''|| end == '0000-00-00 00:00:00' || end == '0000-00-00 00:00'){
        end = $('#endDate').val();
    }
    $(".form-date").datetimepicker(
        {
            weekStart: 1,
            todayBtn:  0,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            format: "yyyy-mm-dd",
            startDate: start,
            endDate: end
     });
}





/**
 * 变更支持地点
 */
function changeArea(areaVal) {
    if(areaVal == '1'){
        $('.jxInfo').removeClass('hidden');
        $('#sysper').parent().addClass('required');
        $('#jxdepart').parent().addClass('required');


    }else {
        $('.jxInfo').addClass('hidden');
        $('#jxdepart').parent().removeClass('required');

    }
}

/*
function changeAppIds() {
    var appIds = [];
    $("#appIds option:selected").each(function () {
        if ($(this).val() != ''){
            var appId = $(this).val();
            appIds.push(appId);
        }
    });
    //原有抄送人员
    var oldAppIds = [];
    $('#owndeptAndSjTBody input[name="appId[]"]').each(function () {
        if ($(this).val() != ''){
            var oldAppId = $(this).val();
            oldAppIds.push(oldAppId);
        }
    });

    var appLength = appIds.length;
    if(appLength == 0){
        $('#owndeptAndSjTBody').html('');
        $('#owndeptInfo').addClass('hidden');
    }else {
        $.each(appIds, function (index, value) {
            if($.inArray(value, oldAppIds) == -1){ //不在数组中新增
                var appInfo = appDataList[value];
                var team     = appInfo['team'];
                var fromUnit = appInfo['fromUnit'];
                if(!team || !fromUnit){ //为空重新获取
                    $.ajaxSettings.async = false;
                    $.get(createLink('localesupport', 'ajaxGetAppInfo', 'appId=' + value), function(res){
                        var data = res.data;
                        team = data['team'];
                        fromUnit = data['fromUnit'];

                    }, 'json');
                    $.ajaxSettings.async = true;
                }
                var currentRow = $('#owndeptLineDemo').children(':first-child').clone();
                currentRow.find('#tempAppId0_chosen').remove();
                currentRow.find('#tempAppId0').attr({'id':'tempAppId' + value});
                currentRow.find('#appId0').attr({'id':'appId' + value});

                currentRow.find('#owndept0_chosen').remove();
                currentRow.find('#owndept0').attr({'id':'owndept' + value});

                currentRow.find('#sj0_chosen').remove();
                currentRow.find('#sj0').attr({'id':'sj' + value});


                $('#owndeptAndSjTBody').append(currentRow);
                $('#tempAppId' + value).attr('class','form-control chosen');
                $('#tempAppId' + value).chosen();
                $('#tempAppId' + value).val(value).trigger("chosen:updated");
                $('#appId' + value).val(value);

                $('#owndept' + value).attr('class','form-control chosen');
                $('#owndept' + value).chosen();
                $('#owndept' + value).val(team).trigger("chosen:updated");

                $('#sj' + value).attr('class','form-control chosen');
                $('#sj' + value).chosen();
                $('#sj' + value).val(fromUnit).trigger("chosen:updated");
            }
        });


        $.each(oldAppIds, function (index, value) {
            if($.inArray(value, appIds) == -1){ //不在数组中去掉
                $('#appId'+value).parent().parent().remove();
            }
        });
        $('#owndeptInfo').removeClass('hidden');
    }

}
*/

/**
 * 修改支持部门
 */
/*
function changeDeptIds() {
    var deptIds = [];
    $("#deptIds option:selected").each(function () {
        if ($(this).val() != ''){
            var deptId = $(this).val();
            deptIds.push(deptId);
        }
    });
    var deptIdsStr = deptIds.join(',');
    $.get(createLink('localesupport', 'ajaxGetUsersByDeptIds', 'deptIds=' + deptIdsStr), function(data){
        $('#supportUsers_chosen').remove();
        $('#supportUsers').replaceWith(data[0]);
        $('#supportUsers').chosen();
        
        $('#deptManagers_chosen').remove();
        $('#deptManagers').replaceWith(data[1]);
        $('#deptManagers').chosen();

    },'json');
}
*/


/**
 * 修改支持人员
 */
function changeSupportUsers() {
    var supportUsers = [];
    $("#supportUsers option:selected").each(function () {
        if ($(this).val() != ''){
            var supportUser = $(this).val();
            supportUsers.push(supportUser);
        }
    });
    var count = supportUsers.length;
    if(count > 0){
        if($('#deptIdsLabel').length > 0){  //部门
            $('#deptIdsLabel').remove();
        }
        if($('#deptManagersLabel').length > 0){ //部门负责人
            $('#deptManagersLabel').remove();
        }
        $('.reportWorkInfo').removeClass('hidden');
    }else {
        $('.reportWorkInfo').addClass('hidden');
    }
    var supportUserStr = supportUsers.join(',');
    $.get(createLink('localesupport', 'ajaxGetDeptAndManagersUsers', 'supportUsers=' + supportUserStr), function(data){
        $('#deptManagers_chosen').remove();
        $('#deptManagers').replaceWith(data[0]);
        $('#deptManagers').chosen();
        var deptIds = $('#deptManagers').attr('node-dept');
        var deptIdsArray = deptIds.split(",");
        $('#tempDeptIds').val(deptIdsArray).trigger("chosen:updated");
        $('#deptIds').val(deptIds);

        $('#workReportLineDemo #supportUser0_chosen').remove();
        $('#workReportLineDemo #supportUser0').replaceWith(data[1]);
        $('#workReportLineDemo #supportUser0').chosen();

        //修改已经展示的用户列表
        $('#supportUserTBody .supportUserSelect').each(function () {
            var tagIndex = $(this).attr('data-index');
            var tagVal   = $(this).val();
            var workReportInfo = {
                'tagIndex':tagIndex,
                'tagVal':tagVal
            };
            updateWork(workReportInfo);
        });
    },'json');


    /**
     *更新报工
     */
    function updateWork(workReportInfo) {
        var originIndex = 0;
        var tagIndex = workReportInfo.tagIndex;
        var tagVal   = workReportInfo.tagVal;
        var currentSupportUserInfo = $('#workReportLineDemo').find('#supportUserTd0').children().clone();
        currentSupportUserInfo.find('#supportUser' + originIndex + '_chosen').remove();
        currentSupportUserInfo.find('#supportUser' + originIndex).attr({'id':'supportUser' + tagIndex,'data-index':tagIndex});
        $('#supportUserTBody #supportUser'+tagIndex+'_chosen').remove();
        $('#supportUserTBody #supportUserTd'+tagIndex).html('');
        $('#supportUserTBody #supportUserTd'+tagIndex).html(currentSupportUserInfo);
        $('#supportUser' + tagIndex).attr('class','form-control chosen supportUserSelect');
        $('#supportUser' + tagIndex).chosen();
        $('#supportUser' + tagIndex).val(tagVal).trigger("chosen:updated");
    }


    /*抄送人员暂时隐藏
    *
    var mailtoUsers  = [];
    var supportUsersCount = supportUsers.length;
    if(supportUsersCount == 0){
        return true;
    }
    //原有抄送人员
    $("#mailto option:selected").each(function () {
        if ($(this).val() != ''){
            var mailtoUser = $(this).val();
            mailtoUsers.push(mailtoUser);
        }
    });

    $.each(supportUsers, function (index, value) {
        if($.inArray(value,mailtoUsers) == -1){ //不在数组中
            mailtoUsers.push(value);
        }
    });
    */
    //$('#mailto').val(supportUsers).trigger('chosen:updated');
}

/**
 * 确认保存
 *
 * @param message
 * @returns {boolean}
 */
function confirmSave(message) {
    $("#isWarn").val("yes");
    if(confirm(message)){
        $("#isWarn").val("no");
        $('#submit').submit();
        return  true;
    }else {
        return false;
    }
}

/**
 * 提交操作
 *
 * @param btnClass
 */
function submitData(btnClass) {
    $('.buttonInfo').attr('type', 'button');
    $('.'+btnClass).attr('type', 'submit');
}

/**
 * 修改审批结果
 */
function changeDealResult() {
    var result = $('#dealResult').val();
    if(result == 'pass') {
        $('#suggestTd').removeClass('required');
    } else {
        $('#suggestTd').addClass('required');
    }
}

/**
 * 设置开始时间最小时间
 */
function setMinStartDate() {
    if(typeof minStartDate !== 'undefined' && minStartDate != '0000-00-00 00:00:00'){
        $(".form-datetime").datetimepicker({
            startDate: minStartDate
        });
    }
}