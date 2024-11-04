function loadProductBuilds(productID)
{
    var applicationID = $('#applicationID').val();
    // var projectID     = $('#project').val(); 按照原有逻辑不需要根据下拉条件动态获取

    var link = createLink('build', 'ajaxGetProductBuildsByJoins', 'app='+applicationID+'&projects='+projectID+'&productID=' + productID + '&varName=resolvedBuild&build=');
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
    // 判断一下如果是项目-测试菜单下创建对象，则不重新加载项目。
    var applicationName = $('#applicationName').val();
    if(typeof(applicationName) == 'undefined')
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
}
