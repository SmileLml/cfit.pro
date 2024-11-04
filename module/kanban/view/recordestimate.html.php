<?php
/**
 * The create view of effort module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件) 
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     effort
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/headerkanban.lite.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php //js::set('noticeSaveRecord', $this->lang->effort->noticeSaveRecord);?>
<style>
.actions .btn { border: none; }
.actions a { padding: 4px; }
</style>
<div id='mainContent' class='main-content'>
    <div class='main-header'>
        <h2><?php echo $lang->effort->create;?></h2>
    </div>
  <form method='post' target='hiddenwin'>

    <table class='table table-form table-fixed' style='margin-bottom:20px;border:1px solid #ddd;'>
      <thead>
        <tr>
          <th class='w-id'><?php echo $lang->idAB;?></th>
          <th class='w-120px'><?php echo $lang->effort->date;?></th>
          <th class='w-120px'><?php echo $lang->effort->consumed;?></th>
          <!--th class='w-120px'><?php echo $lang->task->progress . '%';?></th-->
          <th><?php echo $lang->effort->work;?></th>
        </tr>
      </thead>
      <tbody>
        <?php for($i = 1; $i <= 5; $i++):?>
        <tr class='text-top'>
          <td align='center'><?php echo $i . html::hidden("id[$i]", $i);?></td>
          <td><?php echo html::input("dates[$i]",date(DT_DATE1,strtotime("-".(5-$i)."day")), "class='form-control form-date' readonly='readonly'");?></td>
          <td><?php echo html::input("consumed[$i]", '', "class='form-control' autocomplete='off'");?></td>
          <!--td><?php echo html::input("progress[$i]", '', "class='form-control' autocomplete='off'");?></td-->
          <td>
          <?php
          echo html::hidden("objectType[$i]", 'kanban');
          echo html::hidden("objectID[$i]", $cardID);
          echo html::textarea("work[$i]", '', "class='form-control' style='height:50px'");
          ?>
          </td>
        </tr>
        <?php endfor;?>
      </tbody>
      <tfoot>
        <tr>
          <?php $colspan = $objectType == 'kanban' ? '4' : '3';?>
          <td colspan='<?php echo $colspan?>' class="text-center form-actions">
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
          </td>
        </tr>
<!--      <tr>-->
<!--          <td colspan='--><?php //echo $colspan?><!--' class="text-center form-actions">-->
<!--              <div style="color: lightslategray"><span> --><?php //echo $this->lang->task->workReportPrompt?><!--</span></div>-->
<!--          </td>-->
<!--      </tr>-->
      </tfoot>
    </table>
      <?php if($efforts):?>
          <table class='table table-form table-fixed' id='objectTable' style='margin-bottom:10px;'>
              <thead>
              <tr>
                  <th class='w-120px'><?php echo $lang->task->recorder;?></th>
                  <th class='w-120px'><?php echo $lang->effort->date;?></th>
                  <th class='thWidth'><?php echo $lang->effort->consumed;?></th>
                  <!--th class='w-120px'><?php echo $lang->task->progress;?></th-->
                  <th><?php echo $lang->effort->work;?></th>
                  <th class='w-80px'><?php //if(empty($task->team) or  $task->assignedTo == $this->app->user->account) echo $lang->actions;?></th>
              </tr>
              </thead>
              <tbody>
              <?php foreach($efforts as $effort):?>
                  <tr class='main'>
                      <td align='left'><?php echo zget($users, $effort->account);?></td>
                      <td align='left'><?php echo $effort->date?></td>
                      <td align='left' title="<?php echo $effort->consumed . ' ' . $lang->effort->workHour;?>"><?php echo $effort->consumed . ' H'?></td>
                      <!--td align='left' title=""><?php echo $effort->progress . '%';?></td-->
                      <td title='<?php echo $effort->work;?>'><?php echo $effort->work;?></td>
                      <td align='center' class='actions'>
                          <?php
                          if($isCanRecordEstimate) common::printIcon('kanban', 'editEstimate', "effortID=$effort->id", '', 'list', 'edit', '', 'showinonlybody', true);
                          if($isCanRecordEstimate) common::printIcon('kanban', 'deleteEstimate', "effortID=$effort->id", '', 'list', 'trash', 'hiddenwin', 'showinonlybody');
//
                          //                          if(empty($kanban->team) or  $card->assignedTo == $this->app->user->account) common::printIcon('task', 'editEstimate', "effortID=$effort->id", '', 'list', 'edit', '', 'showinonlybody', true);
//                          if(empty($kanban->team) or  $card->assignedTo == $this->app->user->account) common::printIcon('task', 'deleteEstimate', "effortID=$effort->id", '', 'list', 'trash', 'hiddenwin', 'showinonlybody');
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              </tbody>
          </table>
      <?php else:?>
          <div style='padding-top:45px'></div>
      <?php endif;?>
  </form>
</div>
<?php include '../../common/view/footer.lite.html.php'?>
<script>
    $(function()
    {
        $(".form-date").datetimepicker('setEndDate', '<?php echo date(DT_DATE1)?>');
    })
</script>
