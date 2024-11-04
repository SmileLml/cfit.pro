<?php
/**
 * The control file of calendar module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     calendar
 * @version     $Id$
 * @link        http://www.zentao.net
 */
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class myexecution extends execution
{
    public function effortCalendar($executionID, $userID = '')
    {
        $this->app->loadLang('effort');
        $this->execution->setMenu($executionID);

        // 避免计划和任务同时高亮。
        unset($this->lang->waterfall->menu->programplan['subModule']);

        /* Get users.*/
        $accounts  = $this->execution->getTeamMembers($executionID);
        $users[''] = $this->lang->user->select;
        foreach($accounts as $user) $users[$user->userID] = $user->realname;

        /* The header and position. */
        $this->view->title      = $this->lang->execution->common . $this->lang->colon . $this->lang->execution->effortCalendar;
        $this->view->position[] = $this->lang->execution->effortCalendar;

        /* Assign. */
        $this->view->users       = $users;
        $this->view->userID      = $userID ;
        $this->view->executionID = $executionID;

        $this->display();
    }
}
