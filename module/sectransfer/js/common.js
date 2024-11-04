$('#app').change(function(){
    var app = $(this).val();
    $.get(createLink('sectransfer', 'ajaxGetDepartment', 'app=' + app), function(data)
    {
        $('#department_chosen').remove();
        $('#department').replaceWith(data);
        $('#department').chosen();

        var type = $('#jftype').val();
        var department = $('#department').val();
        if('qszzx' == department && 1 == type){
            $('#projectNotice').removeClass('hidden');
        }else {
            $('#projectNotice').addClass('hidden');
        }
    });
})
$('#jftype').change(function()
{
    var type = $(this).val();
    var department = $('#department').val();
    if(type == 1)
    {
        // $(this).closest('tr').find('.subType').removeClass('showing').addClass('hidden');
        $('#subtype').addClass('hidden');
        $('#secondorderID').addClass('hidden');
        $('#secondorderIdTip').addClass('hidden');
        $('#stage').removeClass('hidden');
        $('#project').removeClass('hidden');
        if('qszzx' == department) $('#projectNotice').removeClass('hidden');
        $('#isLastTransfer').removeClass('hidden');
        $('#iscode').removeClass('hidden');
        $('#recipient').removeClass('hidden');
        $('.nodeleader').removeClass('hidden');
        var iscode = $("input[name='iscode']:checked").val();
        if(iscode == 1){
            $('.nodemaxleader').removeClass('hidden');
        }
    }
    else
    {
        // $(this).closest('tr').find('.subType').removeClass('hidden').addClass('showing');
        $('#subtype').removeClass('hidden');
        $('#secondorderID').removeClass('hidden');
        $('#secondorderIdTip').removeClass('hidden');
        $('#stage').addClass('hidden');
        $('#project').addClass('hidden');
        $('#projectNotice').addClass('hidden');
        $('#isLastTransfer').addClass('hidden');
        $('#iscode').addClass('hidden');
        $('#recipient').addClass('hidden');
        $('.nodeleader').addClass('hidden');
        $('.nodemaxleader').addClass('hidden');
    }
});
$('#mainContent').on('change','#department', function (){
    var type = $('#jftype').val();
    var department = $(this).val();
    if('qszzx' == department && 1 == type){
        $('#projectNotice').removeClass('hidden');
    }else {
        $('#projectNotice').addClass('hidden');
    }
})

