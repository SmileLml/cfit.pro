<?php 
$this->app->loadLang('cmcl');
$this->app->loadLang('baseline');
js::set('methodName', $this->app->methodName);
?>
<?php if(common::hasPriv('cmcl', 'browse')) echo html::a($this->createLink('cmcl', 'browse'), '<span class="text">' . $lang->cmcl->browse . '</span>', '', "class='btn btn-link browseTab'");?>
<?php if(common::hasPriv('baseline', 'catalog')) echo html::a($this->createLink('baseline', 'catalog'), '<span class="text">' . $lang->baseline->catalog . '</span>', '', "class='btn btn-link catalogTab'");?>
<?php if(common::hasPriv('baseline', 'template')) echo html::a($this->createLink('baseline', 'template'), '<span class="text">' . $lang->baseline->template . '</span>', '', "class='btn btn-link templateTab'");?>
<script>
$('#mainMenu .' + methodName + 'Tab').addClass('btn-active-text');
</script>
