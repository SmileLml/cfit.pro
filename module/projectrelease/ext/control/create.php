<?php
include '../../control.php';
class myProjectrelease extends projectrelease
{
    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $projectID
     */
    public function create($projectID)
    {
        $this->app->loadConfig('release');
        $this->config->projectrelease->create = $this->config->release->create;
        if(!empty($_POST))
        {
            $_POST['createdBy'] = $this->app->user->account;
            $releaseID = $this->projectrelease->create($projectID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('release', $releaseID, 'opened');
            $this->executeHooks($releaseID);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "releaseID=$releaseID")));
        }

        /* Get the builds that can select. */
        $productPairs  = $this->loadModel('product')->getProductPairsByProject($projectID);
        $productIdList = array_keys($productPairs);
        $builds        = $this->loadModel('build')->getProductBuildPairs($productIdList, 0, 0, 'notrunk');
        $releaseBuilds = $this->projectrelease->getReleaseBuilds($projectID);

        foreach($releaseBuilds as $build) unset($builds[$build]);
        unset($builds['trunk']);

        $this->project->setMenu($projectID);
        $this->commonAction($projectID);

        $this->view->title       = $this->view->project->name . $this->lang->colon . $this->lang->release->create;
        $this->view->position[]  = $this->lang->release->create;
        $this->view->builds      = $builds;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->lastRelease = $this->projectrelease->getLast($projectID);
        $this->view->plans       = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( 0, 0);;
        $this->view->apps        = array('0' => '') + $this->loadModel('application')->getPairs();
        $this->display();
    }
}
