$(function()
{
    var page = window.page || '';
    var flow = window.flow;
    if(typeof(systemMode) == undefined) var systemMode = '';

    $('#subNavbar a[data-toggle=dropdown]').parent().addClass('dropdown dropdown-hover');

    if(page == 'create' || page == 'edit' || page == 'assignedto' || page == 'confirmbug')
    {
        oldProductID = $('#product').val();
    }

    if(window.flow != 'full')
    {
        $('.querybox-toggle').click(function()
        {
            $(this).parent().toggleClass('active');
        });
    }
});

/**
 * Load all fields.
 *
 * @param  int $productID
 * @access public
 * @return void
 */
function loadAll(productID)
{
    if(page == 'create')
    {
        loadExecutionTeamMembers(productID);
    }

    $('#taskIdBox').innerHTML = '<select id="task"></select>';  // Reset the task.
    $('#task').chosen();
    loadProductModules(productID);
    loadProductProjects(productID);
    loadProductBuilds(productID);
    loadProductStories(productID);
    loadTestTasks(productID);
}

/**
  *Load all builds of one execution or product.
  *
  * @param  object $object
  * @access public
  * @return void
  */
function loadAllBuilds(object)
{
    if(page == 'resolve')
    {
        oldResolvedBuild = $('#resolvedBuild').val() ? $('#resolvedBuild').val() : 0;
        link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=resolvedBuild&build=' + oldResolvedBuild + '&branch=0&index=0&type=all');
        $('#resolvedBuildBox').load(link, function(){$(this).find('select').chosen()});
    }
    else
    {
        productID   = $('#product').val();
        executionID = $('#execution').val();

        var buildBox = '';
        if(page == 'edit') buildBox = $(object).closest('.input-group').attr('id');

        if(executionID)
        {
            loadAllExecutionBuilds(executionID, productID, buildBox);
        }
        else
        {
            loadAllProductBuilds(productID, buildBox);
        }
    }
}

/**
  * Load all builds of the execution.
  *
  * @param  int    $executionID
  * @param  int    $productID
  * @param  string $buildBox
  * @access public
  * @return void
  */
function loadAllExecutionBuilds(executionID, productID, buildBox)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    if(page == 'create')
    {
        oldOpenedBuild = $('#openedBuild').val() ? $('#openedBuild').val() : 0;
        link = createLink('build', 'ajaxGetExecutionBuilds', 'executionID=' + executionID + '&productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + '&branch=' + branch + '&index=0&needCreate=true&type=all');
        $.get(link, function(data)
        {
            if(!data) data = '<select id="openedBuild" name="openedBuild" class="form-control" multiple=multiple></select>';
            $('#openedBuild').replaceWith(data);
            $('#openedBuild_chosen').remove();
            $('#openedBuild').next('.picker').remove();
            $("#openedBuild").chosen();
            notice();
        })
    }
    if(page == 'edit')
    {
        if(buildBox == 'openedBuildBox')
        {
            link = createLink('build', 'ajaxGetExecutionBuilds', 'executionID=' + executionID + '&productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + '&branch=' + branch + '&index=0&needCreate=true&type=all');
            $('#openedBuildBox').load(link, function(){$(this).find('select').chosen()});
        }
        if(buildBox == 'resolvedBuildBox')
        {
            link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=resolvedBuild&build=' + oldResolvedBuild + '&branch=' + branch + '&index=0&type=all');
            $('#resolvedBuildBox').load(link, function(){$(this).find('select').chosen()});
        }
    }
}

/**
  * Load all builds of the product.
  *
  * @param  int    $productID
  * @param  string $buildBox
  * @access public
  * @return void
  */
function loadAllProductBuilds(productID, buildBox)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    if(page == 'create')
    {
        link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + '&branch=' + branch + '&index=0&type=all');
        $.get(link, function(data)
        {
            if(!data) data = '<select id="openedBuild" name="openedBuild" class="form-control" multiple=multiple></select>';
            $('#openedBuild').replaceWith(data);
            $('#openedBuild_chosen').remove();
            $('#openedBuild').next('.picker').remove();
            $("#openedBuild").chosen();
            notice();
        })
    }
    if(page == 'edit')
    {
        if(buildBox == 'openedBuildBox')
        {
            link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + '&branch=' + branch + '&index=0&type=all');
            $('#openedBuildBox').load(link, function(){$(this).find('select').chosen()});
        }
        if(buildBox == 'resolvedBuildBox')
        {
            link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=resolvedBuild&build=' + oldResolvedBuild + '&branch' + branch + '&index=0&type=all');
            $('#resolvedBuildBox').load(link, function(){$(this).find('select').chosen()});
        }
    }
}

