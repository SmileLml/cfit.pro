function getProductName(app){
    var _class = '.productTd1';
    var isMultiple = 1;//下拉框单选
    if ($(".productTd1 ").hasClass("hidden")){
        //显示的是productTd2
        _class = '.productTd2';
        isMultiple = 2;//下拉框多选
    }
    $.get(createLink('outwarddelivery', 'ajaxGetProductWithCodeName', 'app=' + app), function(data){
        $('.productTd1 .chosen-container').remove();
        $('.productTd1 [name="productId[]"]').replaceWith(data[0]);
        $('.productTd1 [name="productId[]"]').chosen();

        $('.productTd2 .chosen-container').remove();
        $('.productTd2 [name="productId[]"]').replaceWith(data[1]);
        $('.productTd2 [name="productId[]"]').chosen();
    },'json')
    //根据系统获取遗留缺陷、修复缺陷
    $.get(createLink('outwarddelivery', 'ajaxGetLeaveDefectsByApp', 'app=' + app), function(data){
        $('#leaveDefect_chosen').remove();
        $('#leaveDefect').replaceWith(data);
        $('#leaveDefect').chosen();
    })
    $.get(createLink('outwarddelivery', 'ajaxGetfixDefectsByApp', 'app=' + app), function(data){
        $('#fixDefect_chosen').remove();
        $('#fixDefect').replaceWith(data);
        $('#fixDefect').chosen();
    })
}
//上线材料清单自动获取
$("[name='checkList']").focus(function () {
    var productEnrollId = $('#productEnrollId option:selected').val();
    var htmlName = '';
    if (parseInt(productEnrollId) > 0){
        $.get(createLink('outwarddelivery', 'ajaxGetCheckList', 'productEnrollId=' + productEnrollId), function(data)
        {
            var mediaInfo = data['mediaInfo'];
            for (var i=0;i<mediaInfo.length;i++){
                if (mediaInfo[i]['name'] != ''){
                    htmlName += mediaInfo[i]['name'] + '\n'
                }
            }
            if ($("[name='checkList']").val() == ''){
                $("[name='checkList']").val(htmlName)
            }
        },'json');
    }else{
        $("#aidMedia tr").each(function () {
            if ($(this).find("[name='mediaName[]']").val() != ''){
                htmlName += $(this).find("[name='mediaName[]']").val() + '\n'
            }
        })
        if ($("[name='checkList']").val() == ''){
            $("[name='checkList']").val(htmlName)
        }
    }


})
//选择异常变更单，获取异常变更单的需求条目、问题单，并不可更改
function selectabnormalCode() {
    $.ajaxSettings.async = false;
    var abnormalId = $("#abnormalCode option:selected").val()
    var isAbnormal = 1;
    if (abnormalId == '' || abnormalId == undefined){
        isAbnormal = 2;
        abnormalId = outwarddeliveryId;
        $("body .abnormalTips").remove()
    }else{
        var str = '<div class="abnormalTips" style="margin-top:3px;color:red">'+abnormalTips+'</div>';
        if ($("body .abnormalTips").length < 1){
            $("#abnormalCode").parent().append(str);
        }
    }
    // $.ajaxSettings.async = false;
    $.get(createLink('outwarddelivery', 'ajaxGetorderByabnormalId', 'id=' + abnormalId+'&isAbnormal='+isAbnormal+'&isDisable='+isDisable), function(data){
        $('#problemId').next().remove();
        $('#problemId').replaceWith(data[1]);
        $('#problemId').chosen();
        // $('#demandId_chosen').remove();
        $('#demandId').next().remove();
        $('#demandId').replaceWith(data[0]);
        $('#demandId').chosen();

        $('#secondorderId').next().remove();
        $('#secondorderId').replaceWith(data[2]);
        $('#secondorderId').chosen();
    },'json')
    //触发需求条目改变事件
    selectDemand();
}
//关联异常变更单是否可选
function isSelectAbnormalList(isMultiple) {
    if(isMultiple){
        // $('select[name="abnormalCode"]').val('').trigger("chosen:updated");
        $('.abnormalCodeTr').removeClass('hidden');
    }else{
        $('select[name="abnormalCode"]').val('').trigger("chosen:updated");
        $('.abnormalCodeTr').addClass('hidden');
    }
    var isFirst = 1;
    if (isFirst != 1){
        selectabnormalCode();
        isFirst = 2
    }
}
//生产变更模块展示方法
function modifycnccModuleShow(isShow){
    if(isShow){
        $('.outwarddeliveryModifycncc').removeClass('hidden');
        $('.manufacturerTr').removeClass('hidden');
        $('.abnormalCodeTr').removeClass('hidden');
    }else{
        $('.outwarddeliveryModifycncc').addClass('hidden');
        $('.manufacturerTr').addClass('hidden');
        $(".abnormalCodeTr").addClass('hidden');
    }
}
//变更阶段为推广是，关联变更单必填
function selectChangeStage(val) {
    if (val == 2){
        $(".changeRequired>div:nth-child(2)").addClass('required')
    }else{
        $(".changeRequired>div:nth-child(2)").removeClass('required')

    }
}
$("#implementModality").change(function () {
    var _val = $("#implementModality option:selected").val();
    if (_val == 1 || _val == 3 || _val == 6){
        $(".aadsReasonTr").removeClass('hidden')
    }else{
        $(".aadsReasonTr").addClass('hidden')
    }

    if (_val == 4 || _val == 5){
        $("[name='automationTools']").parent().removeClass('hidden')
        $("[name='automationTools']").parent().prev().removeClass('hidden')
    }else{
        $("[name='automationTools']").parent().addClass('hidden')
        $("[name='automationTools']").parent().prev().addClass('hidden')
    }
})
// 选择变更紧急程度
function selectChange(){
    var _val = $("select[name='type'] option:selected").val();
    if (_val == 1){
        $(".urgent").removeClass("hidden")
    }else{
        $(".urgent").addClass("hidden")
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
