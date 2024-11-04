$(function()
{
    if(window.self == window)
    {
        $("a[data-dismiss='modal']").addClass('hidden');
    }

    var editURL = $('.modal-body .page-actions .loadInModal').attr('href');
    $('.modal-body .page-actions .loadInModal').attr('href', editURL + '?onlybody=yes');
})
