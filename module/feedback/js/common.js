function setComment(type)
{
    $('#commentBox').toggle();
    $('#commentBox #type').val(type);
    $('#commentBox textarea').val('');
    window.editor['comment'].html('');
    $('.ke-container').css('width', '100%');
}

function like(feedbackID)
{
    var likeLink = createLink('feedback', 'ajaxLike', 'feedbackID=' + feedbackID);
    $('.likesBox').load(likeLink);
}

$(function()
{
    if(window['browseType'] == 'bysearch') $.toggleQueryBox(true);

    $(':checkbox[id^=isFeedback]').click(function()
    {
        var checked = $(this).prop('checked');
        $(this).closest('tr').find('input[type=checkbox]').prop('checked', checked);
    })
})
