<?php
include '../../control.php';
class mymy extends my
{
    /**
     * 更新session
     */
    public function ajaxGetProjectId($project)
    {
        global $app;
        if($app->session->taskList){
            $uri ="/my-work-task.html";
            $app->session->set('taskList', $uri, 'project');
        }
        $this->session->set('project', (int)$project);
    }
}
