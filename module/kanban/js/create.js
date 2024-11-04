$(function()
{
    if(enableImport == 'off')
    {
        $("input[name^='importObjectList']").attr('disabled', 'disabled');
        $('td.objectBox').hide();
    }
    $("input[id='copyContentbasicInfo']").click(function(){return false;})

    copyRegion = $("input[id='copyContentregion']").prop('checked');
    $("input[id='copyContentregion']").click(function()
    {
        copyRegion = $(this).prop('checked');
        $('#copyRegion').val(copyRegion);
    });

    $("input[name='import']").change(function()
    {
        if($(this).val() == 'off')
        {
            $("input[name^='importObjectList']").attr('disabled', 'disabled');
            $('td.objectBox').hide();
        }
        else
        {
            $("input[name^='importObjectList']").removeAttr('disabled');
            $('td.objectBox').show();
        }
    })

    $("input[name^='importObjectList']").change(function()
    {
        if($("input:checked[name^=importObjectList]").length != 0 && !$('#emptyTip').is('.hidden')) $('#emptyTip').addClass('hidden');
    })

    $('#submit').click(function()
    {
        var enableImport     = $("input:checked[name='import']").val();
        var objectListLength = $("input:checked[name^=importObjectList]").length;

        if(enableImport == 'on' && objectListLength == 0 && vision != 'lite')
        {
            $('#emptyTip').removeClass('hidden');
            return false;
        }
    })

    $(document).on('click', '#copyKanbans a', function()
    {
        setCopyKanban($(this).data('id')); $('#copyKanbanModal').modal('hide');
    });
    handleKanbanWidthAttr();
})

/**
 * Set copy kanban.
 *
 * @param  int    $kanbanID
 * @access public
 * @return void
 */
function setCopyKanban(kanbanID)
{
    var extra = copyRegion ? '&extra=copyRegion=' + copyRegion : '';
    location.href = createLink('kanban', 'create', 'spaceID=' + spaceID + '&type=' + spaceType + '&copyKanbanID=' + kanbanID + extra);
}

/**
 * When space type change.
 *
 * @oaram  int    spaceID
 * @param  string type
 * @access public
 * @return void
 */
function changeValue(spaceID, type)
{
    if(typeof type === 'undefined') type = spaceType;
    location.href = createLink('kanban', 'create', 'spaceID=' + spaceID + '&type=' + type);
}

/**
 * The team or whitelist member that loads kanban.
 *
 * @oaram  int    spaceID
 * @access public
 * @return void
 */
function loadUsers(spaceID)
{
    var field = spaceType == 'private' ? 'whitelist' : 'team';
    var link  = createLink('kanban', 'ajaxLoadUsers', 'spaceID='+ spaceID + '&field=' + field + '&selectedUser=' + $('#' + field).val());
    $.get(link, function(data)
    {
        $('#' + field).replaceWith(data);
        $('#' + field).next('.picker').remove();
        $('#' + field).picker();
    });

    if(spaceType != 'private') loadOwners(spaceID);
}
/**
 * Handle radio logic of Kanban column width setting.
 *
 * @access public
 * @return void
 */
function handleKanbanWidthAttr ()
{
    $('#colWidth, #minColWidth, #maxColWidth').attr('onkeyup', 'value=value.match(/^\\d+$/) ? value : ""');
    $('#colWidth, #minColWidth, #maxColWidth').attr('maxlength', '3');
    var fluidBoard = $("#mainContent input[name='fluidBoard'][checked='checked']").val() || 0;
    var addAttrEle = fluidBoard == 0 ? '#colWidth' : '#minColWidth, #maxColWidth';
    var $fixedTip  = $('#colWidth + .fixedTip');
    var $autoTip   = $('#maxColWidth + .autoTip');
    $(addAttrEle).closest('.width-radio-row').addClass('required');
    $('#colWidth').attr('disabled',fluidBoard == 1);
    $('#minColWidth, #maxColWidth').attr('disabled',fluidBoard == 0);
    $("#minColWidth, #maxColWidth").on('input', function()
    {
        $('#minColWidthLabel, #maxColWidthLabel').remove();
        $('#minColWidth, #maxColWidth').removeClass('has-error');
    });

    if(fluidBoard == 1)
    {
        $fixedTip.addClass('hidden');
        $autoTip.removeClass('hidden');
    }
    else
    {
        $fixedTip.removeClass('hidden');
        $autoTip.addClass('hidden');
    }

    $(document).on('change', "#mainContent input[name='fluidBoard']", function(e)
    {
        $('#colWidth').attr('disabled', e.target.value == 1);
        $('#minColWidth, #maxColWidth').attr('disabled', e.target.value == 0);
        if(e.target.value == 0 && $('#minColWidthLabel, #maxColWidthLabel'))
        {
            $('#colWidth').closest('.width-radio-row').addClass('required');
            $('#minColWidth, #maxColWidth').closest('.width-radio-row').removeClass('required');
            $('#minColWidthLabel, #maxColWidthLabel').remove();
            $('#minColWidth, #maxColWidth').removeClass('has-error');
            $fixedTip.removeClass('hidden');
            $autoTip.addClass('hidden');
        }
        else if(e.target.value == 1 && $('#colWidthLabel'))
        {
            $('#minColWidth, #maxColWidth').closest('.width-radio-row').addClass('required');
            $('#colWidth').closest('.width-radio-row').removeClass('required');
            $('#colWidthLabel').remove();
            $('#colWidth').removeClass('has-error');
            $fixedTip.addClass('hidden');
            $autoTip.removeClass('hidden');
        }
    });
}