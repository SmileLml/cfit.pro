function addItem(obj)
{
    var item = $('#addItem').html().replace(/%i%/g, i);
    $(obj).closest('tr').after('<tr class="addedItem">' + item  + '</tr>');
    $(".addedItem .object-select").chosen();
}
function deleteItem(obj)
{
    if($('.object-th').length <= 2) return false;
    $(obj).closest('tr').remove();
}
