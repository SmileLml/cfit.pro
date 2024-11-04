$(document).ready(function()
{
    if(typeof module !== 'undefined')
    { 
        $('#navbar .nav li').removeClass('active');
        if(config.requestType == 'GET')
        {
            $('#navbar .nav li a[href*=' + config.moduleVar + '\\=' + module + '\\&' + config.methodVar + '\\=browse]').parent('li').addClass('active');
        }
        else
        {
            $('#navbar .nav li a[href*=' + module + '-browse-]').parent('li').addClass('active');
        }
    }

    $('[type=file]').each(function()
    {
        var fileName = $(this).attr('name');
        var fileID   = fileName.substring(5, fileName.length - 2);

        $(this).attr('id', fileID);
    })
})

function loadPrevData($selector, dataID, element)
{
    if(typeof dataID  === 'undefined') dataID  = 0;
    if(typeof element === 'undefined') element = 'tr';

    var prev   = $selector.data('prev');
    var next   = $selector.data('next');
    var action = $selector.data('action');
    var field  = $selector.data('field');
    if(dataID == 0) dataID = $selector.data('dataid');

    $('.prevData.' + prev).remove();

    /* Must use flow as module name here because the function ajaxGetPrevData is not a action of a flow. */
    var link = createLink('flow', 'ajaxGetPrevData', 'prev=' + prev + '&next=' + next + '&action=' + action + '&dataID=' + dataID + '&element=' + element);
    $.get(link, function(prevData)
    {
        if(!prevData) return false;

        $selector.after(prevData);
    });
}
