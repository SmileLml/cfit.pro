<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(isset($ganttType)):?>
    <?php echo html::a('javascript:updateCriticalPath()', $lang->execution->gantt->showCriticalPath, '', "class='btn btn-link' id='criticalPath'");?>
    <?php echo html::a('###', $lang->execution->gantt->fullScreen, '', "class='btn btn-link' id='fullScreenBtn'");?>
    <div class='btn btn-link'>
      <strong><?php echo $lang->execution->gantt->format . '：';?></strong>
      <?php echo html::radio('zooming', $lang->execution->gantt->zooming, 'day', "onchange='zoomTasks(this)'");?>
    </div>
    <div class='btn btn-link' id='ganttPris'>
      <strong><?php echo $lang->task->pri . " : "?></strong>
      <?php foreach($lang->execution->gantt->color as $pri => $color):?>
      <span style="background:#<?php echo $color?>"><?php echo $pri;?></span> &nbsp;
      <?php endforeach;?>
    </div>
    <?php else:?>
    <?php echo html::a($this->createLink('execution', 'gantt', "executionID=$executionID"), $lang->execution->gantt->common, '', "class='btn btn-link'");?>
    <?php endif;?>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if((!empty($this->config->CRProject) or $execution->status != 'closed') and common::hasPriv('execution', 'relation')) echo html::a($this->createLink('execution', 'relation', "executionID=$executionID"), "<i class='icon icon-list-alt muted'></i> " . $lang->execution->gantt->relation, '', "class='btn btn-link'");?>
    <?php if($this->app->rawMethod != 'relation' and $this->app->rawMethod != 'maintainrelation'):?>
    <div class="btn-group">
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
        <li><a href='javascript:exportGantt()'><?php echo $lang->execution->gantt->exportImg;?></a></li>
        <li><a href='javascript:exportGantt("pdf")'><?php echo $lang->execution->gantt->exportPDF;?></a></li>
      </ul>
    </div>
    <?php endif;?>
    <?php
    $checkObject = new stdclass();
    $checkObject->execution = $executionID;
    $misc = common::hasPriv('task', 'create', $checkObject) ? "class='btn btn-primary iframe' data-width='1200px'" : "class='btn btn-primary disabled'";
    $link = common::hasPriv('task', 'create', $checkObject) ?  $this->createLink('task', 'create', "execution=$executionID" . (isset($moduleID) ? "&storyID=&moduleID=$moduleID" : ''), '', true) : '#';
    echo html::a($link, "<i class='icon icon-plus'></i>" . $lang->task->create, '', $misc);
    ?>
  </div>
</div>
