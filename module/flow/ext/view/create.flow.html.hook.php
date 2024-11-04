<script>
$(function()
{
    $('#contactListMenu').attr("onchange", "setMailto('toList', this.value)");
})

function setMailto(mailto, contactListID)
{
    link = createLink('user', 'ajaxGetContactUsers', 'listID=' + contactListID);
    $.get(link, function(users)
    {
        $('#' + mailto).replaceWith(users);
        $('#mailto').attr('id', mailto).attr('name', mailto + '');
        $('#' + mailto + '_chosen').remove();
        $('#' + mailto).chosen();
    });
}
</script>
