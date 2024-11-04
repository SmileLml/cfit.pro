<?php
/**
 * Created by Yanqi Tong
 */

include '../../control.php';
class myProject extends project
{
    public function manageProductplans($projectID, $from = 'project')
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
            $newProducts  = $this->project->getProducts($projectID);
            $newProducts  = array_keys($newProducts);
            $diffProducts = array_merge(array_diff($oldProducts, $newProducts), array_diff($newProducts, $oldProducts));
            if($diffProducts) $this->loadModel('action')->create('project', $projectID, 'Managed', '', !empty($_POST['products']) ? join(',', $_POST['products']) : '');

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

        $allProducts    = $this->project->getProjectRelations($projectID);




        /* Assign. */
        $this->view->title                = $this->lang->project->manageProducts . $this->lang->colon . $project->name;
        $this->view->position[]           = $this->lang->project->manageProducts;
        $this->view->allProducts          = $allProducts;
        $this->view->projectIDnow         = $projectID;

        $this->display();
    }
}
