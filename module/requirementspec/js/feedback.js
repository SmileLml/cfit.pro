function loadProjects(projectID)
{
    var link = createLink('requirement', 'ajaxGetProjects', 'projectID=' + projectID);
    $.post(link, function(data)
    {
        $('#project').replaceWith(data);        
        $('#project_chosen').remove();
        $('#project').chosen();
    })
}

function loadProducts(productID)
{
    var link = createLink('requirement', 'ajaxGetProducts', 'productID=' + productID);
    $.post(link, function(data)
    {
        $('#product').replaceWith(data);        
        $('#product_chosen').remove();
        $('#product').chosen();
    })
}

function loadLines()
{
    var link = createLink('requirement', 'ajaxGetLines');
    $.post(link, function(data)
    {
        $('#line').replaceWith(data);
        $('#line_chosen').remove();
        $('#line').chosen();
    })
}

function loadApps(appID)
{
    var link = createLink('requirement', 'ajaxGetApps', 'appID=' + appID);
    $.post(link, function(data)
    {
        $('#app').replaceWith(data);       
        $('#app_chosen').remove();
        $('#app').chosen();
    })
}
