<?php
include '../../control.php';
class myProblem extends problem
{

    /**
     * 获取反馈单所有历史审批记录
     */
    public function historyRecord($problemID)
    {
        $allNodes = $this->loadModel('review')->getAllNodes('problem', $problemID); //所有历史审批信息
        $problem = $this->loadModel('problem')->getByID($problemID);
        $this->view->allNodes = $allNodes;
        $this->view->problem = $problem;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->display();
    }
}
