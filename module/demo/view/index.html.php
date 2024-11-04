<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-row fade">
  <div class="main-col">
  <table class='table has-sort-head' id='applicationList' data-ride="table">
    <thead>
      <tr>
      <th class='c-id w-40px'></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $user):?>
      <tr>
        <td><?php echo $user->account;;?></td>
      </tr>
      <?php endforeach;?>
  </table>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
