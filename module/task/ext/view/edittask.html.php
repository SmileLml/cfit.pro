<?php
/**
 * The complete file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     task
 * @version     $Id: complete.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<div id='mainContent' class='main-content' style="height:400px">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $task->id;?></span>
        <span title='$task->name'><?php echo $task->name ?></span>
        <!--<span title='$task->name'><?php /*echo $this->lang->task->editTask */?></span>-->
      </div>
    </div>
    <form method='post' enctype='multipart/form-data' target='hiddenwin'>
      <table class='table table-form'>

        <tr>
          <th><?php echo $lang->task->estStarted;?></th>
          <td><div class='datepicker-wrapper'><?php echo html::input('estStarted', $task->estStarted != '0000-00-00 00:00:00' ? substr($task->estStarted, 0, 11) : '', "class='form-control form-date' readonly='readonly'");?></div></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->task->deadline;?></th>
          <td><div class='datepicker-wrapper'><?php echo html::input('deadline', $task->deadline != '0000-00-00 00:00:00' ? substr($task->deadline, 0, 11) : '', "class='form-control form-date' readonly='readonly'");?></div></td><td></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'>
            <?php echo html::submitButton($lang->task->finish);?>
            <?php echo html::linkButton($lang->goback, $this->session->taskList, 'self', '', 'btn btn-wide');?>
          </td>
        </tr>
      </table>
    </form>
    <hr class='small' />
    <div class='main'><?php include '../../../common/view/action.html.php';?></div>
  <!--  --><?php /*endif;*/?>
  </div>
</div>
<script>
    $(".form-date").datetimepicker(
        {
            weekStart: 1,
            todayBtn:  0,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            format: "yyyy-mm-dd",
            pickerPosition:'bottem-right',
            // dropdown:'bottem-right'
        });
    $(function()
    {
       /* /!*开始时间*!/
        $(".form-date").datetimepicker(
            'setStartDate', '<?php echo $beginAndEnd->begin ?>'
        );*/
        /*结束时间*/
       /* $(".form-date").datetimepicker(
            'setEndDate', '<?php echo $beginAndEnd->end ?>'
        );*/
    })


</script>
<?php include '../../../common/view/footer.html.php';?>