/**
 * Load product's modules.
 *
 * @param  int    $productID
 * @access public
 * @return void
 */
function loadProductModules(productID)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    link = createLink('tree', 'ajaxGetOptionMenu', 'productID=' + productID + '&viewtype=bug&branch=' + branch + '&rootModuleID=0&returnType=html&fieldID=&needManage=true');
    $('#moduleIdBox').load(link, function()
    {
        $(this).find('select').chosen()
        // if(typeof(bugModule) == 'string') $('#moduleIdBox').prepend("<span class='input-group-addon' style='border-left-width: 1px;'>" + bugModule + "</span>");
    });
}

/**
 * Load product stories
 *
 * @param  int    $productID
 * @access public
 * @return void
 */
function loadProductStories(productID)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    if(typeof(oldStoryID) == 'undefined') oldStoryID = 0;
    link = createLink('story', 'ajaxGetProductStories', 'productID=' + productID + '&branch=' + branch + '&moduleId=0&storyID=' + oldStoryID);
    $('#storyIdBox').load(link, function(){$('#story').chosen();});
}

/**
 * Load projects of product.
 *
 * @param  int    $productID
 * @access public
 * @return void
 */
function loadProductProjects(productID)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    var applicationID = $('#applicationID').val();
    var browseType    = 'bugCommon';

    // 判断一下如果是项目-测试菜单下创建对象，则不重新加载项目。
    var applicationName = $('#applicationName').val();
    if(typeof(applicationName) == 'undefined')
    {
        var link = createLink('rebirth', 'ajaxProjectByProduct', 'applicationID=' + applicationID + '&productID=' + productID + '&browseType=' + browseType + '&projectID=0');
        $('#projectBox').load(link, function()
        {
            $(this).find('select').chosen();
            var projectID = $('#project').find("option:selected").val();

            loadProductExecutions(productID, projectID);
        });
    }
}

/**
 * Load executions of product.
 *
 * @param  int    $productID
 * @param  int    $projectID
 * @access public
 * @return void
 */
function loadProductExecutions(productID, projectID = 0)
{
    if(typeof(bugExecutionID) !== 'undefined') var executionID = bugExecutionID;
    var link = createLink('project', 'ajaxGetExecutionSelect', 'projectID=' + projectID + '&executionID=' + executionID);
    $.post(link, function(data)
    {
        $('#execution').replaceWith(data);
        $('#execution_chosen').remove();
        $('#execution').chosen();
    })
    loadExecutionTasks(0);
    loadAssignedTo(projectID);
    var applicationID = $('#applicationID').val();
    var link          = createLink('testcase', 'ajaxProductProjectCases', 'applicationID=' + applicationID + '&productID=' + productID + '&projectID=' + projectID + '&caseID=' + caseID);
    $.post(link, function(data)
    {
        if(!data) data = '<select id="case" name="case" class="form-control"></select>';
        $('#case').replaceWith(data);
        $('#case_chosen').remove();
        $("#case").chosen();
    })
}

/**
 * Load product plans.
 *
 * @param  productID $productID
 * @access public
 * @return void
 */
function loadProductplans(productID)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    link = createLink('productplan', 'ajaxGetProductplans', 'productID=' + productID + '&branch=' + branch);
    $('#planIdBox').load(link, function(){$(this).find('select').chosen()});
}

/**
 * Load product plans.
 *
 * @param  productID $productID
 * @access public
 * @return void
 */
