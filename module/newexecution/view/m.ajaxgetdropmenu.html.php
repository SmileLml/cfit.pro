<div class='heading divider'>
  <span class='title'>
    <input type='text' class='input' id='search' value='' placeholder='<?php echo $this->app->loadLang('search')->search->common;?>'/>
  </span>
  <nav class='nav'>
    <a data-dismiss='display'><i class='icon-remove muted'></i></a>
  </nav>
</div>
<?php js::set('executionID', $executionID);?>
<?php js::set('module', $module);?>
<?php js::set('method', $method);?>
<?php js::set('extra', $extra);?>
<div id='searchResult' class='content'>
  <div id='defaultMenu' class='search-list'>
    <div id='activedItems'>
    <?php
    $iCharges = 0;
    $others   = 0;
    $dones    = 0;
    foreach($executions as $execution)
    {
        if($execution->status != 'done' and $execution->PM == $this->app->user->account) $iCharges++;
        if($execution->status != 'done' and !($execution->PM == $this->app->user->account)) $others++;
        if($execution->status == 'done') $dones++;
    }

    if($iCharges and $others) echo "<li class='heading'>{$lang->execution->mine}</li>";
    echo "<div class='list'>";
    foreach($executions as $id => $execution)
    {
        if($execution->status != 'done' and $execution->PM == $this->app->user->account)
        {
            echo html::a(sprintf($link, $execution->id), "<i class='icon-folder-close-alt'></i>&nbsp;{$execution->name}", '', "class='mine text-important item' data-id='{$execution->id}' data-tag=':{$execution->status} @{$execution->PM} @mine' data-key='{$execution->code}'");
            unset($executions[$id]);
        }
    }
    echo '</div>';

    if($iCharges and $others) echo "<div class='heading'>{$lang->execution->other}</div>";
    $class = ($iCharges and $others) ? "other" : '';
    echo "<div class='list'>";
    foreach($executions as $id => $execution)
    {
        if($execution->status != 'done' and !($execution->PM == $this->app->user->account))
        {
            echo html::a(sprintf($link, $execution->id), "<i class='icon-folder-close-alt'></i>&nbsp;{$execution->name}", '', "class='$class item' data-id='{$execution->id}' data-tag=':{$execution->status} @{$execution->PM}' data-key='{$execution->code}'");
            unset($executions[$id]);
        }
    }
    echo '</div>';
    ?>
    </div>

    <?php if($dones):?>
    <div class='box'>
      <a class='btn fluid' id='closedCollapse'><?php echo $lang->execution->doneExecutions;?></a>
      <div class="collapse hidden" id="doneExecutions">
        <div class="list">
          <?php
          foreach($executions as $execution)
          {
              if($execution->status == 'done') echo html::a(sprintf($link, $execution->id), "<i class='icon-folder-close-alt'></i> {$execution->name}", '', "data-id='{$execution->id}' data-tag=':{$execution->status} @{$execution->PM}' data-key='{$execution->code}' class='done item'");
          }
          ?>
        </div>
      </div>
    </div>
    <script>
    $(function()
    {
        $('.modal #closedCollapse').click(function()
        {
            if($('.modal #doneExecutions').hasClass('hidden'))
            {
                $('.modal #doneExecutions').removeClass('hidden');
                $('.modal #doneExecutions').addClass('in');
            }
            else
            {
                $('.modal #doneExecutions').removeClass('in');
                $('.modal #doneExecutions').addClass('hidden');
            }
        })
        if($(window).height() < $('.modal #activedItems').height() + $('.modal .heading.divider').height())
        {
            $('.modal #searchResult').addClass('with-closed');
            $('.modal #closedCollapse').closest('.box').addClass('affix').addClass('dock-bottom');
        }
    })
    </script>
    <?php endif;?>
  </div>
</div>
