<?php
/**
 * The control file of customflow currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     customflow
 * @version     $Id: control.php 5107 2013-07-12 01:46:12Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class customflow extends control
{
    public function conf()
    {
        $this->lang->admin->menu->system['subModule'] = 'data,safe,cron,timezone,buildIndex,ldap,libreoffice,requestconf,customflow,iwfp';

        if($_POST)
        {
            $this->customflow->setConf();
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->customflow->common;
        $this->view->position[] = $this->lang->customflow->common;
        $this->view->customList = $this->customflow->getCustomFlow();
        $this->display();
    }
}

