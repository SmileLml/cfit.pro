<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
          <!--th><?php echo $lang->cm->title;?></th>
          <td><?php echo html::input('title', '', "class='form-control'");?></td-->
  <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <div class='main-header'>
      <h2><?php echo $lang->cm->create;?></h2>
    </div>
    <div class="table-responsive">
      <div class='input-group col-sm-3' style='margin-bottom: 10px'>
        <span class='input-group-addon'><?php echo $lang->cm->title;?></span>
        <?php echo html::input('title', '', "class='form-control' style='width: 300px'");?>
        <span class='input-group-addon'><?php echo $lang->cm->type;?></span>
        <?php echo html::select('type', $lang->cm->typeList, '', "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->PM;?></span>
        <?php echo html::select('PM', $users, $project->PM, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->PO;?></span>
        <?php echo html::select('PO', $users, $project->PO, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->QA;?></span>
        <?php echo html::select('QA', $users, $project->QA, "class='form-control chosen'");?>
      </div>
      <div class='input-group col-sm-3' style='margin-bottom: 10px'>
        <span class='input-group-addon'><?php echo $lang->cm->cm;?></span>
        <?php echo html::select('cm', $users, $app->user->account, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->cmDate;?></span>
        <?php echo html::input('cmDate', helper::today(), "class='form-control form-date' style='width: 150px'");?>
        <span class='input-group-addon'><?php echo $lang->cm->reviewer;?></span>
        <?php echo html::select('reviewer', $users, '', "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->reviewedDate;?></span>
        <?php echo html::input('reviewedDate', helper::today(), "class='form-control form-date' style='width: 150px'");?>
      </div>
      <table class='table table-form'>
        <thead>
          <th><?php echo $lang->cm->itemname;?></th>
          <th class='w-200px'><?php echo $lang->cm->itemcode;?></th>
          <th class='w-120px'><?php echo $lang->cm->version;?></th>
          <th class='w-250px'><?php echo $lang->cm->changedID;?></th>
          <th class='w-120px'><?php echo $lang->cm->changedDate;?></th>
          <th class='w-120px'><?php echo $lang->cm->path;?></th>
          <th class='w-150px'><?php echo $lang->cm->comment;?></th>
          <th class='w-100px'><?php echo $lang->actions;?></th>
        </thead>
        <tbody>
          <?php for($i = 1; $i <= 15; $i ++):?>
          <tr> 
            <td><?php echo html::input("itemname[]", '', "class='form-control'");?></td>
            <td><?php echo html::input("itemcode[]", '', "class='form-control'");?></td>
            <td><?php echo html::input("version[]", '', "class='form-control'");?></td>
            <td><?php echo html::select("changedID[]", $changes, '', "class='form-control chosen'");?></td>
            <td><?php echo html::input("changedDate[]", '', "class='form-control form-date'");?></td>
            <td><?php echo html::input("path[]", '', "class='form-control'");?></td>
            <td><?php echo html::input("comment[]", '', "class='form-control'");?></td>
            <td class='c-actions'>
              <button type="button" class="btn btn-link btn-icon btn-add" onclick="addItem(this)"><i class="icon icon-plus"></i></button>
              <button type="button" class="btn btn-link btn-icon btn-delete" onclick="deleteItem(this)"><i class="icon icon-close"></i></button>
            </td>
          </tr> 
          <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan='7' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            <td>
          </tr>
        </tfoot>
      </table>
    </div>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
