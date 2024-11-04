/**
 * Load executions of product.
 *
 * @access public
 * @return void
 * @param projectID
 * @param fixtype
 */
function loadProductExecutions(projectID = 0,fixtype = null,app = null)
{
    if(typeof(bugExecutionID) !== 'undefined') var executionID = bugExecutionID;
    var link = createLink('problem', 'ajaxGetExecutionSelect', 'projectID=' + projectID + '&executionID=' + executionID + '&fixtype=' + fixtype + '&app=' + app);
    $.ajaxSettings.async = false;
    $.post(link, function(data)
    {
        $('#execution').replaceWith(data);
        if(execution && $('#execution').attr("notype") != '1'){
            $('#execution').val(execution);
            $('#executionid').val(execution);
        }else{
            $('#executionid').val( $('#execution').val());
        }
        $('#execution_chosen').remove();
        if($('#execution').attr("notype") == '2'){
            $('#execution').chosen();
        }
    })
    $.ajaxSettings.async = true;
    var fixtype = $('#fixType').val();
    if($('#execution').attr("notype") == '1' && fixtype == 'second'){
        $('#execution').parent().removeClass('required');
        $('#flag').val('1');
    }
    if($('#execution').attr("app") != '' && fixtype == 'second'){
        $('#application').val($('#execution').attr("app"));
    }

    if((fixtype == 'second' && execution) || $('#fixType').val() == 'second'){
        $('#execution').prop('disabled', true).trigger("chosen:updated");
    }else{
        $('#execution').prop('disabled', false).trigger("chosen:updated");
    }
    if(execution && $('#execution').attr("notype") != '1' && execution != $('#execution').val() ){
        $('#executionid').val($('#execution').val());
    }
}

function productChange(obj){
    var productID = $(obj).val();
    var dataid = $(obj).parent().parent().find('.addStage').attr("data-id");
    $.get(createLink('problem', 'ajaxGetProductPlan', "productID=" + productID+"&data_id="+dataid), function (planList) {
        $('#productPlan_chosen').remove();
        if($('#p_'+dataid+'_chosen').length > 0){
            $('#p_'+dataid+'_chosen').remove();
        }
        $('#p-'+dataid).replaceWith(planList);
       // $('.productPlan').chosen();
        if(productID == '99999') {
            $('#p-'+dataid).val('1');
        }
        $('#p-'+dataid).chosen();
    });
    $(obj).parent().parent().find('.addProductPlan').attr({"data-id":productID});
}


//新建产品
function createpro()
{
    var flag = "<?php echo $clickable = commonModel::hasPriv('product', 'create');?>";
    if(!flag){
        js:alert('您没有菜单『产品管理』新建产品的权限，请联系质量部阮涛添加权限');
        return false;
    }
    var app = $('#app').val();
    if(app === "0"){
        js:alert('请选择受影响业务系统后，再新增所属产品！');
        return false;
    }

    var url = 'product-create-0-'+app+".html#app=product";

    window.open(url, "_blank");
    return true;
};
//新建产品版本
function createPlan(obj)
{
    var flag = "<?php echo $clickable = commonModel::hasPriv('productplan', 'create');?>";
    if(!flag){
        js:alert('您没有菜单『产品管理』新建产品版本的权限，请联系质量部阮涛添加权限');
        return false;
    }
    var productID = $(obj).attr('data-id');
    if(productID === "0"){
        js:alert('请选择所属产品后，再新增产品版本!');
        return false;
    }
    var url = 'productplan-create-'+productID+'.html';
    window.open(url, "_blank");
    return true;
}
//刷新操作
function proandver(obj){
    var dataid = $(obj).parent().parent().find('.addStage').attr("data-id");
    var id = dataid == '1' ? '' : dataid;
    var productID = $('#product'+ id).val();
    var app = $('#app').val();
    $.get(createLink('problem', 'ajaxGetProduct','app='+app+"&data_id="+id), function(productlist)
    {
        $('#product'+ id +'_chosen').remove();
        $('#product'+ id).replaceWith(productlist);
        $('#product'+ id).val(productlist);
        $('#product'+ id).chosen();
    });
    $.get(createLink('problem', 'ajaxGetProductPlan', "productID=" + 0 +"&data_id="+dataid), function(planList)
    {
       $('#productPlan_chosen').remove();
       if($('#p_'+dataid+'_chosen').length > 0){
          $('#p_'+dataid+'_chosen').remove();
       }
       $('#p-'+dataid).replaceWith(planList);
       $('#p-'+dataid).val(productPlan);
       $('#p-'+dataid).chosen();
    });

}

//验证权限
function isClickable(id, action){
    $.get(createLink('problem', 'ajaxIsClickable', "id=" + id + "&action="+action), function(data)
    {
        var obj = JSON.parse(data);
        if(obj.data == 1){
            $('#isClickable_' + action + id).click();
        }else {
            let errorMsg = new $.zui.Messager({
                type:'warning',
                time: 2000,
            })
            errorMsg.show(authStatusError);
            setTimeout(function () {
                window.location.reload();
            },2000)
        }
    });
}
