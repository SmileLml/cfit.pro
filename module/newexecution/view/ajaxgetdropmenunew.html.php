<?php js::set('module', $module);?>
<?php js::set('method', $method);?>
<?php js::set('extra', $extra);?>
<style>
.execution-task{padding: 2px 10px 2px 10px; margin-left: 5px; border: 1px solid #bbbec4;}
</style>
<?php
$executionNames = array();
$myProjectsHtml     = '';
$normalProjectsHtml = '';
$closedProjectsHtml = '';
foreach($executions as $execution)
{
    $executionNames[] = $execution->name;
}
$executionsPinYin = common::convert2Pinyin($executionNames);

foreach($executions as $execution)
{
    $selected      = $execution->id == $executionID ? 'selected' : '';
    $executionNameTitle = $execution->name;
    $executionName = $execution->name . '<span class="execution-task">共' . $execution->tasks . '个任务</span>';
    //$normalProjectsHtml .= html::a(sprintf($link, $execution->id), $executionName, '', "class='$selected' title='{$execution->name}' data-key='" . zget($executionsPinYin, $execution->name, '') . "' data-app='{$this->app->openApp}'");
    $normalProjectsHtml .= "<a class='text-muted' title='{$execution->name}'>{$executionName}</a>";

    if(!empty($children[$execution->id]))
    {
        foreach($children[$execution->id] as $execution)
        {
            $selected      = $execution->id == $executionID ? 'selected' : '';
            $executionName = '&nbsp;&nbsp;&nbsp;&nbsp;' . $execution->name . '<span class="execution-task">共' . $execution->tasks . '个任务</span>';
            $normalProjectsHtml .= html::a(sprintf($link, $execution->id), $executionName, '', "class='$selected' title='{$execution->name}' data-key='" . zget($executionsPinYin, $execution->name, '') . "' data-app='{$this->app->openApp}'");
        }
    }
}
?>
<div class="table-row">
  <div class="table-col col-left">
    <div class='list-group'>
      <?php
      echo $normalProjectsHtml;
      ?>
    </div>
  </div>
</div>
<script>scrollToSelected();</script>
