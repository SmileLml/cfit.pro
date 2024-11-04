<?php
include '../../control.php';
class myProjectrelease extends projectrelease
{
    /**
     * Project: chengfangjinke
     * Method: edit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 17:22
     * Desc: This is the code comment. This method is called edit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $releaseID
     */
    public function edit($releaseID)
    {
        $this->app->loadConfig('release');
        $this->config->projectrelease->create = $this->config->release->create;

        if(!empty($_POST))
        {
            $_POST['createdBy'] = $this->app->user->account;
            $changes = $this->projectrelease->update($releaseID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $files = $this->loadModel('file')->saveUpload('release', $releaseID);
            if($changes or $files)
            {
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n" ;
                $actionID = $this->loadModel('action')->create('release', $releaseID, 'Edited', $fileAction);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }
            $this->executeHooks($releaseID);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "releaseID=$releaseID")));
        }
        $this->loadModel('story');
        $this->loadModel('bug');
        $this->loadModel('build');

        /* Get release and build. */
        $release = $this->projectrelease->getById((int)$releaseID);
        $this->commonAction($release->project, $release->product, $release->branch);
        $build = $this->build->getById($release->build);

        /* Set project menu. */
        $this->project->setMenu($release->project);

        $this->view->title      = $this->view->product->name . $this->lang->colon . $this->lang->release->edit;
        $this->view->position[] = $this->lang->release->edit;
        $this->view->release    = $release;
        $this->view->build      = $build;
        $this->view->builds     = $this->loadModel('build')->getProjectBuildPairs($release->project, $release->product, $release->branch, 'notrunk|withbranch');
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->plans       = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $release->product, 0);;
        $this->view->apps        = array('0' => '') + $this->loadModel('application')->getPairs();
        $this->display();
    }
}
