$(function()
{
    $('[name^=canSearch]').change(function()
    {
        var checked = $(this).prop('checked');
        $(this).parents('.settingBox').find('.fieldBox').toggle(checked);
        $(this).parents('.settingBox').find('[name^=checkAll], [name^=fields]').prop('checked', checked);
    });

    $('[name^=checkAll]').change(function()
    {
        var checked = $(this).prop('checked');
        $(this).parents('.fieldBox').find('[name^=fields]').prop('checked', checked);
    });

    $('#submit').click(function()
    {
        /* Check if checked the fields to search. */
        var emptySearch = $('[name^=canSearch]').prop('checked') && $('[name^=fields]:checked').length == 0;

        if(!emptySearch)
        {
            $('#ajaxForm').submit();
            return false;
        }
        
        bootbox.confirm(emptySearch, function(result)
        {
            if(result)
            {
                $('#ajaxForm').submit();
            }
        });
        
        return false;
    });

    $('.sortable .checkbox label').append("<i class='icon-move'></i>");
    $('.sortable').sortable({trigger: '.icon-move'});
});
