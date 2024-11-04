<?php
if(isset($lang->apps->{$flow->app}))
{
    include $this->app->getModuleRoot($flow->app) . 'common/view/header.html.php';
}
else
{
    include '../../common/view/header.html.php';
}
