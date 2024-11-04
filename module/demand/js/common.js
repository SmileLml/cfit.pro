
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
        // $('#execution').chosen();
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
    if($('#execution').attr("notype") == '2' ){
         $('#flag').val(''); //是select，将标志置空
    }
}

function selectproduct (productID){
    //当选择产品为 无(99999) 时增加提示语
    if(productID == 99999){
        bootbox.dialog({
            title:'提示：',
            message:productTip,
            buttons:{
                red:{
                    label:'确认',
                    className:'btn-danger',
                    callback:function(){
                        return true;
                    }
                },
                blue:{
                    label:'取消',
                    className:'btn-primary',
                    callback:function(){
                        window.location.href = createLink('demand', 'browse');
                    }
                }
            }
        })
    }
    $.get(createLink('demand', 'ajaxGetProductPlan', "productID=" + productID), function(planList)
    {
        $('#productPlan_chosen').remove();
        $('#productPlan').replaceWith(planList);
        if(productID == '99999') {
            $('#productPlan').val('1');
        }
        $('#productPlan').chosen();
    });
};