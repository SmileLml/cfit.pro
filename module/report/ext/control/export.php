<?php
/**
 * The model file of export module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     export
 * @link        https://www.zentao.net
 */
include '../../control.php';
class myReport extends report
{
    /**
     * Export.
     *
     * @param  int    $module
     * @param  int    $productID
     * @param  int    $taskID
     * @access public
     * @return void
     */
    public function export($module, $productID = 0, $taskID = 0, $applicationID = 0)
    {
        if($_POST)
        {
            $this->loadModel($module);
            if($module == 'testtask' && $productID && $taskID && $applicationID)
            {
                $task    = $this->testtask->getById($taskID);
                $bugInfo = $this->testtask->getBugInfo($taskID, $applicationID, $productID);
            }
            $items = explode(',', trim($this->post->items, ','));
            foreach($items as $item)
            {
                $chartFunc = 'getDataOf' . $item;
                if($module == 'testtask' && $productID && $taskID && $applicationID)
                {
                    $chartData = isset($bugInfo[$item]) ? $bugInfo[$item] : $this->$module->$chartFunc($taskID);
                }
                else
                {
                    $chartData = $this->$module->$chartFunc();
                }
                $datas[$item]  = $this->report->computePercent($chartData);
                $images[$item] = isset($_POST["chart-$item"]) ? $this->post->{"chart-$item"} : '';
                unset($_POST["chart-$item"]);
            }
            $this->post->set('datas',  $datas);
            $this->post->set('items',  $items);
            $this->post->set('images', $images);
            $this->post->set('kind',   $module);
            $this->fetch('file', 'export2chart', $_POST);
        }

        $this->view->module        = $module;
        $this->view->productID     = $productID;
        $this->view->taskID        = $taskID;
        $this->view->applicationID = $applicationID;
        $this->display();
    }
}
