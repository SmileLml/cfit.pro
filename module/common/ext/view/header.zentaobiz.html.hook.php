<style>
<?php if(defined('IN_USE') and common::checkNotCN()):?>
#navbar ul.nav li a{padding:10px 5px;}
#navbar ul.nav{padding-left:60px;}
#navbar ul.nav li.divider{margin:15px 5px;}
<?php else:?>
#navbar ul.nav{padding-left:10px;}
#navbar ul.nav li.divider{margin:15px 10px;}
<?php endif;?>
</style>
<?php if(!empty($this->app->user->feedback) or $this->cookie->feedbackView):?>
<?php $config->global->novice = false; ?>
<?php if($menuGroup != 'oa' and $menuGroup != 'company' and !$this->app->isFlow):?>
<style>
#header #subHeader{display:none}
</style>
<?php endif;?>
<style>
#userMenu #searchbox{display:none}
#extraNav{display:none;}
</style>
<?php endif;?>
