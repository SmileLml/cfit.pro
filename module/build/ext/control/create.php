<?php
include '../../control.php';
class myBuild extends build
{
    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:25
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $executionID
     * @param int $productID
     * @param int $projectID
     */
    public function create($executionID = 0, $productID = 0, $projectID = 0)
    {
        /* Load these models. */
        $this->loadModel('execution');
        $this->loadModel('user');
        $this->app->loadLang('demand');

        if(!empty($_POST))
        {
            $executionID = empty($executionID) ? $this->post->execution : $executionID;
            $desc = $this->post->desc;
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($_POST['issubmit'] == 'submit'){
                $buildID = $this->build->createBuild($projectID);
            }else{
                $buildID = $this->build->saveBuild($projectID);
            }

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('build', $buildID, 'opened',$desc);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadExecutionBuilds($executionID)")); // Code for task #5126.
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('build', 'view', "buildID=$buildID")));
        }

        /* Set menu. */
        if($this->app->openApp == 'project')
        {
            $this->loadModel('project')->setMenu($projectID);
            $executions  = $this->execution->getPairs($projectID);
            $executionID = empty($executionID) ? key($executions) : $executionID;
            $this->session->set('project', $projectID);
        }
        elseif($this->app->openApp == 'execution')
        {
            $execution  = $this->execution->getByID($executionID);
            $executions = $this->execution->getPairs($execution->project);
            $this->execution->setMenu($executionID);
            $this->session->set('project', $execution->project);
        }
        elseif($this->app->openApp == 'qa')
        {
            $execution  = $this->execution->getByID($executionID);
            $executions = $this->execution->getPairs($execution->project);
        }
        $this->loadModel('project');
        $productGroups = $this->project->getProducts($projectID);
        $productID     = $productID ? $productID : key($productGroups);
       /* $products      = array(''=>'','99999'=>'无');
        foreach($productGroups as $product) $products[$product->id] = $product->name;*/
        $projectDept = $this->project->getProjectBearDept($projectID);
        $depts = $this->loadModel('dept')->getByID($projectDept);
        //质量部CM
        $cmList = $this->loadModel('dept')->getRenameListByAccountStr($depts->cm);

        $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $productID, 0);
        $this->view->title      = $this->lang->build->create;
        $this->view->position[] = $this->lang->build->create;
        //是否需要设置安全测试接口人
        $isSetSeverityTestUser =  $this->loadModel('qualitygate')->getIsSetQualityGate($projectID);
        if($isSetSeverityTestUser){
            $severityTestUsers = $this->loadModel('qualitygate')->getProjectTeamSeverityUsers($projectID);
            $this->view->severityTestUsers = $severityTestUsers;
        }
        $this->view->isSetSeverityTestUser = $isSetSeverityTestUser;

        $this->view->product       = isset($productGroups[$productID]) ? $productGroups[$productID] : '';
        $this->view->branches      = (isset($productGroups[$productID]) and $productGroups[$productID]->type == 'normal') ? array() : $this->loadModel('branch')->getPairs($productID);
        $this->view->executionID   = $executionID;
        $this->view->products      = array();
        $this->view->projectID     = $projectID;
        $this->view->executions    = $executions;
        $this->view->openApp       = $this->app->openApp;
        $this->view->lastBuild     = $this->build->getLast($executionID);
        $this->view->productGroups = $productGroups;
        $this->view->users         = $this->user->getPairs('nodeleted|noclosed');
        $this->view->plans         = $plans;
        $this->view->apps         = array('0' => '') + $this->loadModel('application')->getApps($projectID);
        $this->view->cm     = key($cmList);
        $this->view->secondorder = '';
        $this->view->problems = '';
        $this->view->demands = '';
        $this->display();
    }
}
