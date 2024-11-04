function changeStatus(status) {
    if(status == 'waitconfirm'){
        $('#severityTestUserTd').addClass('required');
        $('#severityTestUserTr').removeClass('hidden');
    }else {
        $('#severityTestUserTd').removeClass('required');
        $('#severityTestUserTr').addClass('hidden');
        $('#severityTestUser').val('').trigger("chosen:updated");
    }
    if(status == 'finish'){
        $('#statusTipMsg').removeClass('hidden');
    }else {
        $('#statusTipMsg').addClass('hidden');
    }
}

/**
 * 产品发生变更
 *
 * @param productId
 */
function changeProduct(productId) {
    $.get(createLink('qualitygate', 'ajaxGetProductVersion', 'productId=' + productId), function(data){
        $('.productVersion #productVersion_chosen').remove();
        $('.productVersion #productVersion').replaceWith(data);
        $('.productVersion #productVersion').chosen();
    },'json');
    //获得安全门禁结果
    getSeverityGateResult();

    if(productId > 0){
        $('#severityGateTr').removeClass('hidden');
    }else {
        $('#severityGateTr').addClass('hidden');
    }
}

/**
 * 修改版本
 *
 */
function changeProductVersion() {
    //获得安全门禁结果
    getSeverityGateResult();
}


/**
 * 获得安全门禁结果
 */
function getSeverityGateResult() {
    var productId      = $('#productId').val();
    var productVersion = $('#productVersion').val();
    if(!productVersion){
        productVersion = 1;
    }
    productVersion = $.trim(productVersion);
    var params = 'projectId='+projectId+'&productId=' + productId+'&productVersion=' + productVersion+'&buildId=' + buildId;
    $.get(createLink('qualitygate', 'ajaxGetSeverityGateResult', params), function(severityGateResult){
        $('#qualityGateResultInfo').html(severityGateResult);
        //跳转链接
        var url = createLink('report', 'qualityGateCheckResult', params)+'#app=project';
        $('#qualityGateResultDetail').attr('href', url);

    });
}