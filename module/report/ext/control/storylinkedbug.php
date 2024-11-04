<?php
/**
 * The control file of report module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @link        https://www.zentao.net
 */
include '../../control.php';
class myReport extends report
{
/**
     * Story related bug summary tab
     *
     * @param  int    $productID
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function storyLinkedBug($productID = 0, $moduleID = 0)
    {
        $products = $this->loadModel('product')->getPairs();
        if(!$productID) $productID = key($products);

        $modules = $this->report->getModule($productID);
        if(!isset($modules[$moduleID])) $moduleID = key($modules);

        $this->app->loadLang('bug');
        $this->view->title     = $this->lang->report->storyLinkedBug;
        $this->view->products  = $products;
        $this->view->modules   = array('' => '') + $modules;
        $this->view->productID = $productID;
        $this->view->moduleID  = $moduleID;
        $this->view->stories   = $this->report->getStoryBugs($moduleID);
        $this->view->submenu   = 'test';
        $this->display();
    }
}