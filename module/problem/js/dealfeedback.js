$('#app').change(function()
{
    var app = $('#app').val();
    $.get(createLink('problem', 'ajaxGetProductZone',"app=" + app), function(planList) {
        for(var i = 1;i <= details;i++){
            $('#productTab'+(i+1)).remove();
        }
        $('#productZone').replaceWith(planList);
        $('#product').chosen();
        $('#p-1').chosen();
        $('.addProducthidden').remove();
    });

 if(status == 'assigned'){
   $.get(createLink('problem', 'ajaxGetFixType'), function(data) {
         $('#fixType_chosen').remove();
         $('#fixType').replaceWith(data);
         $('#fixType').val('');
         $('#fixType').chosen();
   });
   $.get(createLink('problem', 'ajaxGetSecondLine', "fixType="+ 'project'), function(data) {
      $('#projectPlan_chosen').remove();
      $('#projectPlan').replaceWith(data);
      $('#projectPlan').chosen();
   });
   loadProductExecutions(0);
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
    $.get(createLink('problem', 'ajaxGetProduct','app='+app+"&data_id="+rand), function(productlist) {
        $('#product'+ rand +'_chosen').remove();
        $('#product'+ rand).replaceWith(productlist);
        $('#product'+ rand).val(productlist);
        $('#product'+ rand).chosen();
    });
}
//删除产品 产品版本列
function delProductItem(obj) {
    var objIndex = $(obj).attr('data-id');
    $('#productTab' + objIndex).remove();
}

/**
 * 页面初始化
 */
$(function() {
    returnChanged(problem.ifReturn);

    ifultimateChanged(problem.IfultimateSolution, true);
});

function returnChanged(scm)
{
    if(scm == '0') {
        $('.notReturn').removeClass('hide');
        $('.return').addClass('hide');
    } else {
        $('.return').removeClass('hide');
        $('.notReturn').addClass('hide');
    }
}

/**
 * 解决方式 change 事件
 * @param feedback
 */
function solutionFeedbackChange(feedback) {
    if (feedback == ';') {
        feedback = '';
    }

    if (5 == feedback) {
        $('#solutionFeedbackTip').removeClass('hide');
        //【是否最终解决方案】默认为 是
        $('#IfultimateSolution_chosen').remove();
        $('#IfultimateSolution').replaceWith(IfultimateSolutionSelect);
        $('#IfultimateSolution').val(1);
        $('#IfultimateSolution').chosen();

        ifultimateChanged(1);
        //所属产品和版本，默认 无
        $('#product_chosen').remove();
        $('#product').replaceWith(productSelect);
        $('#product').val('99999');
        $('#product').chosen();
        productChange($('#product'))
        //初步反馈（显示，默认：无）
        $('#Tier1Feedback').val('无');
        //发生原因（显示，默认 ：无）
        let cmd = editor['reason'].edit.cmd;
        editor['reason'].html('');
        cmd.inserthtml('无');
        editor['reason'].templateHtml = editor['reason'].html();
        //计划解决时间、计划提交日期、计划变更日期 显示 默认当天
        $('#PlannedDateOfChangeReport').val(dateStr);
        $("#PlannedDateOfChangeReport").data('datetimepicker').update();
        $('#PlannedDateOfChange').val(dateStr);
        $("#PlannedDateOfChange").data('datetimepicker').update();
        $('#PlannedTimeOfChange').val(dateTimeStr);
        $("#PlannedTimeOfChange").data('datetimepicker').update();
        //影响范围默认显示 默认：无
        $('#EditorImpactscope').val('无');

    }else {
        //【是否最终解决方案】
        $('#IfultimateSolution_chosen').remove();
        $('#IfultimateSolution').replaceWith(IfultimateSolutionSelect);
        $('#IfultimateSolution').val(problem.IfultimateSolution);
        $('#IfultimateSolution').chosen();

        ifultimateChanged(problem.IfultimateSolution);

        //所属产品和版本
        $('#product_chosen').remove();
        $('#product').replaceWith(productSelect);
        $('#product').val(problem.product);
        $('#product').chosen();
        productChange($('#product'))
        //初步反馈
        $('#Tier1Feedback').val(problem.Tier1Feedback);
        //发生原因
        let cmd = editor['reason'].edit.cmd;
        editor['reason'].html('');
        cmd.inserthtml(problem.reason);
        editor['reason'].templateHtml = editor['reason'].html();
        //计划解决时间、计划提交日期、计划变更日期
        $('#PlannedDateOfChangeReport').val(problem.PlannedDateOfChangeReport);
        $("#PlannedDateOfChangeReport").data('datetimepicker').update();
        $('#PlannedDateOfChange').val(problem.PlannedDateOfChange);
        $("#PlannedDateOfChange").data('datetimepicker').update();
        $('#PlannedTimeOfChange').val(problem.PlannedTimeOfChange);
        $("#PlannedTimeOfChange").data('datetimepicker').update();
        //影响范围
        $('#EditorImpactscope').val(problem.EditorImpactscope);
        //【解决该问题的变更】
        $('#ChangeSolvingTheIssue').val(problem.ChangeSolvingTheIssue);
    }
}

/**
 * 是否最终方案 change 事件
 * @param ifultimate
 * @param loadFlag
 */
function ifultimateChanged(ifultimate, loadFlag = false)
{
    //重置【是否基准验证】
    $('#standardVerify_chosen').remove();
    $('#standardVerify').replaceWith(standardVerifyItem);
    $('#standardVerify').val('');
    $('#standardVerify').chosen();

    //如果【是否最终方案】为是
    let solutionFeedback = $('#SolutionFeedback').val();
    if (ifultimate == '1') {
        //【是否基准验证】显示
        $('#standardVerifyId').removeClass('hidden');
        $('#standardVerify_chosen').parent().addClass('required');
        $('#standardVerify_chosen').removeClass('hidden');

        //如果【解决方式】为非应用问题【是否基准验证】为 否

        let standardVerifyVal= 5 == solutionFeedback && false === loadFlag ? 'no' : problem.standardVerify;
        $('#standardVerify_chosen').remove();
        $('#standardVerify').replaceWith(standardVerifyItem);
        $('#standardVerify').val(standardVerifyVal);
        $('#standardVerify').chosen();


        //【初步反馈】隐藏。
        $('#Tier1Feedback').parent().parent().addClass('hidden');
        $('#Tier1Feedback').val('');
    } else {
        //【是否基准验证】隐藏
        $('#standardVerify_chosen').parent().removeClass('required');
        $('#standardVerifyId').addClass('hidden');
        $('#standardVerify_chosen').addClass('hidden');
        if ($('#standardVerifyLabel').length > 0) { $('#standardVerifyLabel').remove(); }

        //【初步反馈】显示
        let Tier1FeedbackVal = 5 == solutionFeedback && false === loadFlag ? '无' : problem.Tier1Feedback;
        $('#Tier1Feedback').parent().parent().removeClass('hidden');
        $('#Tier1Feedback').val(Tier1FeedbackVal);
    }
}

/**
 * 当问题类型为【不是问题】时调用该方法
 */
function noproblem(flag = true, loadFlag = false)
{
    let SolutionFeedback = $('#SolutionFeedback').val();
    if(flag){
        $('#problemGrade_chosen').remove();
        $('#problemGrade').replaceWith(problemGradeItem);
        if(!loadFlag) $('#problemGrade').val('notSerious');
        $('#problemGrade').chosen();

        $('#SolutionFeedback_chosen').remove();
        $('#SolutionFeedback').replaceWith(solutionFeedbackItem);
        if(!loadFlag) $('#SolutionFeedback').val(5);
        $('#SolutionFeedback').chosen();
    }else {
        $('#problemGrade_chosen').remove();
        $('#problemGrade').replaceWith(problemGradeItem);
        $('#problemGrade').chosen();

        $('#SolutionFeedback_chosen').remove();
        $('#SolutionFeedback').replaceWith(solutionFeedbackItem);
        $('#SolutionFeedback').chosen();
    }

    let newSolutionFeedback = $('#SolutionFeedback').val();
    if(SolutionFeedback !== newSolutionFeedback){
        solutionFeedbackChange(newSolutionFeedback);
    }
}
