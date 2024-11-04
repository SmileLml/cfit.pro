<?php
include '../../control.php';
class myDemandinside extends demandinside
{

    /**
     * 设置session
     */
    public function ajaxGetProjectId($project)
    {
        global $app;
        if($app->session->taskList){
            $uri ="/project-execution-all-$project.html";
            $app->session->set('taskList', $uri, 'project');
        }
        $this->session->set('project', (int)$project);
    }
}
