<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demandcollection->confirmed;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
          <th ><?php echo $lang->demandcollection->title;?></th>
            <td><?php echo html::input('title',$demandcollection->title,"class='form-control' required");?></td><td></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->submitter;?></th>
            <td><?php echo html::select('submitter',$users,$demandcollection->submitter, "class='form-control chosen' required");?></td><td></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->type;?></th>
            <td><?php echo html::select('type',$lang->demandcollection->typeList,$demandcollection->type, "class='form-control chosen' required");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->desc;?></th>
            <td colspan='2'>
            <?php echo html::textarea('desc',$demandcollection->desc,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->analysis;?></th>
            <td colspan='2'>
            <?php echo html::textarea('analysis',$demandcollection->analysis,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->state;?></th>
            <td><?php echo html::select('state',$lang->demandcollection->statusList,$demandcollection->state, " disabled='true' class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->productmanager;?></th>
            <td><?php echo html::select('productmanager',$users,$demandcollection->productmanager, "class='form-control chosen' required");?></td><td></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
