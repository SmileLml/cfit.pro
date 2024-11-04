<?php 
$this->app->loadLang('reviewcl');
$this->app->loadLang('reviewsetting');
js::set('methodName', $this->app->methodName);
?>
<?php if(common::hasPriv('reviewcl', 'browse')) echo html::a($this->createLink('reviewcl', 'browse'), '<span class="text">' . $lang->reviewcl->browse . '</span>', '', "class='btn btn-link browseTab'");?>
<?php if(common::hasPriv('reviewsetting', 'version')) echo html::a($this->createLink('reviewsetting', 'version'), '<span class="text">' . $lang->reviewsetting->version. '</span>', '', "class='btn btn-link versionTab'");?>
<?php if(common::hasPriv('reviewsetting', 'reviewer')) echo html::a($this->createLink('reviewsetting', 'reviewer'), '<span class="text">' . $lang->reviewsetting->reviewer . '</span>', '', "class='btn btn-link reviewerTab'");?>
<script>
$('#mainMenu .' + methodName + 'Tab').addClass('btn-active-text');
</script>
