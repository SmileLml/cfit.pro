<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>  
      <h2><?php echo $lang->effort->edit;?></h2>
    </div>
    <form method='post' target='hiddenwin'>
      <table class='table table-form'>
        <tr>
          <th class='w-80px'><?php echo $lang->effort->date;?></th>
          <td class='w-p45'><?php echo html::input('date', $effort->date, 'class="form-date form-control"');?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->effort->consumed;?></th>
          <td><?php echo html::input('consumed', $effort->consumed, 'class="form-control" autocomplete="off"');?></td>
        </tr>
        <tr>
          <th><?php echo $lang->task->progress . '%';?></th>
          <td><?php echo html::input('progress', $effort->progress, 'class="form-control" autocomplete="off"');?></td>
        </tr>
        <tr>
          <th><?php echo $lang->effort->work;?></th>
          <td colspan='2'><?php echo html::textarea('work', $effort->work, "rows='6' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center'>
            <?php echo html::submitButton('', "onclick='return checkTaskLeft(\"{$lang->effort->noticeFinish}\")'") . html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php js::set('projectType',$projectType->secondLine)?>
<script>
   /* $(function()
    {
        /!*��ʼʱ��*!/
        $(".form-date").datetimepicker(
            'setStartDate', '<?php echo $beginAndEnd->begin ?>'
        );
        /!*����ʱ��*!/
        $(".form-date").datetimepicker(
            'setEndDate', '<?php echo $beginAndEnd->end ?>'
        );
    })*/
    $(function()
    {
        /*������Ŀ����̨����ʱ�䣬����������Ч��δ���ã������κ�����*/
        if(projectType){
            if('<?php echo $beginAndEnd->flag ?>' != '0'){
                console.log('sdf');
                /*��ʼʱ��*/
                $(".form-date").datetimepicker(
                    'setStartDate', '<?php echo $beginAndEnd->begin ?>'
                );
                /*����ʱ��*/
                $(".form-date").datetimepicker(
                    'setEndDate', '<?php echo $beginAndEnd->end ?>'
                );
            }else{
                /*����ʱ��*/
                $(".form-date").datetimepicker(
                    'setEndDate', '<?php echo date(DT_DATE1)?>'
                );
            }
        }else{
            /*��ʼʱ��*/
            $(".form-date").datetimepicker(
                'setStartDate', '<?php echo $beginAndEnd->begin ?>'
            );
            /*����ʱ��*/
            $(".form-date").datetimepicker(
                'setEndDate', '<?php echo $beginAndEnd->end ?>'
            );
        }

    })
</script>
<?php include '../../../common/view/footer.lite.html.php'?>
