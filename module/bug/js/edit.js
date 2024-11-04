$(function()
{
    loadModuleRelated();

    var projectID     = $('#project').val();
    var applicationID = $('#applicationID').val();
    var link          = createLink('testcase', 'ajaxProductProjectCases', 'applicationID=' + applicationID + '&productID=' + productID + '&projectID=' + projectID + '&caseID=' + caseID);
    $.post(link, function(data)
    {
        if(!data) data = '<select id="case" name="case" class="form-control"></select>';
        $('#case').replaceWith(data);
        $('#case_chosen').remove();
        $("#case").chosen();
    })
});

function ajaxGetProductByApplication(applicationID)
{
    if(typeof(changeApplicationConfirmed) != 'undefined' && !changeApplicationConfirmed)
    {
        firstChoice = confirm(confirmChangeApplication);
        changeApplicationConfirmed = true;    // Only notice the user one time.
        if(!firstChoice)
        {
            $('#applicationID').val(oldApplicationID);//Revert old product id if confirm is no.
            $('#applicationID').trigger("chosen:updated");
            $('#applicationID').chosen();
            return true;
        }
    }

    var browseType = 'edit';
    var link = createLink('rebirth', 'ajaxGetProductByApplication', 'applicationID=' + applicationID + '&browseType=' + browseType + '&showAll=hidden');
    $.get(link, function(data)
    {
        $('#product').replaceWith(data);
        $('#product_chosen').remove();
        $("#product").chosen();
    })

    loadAll(0);
}

/**
 * Set duplicate field.
 *
 * @param  string $resolution
 * @access public
 * @return void
 */
function setDuplicate(resolution)
{
    if(resolution == 'duplicate')
    {
        $('#duplicateBugBox').show();
    }
    else
    {
        $('#duplicateBugBox').hide();
    }
}

/**
 * Get story or task list.
 *
 * @param  string $module
 * @access public
 * @return void
 */
function getList(module)
{
    productID = $('#product').val();
    executionID = $('#execution').val();
    storyID   = $('#story').val();
    taskID    = $('#task').val();
    if(module == 'story')
    {
        link = createLink('search', 'select', 'productID=' + productID + '&executionID=' + executionID + '&module=story&moduleID=' + storyID);
        $('#storyListIdBox a').attr("href", link);
    }
    else
    {
        link = createLink('search', 'select', 'productID=' + productID + '&executionID=' + executionID + '&module=task&moduleID=' + taskID);
        $('#taskListIdBox a').attr("href", link);
    }
}

/**
 * load stories of module.
 *
 * @access public
 * @return void
 */
function loadModuleRelated()
{
    moduleID  = $('#module').val();
    productID = $('#product').val();
    storyID   = $('#story').val();
    setStories(moduleID, productID, storyID);
}
