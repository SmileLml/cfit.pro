<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demandcollection->closed;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th ><?php echo $lang->demandcollection->state;?></th>
            <td><?php echo html::select('state',$lang->demandcollection->statusList,$demandcollection->state, " class='form-control chosen' required");?></td><td></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->launchDate;?></th>
            <td><?php echo html::input('launchDate', $demandcollection->launchDate, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->Implementation;?></th>
            <td><?php echo html::select('Implementation', $depts, $demandcollection->Implementation, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->Actual;?></th>
            <td colspan='2'><?php echo html::select('Actual[]',array('' => '') + $plans, $demandcollection->Actual,"class='form-control chosen' multiple");?></td>

          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->assignFor;?></th>
            <td><?php echo html::select('assignFor',$users,$demandcollection->assignFor, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->copyFor;?></th>
            <td><?php echo html::select('copyFor[]',$users,$demandcollection->copyFor, "class='form-control chosen' multiple");?></td>
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
