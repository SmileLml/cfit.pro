function addItem(obj)
{
    var $inputRow = $(obj).closest('tr');
    var $newRow   = $(obj).closest('tr').clone();
    $newRow.find('input').val('');
    $inputRow.after($newRow);
    $newRow.find("select[name^=changedID]").chosen();
    $newRow.find("#changedID_chosen:last").remove();
    $newRow.find(".form-date").datepicker();
}

function deleteItem(obj)
{
    $(obj).closest('tr').remove();
}
