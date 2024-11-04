$(function()
{
    changeToHide();
    stageName();
    // setEditSubProjectField($("#outsideProject"))

})

function changeToHide()
{
    let type = $("#type").val();
    if(type == 1 || type == 2){
        $(".tohide").removeClass('hidden');
    } else {
        $(".tohide").addClass('hidden');
    }

    return true;
}
var newRow;
var rowNum = 0;
var stageNum = 0;
 // tongyanqi 2022-04-25
function addItem(obj)
{
    rowNum = $(obj).closest('td').find('.table-row').size();
    if(rowNum >= 50) { alert("最多加50个产品信息"); return; }

    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.picker-selections').remove();
    $next.find('.productCol #productIds_chosen').remove();
    $next.find('.productCol select').val('0').chosen();
    $next.find('.form-date').datepicker();
    $next.find('.form-date').val('');
}


function removeItem(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        $(obj).closest('td').find('#productIds').prop('selectedIndex',0);
        $(obj).closest('td').find('.form-date').val('');
        return false;
    }
    $(obj).closest('.table-row').remove();
}

function addStage(obj)
{
    stageNum = $(obj).closest('td').find('.table-row').size();
    if(stageNum >= 20) { alert("最多加20个阶段"); return; }

    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.form-date').datepicker();
    $next.find('.form-date').val('');
    stageName();
}

function removeStage(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        $(obj).closest('td').find('.form-date').val('');
        return false;
    }
    $(obj).closest('.table-row').remove();
    stageName();
}

function stageName()
{
    let num = 'N';
    $(".stageName").each(function (i){
        switch (i){
            case 0:
                num = "一";
                break;
            case 1:
                num = "二";
                break;
            case 2:
                num = "三";
                break;
            case 3:
                num = "四";
                break;
            case 4:
                num = "五";
                break;
            case 5:
                num = "六";
                break;
            case 6:
                num = "七";
                break;
            case 7:
                num = "八";
                break;
            case 8:
                num = "九";
                break;
            case 9:
                num = "十";
                break;
            case 10:
                num = "十一";
                break;
            case 11:
                num = "十二";
                break;
            case 12:
                num = "十三";
                break;
            case 13:
                num = "十四";
                break;
            case 14:
                num = "十五";
                break;
            case 15:
                num = "十六";
                break;
            case 16:
                num = "十七";
                break;
            case 17:
                num = "十八";
                break;
            case 18:
                num = "十九";
                break;
            case 19:
                num = "二十";
                break;
        }
        $(this).html("第"+num+"阶段");
    })
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
    //不再按工作日计算
   /* for(i = 0; i < delta; i++)
    {   
        if((weekend == 2 && date1.getDay() == 6) || date1.getDay() == 0) weekEnds ++; 
        date1 = date1.valueOf();
        date1 += 1000 * 60 * 60 * 24; 
        date1 = new Date(date1);
    }*/
    return delta - weekEnds;
}

function setSubProjectField(obj)
{
    var outPlanID = $(obj).val();

    if(outPlanID == null) outPlanID = 0;

    var suboutPlanID= $("#outsideSubProject").val();
    $.get(createLink('outsideplan', 'ajaxSubProjects', "outPlanID=" + outPlanID+"&orderBy=id_desc&suboutPlanID="+suboutPlanID), function(data)
    {
        $('#outsideSubProject_chosen').remove();
        $('#outsideSubProject').val('');
        $('#outsideSubProject').replaceWith(data);

        $('#outsideSubProject').chosen();
        setTaskField(data)
    });
}

