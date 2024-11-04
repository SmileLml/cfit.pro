<ul class='tree'>
  <?php foreach($lang->qareport->report->charts as $reportTypeIndex => $reportName):?>
  <li style="padding: 2px 0 2px 6px;" class="<?php if($reportTypeIndex == $reportType) echo 'active';?>">
  <?php echo html::a($this->createLink('qareport', 'browse', array('reportType' => $reportTypeIndex, 'chartType' => $chartType, 'switch' => 'yes')), $reportName);?>
  </li>
  <?php endforeach;?>
  <?php
  $this->app->loadLang('report');
  foreach($this->config->qareport->charts as $chartIndex => $params)
  {
      if(common::hasPriv('qareport', $chartIndex))
      {
        $chartName = $this->lang->report->$chartIndex;
        $className = $chartIndex == $reportType ? 'class="active"' : '';
        echo '<li style="padding: 2px 0 2px 6px;" . ' . $className . '>' . html::a($this->createLink('qareport', $chartIndex, $params), $chartName) . '</li>';
      }
  }
  ?>
  <?php
  ksort($lang->qareportList->test->lists);
  foreach($lang->qareportList->test->lists as $list)
  {
      $list .= '|';
      list($label, $module, $method, $requestParams) = explode('|', $list);
      $className = $label == $title ? 'class="active"' : '';
      if(common::hasPriv($module, $method)) echo '<li style="padding: 2px 0 2px 6px;" . ' . $className . '>' . html::a($this->createLink('qareport', $method, $requestParams), $label) . '</li>';
  }
?>
</ul>
