$(function()
{
    $('#mainContent .main-header h2 #selectTask').change(function()
    {
        var taskID = $(this).val();
        if(taskID)
        {
            location.href = createLink('testreport', 'create', 'applicationID=' + applicationID + '&productID=' + productID + '&objectType=testtask&extra=' + taskID);
            return false;
        }
    });

    var taskID = $('#selectTask').val();
    $('#objectID').val(taskID);
})
