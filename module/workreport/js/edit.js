/*获取所属活动*/
function getActivity(obj){
    var index =  $(obj).attr("data-id");
    console.log(index);
    var project = $('#project'+index).val();
    $.get(createLink('workreport', 'ajaxGetActivity','project='+project+"&index="+index), function(data)
    {
        $('#activity'+index+"_chosen").remove();
        $('#activity'+index).replaceWith(data);
        $('#activity'+index).chosen();
    });
    //操作项目，清空所属活动、阶段、任务
    //var apps = $('#apps'+index).val();
   // var objects = $('#objects'+index).val();
    $('#activity'+index).val('');
    $('#apps'+index).val('') ;
    $('#objects'+index).val('');
    //if(apps || objects){
        getApps(obj);
        getTasks(obj);
       /* $('#beginDate\\[' + index + '\\]').val('');
        $('#endDate\\[' + index + '\\]').val('');*/
    //}
    /*if(project){
        getBeginAndEnd(obj);
    }*/
    if(project && append){
        getBeginAndEnd(obj);
    }

}
/*获取阶段/应用系统*/
function getApps(obj){
    var index =  $(obj).attr("data-id");
    var activity = $('#activity'+index).val();
    $.get(createLink('workreport', 'ajaxGetApps','activity='+activity+"&index="+index), function(data)
    {
        $('#apps'+index+"_chosen").remove();
        $('#apps'+index).replaceWith(data);
        $('#apps'+index).chosen();
    });
    //操作所属活动，清空任务
   var objects = $('#objects'+index).val();
    if( objects){
        getTasks(obj);

    }
    /*$('#beginDate\\[' + index + '\\]').val('');
   $('#endDate\\[' + index + '\\]').val('');*/

}
/*获取所属对象（任务）*/
function getTasks(obj){
    var index =  $(obj).attr("data-id");
    var app = $('#apps'+index).val();
   // var projectname = $('#project'+index).find("option:selected").text();
    var project = $('#project'+index).val();
    $.get(createLink('workreport', 'ajaxGetTaskObject','app='+app+"&index="+index+"&projectName="+project), function(data)
    {
        $('#objects'+index+"_chosen").remove();
        $('#objects'+index).replaceWith(data);
        $('#objects'+index).chosen();
    });
  /*  //操作所属对象，清空时间
    $('#beginDate\\[' + index + '\\]').val('');
    $('#endDate\\[' + index + '\\]').val('');*/
}

