<script>
$('#app').prev().remove();
$('#app').remove();
$('#submit').after("<input type='hidden' id='app' name='app' value='system'>");
$('#module').load(createLink('workflowdatasource', 'ajaxGetAppModules', 'app=' + $('#app').val()));
</script>
