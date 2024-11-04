<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->modifycncc->run;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->modifycncc->actualBegin;?></th>
            <td><?php echo html::input('actualBegin', $modifycncc->actualBegin, "class='form-control form-datetime'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modifycncc->actualEnd;?></th>
            <td><?php echo html::input('actualEnd', $modifycncc->actualEnd, "class='form-control form-datetime'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modifycncc->consumed;?></th>
            <td><?php echo html::input('consumed', '', "class='form-control' required ");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modifycncc->internalSupply;?></th>
            <td colspan="2"><?php echo html::select('internalSupply[]', $users, $modifycncc->internalSupply, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->modifycncc->result?></th>
            <td colspan="2"><?php echo html::select('result', $lang->modifycncc->resultList, $modifycncc->result, "class='form-control chosen' required");?></td>
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
