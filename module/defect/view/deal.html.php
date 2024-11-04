<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->defect->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->defect->linkProduct;?></th>
            <td colspan='2'><?php echo html::input('linkProduct',$defect->linkProduct, " class='form-control nextfix' placeholder='请输入产品及版本信息' required");?></td>
          </tr>
<!--          <tr class="dev">-->
<!--            <th class='w-140px'>--><?php //echo $lang->defect->ifTest;?><!--</th>-->
<!--            <td>--><?php //echo html::select('ifTest', $lang->defect->ifList, $defect->ifTest,"class='form-control chosen' disabled");?><!--</td>-->
<!--              <td id="nextUser" class="dev">-->
<!--                  <div class='input-group'>-->
<!--                      <span class='input-group-addon'>--><?php //echo $lang->defect->dealSuggest; ?><!--</span>-->
<!--                      --><?php // echo html::select('dealSuggest', $lang->defect->dealSuggestList, $defect->dealSuggest, "class='form-control chosen' required");?>
<!--                  </div>-->
<!--              </td>-->
<!--          </tr>-->
<!--          <tr class="dev">-->
<!--              <th class='w-140px'>--><?php //echo $lang->defect->ifTest;?><!--</th>-->
<!--              <td colspan="2">--><?php //echo html::select('ifTest', $lang->defect->ifList, $defect->ifTest,"class='form-control chosen' disabled");?><!--</td>-->
<!--          </tr>-->
          <tr class="dev">
              <th class='w-140px'><?php echo $lang->defect->dealSuggest;?></th>
              <td colspan="2"><?php  echo html::select('dealSuggest', $lang->defect->dealSuggestList, $defect->dealSuggest, "class='form-control chosen' required");?></td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->defect->dealComment;?></th>
            <td colspan="2"><?php echo html::textarea('dealComment', $defect->dealComment, "class='form-control nextfix'required rows='4'");?></td>
          </tr>
          <tr class="dev">
            <th class='w-140px'><?php echo $lang->defect->progress;?></th>
            <td colspan="2"><?php echo html::textarea('progress', $defect->progress, "class='form-control'required rows='4'");?></td>
          </tr>
          <tr class="dev">
              <th><?php echo $lang->defect->submitChangeDate;?></th>
              <td id="submitChangeDate"><?php echo html::input('submitChangeDate', $defect->submitChangeDate, "class='form-control form-date changeDate nextfix'");?></td>
              <td id="changeDate">
                  <div class='input-group'>
                      <span class='input-group-addon' class="nextfix"><?php echo $lang->defect->changeDate; ?></span>
                      <?php echo html::input('changeDate', $defect->changeDate, "class='form-control form-date changeDate nextfix'");?>
                  </div>
              </td>
          </tr>
          <tr>
            <th><?php echo $lang->defect->EditorImpactscope;?></th>
            <td colspan='2'><?php echo html::textarea('EditorImpactscope', $defect->EditorImpactscope, "class='form-control nextfix' required rows='4'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->defect->ifHisIssue;?></th>
            <td colspan='2'><?php echo html::select('ifHisIssue', $lang->defect->ifList, $defect->ifHisIssue, "class='form-control chosen nextfix'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->defect->cc;?></th>
            <td colspan='2'><?php echo html::select('cc[]', $users, $defect->cc, "class='form-control chosen nextfix 'multiple");?></td>
          </tr>
<!--          <tr>-->
<!--              <th>--><?php //echo $lang->defect->consumed;?><!--</th>-->
<!--              <td>--><?php //echo html::input('consumed', '', "class='form-control' required");?><!--</td>-->
<!--          </tr>-->
          <tr>
            <th><?php echo $lang->defect->comment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'>
                <?php echo html::commonButton($lang->defect->submit, '', 'btn btn-wide btn-primary submitBtn') ;?>
                <?php echo html::commonButton($lang->save, '', 'btn btn-wide saveBtn');?>
                <?php echo html::backButton();?>
                <input name="isSave" id="isSave" class="hidden"/>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
    $(function() {
        if(status == 'nextfix') {
            $('.nextfix').attr('disabled', true)
            $('#ifHisIssue').prop("disabled",true);
            $('#ifHisIssue').trigger("chosen:updated");
            $('#cc').prop("disabled",true);
            $('#cc').trigger("chosen:updated");
            $('#dealSuggest').prop("disabled",true);
            $('#dealSuggest').trigger("chosen:updated");
            $('.saveBtn').addClass('hidden');
        }
    });
    $('#dealSuggest').change(function () {
        if($(this).val() == 'nextFix') {
            $('#changeDate').addClass('required')
            $('#submitChangeDate').addClass('required')
        }else {
            $('#changeDate').removeClass('required')
            $('#submitChangeDate').removeClass('required')
        }
    })

    $('.saveBtn').click(function () {
        $('#isSave').val('1')
        $('#dataform').submit()
    })
    $('.submitBtn').click(function () {
        var cfm = confirm('是否将当前遗留缺陷向清算总中心同步，提交后该数据将无法进行修改，是否确定?');
        if(cfm == true) {
            $('#dataform').submit()
        }
    })

</script>
<?php js::set('status', $defect->status) ?>
<?php include '../../common/view/footer.html.php';?>
