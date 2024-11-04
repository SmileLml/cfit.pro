$(function()
{
    setTitleWidth()
    $('#feedbackList').resize(function()
    {
        $('#feedbackList th.c-title').width('auto');
        setTitleWidth()
    });
})

function setTitleWidth()
{
    if($('#feedbackList th.c-title').width() <= 150) $('#feedbackList th.c-title').width(150);
}
