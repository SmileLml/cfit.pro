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
    console.log(relevantHtml.html());


}
//刪除
function delRelevantItem(obj)
{
    var objIndex = $(obj).attr('data-id');
    $('#baselineTable' + objIndex).remove();
}

/*二级变更 上报分管领导必选*/
$(document).ready(function(){
    if(status == cm && level == 2){
        $('#isNeedLeaderyes').attr('checked','checked');
        $('#isNeedLeaderyes').attr('disabled','true');
        $('#isNeedLeaderno').attr('disabled','true');
        $('#isleader').val('yes');
    }
});