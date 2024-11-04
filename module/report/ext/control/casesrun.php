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
     * Use case execution statistics table.
     *
     * @param  int $applicationID
     * @param  int $productID
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function casesrun($applicationID = 0, $productID = 0, $projectID = 0)
    {
        $products     = ['' => ''] + $this->loadModel('product')->getPairs();
        $applications = ['' => ''] + $this->loadModel('application')->getPairs();
        $projects     = array('' => '') + $this->loadModel('project')->getPairsCodeName();

        $modules = [];
        if($applicationID or $productID or $projectID) $modules = $this->report->getCasesRun($applicationID, $productID, $projectID);

        $this->app->loadLang('testcase');
        $this->view->title         = $this->lang->report->casesrun;
        $this->view->products      = $products;
        $this->view->productID     = $productID;
        $this->view->applications  = $applications;
        $this->view->applicationID = $applicationID;
        $this->view->projects      = $projects;
        $this->view->projectID     = $projectID;
        $this->view->modules       = $modules;
        $this->view->submenu       = 'test';
        $this->display();
    }
}
