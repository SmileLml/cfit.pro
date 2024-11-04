/**
 * 设置审核信息显示
 *
 * @param type
 */
function setReviewNodeInfo(type){
    //审核节点的展示隐藏
    if(type == 'tech'){
        $('.node3').addClass('hidden');
        $('.node5').addClass('hidden');
    }else {
        $('.node3').removeClass('hidden');
        $('.node5').removeClass('hidden');
    }
    $('.node3').addClass('hidden');
}
//选择需求单位或部门类型
function selectCompany(isLoad=0) {
    var _val = $("#dataCollectApplyCompany option:selected").val();
    var arr = ['1','2','3'];
    if ($.inArray(_val,arr) == -1){
        $(".companyInput").removeClass('hidden');
        $(".companySelect").addClass('hidden');
        if (isLoad != '1'){
            $("#demandUnitOrDepInput").val('');
        }
    }else{
        $(".companyInput").addClass('hidden');
        $(".companySelect").removeClass('hidden');
        if (isLoad != 1){
            $("#demandUnitOrDepInput").val('');
            $.get(createLink('infoqz', 'ajaxGetDemandUnitList', "type=" + _val), function(data)
            {
                $('#demandUnitOrDepSelect_chosen').remove();
                $('#demandUnitOrDepSelect').replaceWith(data);
                $('#demandUnitOrDepSelect').chosen();
            });
        }
    }
}
