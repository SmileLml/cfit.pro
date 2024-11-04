$(function()
{
    $(document).on('click', '.ajaxPager', function()
    {   
        $('#sidebar .side-body').load($(this).attr('data-href'));
        return false;
    })
})
