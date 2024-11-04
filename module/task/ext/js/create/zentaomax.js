function setStoryRelated()
{
    setPreview();
    setStoryModule();
    setStoryDesign();
}

function setStoryDesign()
{
    var storyID = $('#story').val();
    if(storyID)
    {
        var link = createLink('story', 'ajaxGetDesign', 'storyID=' + storyID);
        $.post(link, function(data)
        {
            $('#design').replaceWith(data);
            $('#design_chosen').remove();
            $('#design').chosen();
        })
    }
}
