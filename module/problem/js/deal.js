$('#app').change(function()
{
    var app = $('#app').val();
    $.get(createLink('problem', 'ajaxGetProductZone',"app=" + app), function(planList)
    {
    
        for(var i = 1;i <= details;i++){
            $('#productTab'+(i+1)).remove();
        }
        $('#productZone').replaceWith(planList);
        $('#product').chosen();
        $('#p-1').chosen();
        $('.addProducthidden').remove();
       // $('#projectPlan').chosen();
    });

 if(status == 'assigned'){
   $.get(createLink('problem', 'ajaxGetFixType'), function(data)
   {
         $('#fixType_chosen').remove();
         $('#fixType').replaceWith(data);
         $('#fixType').val('');
         $('#fixType').chosen();
   });
   $.get(createLink('problem', 'ajaxGetSecondLine', "fixType="+ 'project'), function(data)
   {
      $('#projectPlan_chosen').remove();
      $('#projectPlan').replaceWith(data);
      $('#projectPlan').chosen();
   });
   loadProductExecutions(0);
  // }
  }
});

//添加产品 产品版本列
function addProductItem(obj)
{
    var rowNum = $('#productZone .table-row').size();
    if(rowNum >= 17) { alert("添加失败，最多添加15个产品"); return false; }

    var relevantObj  = $('#productTable');
    var relevantHtml = relevantObj.clone();

    var x = 10000;
    var y = 0;
    var rand = parseInt(Math.random() * (x - y + 1) + y);
    relevantHtml.find('#codePlus0').attr({'id':'codePlus' + rand, 'data-id': rand});
    relevantHtml.find('#codeClose0').attr({'id':'codeClose' + rand, 'data-id': rand});

    relevantHtml.find('#product0').attr({'id':'product' + rand});
    relevantHtml.find('#productTab0').attr({'id':'productTab' + rand});

    relevantHtml.find('.productSelect').attr({'data-id': rand});
    relevantHtml.find('.productPlanSelect').attr({'id':'p-' + rand});

    var objIndex = $(obj).attr('data-id');
    $('#productTab' + objIndex).after(relevantHtml.html());

    $('#productTab' + rand).attr('class','addProducthidden');

    $('#product' + rand).attr('class','form-control chosen');
    $('#product' + rand).chosen();

    $('#p-' + rand).attr('class','form-control chosen');
    $('#p-' + rand).chosen();

    var app = $('#app').val();
    $.get(createLink('problem', 'ajaxGetProduct','app='+app+"&data_id="+rand), function(productlist)
    {
        $('#product'+ rand +'_chosen').remove();
        $('#product'+ rand).replaceWith(productlist);
        $('#product'+ rand).val(productlist);
        $('#product'+ rand).chosen();
    });
}
//删除产品 产品版本列
function delProductItem(obj)
{
    var objIndex = $(obj).attr('data-id');
    $('#productTab' + objIndex).remove();
}


$(function()
{
    returnChanged(scm,ifsolutionFeedback);
    ifultimateChanged(ifultimate);
    solutionFeedbackChange(ifsolutionFeedback);

});

function returnChanged(scm,ifsolutionFeedback)
{
    if(scm == '0')
    {
        $('.notReturn').removeClass('hide');
        $('.return').addClass('hide');
        // solutionFeedbackChange(ifsolutionFeedback);
    }
    else
    {
        $('.return').removeClass('hide');
        $('.notReturn').addClass('hide');
    }
}

function ifultimateChanged(ifultimate){
    // if(ifultimate == '1'){
    //     KindEditor.html("#solution", $('#Tier1Feedback').val());
    //     KindEditor.sync("#solution");
    //     //$(".kindeditor-ph").eq(1).attr("style","display: none ;");
    //     $(".kindeditor-ph").eq(1).hide();
    // }else{
    //     //$(".kindeditor-ph").eq(1).attr("style","display: block ;");
    //     if($('#solution').val() == null){
    //         $(".kindeditor-ph").eq(1).show();
    //     }
    // }
    if(ifultimate == '1'){
        $('#standardVerifyId').removeClass('hidden');
        $('#standardVerify_chosen').parent().addClass('required');
        $('#standardVerify_chosen').removeClass('hidden');
        $('#Tier1Feedback').parent().parent().addClass('hidden');
        $('#Tier1Feedback').val('无');
    }else{
        $('#standardVerify_chosen').parent().removeClass('required');
        $('#standardVerifyId').addClass('hidden');
        $('#standardVerify_chosen').addClass('hidden');

        if($('#standardVerifyLabel').length > 0){
            $('#standardVerifyLabel').remove();
        }
        $('#Tier1Feedback').val('');
        $('#Tier1Feedback').parent().parent().removeClass('hidden');
    }
}

function solutionFeedbackChange(ifsolutionFeedback)
{
    if(ifsolutionFeedback == ';') {
        ifsolutionFeedback = '';
    }
    if(ifsolutionFeedback == 5) {
        $('.toHide').addClass('hide');
        ifultimateChanged(1);
        $('#standardVerify_chosen').remove();
        $('#standardVerify').replaceWith(standardVerifyItemNo);
        $('#standardVerify').val('no');
        $('#standardVerify').chosen();
    } else  {
        $('.toHide').removeClass('hide');
        if(!$('#standardVerifyId').hasClass('hidden')){
            $('#standardVerify_chosen').remove();
            $('#standardVerify').replaceWith(standardVerifyItem);
            $('#standardVerify').val('');
            $('#standardVerify').chosen();
        }
    }
    if(ifsolutionFeedback == '1')
    {
        $('.solvingTheIssue').addClass('hide');
    }
    else
    {
        $('.solvingTheIssue').addClass('hide');
    }
    $.get(createLink('problem', 'ajaxGetIfUltimateSolutionTd', "ifsolutionFeedback=" + ifsolutionFeedback), function(item)
    {
        $v = $('#IfultimateSolution').val()
        $('#IfultimateSolution_chosen').remove();
        $('#IfultimateSolution').replaceWith(item);
        $('#IfultimateSolution').val($v);
        $('#IfultimateSolution').chosen();
    });
}


/**
 * 当问题类型为【不是问题】时调用该方法
 */
function noproblem(flag = true)
{
    if(flag){
        $('#problemGrade_chosen').remove();
        $('#problemGrade').replaceWith(problemGradeItemNo);
        $('#problemGrade').chosen();

        $('#SolutionFeedback_chosen').remove();
        $('#SolutionFeedback').replaceWith(solutionFeedbackItemNo);
        $('#SolutionFeedback').chosen();
    }else {
        $('#problemGrade_chosen').remove();
        $('#problemGrade').replaceWith(problemGradeItem);
        $('#problemGrade').val('');
        $('#problemGrade').chosen();

        $('#SolutionFeedback_chosen').remove();
        $('#SolutionFeedback').replaceWith(solutionFeedbackItem);
        $('#SolutionFeedback').chosen();
    }
}
