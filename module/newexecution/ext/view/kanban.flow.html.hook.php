<?php if($laneField == 'subStatus'):?>
<?php
$toolbar  = "<div class='btn-toolbar pull-left'>";
foreach($lang->kanbanSetting->modeList as $modeCode => $modeName) $toolbar .= html::a(inlink('kanban', "executionID=$executionID&type=$type&orderBy=$storyOrder&mode=$modeCode"), "<span class='text'>" . $modeName . '</span>', '', "class='btn btn-link" . ($mode == $modeCode ? ' btn-active-text' : '') . "'");
$toolbar .= '</div>';

$dropdownMenus = '';
foreach($lang->execution->orderList as $key => $value)
{
    $class = ($type == 'story' and $storyOrder == $key) ? " class='active'" : '';

    $dropdownMenus .= "<li $class>" . html::a(inlink('kanban', "executionID=$executionID&type=story&orderBy=$key&mode=$mode"), $value) . '</li>';
}
$dropdownMenus .= "<li" . ($type == 'assignedTo' ? " class='active'" : '') . ">" . html::a(inlink('kanban', "execution=$executionID&type=assignedTo&orderBy=order_desc&mode=$mode"), $lang->execution->groups['assignedTo']) . "</li>";
$dropdownMenus .= "<li" . ($type == 'finishedBy' ? " class='active'" : '') . ">" . html::a(inlink('kanban', "execution=$executionID&type=finishedBy&orderBy=order_desc&mode=$mode"), $lang->execution->groups['finishedBy']) . "</li>";
?>
<script>
$(function()
{
    $('#mainMenu').prepend(<?php echo json_encode($toolbar);?>);
    $('#kanban table thead tr th .dropdown .dropdown-menu').empty().append(<?php echo json_encode($dropdownMenus);?>);
})
</script>
<?php endif;?>