function loadProductLinkPlans(productID,buildId)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch       = 0;
    if(typeof(productID) == 'undefined') productID = $('#product').val();
    if(typeof(buildId) == 'undefined') buildId     = $('#openedBuild').val();
    if(typeof(number) == 'undefined') number       = $('#planIdBox').val();
    
    if(typeof page != 'undefined' && page == 'edit') number = '';

    link = createLink('productplan', 'ajaxGetPlansByProduct', 'productID=' + productID + '&branch=' + branch+'&number='+number+'&build='+buildId);
    $.get(link, function(data)
    {
        if(!data) data = '<select id="planIdBox" name="linkPlan[]" class="form-control chosen" multiple=multiple></select>';
        $('#planIdBox').replaceWith(data);
        $('#planIdBox_chosen').remove();
        $('#planIdBox').next('.picker').remove();
        if(buildId){
            $('#planIdBox').prop('disabled', true);
        }
        if(productID == 'na'){
            $('#planIdBox').prop('disabled', true);
        }
        $("#planIdBox").chosen();
       
        notice();
    })
    
}
function loadProductOnlist(option, branch)
{
    
    var parentTr = $(option).closest('tr');
    var productID = $(parentTr).find('.control-product').val()
    loadProductBuildsOnlist(productID, option, branch);
}
/**
 * Load product plans.
 *
 * @param  productID $productID
 * @access public
 * @return void
 */
function loadPlansOnList(option, branch)
{
    var parentTr  = $(option).closest('tr');
    var productID = $(parentTr).find('.control-product').val()
    var buildId   = $(parentTr).find('.control-build').val()
    var number    = $(parentTr).index() + 2;
    link = createLink('productplan', 'ajaxGetPlansByProductOnList', 'productID=' + productID + '&branch=' + branch + '&number=' + number + '&build=' + buildId);
    $.get(link, function(data)
    {
        if(!data) data = '<select id="planIdBox" name="planIdBox[]" class="form-control chosen" multiple=multiple></select>';
        $(parentTr).find('.control-plan').replaceWith(data);
        $(parentTr).find('.control-plan').next('.chosen-container').remove();
        $(parentTr).find('.control-plan').next('.picker').remove();
        if(productID == 'na')
        {
            $(parentTr).find('.control-plan').prop('disabled', true);
        }
        if(buildId)
        {
            $(parentTr).find('.control-plan').prop('disabled', true);
        }
        $(parentTr).find('.control-plan').chosen();
    })
    
}

/**
 * Load product builds.
 *
 * @param  productID $productID
 * @access public
 * @return void
 */
function loadProductBuilds(productID)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    if(typeof(oldOpenedBuild) == 'undefined') oldOpenedBuild = 0;
    if(typeof page != 'undefined' && page == 'edit') oldOpenedBuild = oldResolvedBuild = '';
    link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + '&branch=' + branch);
    if(page == 'create')
    {
        $.get(link, function(data)
        {
            if(!data) data = '<select id="openedBuild" name="openedBuild" class="form-control" multiple=multiple  onchange="findPlan(this.value)"></select>';
            $('#openedBuild').replaceWith(data);
            $('#openedBuild_chosen').remove();
            $('#openedBuild').next('.picker').remove();
            $("#openedBuild").chosen();
            loadProductLinkPlans(productID);
            notice();
            
        })
    }
    else
    {
        $('#openedBuildBox').load(link, function(){$(this).find('select').chosen()});
        link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=resolvedBuild&build=' + oldResolvedBuild + '&branch=' + branch);
        $('#resolvedBuildBox').load(link, function(){
            $(this).find('select').chosen()
            loadProductLinkPlans(productID);
        });
        
    }
}

/**
 * Load product builds.
 *
 * @param  productID $productID
 * @param  option $option
 * @param  branch $branch
 * @access public
 * @return void
 */
