<?php echo html::hidden('feedbackID', '');?>
<div class="modal fade" id="toTask">
  <div class="modal-dialog mw-500px">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $lang->feedback->selectProjects;?></h4>
      </div>
      <div class="modal-body">
        <table class='table table-form'>
          <?php if($this->config->systemMode == 'new'):?>
          <tr>
            <th><?php echo $lang->feedback->project;?></th>
            <td><?php echo html::select('taskProjects', $projects, '', "class='form-control chosen' onchange='getExecutions(this.value);'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->feedback->execution;?></th>
            <td><?php echo html::select('executions', '', '', "class='form-control chosen'");?></td>
          </tr>
          <?php else:?>
          <tr>
            <th><?php echo $lang->execution->common;?></th>
            <td><?php echo html::select('executions', '', '', "class='form-control chosen'");?></td>
          </tr>
          <?php endif;?>
          <tr>
            <td colspan='2' class='text-center'>
              <?php echo html::commonButton($lang->feedback->nextStep, "id='taskProjectButton'", 'btn btn-primary btn-wide');?>
              <?php echo html::commonButton($lang->cancel, "data-dismiss='modal'", 'btn btn-default btn-wide');?>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="toBug">
  <div class="modal-dialog mw-500px">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $lang->feedback->selectProjects;?></h4>
      </div>
      <div class="modal-body">
        <table class='table table-form'>
          <tr>
            <th><?php echo $lang->feedback->project?></th>
            <td><?php echo html::select('bugProjects', $projects, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <td colspan='2' class='text-center'>
              <?php echo html::hidden('productID', '');?>
              <?php echo html::commonButton($lang->feedback->nextStep, "id='bugProjectButton'", 'btn btn-primary btn-wide');?>
              <?php echo html::commonButton($lang->cancel, "data-dismiss='modal'", 'btn btn-default btn-wide');?>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<?php js::set('systemMode', $this->config->systemMode);?>
<script>
$(function()
{
    $('#taskProjects').change();
})

function getFeedbackID(obj)
{
    var feedbackID = $(obj).attr("data-id");
    $('#feedbackID').val(feedbackID);
    if(systemMode == 'new')
    {
        $('#taskProjects').change();
    }
    else
    {
        getExecutions(0);
    }
}

function initToBug(obj)
{
    var feedbackID = $(obj).attr("data-id");
    var productID  = $(obj).attr("data-product");
    $('#feedbackID').val(feedbackID);
    $('#productID').val(productID);

    var link = createLink('feedback', 'ajaxGetProjects', 'productID=' + productID);
    $.post(link, function(data)
    {
        $('#bugProjects').replaceWith(data);
        $('#bugProjects_chosen').remove();
        $('#bugProjects').chosen();
    })
}

function getExecutions(projectID)
{
    var link = createLink('feedback', 'ajaxGetExecutions', 'projectID=' + projectID);

    $.post(link, function(data)
    {
        $('#executions').replaceWith(data);
        $('#executions_chosen').remove();
        $('#executions').chosen();
    })
}

$('#taskProjectButton').on('click', function()
{
    var projectID   = $('#taskProjects').val();
    var executionID = $('#executions').val();
    var feedbackID  = $('#feedbackID').val();
    var executionID = executionID ? executionID : 0;

    if(systemMode == 'new' && projectID)
    {
        location.href = createLink('task', 'create', 'executionID=' + executionID + '&storyID=0&moduleID=0&taskID=0&extra=projectID=' + projectID + ',feedbackID=' + feedbackID) + '#app=execution';
    }
    else if(systemMode == 'classic' && executionID)
    {
        location.href = createLink('task', 'create', 'executionID=' + executionID + '&storyID=0&moduleID=0&taskID=0&extra=projectID=0,feedbackID=' + feedbackID) + '#app=execution';
    }
    else
    {
        alert('<?php echo $lang->feedback->noProject;?>');
    }
});

$('#bugProjectButton').on('click', function()
{
    var projectID  = $('#bugProjects').val();
    var feedbackID = $('#feedbackID').val();
    var productID  = $('#productID').val();

    if(projectID)
    {
        location.href = createLink('bug', 'create', 'product=' + productID + '&branch=0&extras=projectID=' + projectID + ',feedbackID=' + feedbackID) + '#app=feedback';
    }
    else
    {
        alert('<?php echo $lang->feedback->noProject;?>');
    }
});
</script>
