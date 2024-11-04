<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.read-info {padding: 5px 5px 5px 10px; background-color: rgba(0,0,0,.025); border: 1px solid #eee; word-wrap: break-word;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirementinside->feedback;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-150px'><?php echo $lang->requirementinside->name;?></th>
            <td><div class='read-info'><?php echo $requirement->name;?></div></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->deadline;?></th>
            <td><div class='read-info'><?php echo $opinion->deadline;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->background;?></th>
            <td colspan='2'><div class='read-info'><?php echo $opinion->background;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->overview;?></th>
            <td colspan='2'><div class='read-info'><?php echo $opinion->overview;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->dept;?></th>
            <td><?php echo html::select('dept', $depts, $requirement->dept, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->end;?></th>
            <td><?php echo html::input('end', $requirement->end == '0000-00-00' ? '' : $requirement->end, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->owner;?></th>
            <td><?php echo html::select('owner', $users, $requirement->owner, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->project;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('project', $projects, $requirement->project, "class='form-control chosen'");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('projectplan', 'create', '', '', 1), $lang->requirementinside->createProject, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->line;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('line[]', $lines, $requirement->line, "class='form-control chosen' multiple");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('product', 'manageLine', '', '', 1), $lang->requirementinside->createLine, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->app;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('app', $apps, $requirement->app, "class='form-control chosen'");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('application', 'create', 'programID=0', '', 1), $lang->requirementinside->createApp, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->product;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('product[]', $products, $requirement->product, "class='form-control chosen' multiple");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('product', 'create', '', '', 1), $lang->requirementinside->createProduct, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->requirementinside->feedback) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('prohibitFeedback', json_encode($config->requirement->prohibitFeedback));?>
<?php js::set('entriesCode', empty($requirement->entriesCode) ? 0 : 1);?>
<script>
if(entriesCode)
{
    var prohibitFeedback = eval('(' + prohibitFeedback + ')');
    for(var i in prohibitFeedback)
    {
        $('#' + prohibitFeedback[i]).attr('disabled', 'disabled');
    }
}
</script>
<?php include '../../common/view/footer.html.php';?>
