<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-content fade'>
  <div class='main-header'>
    <h2><?php echo $lang->duty->batchCreate;?></h2>
    <?php if(isonlybody()) echo '<button id="closeModal" type="button" class="btn btn-link pull-right" data-dismiss="modal"><i class="icon icon-close"></i></button>';?>
  </div>
  <form class='batch-actions-form form-ajax' method='post' id='batchCreateForm'>
    <div class="table-responsive">
      <table class='table table-form'>
        <thead>
          <tr>
            <th class='w-10px'><?php echo $lang->idAB;?></th>
            <th class='w-100px text-center'><?php echo $lang->duty->user;?></th>
            <th class='w-100px text-center'><?php echo $lang->duty->type;?></th>
            <th class='w-100px text-center'><?php echo $lang->duty->occurDate;?></th>
            <th class='w-100px text-center'><?php echo $lang->duty->desc;?></th>
          </tr>
        </thead>
        <tbody>
          <?php for($i = 0; $i < 10; $i++):?>
          <tr>
            <td><?php echo $i+1;?></td>
            <td><?php echo html::select("user[$i]", $users, '', "class='form-control chosen'");?></td>
            <td style='overflow:visible'><?php echo html::select("type[$i]", $lang->duty->typeList, '', "class='form-control chosen'");?></td>
            <td><?php echo html::input("occurDate[$i]", $date, "class='form-control form-date'");?></td>
            <td><?php echo html::textarea("desc[$i]", '', "rows='1' class='form-control autosize'");?></td>
          </tr>
          <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan='5' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
