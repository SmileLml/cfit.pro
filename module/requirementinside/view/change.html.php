<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirementinside->change;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-110px'><?php echo $lang->requirementinside->name;?></th>
            <td colspan="2"><?php echo html::input('name', $requirement->name, "class='form-control'");?></td>
            <td></td>
            <td></td>
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
            <td colspan='2'> <?php echo html::select('project', $projects, $requirement->project, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->line;?></th>
            <td colspan='2'> <?php echo html::select('line[]', $lines, $requirement->line, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->app;?></th>
            <td colspan='2'> <?php echo html::select('app', $apps, $requirement->app, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->product;?></th>
            <td colspan='2'><?php echo html::select('product[]', $products, $requirement->product, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->reviewer;?></th>
            <td colspan='3'><?php echo html::select('reviewer[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->mailto;?></th>
            <td colspan='3'><?php echo html::select('mailto[]', $users, $requirement->mailto, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->desc;?></th>
            <td colspan='3'><?php echo html::textarea('desc', $requirement->desc, "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->requirementinside->change) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
