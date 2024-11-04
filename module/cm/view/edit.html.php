<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
  <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <div class='main-header'>
      <h2><?php echo $lang->cm->edit;?></h2>
    </div>
    <div class="table-responsive">
      <div class='input-group col-sm-3' style='margin-bottom: 10px'>
        <span class='input-group-addon'><?php echo $lang->cm->title;?></span>
        <?php echo html::input('title', $baseline->title, "class='form-control' style='width: 300px'");?>
        <span class='input-group-addon'><?php echo $lang->cm->type;?></span>
        <?php echo html::select('type', $lang->cm->typeList, $baseline->type, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->PM;?></span>
        <?php echo html::select('PM', $users, $project->PM, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->PO;?></span>
        <?php echo html::select('PO', $users, $project->PO, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->QA;?></span>
        <?php echo html::select('QA', $users, $project->QA, "class='form-control chosen'");?>
      </div>
      <div class='input-group col-sm-3' style='margin-bottom: 10px'>
        <span class='input-group-addon'><?php echo $lang->cm->cm;?></span>
        <?php echo html::select('cm', $users, $baseline->cm, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->cmDate;?></span>
        <?php echo html::input('cmDate', $baseline->cmDate, "class='form-control form-date' style='width: 150px'");?>
        <span class='input-group-addon'><?php echo $lang->cm->reviewer;?></span>
        <?php echo html::select('reviewer', $users, $baseline->reviewer, "class='form-control chosen'");?>
        <span class='input-group-addon'><?php echo $lang->cm->reviewedDate;?></span>
        <?php echo html::input('reviewedDate', $baseline->reviewedDate, "class='form-control form-date' style='width: 150px'");?>
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
          <?php foreach($baseline->items as $id => $item):?>
          <tr> 
            <td><?php echo html::input("itemname[]", $item->title, "class='form-control'");?></td>
            <td><?php echo html::input("itemcode[]", $item->code, "class='form-control'");?></td>
            <td><?php echo html::input("version[]", $item->version, "class='form-control'");?></td>
            <td><?php echo html::select("changedID[]", $changes, $item->changedID, "class='form-control chosen'");?></td>
            <td><?php echo html::input("changedDate[]", $item->changedDate, "class='form-control form-date'");?></td>
            <td><?php echo html::input("path[]", $item->path, "class='form-control'");?></td>
            <td><?php echo html::input("comment[]", $item->comment, "class='form-control'");?></td>
            <td class='c-actions'>
              <button type="button" class="btn btn-link btn-icon btn-add" onclick="addItem(this)"><i class="icon icon-plus"></i></button>
              <button type="button" class="btn btn-link btn-icon btn-delete" onclick="deleteItem(this)"><i class="icon icon-close"></i></button>
            </td>
          </tr> 
          <?php endforeach;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan='5' class='text-center form-actions'>
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
