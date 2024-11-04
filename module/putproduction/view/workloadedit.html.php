<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->putproduction->workloadedit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <tr>
            <th class='w-140px'><?php echo $lang->consumed->before;?></th>
            <td><?php echo html::select('before', $lang->putproduction->statusList, $consumed->before, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->consumed->after;?></th>
            <td><?php echo html::select('after', $enableChoseStatus, $consumed->after, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
          <tr style="height: 100px">
              <td></td>
          </tr>
          <tr>
              <td></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
<script>

</script>
