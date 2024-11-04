/**
 * Load branches
 *
 * @param  int $productID
 * @access public
 * @return void
 */
function loadBranches(productID)
{
    $('#branch').remove();
    $('#branch_chosen').remove();
    var oldBranch = 0;
    if(typeof(productGroups[productID]) != "undefined")
    {
        oldBranch = productGroups[productID]['branch'];
    }

    $.get(createLink('branch', 'ajaxGetBranches', 'productID=' + productID + '&oldBranch=' + oldBranch), function(data)
    {
        if(data)
        {
            $('#product').closest('.input-group').append(data);
            $('#branch').chosen();
        }
    });
}

var relevantIndex = 1;
function addRelevantItem(obj)
{
    var relevantObj  = $('#relevantDeptTable');
    var relevantHtml = relevantObj.clone();
    relevantIndex++;

    relevantHtml.find('#codePlus0').attr({'id':'codePlus' + relevantIndex, 'data-id': relevantIndex});
    relevantHtml.find('#codeClose0').attr({'id':'codeClose' + relevantIndex, 'data-id': relevantIndex});

    relevantHtml.find('#relevantUser0').attr({'id':'relevantUser' + relevantIndex});
    relevantHtml.find('#relevantDept0').attr({'id':'relevantDept' + relevantIndex});

    var objIndex = $(obj).attr('data-id');
    $('#relevantDept' + objIndex).after(relevantHtml.html());

    $('#relevantUser' + relevantIndex).attr('class','form-control chosen');
    $('#relevantUser' + relevantIndex).chosen();;
}
function delRelevantItem(obj)
{
    var objIndex = $(obj).attr('data-id');
    $('#relevantDept' + objIndex).remove();
}

$(document).ready(function(){

    if(status == 'build' || status == 'testfailed' || status == 'versionfailed'|| status == 'verifyfailed' ){
        $('#name').parent().addClass('required');
        $('#filePath').parent().addClass('required');
        $('#status').val('waittest');
    }
    if(status != 'build' && status != 'testfailed' && status != 'versionfailed' && status != 'verifyfailed'){
        $('.dev').removeClass('hidden');
        $('#name').parent().parent().addClass('hidden');
        $('#filePath').parent().parent().addClass('hidden');
    }
    if(status == 'waitverify'){
        $('#name').parent().parent().removeClass('hidden');
        $('#name').parent().addClass('required');

    }
    if(status == 'testsuccess'){
        $('.dev3').removeClass('hidden');
    }

    if(status == 'verifysuccess' ){
        $('.dev2').removeClass('hidden');
        $('.dev').addClass('hidden');
        $('#releasePath').parent().addClass('required');
        $('#releaseName').parent().addClass('required');
        $('#plateName').parent().addClass('required');
        $('#status').val('released');
    }
    if(status == 'waitverifyapprove' ){
       $('#relevantDept1').addClass('hidden');
    }



});

//获取版本关联 问题单 需求单
function getTypeList(product,version,app,type){

    $.ajaxSettings.async = false;
    var value = (type == 'demand' ||type == 'demandinside')? demandID : ( type == 'problem' ? problemID : secondID);
    $.get(createLink('build', 'ajaxGetTypeList', "app=" + app + "&projectID=" + projectID +"&product=" + product + "&version=" + version + "&type=" + type+'&value=' +value), function(planList)
    {type = (type == 'demand' ||type == 'demandinside') ? 'demandid' : ( type == 'problem' ? 'problemid' : 'sendlineId');
        $('#'+ type +'_chosen').remove();
        $('#'+ type).replaceWith(planList);
        $('#'+ type).chosen();

    });
    $("#demandChosen").val($("#demandid").text());
    $("#problemChosen").val($("#problemid").text());
    $("#sendlineChosen").val($("#sendlineId").text());
}
function changeproblemid(){
    //$("#problemChosen").val( $('#problemid').find("option:selected").text());
    var problemtext ='';
    $('#problemid').find("option:selected").each(function()
    {
        problemtext += $(this).text()+'\r\n';
    })
    $("#problemChosen").val(problemtext );
}
function changedemandid(){
    var demandtext ='';
    $('#demandid').find("option:selected").each(function()
    {
       demandtext += $(this).text()+'\r\n';
    })
    $("#demandChosen").val(demandtext );
}
function changesendlineId(){
    //$("#sendlineChosen").val( $('#sendlineId').find("option:selected").text());
    var secondtext ='';
    $('#sendlineId').find("option:selected").each(function()
    {
        secondtext += $(this).text()+'\r\n';
    })
    $("#sendlineChosen").val(secondtext );
}

/**
 * 确认保存
 *
 * @param message
 * @returns {boolean}
 */
function confirmSave(message) {
    $("#isWarn").val("yes");
    // if(confirm(message)){
    //     $("#isWarn").val("no");
    //     $('#submit').submit();
    //     return  true;
    // }else {
    //     return false;
    // }

    bootbox.confirm(message, function (result){
        if((result)){
            $("#isWarn").val("no");
            $('button[data-bb-handler="cancel"]').click();
            $('#submit').submit();
            return false;
        }
    });
    return false;
}
