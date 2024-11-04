<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2>重新推送</h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th style="width: 300px">选择同步失败的子表单：</td>
            <td style="width: 300px"><?php echo html::select('code', $list, '', "class='form-control chosen' required");?></td>
              <td> </td>
          </tr>
          <tr>
              <th>本次操作备注：</td>
              <td class="required"><?php echo html::textarea('remark', '', "class='form-control' rows='5' required");?></td>
              <td> </td>
          </tr>

          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton('推送') . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
