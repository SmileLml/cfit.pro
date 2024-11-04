<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mytree extends tree
{
    /**
     * Module browse.
     *
     * @param  int    $rootID
     * @param  string $viewType         story|bug|case|doc
     * @param  int    $currentModuleID
     * @param  int    $branch
     * @param  string $from
     * @access public
     * @return void
     */
    public function browse($rootID, $viewType, $currentModuleID = 0, $branch = 0, $from = '')
    {
        if((!empty($this->app->user->feedback) or $this->cookie->feedbackView) and $viewType != 'doc') die();
        if($this->app->openApp == 'feedback') $this->lang->feedback->menu->browse['subModule'] = 'tree';

        return parent::browse($rootID, $viewType, $currentModuleID, $branch, $from);
    }
}
