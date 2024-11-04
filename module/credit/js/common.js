/**
 * 根据系统获得产品列表
 *
 * @param productIds
 */
function getProductName(productIds){
    productIds = productIds || [];
    var app = '';
    $("#appIds option:selected").each(function () {
        if ($(this).val() != ''){
            app += $(this).val()+',';
        }
    });

    $.get(createLink('outwarddelivery', 'ajaxGetProductWithCodeName', 'app=' + app+"&name=productIds"), function(data){
        $('.productTd #productIds_chosen').remove();
        $('.productTd #productIds').replaceWith(data[1]);
        $('.productTd #productIds').chosen();
        $('.productTd #productIds').val(productIds).trigger('chosen:updated');
    },'json');
}
function selectProductMultId() {
    return true;
}


/**
 * 选择异常变更单，获取异常变更单的需求条目、问题单，并不可更改
 */
function selectabnormalCode(problemIds, demandIds, secondorderIds) {
    problemIds     = problemIds || [];
    demandIds      = demandIds || [];
    secondorderIds = secondorderIds || [];
    var abnormalId = $("#abnormalId option:selected").val();
    var creditId   =  $("#creditId").val();
    var isAbnormal = 1;
    if (abnormalId == ''){
        $('body .abnormalTips').remove();
        abnormalId = $("#id").val();
        isAbnormal = 2;
    }else{
        var str = '<div class="abnormalTips" style="margin-top:3px;color:red">'+abnormalTips+'</div>';
        if ($("body .abnormalTips").length < 1){
            $("#abnormalId").parent().append(str);
        }
    }

    $.get(createLink('credit', 'ajaxGetInfoByAbnormalId', 'id=' + abnormalId+'&isAbnormal='+isAbnormal+'&creditId='+creditId), function (data) {
        $('#problemIds').next().remove();
        $('#problemIds').replaceWith(data[1]);
        $('#problemIds').chosen();
        if(problemIds.length > 0){
            $('#problemIds').val(problemIds).trigger('chosen:updated');
        }

        // $('#demandId_chosen').remove();
        $('#demandIds').next().remove();
        $('#demandIds').replaceWith(data[0]);
        $('#demandIds').chosen();
        if(demandIds.length > 0){
            $('#demandIds').val(demandIds).trigger('chosen:updated');
        }

        //任务工单
        $('#secondorderIds').next().remove();
        $('#secondorderIds').replaceWith(data[2]);
        $('#secondorderIds').chosen();
        if(secondorderIds.length > 0){
            //console.log(secondorderIds);
            $('#secondorderIds').val(secondorderIds).trigger('chosen:updated');
        }

    }, 'json');
}

/**
 * 修改变更级别
 *
 * @param level
 */
function changeLevel(level) {
    var reviewNodeCodes = reviewNodeCodeList;
    if(reviewNodeCodeListGroupLevel[level]){
        reviewNodeCodes = reviewNodeCodeListGroupLevel[level];
    }
    $('.node-item').each(function () {
        var nodeCode = $(this).attr('id');
        if($.inArray(nodeCode, reviewNodeCodes) !== -1){
            $('#'+nodeCode).removeClass('hidden');
        }else {
            $('#'+nodeCode).addClass('hidden');
        }
    });
}

/**
 * 增加风险分析与应急处置
 *
 * @param obj
 */
function addLine(obj)
{
    $(obj).parent().parent().after($('#lineDemo').children(':first-child').clone())
    sortline();
}

/**
 * 删除风险分析与应急处置
 *
 * @param obj
 */
function deleteLine(obj)
{
    if($(obj).parent().parent().parent().children().length>1){
        $(obj).parent().parent().remove()
        sortline();
    }
}

/**
 * 风险分析与应急处置排序
 */
function sortline()
{
    $('#aid').children('tr').each(function (index){
        var keyIndex = index+1;
        $(this).attr('id', 'risk_'+keyIndex);
        $(this).children(':first-child').text(keyIndex);
        $(this).children(':nth-child(2)').children('textarea').attr('id', 'riskAnalysis_'+keyIndex);
        $(this).children(':nth-child(3)').children('textarea').attr('id', 'emergencyBackWay_'+keyIndex);
        var riskDangerDiv        = $(this).children(':nth-child(2)').children('.text-danger');
        var riskDangerIdTag      = 'riskAnalysis_' + keyIndex + 'Label';
        var emergencyDangerDiv   = $(this).children(':nth-child(3)').children('.text-danger');
        var emergencyDangerIdTag = 'emergencyBackWay_' + keyIndex + 'Label';
        var dangerCount = riskDangerDiv.length;
        if(dangerCount > 0){
            riskDangerDiv.each(function (tempIndex){
                if($(this).attr('id') != riskDangerIdTag){
                    $(this).remove();
                }
            });
            emergencyDangerDiv.each(function (tempIndex){
                if($(this).attr('id') != emergencyDangerIdTag){
                    $(this).remove();
                }
            });
        }
    })
}

/**
 * 提交操作
 *
 * @param btnClass
 */
function submitData(btnClass) {
    var abnormalId = $("#abnormalId option:selected").val()
    if (abnormalId != ''){
        $("#problemIds").removeAttr('disabled');
        $("#demandIds").removeAttr('disabled');
        $("#secondorderIds").removeAttr('disabled');
    }
    $('.buttonInfo').attr('type', 'button');
    $('.'+btnClass).attr('type', 'submit');
}

/**
 * 根据项目实现方式获得项目列表
 *
 * @param type
 */
function changeFixType(type, projectPlanId){
    projectPlanId = projectPlanId || '';
    $.get(createLink('outwarddelivery', 'ajaxGetSecondLine', "fixType=" + type), function(data){
        $('#projectPlanId_chosen').remove();
        $('#projectPlanId').replaceWith(data);
        $('#projectPlanId').chosen();
        $('#projectPlanId').val(projectPlanId).trigger('chosen:updated');

    });
}
function selectIsMakeAmends() {
    var _val = $("[name='isMakeAmends'] option:selected").val()
    if (_val ==  'yes'){
        $("[name='actualDeliveryTime']").parent().addClass('required')
    }else{
        $("[name='actualDeliveryTime']").parent().removeClass('required')
    }
}
// 选择是否后补流程
function selectIsMakeAmends() {
    var _val = $("[name='isMakeAmends'] option:selected").val()
    if (_val ==  'yes'){
        $("[name='actualDeliveryTime']").parent().addClass('required')
        $("[name='actualDeliveryTime']").parent().removeClass('hidden')
        $("[name='actualDeliveryTime']").parent().prev().removeClass('hidden')
    }else{
        $("[name='actualDeliveryTime']").parent().removeClass('required')
        $("[name='actualDeliveryTime']").parent().addClass('hidden')
        $("[name='actualDeliveryTime']").parent().prev().addClass('hidden')
        $("[name='isMakeAmends']").parent().parent().addClass("hidden")
    }
}
