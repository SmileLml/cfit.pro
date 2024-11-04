/**
  * Load all users as assignedTo list.
  *
  * @access public
  * @return void
  */
function loadAllUsers()
{
    var link = createLink('bug', 'ajaxLoadAllUsers', 'selectedUser=' + $('#assignedTo').val());
    $.get(link, function(data)
    {
        if(data)
        {
            var moduleID  = $('#module').val();
            var productID = $('#product').val();
            $('#assignedTo').replaceWith(data);
            $('#assignedTo_chosen').remove();
            $('#assignedTo').chosen();
        }
    });
}

/**
  * Load team members of the latest execution of a product as assignedTo list.
  *
  * @param  int    $productID
  * @access public
  * @return void
  */
function loadExecutionTeamMembers(productID)
{
    var link = createLink('bug', 'ajaxLoadExecutionTeamMembers', 'productID=' + productID + '&selectedUser=' + $('#assignedTo').val());
    $.post(link, function(data)
    {
        $('#assignedTo').replaceWith(data);
        $('#assignedTo_chosen').remove();
        $('#assignedTo').chosen();
    })
}

/**
 * Load assignedTo and stories of module.
 *
 * @access public
 * @return void
 */
function loadModuleRelated()
{
    var moduleID  = $('#module').val();
    var productID = $('#product').val();
    var storyID   = $('#story').val();
    setStories(moduleID, productID, storyID);
}

$(function()
{
    var productID  = $('#product').val();
    var moduleID   = $('#module').val();
    var assignedto = $('#assignedTo').val();
    changeProductConfirmed = true;
    oldStoryID             = $('#story').val() || 0;
    oldExecutionID         = 0;
    oldOpenedBuild         = '';
    oldTaskID              = $('#oldTaskID').val() || 0;

    if($('#project').val()) loadProductExecutions(productID, $('#project').val());
    notice();


    $('[data-toggle=tooltip]').tooltip();

    /* Adjust size of bug type input group. */
    var adjustBugTypeGroup = function()
    {
        var $group = $('#bugTypeInputGroup');
        var width = ($group.parent().width()), addonWidth = 0;
        var $controls = $group.find('.chosen-single');
        $group.children('.input-group-addon').each(function()
        {
            addonWidth += $(this).outerWidth();
        });
        var bestWidth = Math.floor((width - addonWidth)/$controls.length);
        $controls.css('width', bestWidth);
        var lastWidth = width - addonWidth - bestWidth * ($controls.length - 1);
        $controls.last().css('width', lastWidth);
    };
    adjustBugTypeGroup();
    $(window).on('resize', adjustBugTypeGroup);

    /* Init pri and severity selector. */
    $('#severity, #pri').on('change', function()
    {
        var $select = $(this);
        var $selector = $select.closest('.pri-selector');
        var value = $select.val();
        $selector.find('.pri-text').html($selector.data('type') === 'severity' ? '<span class="label-severity" data-severity="' + value + '" title="' + value + '"></span>' : '<span class="label-pri label-pri-' + value + '" title="' + value + '">' + value + '</span>');
    });

    /* Get steps template. */
    var stepsTemplate = editor['steps'].html();

    /* Judgment of required items for steps. */
    $('#submit').on('click', function()
    {
        var steps = editor['steps'].html();
        if(stepsRequired !== false && (steps == stepsTemplate || steps == editor.steps.templateHtml) && isStepsTemplate)
        {
            bootbox.alert(stepsNotEmpty);
            return false;
        }
    });
});

$(window).unload(function(){
    if(blockID) window.parent.refreshBlock($('#block' + blockID));
});