function loadProductBuildsOnlist(productID, option, branch)
{
    if(typeof(branch) == 'undefined') branch = 0;
    if(typeof(oldOpenedBuild) == 'undefined') oldOpenedBuild = 0;
    link = createLink('build', 'ajaxGetProductBuildsOnlist', 'productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + '&branch=' + branch);
    $.get(link, function(data)
    {
        var parentTr =(option).closest('tr');
        if(!data) data = '<select name="openedBuild" class="form-control control-build" multiple=multiple  onchange="loadPlansOnList(this)"></select>';
        $(parentTr).find('.control-build').replaceWith(data);
        $(parentTr).find('.control-build').next('.chosen-container').remove();
        $(parentTr).find('.control-build').next('.picker').remove();
        $(parentTr).find('.control-build').chosen();
        loadPlansOnList(option, branch)
    })
}

/**
 * Load execution related bugs and tasks.
 *
 * @param  int    $executionID
 * @access public
 * @return void
 */
function loadExecutionRelated(executionID)
{
    if(executionID)
    {
        loadExecutionTasks(executionID);
    }
}

/**
 * Load execution tasks.
 *
 * @param  executionID $executionID
 * @access public
 * @return void
 */
function loadExecutionTasks(executionID)
{
    link = createLink('task', 'ajaxGetExecutionTasks', 'executionID=' + executionID + '&taskID=' + oldTaskID);
    $.post(link, function(data)
    {
        if(!data) data = '<select id="task" name="task" class="form-control"></select>';
        $('#task').replaceWith(data);
        $('#task_chosen').remove();
        $('#task').next('.picker').remove();
        $("#task").chosen();
    })
}

/**
 * Load execution stories.
 *
 * @param  executionID $executionID
 * @access public
 * @return void
 */
function loadExecutionStories(executionID)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    if(typeof(oldStoryID) == 'undefined') oldStoryID = 0;
    link = createLink('story', 'ajaxGetExecutionStories', 'executionID=' + executionID + '&productID=' + $('#product').val() + '&branch=' + branch + '&moduleID=0&storyID=' + oldStoryID);
    $('#storyIdBox').load(link, function(){$('#story').chosen();});
}

/**
 * Load builds of a execution.
 *
 * @param  int      $executionID
 * @access public
 * @return void
 */
