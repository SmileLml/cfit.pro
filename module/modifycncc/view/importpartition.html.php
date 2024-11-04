<?php include '../../common/view/header.lite.html.php';?>
<main>
  <div class="container">
    <div id="mainContent" class='main-content'>
      <div class='main-header'>
        <h2>导入分区</h2>
      </div>
      <form enctype='multipart/form-data' method='post' target='hiddenwin' style='padding: 20px 0 15px'>
        <table class='table table-form w-p100'>
          <tr>
              <th class='w-100px'>分区名</th>
              <td>
                  <?php echo html::input('name','partition_name',"class='form-control' placeholder='分区名'");?>
              </td>
          </tr>
          <tr>
              <th>系统英文名</th>
              <td>
                  <?php echo html::input('application','app_en_name',"class='form-control' placeholder='系统英文名'");?>
              </td>
          </tr>
          <tr>
              <th>系统中文名</th>
              <td>
                  <?php echo html::input('applicationName','app_cn_name',"class='form-control' placeholder='系统中文名'");?>
              </td>
          </tr>
          <tr>
              <th>分区ip地址</th>
              <td>
                  <?php echo html::input('ip','manage_ip',"class='form-control' placeholder='分区ip地址'");?>
              </td>
          </tr>
          <tr>
              <th>分区来源</th>
              <td>
                  <?php echo html::input('dataOrigin','data_source',"class='form-control' placeholder='分区来源'");?>
              </td>
          </tr>          
          <tr>
            <td colspan='2' style="color:red">注意:1.所有参数必填;2.所有参数不能相同;3.清总给的数据缺少系统中文名,需要自己补齐</td>
          </tr>
          <tr>
            <td colspan="2"><input type='file' name='file' class='form-control'/></td>
          </tr>
          <tr>
            <td class='w-150px'colspan="2"><?php echo html::submitButton('', '', 'btn btn-primary btn-block');?></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</main>
<?php include '../../common/view/footer.lite.html.php';?>
