<script>
$('a[disabled=disabled]').addClass('disabled');

$(function()
{
    var html = $('#fieldList td.actions a.edit:first').html();
    $('#fieldList td.actions a.edit').attr('title', html).addClass('btn').html("<i class='icon icon-edit'></i>");

    var html = $('#fieldList td.actions a.deleteField:first').html();
    $('#fieldList td.actions a.deleteField').attr('title', html).addClass('btn').html("<i class='icon icon-trash'></i>");
    var html = $('#fieldList td.actions a.disabled:first').html();
    $('#fieldList td.actions a.disabled').attr('title', html).addClass('btn').html("<i class='icon icon-trash'></i>");
})
</script>
