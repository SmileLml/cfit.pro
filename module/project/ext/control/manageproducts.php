<?php
include '../../control.php';
class myProject extends project
{
    public function manageProducts($projectID, $from = 'project')
    {
        $this->loadModel('product');
        $this->loadModel('execution');
        $this->loadModel('program');
        $this->loadModel('productline');

        if(!empty($_POST))
        {

            if(!isset($_POST['products']))
            {
                dao::$errors['message'][] = $this->lang->project->errorNoProducts;
                $this->send(array('result' => 'fail', 'message' => dao::getError()));
            }

            $oldProducts = $this->project->getProducts($projectID);

            $this->project->updateProducts($projectID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $oldProducts  = array_keys($oldProducts);

            // 2022-4-27 tongyanqi 删除已有的年度计划关联关系。
            foreach ($oldProducts as $oldProductId)
            {
                if(!in_array($oldProductId, $_POST['products'])){
                    //echo $this->dao->delete()->from(TABLE_RELATIONPLAN)->where('project')->eq($projectID)->andWhere('product')->eq($oldProductId)->printSQL();
                }
            }
            $record = $this->dao->select('*')->from(TABLE_PROJECTPLANRELATION)->where('projectId')->eq($projectID)->fetchOne();
            if(!empty($record)){
                $planRelations = json_decode($record[0]->planRelation, 1);
                $newPlanRelations = [];
                foreach ($planRelations as $planRelation)
                {
                    if(in_array($planRelation['id'], $_POST['products'])) { $newPlanRelations[] = $planRelation; }
                }
                if(!empty($newPlanRelations)){
                    $this->dao->update(TABLE_PROJECTPLANRELATION)->set('PlanRelation')->eq(json_encode($newPlanRelations))->where('projectId')->eq($projectID)->exec();
                }
            }
            // end 2022-4-27 tongyanqi 删除已有的年度计划关联关系。

            $newProducts  = $this->project->getProducts($projectID);
            $newProducts  = array_keys($newProducts);
            $diffProducts = array_merge(array_diff($oldProducts, $newProducts), array_diff($newProducts, $oldProducts));
            $comment = '原产品：'.implode(',',$oldProducts).'<br>'.'更新后：'.implode(',',$newProducts);
            if($diffProducts) $this->loadModel('action')->create('project', $projectID, 'Managed',$comment,$comment);

            $locateLink = inLink('manageProducts', "projectID=$projectID");

            if($from == 'program')  $locateLink = $this->createLink('program', 'browse');

            if($from == 'programproject') $locateLink = $this->session->programProject ? $this->session->programProject : inLink('programProject', "projectID=$projectID");

            // 查询项目下的执行，为这些执行同步关联产品。
            $executionIdList = $this->dao->select('id')->from(TABLE_EXECUTION)->where('project')->eq($projectID)->andWhere('deleted')->eq('0')->fetchAll();
            foreach($executionIdList as $execution)
            {
                $this->execution->updateProducts($execution->id);
            }

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locateLink));
        }

        $project = $this->project->getById($projectID);
        if($this->app->openApp == 'program')
        {
            $this->program->setMenu($project->parent);
        }

        else if($this->app->openApp == 'project')
        {
            $this->project->setMenu($projectID);
        }

        $allProducts    = $this->program->getProductCodeNamePairs($project->parent, 'assign', 'noclosed');
        $linkedProducts = $this->product->getProducts($projectID);
        $linkedBranches = array();

        /* If the story of the product which linked the project, you don't allow to remove the product. */
        $unmodifiableProducts = array();
        foreach($linkedProducts as $productID => $linkedProduct)
        {
            $projectStories = $this->dao->select('*')->from(TABLE_PROJECTSTORY)->where('project')->eq($projectID)->andWhere('product')->eq($productID)->fetchAll('story');
            if(!empty($projectStories)) array_push($unmodifiableProducts, $productID);
        }

        /* Merge allProducts and linkedProducts for closed product. */
        foreach($linkedProducts as $product)
        {
            if(!isset($allProducts[$product->id])) $allProducts[$product->id] = $product->name;

            if(!empty($product->branch)) $linkedBranches[$product->branch] = $product->branch;
        }

        /* Assign. */
        $this->view->title                = $this->lang->project->manageProducts . $this->lang->colon . $project->name;
        $this->view->position[]           = $this->lang->project->manageProducts;
        $this->view->allProducts          = $allProducts;
        $this->view->linkedProducts       = $linkedProducts;
        $this->view->projectIDnow         = $projectID;
        $this->view->unmodifiableProducts = $unmodifiableProducts;
        $this->view->branchGroups         = $this->loadModel('branch')->getByProducts(array_keys($allProducts), '', $linkedBranches);

        $this->display();
    }
}

