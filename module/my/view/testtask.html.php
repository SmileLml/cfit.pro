<?php
/**
 * The testtask view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('mode', $mode);?>
<?php js::set('total', $pager->recTotal);?>
<?php js::set('rawMethod', $app->rawMethod);?>
<?php js::set('confirmDelete', $lang->testtask->confirmDelete)?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $recTotalLabel = " <span class='label label-light label-badge'>{$pager->recTotal}</span>";
    if($app->rawMethod == 'contribute') echo html::a(inlink($app->rawMethod, "mode=$mode&type=done"), "<span class='text'>{$lang->testtask->done}</span>" . ($type == 'done' ? $recTotalLabel : ''), '', "class='btn btn-link" . ($type == 'done' ? ' btn-active-text' : '') . "'");
    ?>
  </div>
</div>
<div id="mainContent" class='main-table' data-ride='table'>
  <?php if(empty($tasks)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->testtask->noTesttask;?></span></p>
  </div>
  <?php else:?>
  <table class="table has-sort-head table-fixed" id='taskList'>
    <?php $vars = "mode=$mode&type=$type&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
    <thead>
      <tr>
        <th class='w-id'>   <?php common::printOrderLink('id',        $orderBy, $vars, $lang->idAB);?></th>
        <th class='w-160px'><?php common::printOrderLink('oddNumber', $orderBy, $vars, $lang->testtask->oddNumber);?></th>
        <th>                <?php common::printOrderLink('name',      $orderBy, $vars, $lang->testtask->name);?></th>
        <th class='w-140px'><?php common::printOrderLink('applicationID',   $orderBy, $vars, $lang->testtask->applicationID);?></th>
        <th class='w-140px'><?php common::printOrderLink('project',   $orderBy, $vars, $lang->testtask->project);?></th>
        <th class='w-120px'><?php common::printOrderLink('build',     $orderBy, $vars, $lang->testtask->build);?></th>
        <th class='w-90px'> <?php common::printOrderLink('begin',     $orderBy, $vars, $lang->testtask->begin);?></th>
        <th class='w-90px'> <?php common::printOrderLink('end',       $orderBy, $vars, $lang->testtask->end);?></th>
        <th class='w-90px'> <?php echo $lang->testtask->progress;?></th>
        <th class='w-80px'> <?php common::printOrderLink('status',    $orderBy, $vars, $lang->statusAB);?></th>
        <th class='c-actions-6'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($tasks as $task):?>
      <tr>
        <td class="c-id"><?php printf('%03d', $task->id);?></td>
        <td class='nobr'><?php echo $task->oddNumber;?></td>
        <td class='text-left nobr' title='<?php echo $task->name;?>'><?php echo html::a($this->createLink('testtask', 'view', "taskID=$task->id"), $task->name);?></td>
        <?php
        $applicationName = '';
        $projectName = '';
        if($task->applicationData) $applicationName = $task->applicationData->name;
        if($task->projectData) $projectName = $task->projectData->name;

        $buildData = '';
        foreach($task->buildData as $build)
        {
            $buildData .= html::a($this->createLink('build', 'view', "buildID=$build->id"), $build->name, '', "data-group=project") . '<br>';
        }
        ?>
        <td class='nobr text-ellipsis' title='<?php echo $applicationName;?>'><?php if($applicationName) echo html::a($this->createLink('application', 'view', "id=$task->applicationID"), $applicationName);?></td>
        <td class='nobr text-ellipsis' title='<?php echo $projectName;?>'><?php echo $projectName;?></td>
        <td class='nobr text-ellipsis'><?php echo $buildData;?></td>
        <td><?php echo $task->begin?></td>
        <td><?php echo $task->end?></td>
        <td title='<?php echo $lang->testtask->progress?>'><span class="nobr text-ellipsis"><?php echo $this->loadModel('testtask')->processProgress($task->id);?></span></td>
        <td title='<?php echo $task->status?>'><span class="status-task status-<?php echo $task->status?>"><?php echo $this->processStatus('testtask', $task);?></span></td>
        <td class='c-actions'>
          <?php
          common::printIcon('testtask',   'cases',    "taskID=$task->id", $task, 'list', 'sitemap', '', '', '', "data-app='qa'");
          common::printIcon('testtask',   'view',     "taskID=$task->id", '', 'list', 'list-alt', '', 'iframe', true, "data-width='90%'");
          common::printIcon('testtask',   'linkCase', "taskID=$task->id", $task, 'list', 'link', '', '', false, "data-app='qa'");
          common::printIcon('testreport', 'browse',   "objectID=$task->product&objectType=product&extra=$task->id", $task, 'list', 'flag', '', '', false, "data-app='qa'");
          common::printIcon('testtask',   'edit',     "taskID=$task->id", $task, 'list', '', '', 'iframe', true, "data-width='90%'");
          if(common::hasPriv('testtask', 'delete', $task))
          {
              $deleteURL = $this->createLink('testtask', 'delete', "taskID=$task->id&confirm=yes");
              echo html::a("javascript:ajaxDelete(\"$deleteURL\", \"taskList\", confirmDelete)", '<i class="icon-common-delete icon-trash"></i>', '', "title='{$lang->testtask->delete}' class='btn'");
          }
          ?>
        </td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <div class="table-footer"><?php $pager->show('right', 'pagerjs');?></div>
  <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
