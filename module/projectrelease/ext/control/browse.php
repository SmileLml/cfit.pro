<?php
include '../../control.php';
class myProjectrelease extends projectrelease
{
    /**
     * Browse releases.
     *
     * @param  int    $projectID
     * @param  int    $executionID
     * @param  string $type
     * @access public
     * @return void
     */
    public function browse($projectID = 0, $executionID = 0, $type = 'all')
    {
        $this->session->set('releaseList', $this->app->getURI(true), 'project');
        $project   = $this->project->getById($projectID);
        $execution = $this->loadModel('execution')->getById($executionID);

        if($projectID) $this->project->setMenu($projectID);
        if($executionID) $this->execution->setMenu($executionID, $this->app->rawModule, $this->app->rawMethod);

        $objectName = isset($project->name) ? $project->name : $execution->name;
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        $this->view->title       = $objectName . $this->lang->colon . $this->lang->release->browse;
        $this->view->position[]  = $this->lang->release->browse;
        $this->view->execution   = $execution;
        $this->view->project     = $project;
        $this->view->products    = $this->loadModel('product')->getProducts($projectID);
        $releases = $this->projectrelease->getList($projectID, $type);
        foreach ($releases as $release) {
            if ($release->dealUser != ''){
                $dealUsers = explode(',',$release->dealUser);
                $userArray = [];
                foreach ($dealUsers as $dealUser) {
                    $userArray[] = $users[$dealUser];
                }
                $release->dealUserStr = implode(',',$userArray);
            }else{
                $release->dealUserStr = '';
            }
        }

        $this->view->releases    = $releases;
        $this->view->projectID   = $projectID;
        $this->view->executionID = $executionID;
        $this->view->type        = $type;
        $this->view->from        = $this->app->openApp;
        $this->view->users       = $users;
        $this->display();
    }
}
