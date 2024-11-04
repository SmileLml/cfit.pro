<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->nc->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-110px'><?php echo $lang->nc->title;?></th>
            <td><?php echo html::input('title', $nc->title, "class='form-control'");?></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->type;?></th>
            <td><?php echo html::select('type', $lang->nc->typeList, $nc->type, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->assignedTo;?></th>
            <td><?php echo html::select('assignedTo', $users, $nc->assignedTo, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->deadline;?></th>
            <td><?php echo html::input('deadline', $nc->deadline, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->nc->desc;?></th>
            <td colspan='2'><?php echo html::textarea('desc', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td colspan='4' class='form-actions text-center'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
