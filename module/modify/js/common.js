function getProductName(){
    var app = '';
    var  appArray = [];
    $(".outwarddeliveryApp option:selected").each(function () {
        if ($(this).val() != ''){
            app += $(this).val()+',';
            appArray.push($(this).val())
        }
        if ($(this).val() == 1){
            $(".tcbstr").removeClass('hidden')
        }
    })
    if($.inArray('1',appArray) == -1){
        $(".tcbstr").addClass('hidden')
    }
    $.get(createLink('outwarddelivery', 'ajaxGetProductWithCodeName', 'app=' + app), function(data){
        if($(".productTd1").hasClass('hidden')){
            $('.productTd2 #productId_chosen').remove();
            $('.productTd2 #productId').replaceWith(data[1]);
            $('.productTd2 #productId').chosen();
        }else{
            $('.productTd1 #productId_chosen').remove();
            $('.productTd1 #productId').replaceWith(data[0]);
            $('.productTd1 #productId').chosen();
        }
    },'json')
}
var source = ''
//选择异常变更单，获取异常变更单的需求条目、问题单，并不可更改
function selectabnormalCode(source='') {
    var abnormalId = $("#abnormalCode option:selected").val();
    var isAbnormal = 1;
    if (abnormalId == ''){
        $('body .abnormalTips').remove();
        abnormalId = $("#id").val();
        isAbnormal = 2;
    }else{
        var str = '<div class="abnormalTips" style="margin-top:3px;color:red">'+abnormalTips+'</div>';
        if ($("body .abnormalTips").length < 1){
            $("#abnormalCode").parent().append(str);
        }
    }
    $.get(createLink('modify', 'ajaxGetorderByabnormalId', 'id=' + abnormalId+'&isAbnormal='+isAbnormal+'&source='+source), function (data) {
        $('#problemId').next().remove();
        $('#problemId').replaceWith(data[1]);
        $('#problemId').chosen();
        // $('#demandId_chosen').remove();
        $('#demandId').next().remove();
        $('#demandId').replaceWith(data[0]);
        $('#demandId').chosen();
        //任务工单
        $('#secondorderId').next().remove();
        $('#secondorderId').replaceWith(data[2]);
        $('#secondorderId').chosen();
    }, 'json');
}
function selectIsReview2() {
    var val = $("#materialIsReview option:selected").val()
    if (val == 1){
        $("#materialReviewResult").parent().addClass('required')
        $("#materialReviewUser").parent().addClass('required')
    }else{
        $("#materialReviewResult").parent().removeClass('required')
        $("#materialReviewUser").parent().removeClass('required')
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
        // 紧急需求临时取消上线，故临时隐藏
        $("[name='isMakeAmends']").parent().parent().addClass("hidden")
    }
}