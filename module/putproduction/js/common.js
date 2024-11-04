/**
 * 设置级别
 */
function changeLevel(){
    setReviewNodes();
}

/**
 * 设置投产材料阶段信息
 *
 */
function setStageInfo() {
    var stageIds = [];
    $('.firstStagePidInfo').removeClass('required');
    $('input[name="stage[]"]').each(function () {
        if($(this).prop("checked")){
            var stageId = $(this).val();
            stageIds.push(stageId);
        }
    });
    //仅仅包含第一阶段不包含第二阶段
    if(($.inArray('1', stageIds) >= 0) && ($.inArray('2', stageIds) == -1)){
        $('.firstStageInfo').removeClass('hidden');
        $('.secondStageInfo').addClass('hidden');
    }else {
        $('.firstStageInfo').addClass('hidden');
    }

    //包含第二阶段
    if($.inArray('2', stageIds) >= 0){
        $('.secondStageInfo').removeClass('hidden');
    }else {
        $('.secondStageInfo').addClass('hidden');

        $('#isBusinessCoopera').val('1').trigger('chosen:updated');
        $('#isBusinessAffect').val('1').trigger('chosen:updated');
    }

    //只包含第二阶段，不包含第一阶段
    if(($.inArray('2', stageIds) >= 0) && ($.inArray('1', stageIds) == -1)){
        $('.firstStagePidTd').addClass('required');
        $('.firstStagePidInfo').removeClass('hidden');
    }else {
        $('.firstStagePidInfo').addClass('hidden');
        $('.firstStagePidTd').removeClass('required');
        $('#firstStagePid').val('').trigger('chosen:updated');
    }
    changeIsBusinessCoopera();
    changeIsBusinessAffect();
}

/**
 *是否经过评审
 */
function changeIsReview() {
    var isReview = $('#isReview').val(); //投产材料是否经过评审
    if(isReview == '2'){
        $('.reviewCommentInfo').removeClass('hidden');
    }else {
        $('.reviewCommentInfo').addClass('hidden');
        $('#reviewComment').val('');
    }
}

/**
 * 是否需要业务配合
 */
function changeIsBusinessCoopera() {
    var isBusinessCoopera = $('#isBusinessCoopera').val(); //是否需要业务配合
    if(isBusinessCoopera == '2'){
        $('.businessCooperaContentInfo').removeClass('hidden');
    }else {
        $('.businessCooperaContentInfo').addClass('hidden');
        $('#businessCooperaContent').val('');
    }
}

/**
 * 是否投产期间是否有业务影响
 */
function changeIsBusinessAffect() {
    var isBusinessAffect = $('#isBusinessAffect').val(); //投产期间是否有业务影响
    if(isBusinessAffect == '2'){
        $('.businessAffectInfo').removeClass('hidden');
    }else {
        $('.businessAffectInfo').addClass('hidden');
        $('#businessAffect').val('');
    }
}

/**
 * 设置审批节点是否展示
 *
 * @returns {boolean}
 */
function setReviewNodes() {
    var level = $('#level').val(); //级别
    $('.node-item').removeClass('hidden');
    if(level != 1) {
        $('.nodewaitgm').addClass('hidden'); //默认隐藏总经理审批
    }
    //调用是否设置必须传字段的值
    setReviewRequiredNodes();
    return true;
}

/**
 * 设置必传节点
 *
 */
function setReviewRequiredNodes(){
    $('.node-item').each(function () {
        var nodeKeyName = $(this).attr('id');
        if($(this).hasClass('hidden')){
            $('#requiredNodes-'+nodeKeyName).val('0');
        }else {
            $('#requiredNodes-'+nodeKeyName).val('1');
        }
    });
    return true;
}

function getInProjectList() {
    var outsidePlanId = $('#outsidePlanId').val(); //(外部)项目/任务
    $.get(createLink('putproduction', 'ajaxGetInProjectPlanList', 'outsidePlanId=' + outsidePlanId), function(data){
        $('.inProjectIdsTd #inProjectIds_chosen').remove();
        $('.inProjectIdsTd #inProjectIds').replaceWith(data);
        $('.inProjectIdsTd #inProjectIds').chosen();
    },'json');
}


/**
 * 获得产品
 * @param app
 */
function getProductName(){
    var app = '';
    $("#app option:selected").each(function () {
        if ($(this).val() != ''){
            app += $(this).val()+',';
        }
    });

    var isNew = false;
    $("#property option:selected").each(function () {
        if ($(this).val() == '1'){
            isNew = true;
        }
    });

    if(isNew){
        $.get(createLink('putproduction', 'ajaxGetApplicationInfo', "$app=" + app), function(data){
            if(data != ''){
                var obj = JSON.parse(data);
                var appList = app.split(",");
                appList = appList.filter(function (item){
                    return item != obj.id;
                });
                app = appList.join(",");
                js:alert(obj.name+'已投产，请勿投产属性为新建系统时选择该系统！');
                $('#app').val(appList).trigger("chosen:updated");
            }

            $.get(createLink('outwarddelivery', 'ajaxGetProductWithCodeName', 'app=' + app), function(data){
                $('.productTd #productId_chosen').remove();
                $('.productTd #productId').replaceWith(data[1]);
                $('.productTd #productId').chosen();
            },'json');
        });
    }else{
        $.get(createLink('outwarddelivery', 'ajaxGetProductWithCodeName', 'app=' + app), function(data){
            $('.productTd #productId_chosen').remove();
            $('.productTd #productId').replaceWith(data[1]);
            $('.productTd #productId').chosen();
        },'json');
    }
}

function changeProperty(){
    var isNew = false;
    $("#property option:selected").each(function () {
        if ($(this).val() == '1'){
            isNew = true;
        }
    });
    if(isNew){
        var app = '';
        $("#app option:selected").each(function () {
            if ($(this).val() != ''){
                app += $(this).val()+',';
            }
        });
        $.get(createLink('putproduction', 'ajaxGetApplicationInfo', "$app=" + app), function(data){
            if(data != ''){
                var obj = JSON.parse(data);
                var appList = app.split(",");
                appList = appList.filter(function (item){
                    return item != obj.id;
                });
                app = appList.join(",");
                js:alert(obj.name+'已投产，请勿投产属性为新建系统时选择该系统！');
                $('#app').val(appList).trigger("chosen:updated");

                $.get(createLink('outwarddelivery', 'ajaxGetProductWithCodeName', 'app=' + app), function(data){
                    $('.productTd #productId_chosen').remove();
                    $('.productTd #productId').replaceWith(data[1]);
                    $('.productTd #productId').chosen();
                },'json');
            }
        });
    }
}

/**
 * 判断是否选择产品
 */
function selectProductMultId() {
    var productIds = '';
    $("#productId option:selected").each(function () {
        if ($(this).val() != ''){
            productIds += $(this).val()+',';
        }
    });
    if(productIds){
        if($('#productIdLabel').length > 0){
            $('#productIdLabel').reverse();
        }
    }
}


function submitData(btnClass) {
    $('.buttonInfo').attr('type', 'button');
    $('.'+btnClass).attr('type', 'submit');
}



