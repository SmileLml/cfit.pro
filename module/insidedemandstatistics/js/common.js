
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
}