<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block" style="height: 350px;">
    <div class="main-header">
      <h2><?php echo $lang->outsideplan->copySub;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <tr style="height: 50px;">
            <th class='w-110px'></th>
            <td >

            </td>
        </tr>
          <tr class="">
            <th class='w-110px'><?php echo $lang->outsideplan->outsideplan;?></th>
            <td >
            <?php echo html::select('outsideplanID', $outsidePlanList,'', "class='form-control chosen'");?>
            </td>
          </tr>
        <tr class="hidden">
            <th class='w-110px'></th>
            <td >
                <?php echo html::input('sourcesubProjectId', $subProjectId,'', "");?>
            </td>
        </tr>
          <tr>

            <td class='form-actions text-center' colspan='2'><?php echo html::submitButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>

  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
