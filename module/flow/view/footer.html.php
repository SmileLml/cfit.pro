<?php
if(isset($lang->apps->{$flow->app}))
{
    include $this->app->getModuleRoot($flow->app) . 'common/view/footer.html.php';
}
else
{
    include '../../common/view/footer.html.php';
}
