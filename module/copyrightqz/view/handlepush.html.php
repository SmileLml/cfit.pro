<?php include '../../common/view/header.html.php';?>
<style>

</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
        <div class="main-header">
          <h2>
              <?php echo $lang->copyrightqz->handlepush;?>
          </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
            <tbody>
              <tr>
                <th class="w-140px"><?php echo $lang->copyrightqz->handlepushresult;?></th>
                <td><?php echo $item;?></td>
              </tr>
            </tbody>
          </table>
        </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>