<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
  .txt{color:#F56C6C;font-size: 12px;padding: 6px !important;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->infoqz->run;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th><?php echo $lang->infoqz->actualBegin;?></th>
            <td class="required"><?php echo html::input('actualBegin', $info->actualBegin, "class='form-control form-date'");?></td>
            <th><?php echo $lang->infoqz->actualEnd;?></th>
            <td class="required"><?php echo html::input('actualEnd', $info->actualEnd, "class='form-control form-date'");?></td>
          
          </tr>
          <tr>
           </tr>
          <tr>
            <th><?php echo $lang->infoqz->supply;?></th>
            <td colspan="3" class="required"><?php echo html::select('supply[]', $users, $info->supply, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->infoqz->fetchResult;?></th>
            <td class="required"><?php echo html::select('fetchResult', $lang->infoqz->fetchResultList, '', "class='form-control chosen'");?></td>
            <td class="txt hidden" colspan='2'><?php echo $lang->infoqz->fetchfailtxt;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->infoqz->consumed;?></th>
            <td colspan='3' class="required"><?php echo html::input('consumed', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->infoqz->comment;?></th>
            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
  $('#fetchResult').change(function(){
    $(this).val() == '1'?$('.txt').addClass('hidden'):$('.txt').removeClass('hidden');
  })
  $(function() {
        $('#submit').click(function() {
            if($('#fetchResult').val() == '2'){
                var msg = "数据获取结果为：获取失败\n保存后如需获取数据，需要重新进行审批，是否继续保存？";
                if(confirm(msg) == true){
                    return true;
                }else{
                    return false;
                }
            }
        });
    });
</script>
