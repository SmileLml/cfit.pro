<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->defect->change;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->defect->linkProduct;?></th>
            <td colspan='4'><?php echo html::input('linkProduct','', " class='form-control' placeholder='请输入产品及版本信息' required");?></td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->defect->ifTest;?></th>
            <td colspan='2' ><?php echo html::select('ifTest', $lang->defect->ifList, $defect->ifTest,"class='form-control chosen' disabled");?></td>
              <td id="nextUser" class="dev" colspan='2' >
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->defect->dealSuggest; ?></span>
                      <?php  echo html::select('dealSuggest', $lang->defect->dealSuggestList, $defect->dealSuggest, "class='form-control chosen' required");?>
                  </div>
              </td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->defect->dealComment;?></th>
            <td colspan="4"><?php echo html::textarea('dealComment', $defect->dealComment, "class='form-control'required ");?></td>
          </tr>
          <tr class="dev">
              <th><?php echo $lang->defect->submitChangeDate;?></th>
              <td id="submitChangeDate" colspan='2' ><?php echo html::input('submitChangeDate', $defect->submitChangeDate, "class='form-control form-date changeDate'");?></td>
              <td id="changeDate" colspan='2' >
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->defect->changeDate; ?></span>
                      <?php echo html::input('changeDate', $defect->changeDate, "class='form-control form-date changeDate'");?>
                  </div>
              </td>
          </tr>
          <tr>
            <th><?php echo $lang->defect->EditorImpactscope;?></th>
            <td colspan='4'><?php echo html::textarea('EditorImpactscope', $defect->EditorImpactscope, "class='form-control' required");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->defect->ifHisIssue;?></th>
            <td colspan='4'><?php echo html::select('ifHisIssue', $lang->defect->ifList, $defect->ifHisIssue, "class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->defect->resolution;?></th>
              <td colspan="2" class="required">
                  <?php echo html::select('resolution', $lang->bug->resolutionList,$defect->resolution, "class='form-control chosen'");?>
              </td>
              <td colspan="2">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->defect->resolvedBuild; ?></span>
                      <?php echo html::select('resolvedBuild', $resolvedBuilds,$defect->resolvedBuild, "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->defect->resolvedDate;?></th>
              <td colspan="4">
                  <?php echo html::input('resolvedDate', $defect->resolvedDate, "class='form-control form-datetime'");?>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->defect->dealUser; ?></th>
              <td colspan="4">
                  <?php echo html::select('dealUser', $users,$defect->dealUser, "class='form-control chosen'");?>
              </td>
          </tr>
          <tr>
            <th><?php echo $lang->defect->cc;?></th>
            <td colspan='4' ><?php echo html::select('cc', $users, '', "class='form-control chosen' required");?></td>
          </tr>
<!--          <tr>-->
<!--              <th>--><?php //echo $lang->defect->consumed;?><!--</th>-->
<!--              <td>--><?php //echo html::input('consumed', '', "class='form-control' required");?><!--</td>-->
<!--          </tr>-->
          <tr>
            <th><?php echo $lang->defect->comment;?></th>
            <td colspan='4' ><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='5'>
                <?php echo html::submitButton();?>
                <?php echo html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
    $('#dealSuggest').change(function () {
        if($(this).val() == 'nextFix') {
            $('#changeDate').addClass('required')
            $('#submitChangeDate').addClass('required')
        }else {
            $('#changeDate').removeClass('required')
            $('#submitChangeDate').removeClass('required')
        }
    })

</script>
<?php include '../../common/view/footer.html.php';?>
