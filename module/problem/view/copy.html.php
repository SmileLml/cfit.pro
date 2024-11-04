<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->problem->copytable;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->problem->abstract;?></th>
            <td><?php echo html::input('abstract', $problem->abstract, "class='form-control'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->problem->source;?></th>
            <td><?php echo html::select('source', $lang->problem->sourceList, $problem->source, "class='form-control chosen'");?></td>
          </tr>
          <!--          迭代35 需求收集3512 自建问题单去掉问题级别-->
<!--          <tr>-->
<!--            <th>--><?php //echo $lang->problem->severity;?><!--</th>-->
<!--            <td>--><?php //echo html::select('severity', $lang->problem->severityList, $problem->severity, "class='form-control chosen'");?><!--</td>-->
<!--          </tr>-->
          <tr>
            <th><?php echo $lang->problem->app;?></th>
            <td><?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'  required");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->pri;?></th>
            <td><?php echo html::select('pri', $lang->problem->priList, $problem->pri, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->occurDate;?></th>
            <td><?php echo html::input('occurDate', '', "class='form-control form-date' ");?></td>
          </tr>
          <!-- <tr>
                      <th><?php /*echo $lang->problem->consumed;*/?></th>
                      <td><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>
          </tr>-->
          <tr>
            <th><?php echo $lang->problem->PO;?></th>
            <td><?php echo html::select('dealUser', ['' => ''] + $executives, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->desc;?></th>
            <td colspan='2'><?php echo html::textarea('desc', $problem->desc, "class='form-control'");?></td>
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
