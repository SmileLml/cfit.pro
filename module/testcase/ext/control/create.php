<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mytestcase extends testcase
{
    /**
     * Create a test case.
     * @param        $productID
     * @param string $branch
     * @param int    $moduleID
     * @param string $from
     * @param int    $param
     * @param int    $storyID
     * @param string $extras
     * @access public
     * @return void
     */
    public function create($applicationID, $productID, $branch = '', $moduleID = 0, $from = '', $param = 0, $storyID = 0, $extras = '')
    {
        $this->loadModel('story');
        $this->loadModel('custom');

        $projectID = 0;
        if($this->app->openApp == 'project')
        {
            $projectID = $this->session->project;
            $this->loadModel('project')->setMenu($projectID);
        }
        else
        {
            $this->rebirth->setMenu($applicationID, $productID);
            $applicationID = $this->rebirth->saveState($this->applicationList, $applicationID, $productID);
            $productID     = $this->rebirth->getProductIdByApplication($applicationID, $productID);
        }
        
        $testcaseID = ($from and strpos('testcase|work|contribute', $from) !== false) ? $param : 0;
        $bugID      = $from == 'bug' ? $param : 0;

        if(!empty($_POST))
        {
            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;

            setcookie('lastCaseModule', (int)$this->post->module, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $caseResult = $this->testcase->create($bugID);
            if(!$caseResult or dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $caseID = $caseResult['id'];
            if($caseResult['status'] == 'exists')
            {
                $response['message'] = sprintf($this->lang->duplicate, $this->lang->testcase->common);
                $response['locate']  = $this->createLink('testcase', 'view', "caseID=$caseID");
                $this->send($response);
            }

            $this->loadModel('action');
            $this->action->create('case', $caseID, 'Opened');

            $this->executeHooks($caseID);

            /* If link from no head then reload. */
            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true));

            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $response['locate'] = $this->session->caseList ? $this->session->caseList : $this->createLink('testcase', 'browse', "applicationID=$applicationID&productID=$productID&branch=$branch&browseType=all&param=0&orderBy=id_desc");
            $this->send($response);
        }

        /* Init vars. */
        $executionID    = 0;
        $type           = 'feature';
        $stage          = '';
        $pri            = 3;
        $caseTitle      = '';
        $precondition   = '';
        $keywords       = '';
        $intro          = '';
        $steps          = array();
        $color          = '';
        $caseCategories = '';

        /* If testcaseID large than 0, use this testcase as template. */
        if($testcaseID > 0)
        {
            $testcase       = $this->testcase->getById($testcaseID);
            $productID      = $testcase->product;
            $projectID      = $testcase->project;
            $executionID    = $testcase->execution;
            $type           = $testcase->type ? $testcase->type : 'feature';
            $stage          = $testcase->stage;
            $pri            = $testcase->pri;
            $storyID        = $testcase->story;
            $caseTitle      = $testcase->title;
            $precondition   = $testcase->precondition;
            $keywords       = $testcase->keywords;
            $intro          = $testcase->intro;
            $steps          = $testcase->steps;
            $color          = $testcase->color;
            $caseCategories = $testcase->categories;
        }

        /* If bugID large than 0, use this bug as template. */
        if($bugID > 0)
        {
            $bug         = $this->loadModel('bug')->getById($bugID);
            $projectID   = $bug->project;
            $executionID = $bug->execution;
            $type        = $bug->type;
            $pri         = $bug->pri ? $bug->pri : $bug->severity;
            $storyID     = $bug->story;
            $caseTitle   = $bug->title;
            $keywords    = $bug->keywords;
            $steps       = $this->testcase->createStepsFromBug($bug->steps);
        }

        /* Padding the steps to the default steps count. */
        if(count($steps) < $this->config->testcase->defaultSteps)
        {
            $paddingCount = $this->config->testcase->defaultSteps - count($steps);
            $step = new stdclass();
            $step->type   = 'item';
            $step->desc   = '';
            $step->expect = '';
            for($i = 1; $i <= $paddingCount; $i ++) $steps[] = $step;
        }

        /* Set story and currentModuleID. */
        if($storyID)
        {
            $story = $this->loadModel('story')->getByID($storyID);
            if(empty($moduleID)) $moduleID = $story->module;
        }
        $currentModuleID = (int)$moduleID;

        /* Get the status of stories are not closed. */
        $storyStatus = $this->lang->story->statusList;
        unset($storyStatus['closed']);
        $modules = array();
        if($currentModuleID)
        {
            $modules = $this->loadModel('tree')->getStoryModule($currentModuleID);
            $modules = $this->tree->getAllChildID($modules);
        }
        $stories = $this->story->getProductStoryPairs($productID, $branch, $modules, array_keys($storyStatus), 'id_desc', 50, 'null', 'story', false);
        if($storyID and !isset($stories[$storyID])) $stories = $this->story->formatStories(array($storyID => $story)) + $stories;//Fix bug #2406.

        /* Set custom. */
        foreach(explode(',', $this->config->testcase->customCreateFields) as $field) $customFields[$field] = $this->lang->testcase->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->testcase->custom->createFields;

        /* 获取产品关联的项目。*/
        if($this->app->openApp == 'project')
        {
            $products    = $this->rebirth->getProjectProductPairs($applicationID, $projectID);
            $projectName = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch('name');
            $projects    = array($projectID => $projectName);
        }
        else
        {
            $products = $this->rebirth->getProductPairs($applicationID, true);
            $projects = array(0 => '') + $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
        }

        $this->view->title            = $this->applicationList[$applicationID] . $this->lang->colon . $this->lang->testcase->create;
        $this->view->applicationID    = $applicationID;
        $this->view->productID        = $productID;
        $this->view->projectID        = $projectID;
        $this->view->executionID      = $executionID;
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $branch);
        $this->view->currentModuleID  = $currentModuleID ? $currentModuleID : (int)$this->cookie->lastCaseModule;
        $this->view->stories          = $stories;
        $this->view->caseTitle        = $caseTitle;
        $this->view->color            = $color;
        $this->view->type             = $type;
        $this->view->stage            = $stage;
        $this->view->products         = $products;
        $this->view->projects         = $projects;
        $this->view->executions       = array(0 => '');
        $this->view->pri              = $pri;
        $this->view->storyID          = $storyID;
        $this->view->precondition     = $precondition;
        $this->view->keywords         = $keywords;
        $this->view->intro            = $intro;
        $this->view->steps            = $steps;
        $this->view->users            = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->branch           = $branch;
        $this->view->branches         = array();
        $this->view->caseCategories   = $caseCategories;

        $this->display();
    }
}
