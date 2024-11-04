<?php
/**
 * The control file of qa module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     qa
 * @version     $Id: control.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
class qa extends control
{
    /**
     * The index of qa, go to bug's browse page.
     *
     * @access public
     * @return void
     */
    public function index($locate = 'auto', $applicationID = 0, $productID = 0)
    {
        $applicationList = $this->loadModel('rebirth')->getApplicationPairs();
        if(empty($applicationList) and !helper::isAjaxRequest()) die($this->locate($this->createLink('application', 'create')));
        if($locate == 'yes') $this->locate($this->createLink('bug', 'browse'));

        $applicationID = $this->rebirth->saveState($applicationList, $applicationID, $productID);
        $this->rebirth->setMenu($applicationID, $productID);

        $this->view->title = $this->lang->qa->index;
        $this->display();
    }
}