$('#externalRecipient').change(function()
{
    var value = $(this).val();
    if(value == '2')
    {
        $('#isLastTransfer').removeClass('hidden');
        $('#transitionPhaseId').addClass('required');
    }
    else
    {
        $('#isLastTransfer').addClass('hidden');
        $('#transitionPhaseId').removeClass('required');
    }
});
$('#submit').click(function()
{
    $('#secondorderId').removeAttr('disabled');
    $('#transitionPhase').removeAttr('disabled');
    $('#lastTransfer').removeAttr('disabled');
});
function toggleAcl(num, type)
{
    if(num == 1)
    {
        $('#backReason').removeClass('hidden');
        if(type == 1){
            $('#stage').removeClass('hidden');
            $('#recipient').removeClass('hidden');
            $('#isLastTransfer').removeClass('hidden');
        }
    }
    else
    {
        $('#backReason').addClass('hidden');
        $('#stage').addClass('hidden');
        $('#recipient').addClass('hidden');
        $('#isLastTransfer').addClass('hidden');
    }
}
function toggleMaxLeader(iscode){
    if(iscode == 1)
    {
        $('.nodemaxleader').removeClass('hidden');
    }
    else
    {
        $('.nodemaxleader').addClass('hidden');
    }
}
$('#result').change(function()
{
    var type = $(this).val();
    if(type == 'reject')
    {
        $('#suggestTd').addClass('required');
        $('#sftpPathTr').addClass('hidden');
    }
    else
    {
        $('#suggestTd').removeClass('required');
        $('#sftpPathTr').removeClass('hidden');
    }
});
function changeOrder(orderId){
    oldSecondOrder = typeof oldSecondOrder != 'undefined' ? oldSecondOrder : '';
    if(orderId != null && orderId != ''&& orderId != 0){
        $.get(createLink('sectransfer', 'ajaxGetSecondOrder', "orderId=" + orderId +"&secondOrderID=" + oldSecondOrder), function(data){
            var obj = JSON.parse(data);
            if(obj.status == 'toconfirmed' || obj.status == 'assigned' || obj.status == 'tosolve' || obj.status == 'backed'){
                js:alert('该工单未完成，请先移至工单池补充工单完成情况。');
                $('#secondorderId').val('').trigger("chosen:updated");
                return ;
            }
            //迭代34 工单可以关联多个对外已移交（外部工单只支持一次）     (oldSecondOrder == '' || oldSecondOrder != orderId)[此处解决编辑时不能选择原工单问题]
            if(obj.formType == 'external' && (obj.status == 'indelivery' || obj.status == 'delivered' || obj.status == 'passed' || obj.status == 'returned') && (oldSecondOrder == '' || oldSecondOrder != orderId)){
                js:alert('该工单已交付，请重新选择');
                $('#secondorderId').val('').trigger("chosen:updated");
                return ;
            }
            //迭代34：只有自建工单状态 = 待交付、交付审批中、已交付且是‘否’可以关联工单可以关联多个对外移交 反之 提示
            //1、内部工单，是否最终移交： 是
            if( (obj.formType == 'internal' && obj.finallyHandOver == '1') && (oldSecondOrder == '' || oldSecondOrder != orderId)){
                js:alert('该工单已是最终移交，请重新选择。');
                $('#secondorderId').val('').trigger("chosen:updated");
                $('#finallyHandOver').val('').trigger("chosen:updated");
                return ;
            }
            //2、内部工单 状态未处于 待交付、交付审批中、已交付 、上线成功、上线异常 且是否最终移交 否
            if(obj.formType == 'internal' && !(obj.status == 'todelivered' || obj.status == 'indelivery' || obj.status == 'delivered' || obj.status == 'onlinesuccess' || obj.status == 'exception') &&  (obj.finallyHandOver == '2' ||(obj.finallyHandOver == 0))){
                js:alert('该工单未处在交付阶段，请重新选择。');
                $('#secondorderId').val('').trigger("chosen:updated");
                return ;
            }
            //2、内部工单 状态处于 待交付、交付审批中、已交付 、上线成功、上线异常 且所有对外移交单的是否最终移交 并集 为 是 且选择工单和并集为‘是’的单号不一致
            if(obj.formType == 'internal' && (obj.status == 'todelivered' || obj.status == 'indelivery' || obj.status == 'delivered' || obj.status == 'onlinesuccess' || obj.status == 'exception') &&  obj.handOver == '1' && id != obj.whichSecTransfer){
                js:alert("当前工单关联的对外移交单已存在最终移交（"+ obj.whichSecTransfer+"），请重新选择。");
                $('#secondorderId').val('').trigger("chosen:updated");
                $('#finallyHandOver').val('').trigger("chosen:updated");
                return ;
            }

            if(obj.status == 'closed' && obj.externalCode != null && obj.externalCode != ''){
                js:alert('该工单已关闭，请重新选择。');
                $('#secondorderId').val('').trigger("chosen:updated");
                return ;
            }
            if(obj.status == 'closed' && obj.ifAccept == 0){
                js:alert('该工单未受理，请重新选择。');
                $('#secondorderId').val('').trigger("chosen:updated");
                return ;
            }
            //迭代34：自建工单可以关联多个对外移交增加逻辑 （外部工单只能移交一次）
            if(obj.formType == 'external' && obj.secondOrderId != null && obj.secondOrderId != '' && (oldSecondOrder == '' || oldSecondOrder != orderId)){
                js:alert('该工单已被移交单关联，请重新选择。');
                $('#secondorderId').val('').trigger("chosen:updated");
                return ;
            }
            //迭代34：自建工单可以关联多个对外移交增加逻辑 （外部工单只能移交一次）
            /* if(obj.formType == 'external' && (obj.secondOrderId == null || obj.secondOrderId == '')){
                 $('.finallyHandOver').hide();
                 js:alert('此工单只能发起一次移交，请确认移交是否完整。');
                 return ;
             }*/
            //外部工单隐藏是否最终移交
            if(obj.formType == 'external'){
                $('.finallyHandOver').hide();
                $('#finallyHandOver').val('').trigger("chosen:updated");
                $('#finallyHandOverTip').addClass('hidden');
            }else{
                $('.finallyHandOver').show();
                $('#finallyHandOverTip').removeClass('hidden');
                $('#finallyHandOver').val(finallyHandOver).trigger("chosen:updated");
                //$('#finallyHandOver').val('').trigger("chosen:updated");
            }
        });
    }
}

$(document).ready(function()
{
    var orderId = $('#secondorderId').val();
    //工单id非空，主动调起检测
    if(orderId != '' && orderId != null){
        changeOrder(orderId);
    }
});