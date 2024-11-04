<?php
if($action->open == 'modal')
{
    $title = "<span class='label label-id'>{$dataID}</span> <span title='$title'>{$action->name}</span>";
}

$oldDir = getcwd();
chdir(dirname(dirname($oldDir)) . '/view');
include './operate.html.php';
chdir($oldDir);
?>
<?php if($action->open == 'normal'):?>
<script>
$(function()
{
    var moduleNavigator = '<?php echo $flow->navigator;?>';
    var moduleApp       = '<?php echo $flow->app;?>';

    if(moduleNavigator == 'primary')
    {
        $('#subNavbar li:first').addClass('active');
    }
    else if(moduleNavigator == 'secondary')
    {
        $('#navbar li[data-id=<?php echo $flow->module;?>]').addClass('active');
    }
})
</script>
<?php endif;?>
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
