<?php
include '../../control.php';
class myDemandinside extends demandinside
{

      /**
       * 设置session
       */
        public function ajaxGetProjectSession($project)
        {
            global $app;
            if($app->session->releaseList){
                $uri ="/projectrelease-browse-$project.html";
                $app->session->set('releaseList', $uri, 'project');
            }
            $this->session->set('project', (int)$project);
        }
}
