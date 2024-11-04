<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php
    if(!isset($type))   $type   = 'today';
    if(!isset($period)) $period = 'today';
    $date = isset($date) ? $date : helper::today();

    echo "<div class='input-control w-120px'>" . $userList . "</div>";

    $methodName = $this->app->getMethodName();

    $label  = "<span class='text'>{$lang->user->schedule}</span>";
    if(common::hasPriv('user', 'todo'))
    {
        $link = $this->createLink('user', 'todo', "userID={$user->id}");
        $active = $methodName == 'todo' ? ' btn-active-text' : '';
    }
    elseif(common::hasPriv('user', 'effort'))
    {
        $link = $this->createLink('user', 'effort', "userID={$user->id}");
        $active = $methodName == 'effort' ? ' btn-active-text' : '';
    }
    if($link) echo html::a($link, $label, '', "class='btn btn-link $active todoTab'");

    $label  = "<span class='text'>{$lang->user->task}</span>";
    $active = $methodName == 'task' ? ' btn-active-text' : '';
    common::printLink('user', 'task', "userID={$user->id}", $label, '', "class='btn btn-link $active taskTab'");

    if($config->URAndSR)
    {
        $label  = "<span class='text'>{$lang->URCommon}</span>";
        $active = ($methodName == 'story' and $storyType == 'requirement') ? ' btn-active-text' : '';
        common::printLink('user', 'story', "userID={$user->id}&storyType=requirement", $label, '', "class='btn btn-link $active'");
    }

    $label  = "<span class='text'>{$lang->SRCommon}</span>";
    $active = ($methodName == 'story' and $storyType == 'story')  ? ' btn-active-text' : '';
    common::printLink('user', 'story', "userID={$user->id}&storyType=story", $label, '', "class='btn btn-link $active'");

    $label  = "<span class='text'>{$lang->user->bug}</span>";
    $active = $methodName == 'bug' ? ' btn-active-text' : '';
    common::printLink('user', 'bug', "userID={$user->id}", $label, '', "class='btn btn-link $active bugTab'");

    $label  = "<span class='text'>{$lang->user->testTask}</span>";
    $active = $methodName == 'testtask' ? ' btn-active-text' : '';
    common::printLink('user', 'testtask', "userID={$user->id}", $label, '', "class='btn btn-link $active'");

    $label  = "<span class='text'>{$lang->user->testCase}</span>";
    $active = $methodName == 'testcase' ? ' btn-active-text' : '';
    common::printLink('user', 'testcase', "userID={$user->id}", $label, '', "class='btn btn-link $active'");

    if($this->config->systemMode == 'new')
    {
        $label  = "<span class='text'>{$lang->user->execution}</span>";
        $active = $methodName == 'execution' ? ' btn-active-text' : '';
        common::printLink('user', 'execution',  "userID={$user->id}", $label, '', "class='btn btn-link $active'");
    }

    if(isset($this->config->maxVersion))
    {
        $label  = "<span class='text'>{$lang->user->issue}</span>";
        $active = ($methodName == 'issue' or $methodName == 'issue')? ' btn-active-text' : '';
        common::printLink('user', 'issue', "userID={$user->id}", $label, '', "class='btn btn-link $active'");

        $label  = "<span class='text'>{$lang->user->risk}</span>";
        $active = ($methodName == 'risk' or $methodName == 'risk')? ' btn-active-text' : '';
        common::printLink('user', 'risk', "userID={$user->id}", $label, '', "class='btn btn-link $active'");
    }

    $label  = "<span class='text'>{$lang->user->dynamic}</span>";
    $active = $methodName == 'dynamic' ? ' btn-active-text' : '';
    common::printLink('user', 'dynamic',  "userID={$user->id}&type=today", $label, '', "class='btn btn-link $active dynamicTab'");

    $label  = "<span class='text'>{$lang->user->profile}</span>";
    $active = $methodName == 'profile' ? ' btn-active-text' : '';
    common::printLink('user', 'profile',  "userID={$user->id}", $label, '', "class='btn btn-link $active profileTab'");
    ?>
  </div>
  <div class='actions'></div>
</div>
<script>
var type   = '<?php echo $type;?>';
var period = '<?php echo $period;?>';
</script>
<?php
$calendarHook = dirname(__FILE__) . '/featurebar.calendar.html.hook.php';
if(file_exists($calendarHook)) include $calendarHook;
?>
