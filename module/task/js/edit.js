$(function()
{
    $('.record-estimate-toggle').modalTrigger({width:900, type:'iframe', afterHide: function(){parent.location.href=parent.location.href;}});
})

$('#modalTeam .btn').click(function()
{
    var team = '';
    var time = 0;
    $('[name*=team]').each(function()
    {
        if($(this).find('option:selected').text() != '')
        {
            team += ' ' + $(this).find('option:selected').text();
        }

        estimate = parseFloat($(this).parents('td').next('td').find('[name*=teamEstimate]').val());
        if(!isNaN(estimate))
        {
            time += estimate;
        }

        $('#teamMember').val(team);
        $('#estimate').val(time);
    })
});

/**
 * Load module, stories and members.
 *
 * @param  int    $executionID
 * @access public
 * @return void
 */
function loadAll(executionID)
{
    if(!changeExecutionConfirmed)
    {
        firstChoice = confirm(confirmChangeExecution);
        changeExecutionConfirmed = true;    // Only notice the user one time.
    }
    if(changeExecutionConfirmed && firstChoice)
    {
        loadModuleMenu(executionID);
        //loadExecutionStories(executionID);
        loadExecutionMembers(executionID);
    }
    else
    {
        $('#execution').val(oldExecutionID);
        $("#execution").trigger("chosen:updated");
    }
}

/**
 * Load module of the execution.
 *
 * @param  int    $executionID
 * @access public
 * @return void
 */
function loadModuleMenu(executionID)
{
    var link = createLink('tree', 'ajaxGetOptionMenu', 'rootID=' + executionID + '&viewtype=task');
    $('#moduleIdBox').load(link, function(){$('#module').chosen();});
}

/**
 * Load stories of the execution.
 *
 * @param  int    $executionID
 * @access public
 * @return void
 */
function loadExecutionStories(executionID, moduleID)
{
    if(typeof(moduleID) == 'undefined') moduleID = 0;
    var link = createLink('story', 'ajaxGetExecutionStories', 'executionID=' + executionID + '&productID=0&branch=0&moduleID=' + moduleID + '&storyID=' + oldStoryID);
    $('#storyIdBox').load(link, function(){$('#story').chosen();});
}

/**
 * Load team members of the execution.
 *
 * @param  int    $executionID
 * @access public
 * @return void
 */
function loadExecutionMembers(executionID)
{
    var link = createLink('execution', 'ajaxGetMembers', 'executionID=' + executionID + '&assignedTo=' + oldAssignedTo);
    $('#assignedToIdBox').load(link, function(){$('#assignedToIdBox').find('select').chosen()});
}

/**
 * Load module related
 *
 * @access public
 * @return void
 */
function loadModuleRelated()
{
    moduleID    = $('#module').val();
    executionID = $('#execution').val();
    loadExecutionStories(executionID, moduleID)
}

/* empty function. */
function setPreview(){}

$(document).ready(function()
{
    /* show team menu. */
    if($('#multiple').length > 0){ //20220630 增加判断 区分单人多人任务
        $('[name=multiple]').change(function()
        {
            var checked = $(this).prop('checked');
            if(checked)
            {
                $('#teamTr').removeClass('hidden');
                $('#parent').val('');
                $('#parent').trigger('chosen:updated');
                $('#parent').closest('tr').addClass('hidden');
                $('#estimate').attr('readonly','readonly');//增加readonly属性
            }
            else
            {
                $('#teamTr').addClass('hidden');
                $('#parent').closest('tr').removeClass('hidden');
                $('#estimate').removeAttr('readonly');//去掉readonly属性
            }

        });
    }else{
        $('#estimate').removeAttr('readonly');//去掉readonly属性  所有单人都可编辑
    }

    /* Init task team manage dialog */
    var $taskTeamEditor = $('#taskTeamEditor').batchActionForm(
    {
        idStart: 0,
        idEnd: newRowCount - 1,
        chosen: true,
        datetimepicker: false,
        colorPicker: false,
    });
    var taskTeamEditor = $taskTeamEditor.data('zui.batchActionForm');

    var adjustButtons = function()
    {
        var $deleteBtn = $taskTeamEditor.find('.btn-delete');
        if ($deleteBtn.length == 1) $deleteBtn.addClass('disabled').attr('disabled', 'disabled');
    };

    $taskTeamEditor.on('click', '.btn-add', function()
    {
        var $newRow = taskTeamEditor.createRow(null, $(this).closest('tr'));
        $newRow.addClass('highlight');
        setTimeout(function()
        {
            $newRow.removeClass('highlight');
        }, 1600);
        adjustButtons();
    }).on('click', '.btn-delete', function()
    {
        var $row = $(this).closest('tr');
        $row.addClass('highlight').fadeOut(700, function()
        {
            $row.remove();
            adjustButtons();
        });
    });

    adjustButtons();

    $('#showAllModule').change(function()
    {
        var moduleID = $('#moduleIdBox #module').val();
        var extra    = $(this).prop('checked') ? 'allModule' : '';
        $('#moduleIdBox').load(createLink('tree', 'ajaxGetOptionMenu', "rootID=" + executionID + '&viewType=task&branch=0&rootModuleID=0&returnType=html&fieldID=&needManage=0&extra=' + extra), function()
        {
            $('#moduleIdBox #module').val(moduleID).attr('onchange', "loadModuleRelated()").chosen();
        });
    });
});