/*根据项目设置报工开始时间*/
function getBeginAndEnd(obj){
    var index =  $(obj).attr("data-id");
    console.log(index)
    var project = $('#project'+index).val();
    $.ajaxSettings.async = false;
    $.get(createLink('workreport', 'ajaxGetBeginAndEnd','project='+project), function(data)
    {console.log(data)
        console.log(index)
        $(".form-date").eq(index*2-2).datetimepicker('setStartDate', data);
        $(".form-date").eq(index*2-1).datetimepicker('setStartDate', data);
    });
}
//设置开始时间
function getCreateBeginAndEnd(){

    $.ajaxSettings.async = false;
    $.get(createLink('workreport', 'ajaxGetCreateBeginAndEnd'), function(data)
    {
        $(".form-date").datetimepicker('setStartDate', data);

    });
}
/*查询结束时间和开始时间 是否在一周*/
function checkEndDate(obj){
    var index =  $(obj).attr("data-id");
    var beginDate = ($('#beginDate\\[' + index + '\\]').val()).replaceAll('-','')  ;
    var endDate   = $('#endDate\\[' + index + '\\]').val().replaceAll('-','');
    $.get(createLink('workreport', 'ajaxGetOneWeekly','start='+beginDate+"&end="+endDate+"&index="+index), function(data)
    {
        if(data){
            bootbox:alert(data);
            return false;
        }

    });
}
rand = ($('#taskTable > tbody >tr').size()) -1;
//添加报工列
function addTaskItem(obj)
{
    console.log('num: '+($('#taskTable > tbody >tr').size()));
    var rowNum = $('#taskTable > tbody >tr').size();
    if(rowNum >= 11) { alert("添加失败，最多添加10个报工任务"); return false; }
    var relevantObj  = $('#workTable');
    var relevantHtml = relevantObj.clone();
    // var x = 10000;
    // var y = 0;
   // var rand = parseInt(Math.random() * (x - y + 1) + y);
    rand++;
   /* relevantHtml.find('#codePlus0').attr({'id':'codePlus' + rand, 'data-id': rand});
    relevantHtml.find('#codeClose0').attr({'id':'codeClose' + rand, 'data-id': rand});*/
    relevantHtml.find('.addStage').attr({'id':'codePlus' + rand, 'data-id': rand});
    relevantHtml.find('.delStage').attr({'id':'codeClose' + rand, 'data-id': rand});

    relevantHtml.find('#project0').attr({'id':'project' + rand});
   /* relevantHtml.find('#workTab0').attr({'id':'workTab' + rand});*/
    relevantHtml.find('.workTab').attr({'id':'workTab' + rand});

    relevantHtml.find('.idSelect').attr({'data-id': rand,'name': 'id' + "["+rand+"]",'id' :  'id' +"["+rand+"]"});
    relevantHtml.find('.projectSelect').attr({'data-id': rand,'name': 'project' + "["+rand+"]",'id': 'project' +rand});
    relevantHtml.find('.activitySelect').attr({'data-id': rand,'name': 'activity' + "["+rand+"]",'id': 'activity' +rand});
    relevantHtml.find('.appsSelect').attr({'data-id': rand,'name': 'apps' + "["+rand+"]",'id': 'apps' +rand});
    relevantHtml.find('.objectsSelect').attr({'data-id': rand,'name': 'objects' + "["+rand+"]",'id': 'objects' + rand});
    relevantHtml.find('.beginDateSelect').attr({'data-id': rand,'name': 'beginDate' + "["+rand+"]",'id': 'beginDate' +"["+rand+"]"});
   /* relevantHtml.find('.endDateSelect').attr({'data-id': rand,'name': 'endDate' + "["+rand+"]",'id': 'endDate' +"["+rand+"]"});*/
    relevantHtml.find('.consumedSelect').attr({'data-id': rand,'name': 'consumed' + "["+rand+"]",'id': 'consumed' +rand});
    relevantHtml.find('.workTypeSelect').attr({'data-id': rand,'name': 'workType' + "["+rand+"]",'id': 'workType' +rand});
    relevantHtml.find('.workContentSelect').attr({'data-id': rand,'name': 'workContent' + "["+rand+"]",'id': 'workContent' +rand});

    /*var objIndex = $(obj).attr('data-id');
    console.log('obhj :'+objIndex);
    $('#workTab' + objIndex).after(relevantHtml.html());*/
    if(rand-1 > 1){
        //如果下标大于1 ，则新增的每次追加在最后一个后面
        $('#workTab' + (rand-1)).after(relevantHtml.html());
    }else{
        var objIndex = $(obj).attr('data-id');
        $('#workTab' + objIndex).after(relevantHtml.html());
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

    $("#pk_workType0-search").parent().parent().remove();
    $('#workType' + rand).picker();


    $('#project' + rand).chosen();
    $('#project0_chosen').remove();
    $('#project' + rand).chosen();
    $('#activity0_chosen').remove();
    $('#activity' + rand).chosen();
    $('#apps0_chosen').remove();
    $('#apps' + rand).chosen();
    $('#objects0_chosen').remove();
    $('#objects' + rand).chosen();
    //$('#workType0_chosen').remove();
   // $('#workType' + rand).chosen();


}

//删除报工列
function delTaskItem(obj)
{

    var objIndex = $(obj).attr('data-id');
    $('#workTab' + objIndex).remove();
    rand--;
    //删除后重置下标保持下标有序
    $("tr[id^=workTab]").each((index, item)=>
    {
        index++;
        $(item).attr({'id': 'workTab' +index});
    })
    $("input[id^=id]").each((index, item)=>
    {
        index++;
        $(item).attr({'name': 'id' + "["+index+"]",'id' :'id' + "["+index+"]",'data-id': index,'value' : index});
    })

    $("select[id^=project]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'project' + "["+index+"]",'id': 'project' +index});
    })
    $("select[id^=activity]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'activity' + "["+index+"]",'id': 'activity' +index});
    })
    $("select[id^=apps]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'apps' + "["+index+"]",'id': 'apps' +index});
    })
    $("select[id^=objects]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'objects' + "["+index+"]",'id': 'objects' +index});
    })
    $("input[id^=beginDate]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'beginDate' + "["+index+"]",'id': 'beginDate' +index});
    })
   /* $("input[id^=endDate]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'endDate' + "["+index+"]",'id': 'endDate' +index});
    })*/
    $("input[id^=consumed]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'consumed' + "["+index+"]",'id': 'consumed' +index});
    })
    $("select[id^=workType]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'workType' + "["+index+"]",'id': 'workType' +index});
    })
    $("textarea[id^=workContent]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'name': 'workContent' + "["+index+"]",'id': 'workContent' +index});
    })

    $("span[id^=codePlus]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'id': 'codePlus' +index});
    })
    $("span[id^=codeClose]").each((index, item)=>
    {
        index++;
        $(item).attr({'data-id': index,'id': 'codeClose' +index});
    })
}

$(document).ready(function()
{
    getCreateBeginAndEnd();
    var m = 1;
    var project = $('#project'+m).val();
    $.ajaxSettings.async = false;
    $.get(createLink('workreport', 'ajaxGetActivity','project='+project+"&index="+m +"&activity="+activity), function(data)
    {
        $('#activity'+m+"_chosen").remove();
        $('#activity'+m).replaceWith(data);
        $('#activity'+m).trigger('chosen:updated');
        $('#activity'+m).chosen();
    });
    $.get(createLink('workreport', 'ajaxGetApps','activity='+activity+"&index="+m +"&app="+ apps), function(data)
    {
        $('#apps'+m+"_chosen").remove();
        $('#apps'+m).replaceWith(data);
        $('#apps'+m).chosen();
    });
   // var projectname =($('#project'+m).find("option:selected").text());

    $.get(createLink('workreport', 'ajaxGetTaskObject','app='+apps+"&index="+m+"&projectName="+project+"&task="+task), function(data)
    {
        $('#objects'+m+"_chosen").remove();
        $('#objects'+m).replaceWith(data);
        $('#objects'+m).chosen();
    });
    if(project && append){
        $.ajaxSettings.async = false;
        $.get(createLink('workreport', 'ajaxGetBeginAndEnd','project='+project), function(da)
        {console.log(da)
            $(".form-date").eq(m*2-2).datetimepicker('setStartDate', da);
            $(".form-date").eq(m*2-1).datetimepicker('setStartDate', da);
        });
    }
    $.ajaxSettings.async = true;
});



