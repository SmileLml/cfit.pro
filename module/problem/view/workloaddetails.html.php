<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->problem->workloadDetails;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <thead>
          <tr style='width:50%;'>
            <th><?php echo $lang->problem->relevantDept;?></th>
            <th><?php echo $lang->problem->consumed;?></th>
            <th></th>
        </thead>
        <tbody>
          <?php foreach($details as $workload):?>
          <tr>
            <td><?php echo zget($users, $workload->account, '');?></td>
            <td><?php echo $workload->workload;?></td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
