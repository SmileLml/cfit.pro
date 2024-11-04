<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demandinside->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->demandinside->type;?></th>
            <td><?php echo html::select('type', $lang->demandinside->typeList, $demand->type, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->endDate;?></th>
            <td><?php echo html::input('endDate', $demand->endDate, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->app;?></th>
            <td><?php echo html::select('app[]', $apps, $demand->app, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->requirement;?></th>
            <td colspan='2'><?php echo html::input('requirement', $demand->requirement, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->source;?></th>
            <td colspan='2'><?php echo html::input('source', $demand->source, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->title;?></th>
            <td colspan='2'><?php echo html::input('title', $demand->title, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandinside->reason;?></th>
            <td colspan='2'><?php echo html::textarea('reason', $demand->reason, "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demandinside->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
