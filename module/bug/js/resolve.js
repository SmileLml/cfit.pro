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

$(function()
{
    /* Fix bug #3227. */
    var requiredFields = config.requiredFields;
    if(requiredFields.indexOf('resolvedBuild') == -1)
    {
        resolvedBuildTd  = $('#resolvedBuild').closest('td');
        $('#resolution').change(function()
        {
            if($(this).val() == 'fixed')
            {
                resolvedBuildTd.addClass('required');
            }
            else
            {
                resolvedBuildTd.removeClass('required');
            }
        });
    }

    $('#product').change();
})

function ajaxGetProductByApplication(applicationID)
{
    var browseType = 'resolved';
    var link       = createLink('rebirth', 'ajaxGetProductByApplication', 'applicationID=' + applicationID + '&browseType=' + browseType + '&showAll=hidden&showNa=hidden');
    $.get(link, function(data)
    {
        $('#product').replaceWith(data);
        $('#product_chosen').remove();
        $("#product").chosen();
    })

    ajaxProjectByProduct(0);
}

function ajaxProjectByProduct(productID)
{
    var browseType    = 'resolved';
    var applicationID = $('#applicationID').val();
    var link          = createLink('rebirth', 'ajaxProjectByProduct', 'applicationID=' + applicationID + '&productID=' + productID + '&browseType=' + browseType + '&projectID=' + oldProjectID);
    $.get(link, function(data)
    {
        $('#project').replaceWith(data);
        $('#project_chosen').remove();
        $("#project").chosen();
    })

    var link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=resolvedBuild&build=&branch=');
    $('#resolvedBuildBox').load(link, function(){$(this).find('select').chosen()});
}