function loadExecutionBuilds(executionID)
{
    branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    productID = $('#product').val();
    oldOpenedBuild = $('#openedBuild').val() ? $('#openedBuild').val() : 0;
    if(page == 'create')
    {
        link = createLink('build', 'ajaxGetExecutionBuilds', 'executionID=' + executionID + '&productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + "&branch=" + branch + "&index=0&needCreate=true");
        $.get(link, function(data)
        {
            if(!data) data = '<select id="openedBuild" name="openedBuild" class="form-control" multiple=multiple></select>';
            $('#openedBuild').replaceWith(data);
            $('#openedBuild').val(oldOpenedBuild);
            $('#openedBuild_chosen').remove();
            $('#openedBuild').next('.picker').remove();
            $("#openedBuild").chosen();
        })
    }
    else
    {
        if(typeof page != 'undefined' && page == 'edit')
        {
            oldOpenedBuild = ''
        }
        link = createLink('build', 'ajaxGetExecutionBuilds', 'executionID=' + executionID + '&productID=' + productID + '&varName=openedBuild&build=' + oldOpenedBuild + '&branch=' + branch);
        $('#openedBuildBox').load(link, function(){$(this).find('select').val(oldOpenedBuild).chosen()});

        oldResolvedBuild = $('#resolvedBuild').val() ? $('#resolvedBuild').val() : 0;
        link = createLink('build', 'ajaxGetProductBuilds', 'productID=' + productID + '&varName=resolvedBuild&build=' + oldResolvedBuild + '&branch=' + branch);
        $('#resolvedBuildBox').load(link, function(){$(this).find('select').val(oldResolvedBuild).chosen()});
    }
}

/**
 * Set story field.
 *
 * @param  moduleID $moduleID
 * @param  productID $productID
 * @param  storyID $storyID
 * @access public
 * @return void
 */
function setStories(moduleID, productID, storyID)
{
    var branch = $('#branch').val();
    if(typeof(branch) == 'undefined') branch = 0;
    link = createLink('story', 'ajaxGetProductStories', 'productID=' + productID + '&branch=' + branch + '&moduleID=' + moduleID + '&storyID=' + storyID);
    $.get(link, function(stories)
    {
        if(!stories) stories = '<select id="story" name="story" class="form-control"></select>';
        $('#story').replaceWith(stories);
        $('#story_chosen').remove();
        $('#story').next('.picker').remove();
        $("#story").chosen();
    });
}

var oldAssignedToTitle = $("#assignedTo").find("option:selected").text();
var oldAssignedTo      = $("#assignedTo").find("option:selected").val();

/**
 * Load team members of the execution as assignedTo list.
 *
 * @param  int     $projectID
 * @access public
 * @return void
 */
function loadAssignedTo(projectID, selectedUser)
{
    selectedUser = (typeof(selectedUser) == 'undefined') ? '' : $('#assignedTo').val();
    link = createLink('bug', 'ajaxLoadAssignedTo', 'projectID=' + projectID + '&selectedUser=' + selectedUser);
    $.get(link, function(data)
    {
        var defaultOption = '<option title="' + oldAssignedToTitle + '" value="' + oldAssignedTo + '" selected="selected">' + oldAssignedToTitle + '</option>';
        $('#assignedTo_chosen').remove();
        $('#assignedTo').next('.picker').remove();
        $('#assignedTo').replaceWith(data);
        var defaultAssignedTo = $('#assignedTo').val();
        if(defaultAssignedTo !== oldAssignedTo && selectedUser == '') $('#assignedTo').append(defaultOption);
        $('#assignedTo').chosen();
    });
}

var oldTestTaskTitle = $("#testtask").find("option:selected").text();
var oldTestTask      = $("#testtask").find("option:selected").val();

/**
 * Load test tasks.
 *
 * @param  int $productID
 * @param  int $executionID
 * @access public
 * @return void
 */
function loadTestTasks(productID, executionID)
{
    if(typeof(executionID) == 'undefined') executionID = 0;
    link = createLink('testtask', 'ajaxGetTestTasks', 'productID=' + productID + '&executionID=' + executionID);
    $.get(link, function(data)
    {
        var defaultOption = '<option title="' + oldTestTaskTitle + '" value="' + oldTestTask + '" selected="selected">' + oldTestTaskTitle + '</option>';
        $('#testtaskBox').html(data);
        $('#testtask').append(defaultOption);
        $('#testtask').chosen();
    });
}

/**
 * notice for create build.
 *
 * @access public
 * @return void
 */
function notice()
{
    $('#buildBoxActions').empty().hide();
    if($('#openedBuild').find('option').length <= 1)
    {
        var html = '';
        if($('#execution').length == 0 || $('#execution').val() == '')
        {
            var branch = $('#branch').val();
            if(typeof(branch) == 'undefined') branch = 0;
            var link = createLink('release', 'create', 'productID=' + $('#product').val() + '&branch=' + branch);
            link += config.requestType == 'GET' ? '&onlybody=yes' : '?onlybody=yes';
            html += '<a href="' + link + '" data-toggle="modal" data-type="iframe" style="padding-right:5px">' + createBuild + '</a> ';
            html += '<a href="javascript:loadProductBuilds(' + $('#product').val() + ')">' + refresh + '</a>';
        }
        else
        {
            executionID = $('#execution').val();
            link = createLink('build', 'create','executionID=' + executionID + '&productID=' + $('#product').val());
            link += config.requestType == 'GET' ? '&onlybody=yes' : '?onlybody=yes';
            html += '<a href="' + link + '" data-toggle="modal" data-type="iframe" style="padding-right:5px">' + createBuild + '</a> ';
            html += '<a href="javascript:loadExecutionBuilds(' + executionID + ')">' + refresh + '</a>';
        }
        var $bba = $('#buildBoxActions');
        if($bba.length)
        {
            $bba.html(html);
            $bba.show();
        }
        else
        {
            if($('#buildBox').closest('tr').find('td').size() > 1)
            {
                $('#buildBox').closest('td').next().attr('id', 'buildBoxActions');
                $('#buildBox').closest('td').next().html(html);
            }
            else
            {
                html = "<td id='buildBoxActions'>" + html + '</td>';
                $('#buildBox').closest('td').after(html);
            }
        }
    }
}
