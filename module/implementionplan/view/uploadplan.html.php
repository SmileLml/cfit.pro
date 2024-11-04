<?php include '../../common/view/header.lite.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<main>
  <div class="container">
    <div id="mainContent" class='main-content' style="height:300px;overflow:auto">
      <div class='main-header'>
        <h2><?php echo $lang->implementionplan->uploadPlan;?></h2>
      </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' >
        <table class='table table-form '>
          <tr>
              <th class='w-10px'><?php echo $lang->implementionplan->level;?></th>
              <td class='w-80px'><?php echo html::select('level',$lang->implementionplan->levelList,'',"class='form-control chosen'")?></td>
          </tr>
          <tr>
            <th class='w-10px'><?php echo $lang->implementionplan->file;?></th>
            <td class='w-80px required'><input type='file' name='files' id ='files' class='form-control' /></td>
          </tr>
          <tr>
              <td class='form-actions text-center' colspan='2'><?php echo html::submitButton() ;?></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</main>
<?php include '../../common/view/footer.lite.html.php';?>