function setNewSubProjectField(obj)
{
    var outTaskID = $(obj).val();

    if(outTaskID == null) outTaskID = 0;

    // var suboutPlanID= $("#outsideSubProject").val();

    // +"&orderBy=id_desc&suboutPlanID="+suboutPlanID

    $.get(createLink('outsideplan', 'ajaxNewSubProjects', "outTaskID=" + outTaskID), function(data)
    {
        $("#outsideProjectShow").html(data.outsideNames);
        $("#outsideProject").val(data.outsideIDs);
        $("#outsideSubProjectShow").html(data.subOutsideNames);
        $("#outsideSubProject").val(data.subOutsideIDs);
        /*$('#outsideSubProject_chosen').remove();
        $('#outsideSubProject').val('');
        $('#outsideSubProject').replaceWith(data);

        $('#outsideSubProject').chosen();*/
        // setTaskField(data)
    },'json');
}
function setOutPlanField(obj)
{
    var subProjectID = $(obj).val();

    if(subProjectID == null) subProjectID = 0;
    var taskID = $("#outsideTask").val();
    $.get(createLink('outsideplan', 'ajaxTask', "subProjectID=" + subProjectID+"&orderBy=id_desc&taskID="+taskID), function(data)
    {
        $('#outsideTask_chosen').remove();
        $('#outsideTask').val('');
        $('#outsideTask').replaceWith(data);
        $('#outsideTask').chosen();
    });
}
function setTaskField(obj)
{
    var subProjectID = $(obj).val();

    if(subProjectID == null) subProjectID = 0;
    var taskID = $("#outsideTask").val();
    $.get(createLink('outsideplan', 'ajaxTask', "subProjectID=" + subProjectID+"&orderBy=id_desc&taskID="+taskID), function(data)
    {
        $('#outsideTask_chosen').remove();
        $('#outsideTask').val('');
        $('#outsideTask').replaceWith(data);
        $('#outsideTask').chosen();
    });
}
function setEditSubProjectField(obj)
{
    var outPlanID = $(obj).val();

    if(outPlanID == null) outPlanID = 0;

    $.get(createLink('outsideplan', 'ajaxSubProjects', "outPlanID=" + outPlanID), function(data)
    {
        $('#outsideSubProject_chosen').remove();
        var tempval = $('#outsideSubProject').val();
        $('#outsideSubProject').replaceWith(data);

        $('#outsideSubProject').val(tempval);
        $('#outsideSubProject').chosen();
        setEditTaskField($('#outsideSubProject'))
    });
}
function setEditTaskField(obj)
{
    var subProjectID = $(obj).val();

    if(subProjectID == null) subProjectID = 0;
    $.get(createLink('outsideplan', 'ajaxTask', "subProjectID=" + subProjectID), function(data)
    {
         $('#outsideTask_chosen').remove();
        var tempval = $('#outsideTask').val();

        $('#outsideTask').replaceWith(data);
        $('#outsideTask').val(tempval);
        $('#outsideTask').chosen();
    });
}
function checkTaskDate()
{
    end = $('#end').val();
    ids = '';
    $('#outsideTask option:selected').each(function (){
        if($(this).prop('value')){
            ids = ids +","+ ($(this).prop('value')+"");
        }

    })
    if(ids == '') return true;
    $flag = false;
    noticetxt = '';
    $.ajaxSettings.async = false;
    $.get(createLink('projectplan', 'checkTaskDate', )+"?end="+end+"&ids=" + ids, function(data)
    {
        noticetxt = data;
    });
    if(noticetxt != '') {
        if (confirm(noticetxt)) {
            return true;
        } else {
            return false;
        }
    }
}

//计算工作量
function totalWorkload(obj)
{
    obj.value = obj.value.replace(/[^\d.]/g,"");  //清除“数字”和“.”以外的字符
    obj.value = obj.value.replace(/^\./g,"");  //验证第一个字符是数字而不是.
    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的.
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    if(obj.value == 0) { obj.value = "0.0"; }
    obj.value= parseFloat(obj.value).toFixed(1);
    workloadBase            = ($('#workloadBase').val()) - 0;
    workloadChengdu         = ($('#workloadChengdu').val()) - 0;
    nextYearWorkloadBase    = ($('#nextYearWorkloadBase').val()) - 0;
    nextYearWorkloadChengdu = ($('#nextYearWorkloadChengdu').val()) - 0;
    total = workloadBase + workloadChengdu + nextYearWorkloadBase + nextYearWorkloadChengdu;
    $('#workload').val(total);
}