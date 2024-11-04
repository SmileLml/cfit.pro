<?php if($config->global->flow == 'full'):?>
<?php $methodName = $this->app->getMethodName();?>
<?php if(common::hasPriv('user', 'todocalendar')):?>
<script>
$('#mainMenu .todoTab').attr('href', <?php echo json_encode(inlink('todocalendar', "userID={$user->id}"));?>);
</script>
<?php endif;?>
<?php if(common::hasPriv('user', 'effortcalendar') and !common::hasPriv('user', 'todocalendar')):?>
<script>
$('#mainMenu .todoTab').attr('href', <?php echo json_encode(inlink('effortcalendar', "userID={$user->id}"));?>);
</script>
<?php endif;?>
<script>
<?php $active = ($methodName == 'todocalendar' or $methodName == 'effortcalendar') ? ' btn-active-text' : '';?>
$('#mainMenu .todoTab').addClass('<?php echo $active;?>');
</script>
<?php endif;?>
