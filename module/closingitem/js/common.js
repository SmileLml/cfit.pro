function addItem(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.picker-selections').remove();
    // $next.find('.assemblyIndex #assemblyIndex_chosen').remove();
    $next.find('.assemblyIndex .chosen-container').remove();
    $next.find('.assemblyIndex select').val('0').chosen();
    // $next.find('.assemblyLevel #assemblyLevel_chosen').remove();
    // $next.find('.assemblyLevel select').val('0').chosen();
    $next.find('.assemblyLevel select').empty();
    // $next.find('.assemblyStatus select').empty();
    // $next.find('.assemblyStatus #assemblyStatus_chosen').remove();
    $next.find('.assemblyDesc input').val('');
    // $next.find('.assemblyStatus select').val('0').chosen();
    $("body #assemblyContent>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes1[]']").val(_index+1)
    })
}
function removeItem(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
    $("body #assemblyContent>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes1[]']").val(_index+1)
    })
}
function toggleAssembly(assembly)
{
    if(assembly == '1')
    {
        $('#assemblyNum').removeClass('hidden');
        $('#assemblyContent').removeClass('hidden');
    }
    else
    {
        $('#assemblyNum').addClass('hidden');
        $('#assemblyContent').addClass('hidden');
    }
}
function toggleAssemblyAdvise(advise){
    if(advise == '1')
    {
        $('#assemblyAdviseInput').removeClass('hidden');
    }
    else
    {
        $('#assemblyAdviseInput').addClass('hidden');
    }
}

function toggleUsage(usage)
{
    if(usage == '1')
    {
        $('#usageContent').removeClass('hidden');
    }
    else
    {
        $('#usageContent').addClass('hidden');
    }
}

function toggleAdvise(advise)
{
    if(advise == '1')
    {
        $('#usageAdviseInput').removeClass('hidden');
    }
    else
    {
        $('#usageAdviseInput').addClass('hidden');
    }
}

function toggleOsspAdvise(ossp)
{
    if(ossp == '1')
    {
        $('#osspAdvise').removeClass('hidden');
    }
    else
    {
        $('#osspAdvise').addClass('hidden');
    }
}
function togglePlatformAdvise(platform)
{
    if(platform == '1')
    {
        $('#platformAdvise').removeClass('hidden');
    }
    else
    {
        $('#platformAdvise').addClass('hidden');
    }
}
function toggleAdviseChecklist(platformAdvise)
{
    if(platformAdvise == '1')
    {
        $('#platformAdviseList').removeClass('hidden');
    }
    else
    {
        $('#platformAdviseList').addClass('hidden');
    }
}

$('#projectType').change(function(){
    var project = $(this).val();
    if(project == '6'){
        $('.inputNums').removeClass('hidden');
    }else{
        $('.inputNums').addClass('hidden');
    }
});

 function toggleAssemblyIndex(value,obj){
    $.get(createLink('closingitem', 'ajaxGetComponent', 'id='+value+'&val=functionDesc'), function(data)
    {
        $(obj).closest(".table-row").find(".assemblyDesc .form-control").val(data)
        $(obj).closest(".table-row").find(".assemblyDesc .form-control").prop('readonly', true)
    });
    $.get(createLink('closingitem', 'ajaxGetComponent', 'id='+value+'&val=level'), function(data)
    {
        $(obj).closest(".table-row").find(".assemblyLevel .chosen-container").remove()
        $(obj).closest(".table-row").find("[name='assemblyLevel[]']").replaceWith(data)
        // $(obj).closest(".table-row").find("[name='assemblyLevel[]']").chosen()
        $(obj).closest(".table-row").find(".assemblyLevel .chosen-container").attr('disabled',true)
        $(obj).closest(".table-row").find("[name='assemblyLevel[]']").attr('disabled',true)
    });
    // $.get(createLink('closingitem', 'ajaxGetComponent', 'id='+value+'&val=status'), function(data)
    // {
    //     $(obj).closest(".table-row").find(".assemblyStatus .chosen-container").remove()
    //     $(obj).closest(".table-row").find("[name='assemblyStatus[]']").replaceWith(data)
    //     // $(obj).closest(".table-row").find("[name='assemblyStatus[]']").chosen()
    //     $(obj).closest(".table-row").find(".assemblyStatus .chosen-container").attr('disabled',true)
    //     $(obj).closest(".table-row").find("[name='assemblyStatus[]']").attr('disabled',true)
    // });
}
$('#submit').click(function()
{
    $("[name='assemblyLevel[]']").removeAttr('disabled');
    //$("[name='assemblyStatus[]']").removeAttr('disabled');
    // $('#lastTransfer').removeAttr('disabled');
});
function addDisabled(){
    $("[name='assemblyLevel[]']").attr('disabled','disabled');
    //$("[name='assemblyStatus[]']").attr('disabled','disabled');
}
function addItem1(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.advise2 input').val('');
    $("body #assemblyAdviseInput>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes2[]']").val(_index+1)
        //$(this).siblings()
    })
}
function removeItem1(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
    $("body #assemblyAdviseInput>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes2[]']").val(_index+1)
    })
}
function addItem2(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.toolsType #toolsType_chosen').remove();
    $next.find('.toolsType select').val('').chosen();
    $next.find('.toolsName input').val('');
    $next.find('.toolsVersion input').val('');
    $next.find('.isTesting #isTesting_chosen').remove();
    $next.find('.isTesting select').val(1).chosen();
    $next.find('.toolsDesc input').val('');
    $("body #usageContent>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes3[]']").val(_index+1)
        //$(this).siblings()
    })
}
function removeItem2(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
    $("body #usageContent>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes3[]']").val(_index+1)
    })
}
function addItem3(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.advise4 input').val('');
    $("body #usageAdviseInput>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes4[]']").val(_index+1)
    })
}
function removeItem3(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
    $("body #usageAdviseInput>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes4[]']").val(_index+1)
    })
}
function addItem4(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.advise5 input').val('');
    $("body #osspAdvise>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes5[]']").val(_index+1)
    })
}
function removeItem4(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
    $("body #osspAdvise>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes5[]']").val(_index+1)
    })
}
function addItem5(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.advise6 input').val('');
    $("body #platformAdvise>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes6[]']").val(_index+1)
    })
}
function removeItem5(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
    $("body #platformAdvise>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes6[]']").val(_index+1)
    })
}
function addItem6(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.versionCodeOSSP #versionCodeOSSP_chosen').remove();
    $next.find('.versionCodeOSSP select').val('').chosen();
    $next.find('.submitFileName input').val('');
    $next.find('.submitReason input').val('');
    $next.find('.comment input').val('');
    $("body #platformAdviseList>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes7[]']").val(_index+1)
        //$(this).siblings()
    })
}
function removeItem6(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
    $("body #platformAdviseList>td>div").each(function () {
        var _index = $(this).index();
        $(this).children().children().children("[name='codes7[]']").val(_index+1)
    })
}
function addItem7(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.demandAdvise #demandAdvise_chosen').remove();
    $next.find('.demandAdvise select').val('').chosen();
}
function removeItem7(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
}
function addItem8(obj)
{
    $row = $(obj).closest('.table-row');
    $row.after($row.clone());
    $next = $row.next();
    $next.find('.constructionAdvise #constructionAdvise_chosen').remove();
    $next.find('.constructionAdvise select').val('').chosen();
}
function removeItem8(obj)
{
    if($(obj).closest('td').find('.table-row').size() == 1) {
        return false;
    }
    $(obj).closest('.table-row').remove();
}