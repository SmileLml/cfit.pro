<?php
/**
 * The control file of milestone module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     milestone
 * @version     $Id: control.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class milestone extends control
{
    public function index($projectID = 0, $executionID = 0, $productID = 0)
    {
        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('execution');
        list($this->lang->modulePageNav, $executionID) = $this->milestone->getPageNav($projectID, $executionID, $productID);

        $this->view->title = $this->lang->milestone->title;

        if(!$executionID)
        {
            $this->view->executionID = $executionID;
            $this->display();
            die;
        }

        $productID = $this->loadModel('product')->getProductIDByProject($executionID);
        $stageList = $this->loadModel('programplan')->getPairs($projectID, $productID);
        unset($stageList[0]);

        $this->view->executionID    = $executionID;
        $this->view->projectID      = $projectID;
        $this->view->stageList      = $stageList;
        $this->view->basicInfo      = $this->milestone->getBasicInfo($projectID, $executionID);
        $this->view->process        = $this->milestone->getProcess($projectID, $executionID);
        $this->view->charts         = $this->milestone->getCharts($projectID, $executionID);
        $this->view->productQuality = $this->milestone->getProductQuality($projectID, $executionID);
        $this->view->workhours      = $this->milestone->getWorkhours($projectID, $executionID);
        $this->view->measures       = $this->milestone->getMeasures($projectID, $executionID);
        $this->view->executionRisk  = $this->milestone->getProjectRisk($projectID);
        $this->view->users          = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->stageInfo      = $this->milestone->getStageDemand($projectID, $executionID, $productID, $stageList);
        $this->view->otherproblems  = $this->milestone->otherProblemsList($projectID, $executionID);
        $this->view->nextMilestone  = $this->milestone->getNextMilestone($projectID, $executionID, $stageList);

        $this->display();
    }

    public function ajaxAddMeasures()
    {
        $data = fixer::input('post')->get();
        if(empty($data->executionID)) return 0;
        return $this->milestone->ajaxAddMeasures($data);
    }

    public function saveOtherProblem()
    {
        $re = $this->milestone->saveOtherProblem();
        $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
    }

    public function ajaxSaveEstimate()
    {
        $taskID = $this->post->taskID;
        $estimate = $this->post->estimate;
        $re = $this->milestone->ajaxSaveEstimate($taskID,$estimate);
        $this->send(array('result' => 'success','message' => $this->lang->saveSuccess));
    }
}
