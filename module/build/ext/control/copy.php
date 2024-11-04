<?php
include '../../control.php';
class myBuild extends build
{
    /**
     * Copy a build.
     *
     * @param  int    $buildID
     * @access public
     * @return void
     */
    public function copy($buildID,  $productID = 0, $projectID = 0)
    {
        if (!empty($_POST)) {
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

        $this->loadModel('execution');
        $this->loadModel('product');
        $this->app->loadLang('demand');
        $build = $this->build->getById((int)$buildID);

        /* Set menu. */
        if ($this->app->openApp == 'project') {
            $this->loadModel('project')->setMenu($build->project);
        } elseif ($this->app->openApp == 'execution') {
            $this->execution->setMenu($build->project);
        }

        /* Get stories and bugs. */
        $orderBy = 'status_asc, stage_asc, id_desc';

        /* Assign. */
        $execution = $this->execution->getByID($build->project);
        if (empty($execution)) {
            $execution = new stdclass();
            $execution->name = '';
        }

        $executions = $this->product->getExecutionPairsByProduct($build->product, $build->branch, 'id_desc', $this->session->project);
        if (!isset($executions[$build->execution])) $executions[$build->execution] = $execution->name;

        $this->loadModel('project');
        $productGroups = $this->project->getProducts($projectID);
        $productGroups['99999']->id = '99999';
        $productGroups['99999']->name = '无';
        $productGroups['99999']->type = 'normal';

        if (!isset($productGroups[$build->product])) {
            $product = $this->product->getById($build->product);
            $product->branch = $build->branch;
            $productGroups[$build->product] = $product;
        }

       /* $products = array();
        foreach ($productGroups as $product) $products[$product->id] = $product->name;
        krsort($products);*/

        $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $productID, 0);
        $this->view->plans         = $plans;

        $this->view->title = $execution->name . $this->lang->colon . $this->lang->build->edit;
        $this->view->position[] = html::a($this->createLink('execution', 'task', "executionID=$build->execution"), $execution->name);
        $this->view->position[] = $this->lang->build->edit;
        $this->view->product = isset($productGroups[$build->product]) ? $productGroups[$build->product] : '';
        $this->view->branches = (isset($productGroups[$build->product]) and $productGroups[$build->product]->type == 'normal') ? array() : $this->loadModel('branch')->getPairs($build->product);
        $this->view->executions = $executions;
        $this->view->orderBy = $orderBy;

        $this->view->productGroups = $productGroups;
        $this->view->products = array('0' => '','99999' =>'无') + $this->loadModel('application')->getAppProducts($projectID,$build->app);
        $this->view->users = $this->loadModel('user')->getPairs('noletter', $build->builder);
        $this->view->build = $build;
        $this->view->testtaskID = $this->dao->select('id')->from(TABLE_TESTTASK)->where('build')->eq($build->id)->andWhere('deleted')->eq(0)->fetch('id');
        $this->view->apps         = array('0' => '') + $this->loadModel('application')->getApps($projectID);

        $this->view->secondorder = $this->loadModel('secondorder')->getPairs(explode(',', trim($build->sendlineId,','))) ?? '';
        $this->view->problems = $this->loadModel('problem')->getPairsBycode(explode(',',str_replace('\r','',trim($build->problemid,','))));
        $this->view->demands = $this->loadModel('demand')->getPairsBycode(explode(',', trim($build->demandid,',')));
        //是否需要设置安全测试接口人
        $isSetSeverityTestUser =  $this->loadModel('qualitygate')->getIsSetQualityGate($projectID);
        if($isSetSeverityTestUser){
            $severityTestUsers = $this->loadModel('qualitygate')->getProjectTeamSeverityUsers($projectID);
            $this->view->severityTestUsers = $severityTestUsers;
        }
        $this->view->isSetSeverityTestUser = $isSetSeverityTestUser;
        $this->display();
    }
}
