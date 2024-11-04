<?php
/**
 * The import execution view of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2022 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Qiyu Xie<xieqiyu@cnezsoft.com>
 * @package     kanban
 * @version     $Id: importexecution.html.php 5090 2022-01-19 14:19:24Z xieqiyu@cnezsoft.com $
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/headerkanban.lite.html.php';?>
<?php js::set('kanbanID', $kanbanID);?>
<?php js::set('regionID', $regionID);?>
<?php js::set('groupID', $groupID);?>
<?php js::set('columnID', $columnID);?>
<?php js::set('methodName', $this->app->rawMethod);?>
<?php if(count($tasks2Imported) <= 3):?>
<style>#importExecutionForm, .table-empty-tip {margin-bottom: 120px}</style>
<?php endif;?>
<div id='mainContent' class='main-content importModal'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->kanban->importAB . $lang->kanban->importTask;?></h2>
    </div>
  </div>
  <div class='input-group space'>
    <span class='input-group-addon'><?php echo $lang->kanban->selectedTask;?></span>
    <?php echo html::select('project', $projects, $selectedProjectID, "onchange='reloadObjectList(this.value)' class='form-control chosen' data-drop_direction='down'");?>
    <span class='input-group-addon'><?php echo $lang->kanban->selectedLane;?></span>
    <?php echo html::select('lane', $lanePairs, '', "onchange='setTargetLane(this.value)' class='form-control chosen' data-drop_direction='down'");?>
  </div>
  <?php if($tasks2Imported):?>
  <form class='main-table' method='post' data-ride='table' target='hiddenwin' id='importExecutionForm'>
    <table class='table table-fixed' id='executionList'>
      <thead>
        <tr>
          <th class="c-id">
            <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
              <label></label>
            </div>
            <?php echo $lang->idAB;?>
          </th>
          <th class='c-id'><?php echo $lang->kanban->task->id;?></th>
            <th class='c-pri' title=<?php echo $lang->pri;?>><?php echo $lang->priAB;?></th>
          <th class='c-name'><?php echo $lang->kanban->task->name;?></th>
          <th class='c-user'><?php echo $lang->kanban->task->status;?></th>
          <th class='c-hour'><?php echo $lang->kanban->task->estimate;?></th>
            <th class='c-hour'><?php echo $lang->kanban->task->consumed;?></th>
            <th class='c-hour'><?php echo $lang->kanban->task->left;?></th>
            <th class='c-name'><?php echo $lang->kanban->task->progress;?></th>
            <th class='c-date'><?php echo $lang->kanban->task->deadline;?></th>
            <th class='c-date'><?php echo $lang->kanban->task->estStarted;?></th>
            <th class='c-date'><?php echo $lang->kanban->task->realStarted;?></th>
            <th class='c-user'><?php echo $lang->kanban->task->assignedTo;?></th>
            <th class='c-user'><?php echo $lang->kanban->task->finishedBy;?></th>
            <th class='c-date'><?php echo $lang->kanban->task->finishedDate;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($tasks2Imported as $task):?>
        <tr>
          <td class='c-id'>
            <div class="checkbox-primary">
              <input type='checkbox' name='executions[]' value='<?php echo $task->id;?>'/>
              <label></label>
            </div>
            <?php printf('%03d', $task->id);?>
          </td>
            <td><span class='label-pri <?php echo 'label-pri-' . $task->pri?>' title='<?php echo zget($lang->kanbancard->priList, $card->pri, $card->pri);?>'><?php echo zget($lang->kanbancard->priList, $card->pri, $card->pri);?></span></td>
          <?php if(common::hasPriv('task', 'view')):?>
          <td title='<?php echo $task->name;?>'>
            <a href='javascript:void(0);' onclick="locateView('task', <?php echo $task->id;?>)"><?php echo $task->name;?></a>
          </td>
          <?php else:?>
          <td title='<?php echo $task->name;?>'><?php echo $task->name;?></td>
          <?php endif;?>
          <td title='<?php echo zget($lang->execution->statusList, $execution->status);?>'><?php echo zget($lang->execution->statusList, $execution->status);?></td>
          <td title='<?php echo zget($users, $execution->PM);?>'><?php echo zget($users, $execution->PM);?></td>
          <td title='<?php echo $execution->end;?>'><?php echo $execution->end;?></td>
          <td title='<?php echo $execution->hours->totalEstimate;?>'><?php echo $execution->hours->totalEstimate;?></td>
          <td title='<?php echo $execution->hours->totalConsumed;?>'><?php echo $execution->hours->totalConsumed;?></td>
          <td title='<?php echo $execution->hours->totalLeft;?>'><?php echo $execution->hours->totalLeft;?></td>
        </tr>
        <?php endforeach;?>
        <tr><?php echo html::hidden('targetLane', key($lanePairs));?></tr>
      </tbody>
    </table>
    <div class='table-footer'>
      <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
      <div class="table-actions btn-toolbar show-always"><?php echo html::submitButton($lang->kanban->importAB, '', 'btn btn-default');?></div>
      <?php $pager->show('right', 'pagerjs');?>
    </div>
  </form>
  <?php else:?>
  <div class='table-empty-tip'><?php echo $lang->noData;?></div>
  <?php endif;?>
</div>
<?php if($config->systemMode == 'light'):?>
<style>.input-group {width: 45% !important}</style>
<?php else:?>
<style>#project_chosen {width: 45% !important}</style>
<?php endif;?>
<?php include '../../common/view/footer.lite.html.php';?>
