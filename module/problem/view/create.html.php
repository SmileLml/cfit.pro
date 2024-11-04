<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->problem->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-100px'><?php echo $lang->problem->abstract;?></th>
            <td class='w-400px'><?php echo html::input('abstract', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->source;?></th>
            <td><?php echo html::select('source', $lang->problem->sourceList, '', "class='form-control chosen'");?></td>
          </tr>
<!--          迭代35 需求收集3512 自建问题单去掉问题级别-->
<!--          <tr>-->
<!--            <th>--><?php //echo $lang->problem->severity;?><!--</th>-->
<!--            <td>--><?php //echo html::select('severity', $lang->problem->severityList, '', "class='form-control chosen'");?><!--</td>-->
<!--          </tr>-->
          <tr>
            <th><?php echo $lang->problem->app;?></th>
            <td id="appbox"><?php echo html::select('app[]', $apps, '', "class='form-control chosen'  required '"); //onchange='setAppInfo() ?></td>
          </tr>

          <tr class="hidden">
              <th><?php echo $lang->problem->isPayment;?></th>
              <td><?php
                  foreach ($lang->application->isPaymentList as $k => $v)
                  {
                      if(empty($k)) continue;
                      echo '<span id="isPayment_'.$k.'" class="isPayment_box hidden">'.$v.',</span>';
                  }
                  ?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->pri;?></th>
            <td><?php echo html::select('pri', $lang->problem->priList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->occurDate;?></th>
            <td><?php echo html::input('occurDate', '', "class='form-control form-date' ");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->nextExecutive;?></th>
            <td><?php echo html::select('dealUser', ['' => ''] + $executives, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->desc;?></th>
            <td colspan='2'><?php echo html::textarea('desc', '', "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->files;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<div class="hidden">
<?php
 foreach ($appAll as $app) {
     echo "<span id='sys_".$app->id."'>".$app->isPayment."</span>";
 }
?>
</div>
<script>
    // 选择系统后显示对应的类型
    function setAppInfo(){
        $('.isPayment_box').addClass('hidden');
        appBox = $('#app').val();
        for (let i = 0; i< appBox.length; i++)
        {
            console.log(appBox[i]);
            let paymentId = $('#sys_'+appBox[i]).text();
            console.log('isPayment_'+paymentId);
            $('#isPayment_'+paymentId).removeClass('hidden');
        }
    }

    function createPlan(obj)
    {
        var flag = "<?php echo $clickable = commonModel::hasPriv('productplan', 'create');?>";
        if(!flag){
            js:alert('您没有菜单『产品管理』新建产品版本的权限，请联系质量部阮涛添加权限');
            return false;
        }
        var productID = $(obj).attr('data-id');
        console.log(productID);
        if(productID === "0"){
            js:alert('请选择所属产品后,再新增产品版本!');
            return false;
        }
        var url = 'productplan-create-'+productID+'.html';
        window.open(url, "_blank");
        return true;
    }
</script>
<?php include '../../common/view/footer.html.php';?>
