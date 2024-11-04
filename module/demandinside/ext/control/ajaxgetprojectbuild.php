<?php
include '../../control.php';
class myDemandinside extends demandinside
{

      /**
       * 设置session
       */
        public function ajaxGetProjectBuild($project)
        {

            if($this->session->buildList){
                $uri ="/project-build-$project.html";
                $this->session->set('buildList', $uri, 'project');
            }
            $this->session->set('project', (int)$project);
        }
}
