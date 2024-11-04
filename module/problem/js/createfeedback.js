$(function () {
    returnChanged(scm);
    ifultimateChanged(problem.IfultimateSolution, true);
});

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
        //【问题级别】默认为非严重级别
        $('#problemGrade_chosen').remove();
        $('#problemGrade').replaceWith(problemGradeItem);
        $('#problemGrade').val('notSerious');
        $('#problemGrade').chosen();
        //【是否最终解决方案】默认为 是
        $('#IfultimateSolution_chosen').remove();
        $('#IfultimateSolution').replaceWith(IfultimateSolutionSelect);
        $('#IfultimateSolution').val(1);
        $('#IfultimateSolution').chosen();

        ifultimateChanged(1);

        //初步反馈（显示，默认：无）
        $('#Tier1Feedback').val('无');
        //发生原因（显示，默认 ：无）
        $('#reason').val('无');
        //计划解决时间、计划提交日期、计划变更日期 显示 默认当天
        $('#PlannedDateOfChangeReport').val(dateStr);
        $("#PlannedDateOfChangeReport").data('datetimepicker').update();
        $('#PlannedDateOfChange').val(dateStr);
        $("#PlannedDateOfChange").data('datetimepicker').update();
        $('#PlannedTimeOfChange').val(dateTimeStr);
        $("#PlannedTimeOfChange").data('datetimepicker').update();
        //对应产品及版本 显示 默认：无
        $('#CorresProduct').val('无');
        //影响范围默认显示 默认：无
        $('#EditorImpactscope').val('无');
        //【解决该问题的变更】
        $('#ChangeSolvingTheIssue').val('无');
        //【修订记录】默认 无
        $('#revisionRecord').val('无');
    }else {
        $('#solutionFeedbackTip').addClass('hide');

        //【问题级别】默认为非严重级别
        $('#problemGrade_chosen').remove();
        $('#problemGrade').replaceWith(problemGradeItem);
        $('#problemGrade').val(problem.problemGrade);
        $('#problemGrade').chosen();
        //【是否最终解决方案】
        $('#IfultimateSolution_chosen').remove();
        $('#IfultimateSolution').replaceWith(IfultimateSolutionSelect);
        $('#IfultimateSolution').val(problem.IfultimateSolution);
        $('#IfultimateSolution').chosen();

        ifultimateChanged(problem.IfultimateSolution);

        //初步反馈
        $('#Tier1Feedback').val(problem.Tier1Feedback);
        //发生原因
        $('#reason').val(problem.reason);
        //计划解决时间、计划提交日期、计划变更日期
        $('#PlannedDateOfChangeReport').val(problem.PlannedDateOfChangeReport);
        $("#PlannedDateOfChangeReport").data('datetimepicker').update();
        $('#PlannedDateOfChange').val(problem.PlannedDateOfChange);
        $("#PlannedDateOfChange").data('datetimepicker').update();
        $('#PlannedTimeOfChange').val(problem.PlannedTimeOfChange);
        $("#PlannedTimeOfChange").data('datetimepicker').update();
        //对应产品及版本
        $('#CorresProduct').val(problem.CorresProduct);
        //影响范围
        $('#EditorImpactscope').val(problem.EditorImpactscope);
        //【解决该问题的变更】
        $('#ChangeSolvingTheIssue').val(problem.ChangeSolvingTheIssue);
        //【修订记录】
        $('#revisionRecord').val(problem.revisionRecord);
    }
}

function returnChanged(scm)
{
    if (scm == '0') {
        $('.notReturn').removeClass('hide');
        $('.return').addClass('hide');
    } else {
        $('.return').removeClass('hide');
        $('.notReturn').addClass('hide');
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

