function loadProductBuilds(productID)
{
    var link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=resolvedBuild&build=');
    $.post(link, function(data)
    {
        $('#build').replaceWith(data);
        $('#build_chosen').remove();
        $('#resolvedBuild').attr('multiple', '');
        $('#resolvedBuild').attr('id', 'build').attr('name', 'build[]').find('option[value=trunk]').remove();
        $('#build').chosen();
    });

    loadProductProjects(productID);
}

function loadProductProjects(productID)
{
    var applicationID = $('#applicationID').val();
    var link = createLink('rebirth', 'ajaxProjectByProduct', 'applicationID=' + applicationID + '&productID=' + productID + '&browseType=&projectID=0');
    $.post(link, function(data)
    {
        $('#project').replaceWith(data);
        $('#project_chosen').remove();
        $('#project').chosen();
    })
}
