<?php
include '../../control.php';
class myExecution extends execution
{
    /**
     * Project: chengfangjinke
     * Method: view
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:28
     * Desc: This is the code comment. This method is called view.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $executionID
     */
    public function view($executionID, $version = 0)
    {
        $this->loadModel('project');
        $executionID = (int)$executionID;
        $execution   = $this->execution->getByIDAndVersion($executionID, true, $version);
        /* Set menu. */
        $this->session->set('project',$execution->project);
        $this->project->setMenu($this->session->project);
        // 获取详情页中的参与人员数据。
        $participants = $this->execution->getParticipantsByExecution($executionID);

        // 获取详情页中的任务统计数据。
        $taskStatistics = $this->execution->getTaskStatisticsByExecution($executionID);
        //$this->loadModel('project')->setMenu($execution->project);

        // 避免计划和任务同时高亮。
        unset($this->lang->waterfall->menu->task['subModule']);

        $this->view->title     = $this->lang->execution->view;
        $this->view->users     = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->actions   = $this->loadModel('action')->getList('execution', $executionID);
        $this->view->project   = $this->loadModel('project')->getById($execution->project);
        $this->view->execution = $execution;
        $this->view->version   = $version == 0 ? $execution->version : $version;
        $this->view->taskStatistics = $taskStatistics;
        $this->view->participants   = $participants;
        $this->view->deptMap        = $this->loadModel('dept')->getOptionMenu();

        $this->display();
    }
}
