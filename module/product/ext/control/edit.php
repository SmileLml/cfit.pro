<?php
include '../../control.php';
class myProduct extends product
{
    /**
     * Project: chengfangjinke
     * Method: edit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:55
     * Desc: This is the code comment. This method is called edit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $productID
     * @param string $action
     * @param string $extra
     * @param int $programID
     */
    public function edit($productID, $action = 'edit', $extra = '', $programID = 0)
    {

        $this->config->systemMode = 'classic';
        $this->view->apps         = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        //parent::edit($productID, $action, $extra, $programID);

        $this->app->loadLang('custom');

        /* Init vars. */
        $product = $this->product->getById($productID);
        if($product->bind) $this->config->product->edit->requiredFields = 'name';

        $unmodifiableProjects = array();
        $canChangeProgram     = true;
        $singleLinkProjects   = array();
        $multipleLinkProjects = array();
        $linkStoriesProjects  = array();

        /* Link the projects stories under this product. */
        $unmodifiableProjects = $this->dao->select('t1.*')->from(TABLE_PROJECTSTORY)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.deleted')->eq('0')
            ->fetchPairs('project', 'product');

        if(!empty($unmodifiableProjects)) $canChangeProgram = false;

        /* Get the projects linked with this product. */
        $projectPairs = $this->dao->select('t2.id,t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.deleted')->eq('0')
            ->fetchPairs();

        if(!empty($projectPairs))
        {
            foreach($projectPairs as $projectID => $projectName)
            {
                if($canChangeProgram)
                {
                    $products = $this->dao->select('product')->from(TABLE_PROJECTPRODUCT)->where('project')->eq($projectID)->fetchPairs();
                    if(count($products) == 1)
                    {
                        $singleLinkProjects[$projectID] = $projectName;
                    }

                    if(count($products) > 1)
                    {
                        $multipleLinkProjects[$projectID] = $projectName;
                    }
                }
                else
                {
                    if(isset($unmodifiableProjects[$projectID])) $linkStoriesProjects[$projectID] = $projectName;
                }
            }
        }

        if(!empty($_POST))
        {
           /* if(!$this->post->code)
            {
                dao::$errors['code'] = [$this->config->product->codeIsEmpty];
                $response['result']  = 'fail';
                $response['message'] = dao::$errors;
                $this->send($response);
            }*/
            $oldProduct = $this->dao->select('id,app')->from(TABLE_PRODUCT)->where('id')->eq($productID)->fetch();
            $changes = $this->product->update($productID);

            if($this->config->systemMode == 'new')
            {
                /* Change the projects set of the program. */
                if(($_POST['program'] != $product->program) and $singleLinkProjects or $multipleLinkProjects)
                {
                    $this->product->updateProjects($productID, $singleLinkProjects, $multipleLinkProjects);
                }
            }

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($action == 'undelete')
            {
                $this->loadModel('action');
                $this->dao->update(TABLE_PRODUCT)->set('deleted')->eq(0)->where('id')->eq($productID)->exec();
                $this->dao->update(TABLE_ACTION)->set('extra')->eq(ACTIONMODEL::BE_UNDELETED)->where('id')->eq($extra)->exec();
                $this->action->create('product', $productID, 'undeleted');
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('product', $productID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $this->product->changeApplication($productID, 'update', $oldProduct->app);

            $this->executeHooks($productID);

            $moduleName = $programID ? 'program'    : 'product';
            $methodName = $programID ? 'product' : 'view';
            $param      = $programID ? "programID=$programID" : "product=$productID";
            $locate     = $this->createLink($moduleName, $methodName, $param);

            if(!$programID) $this->session->set('productList', $this->createLink('product', 'browse', $param), 'product');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
        }

        $productID = $this->product->saveState($productID, $this->products);
        $this->product->setMenu($productID);

        if($programID)
        {
            $this->lang->program->switcherMenu = $this->loadModel('program')->getSwitcher($programID, true);
        }

        /* Get the relevant person in charge. */
        $this->loadModel('user');
        $poUsers = $this->user->getPairs('nodeleted|pofirst|noclosed',  $product->PO, $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["PO"] = $this->config->user->moreLink;

        $qdUsers = $this->user->getPairs('nodeleted|qdfirst|noclosed',  $product->QD, $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["QD"] = $this->config->user->moreLink;

        $rdUsers = $this->user->getPairs('nodeleted|devfirst|noclosed', $product->RD, $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["RD"] = $this->config->user->moreLink;

        $lines = array();$this->loadModel('productline');
        if($product->program) $lines = array('') + $this->productline->getPairsLineAndName($product->program);
        if($this->config->systemMode == 'classic') $lines = array('') + $this->productline->getPairsLineAndName();

        $this->view->title      = $this->lang->product->edit . $this->lang->colon . $product->name;
        $this->view->position[] = html::a($this->createLink($this->moduleName, 'browse'), $product->name);
        $this->view->position[] = $this->lang->product->edit;

        $this->view->product              = $product;
        $this->view->groups               = $this->loadModel('group')->getPairs();
        $this->view->program              = $this->loadModel('program')->getParentPairs();
        $this->view->poUsers              = $poUsers;
        $this->view->poUsers              = $poUsers;
        $this->view->qdUsers              = $qdUsers;
        $this->view->rdUsers              = $rdUsers;
        $this->view->users                = $this->user->getPairs('nodeleted|noclosed');
        $this->view->lines                = $lines;
        $this->view->URSRPairs            = $this->loadModel('custom')->getURSRPairs();
        $this->view->canChangeProgram     = $canChangeProgram;
        $this->view->singleLinkProjects   = $singleLinkProjects;
        $this->view->multipleLinkProjects = $multipleLinkProjects;
        $this->view->linkStoriesProjects  = $linkStoriesProjects;
        $this->view->selects              = $this->product->getSelects();

        $codeinfos = $this->product->getCodeInfo($productID);
        $this->view->depts = $this->loadModel('dept')->getTopPairs();
        $this->view->codeinfos= $codeinfos;

        unset($this->lang->product->typeList['']);
        $this->display();
    }
}
