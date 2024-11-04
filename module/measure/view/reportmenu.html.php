<div class='panel'>
  <div class='panel-heading'>
    <div class='panel-title'><?php echo $lang->measure->listTitle;?></div>
  </div>
  <div class='panel-body'>
    <div class='list-group'>
      <?php
//      ksort($lang->reportList->$submenu->lists);
      foreach($lang->measure->leftMenu as $list)
      {
          $arr = explode(',',$list);
          $requestParams = "";
          $class  = $this->app->methodName == $arr[1] ? 'selected' : '';
          if(common::hasPriv($arr[0], $arr[1])) echo html::a($this->createLink($arr[0], $arr[1], $requestParams), '<i class="icon icon-file-text"></i> ' . $arr[2], '', "class='$class' title='$arr[2]' data-app='{$this->app->openApp}'");
      }
      ?>
    </div>
  </div>
</div>
