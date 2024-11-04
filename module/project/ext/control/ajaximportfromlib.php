<?php
include '../../control.php';
class myProject extends project
{
    /**
     * Desc: ajax open dialog to select case.
     *
     * @param int $projectID
     * @param int $applicationID
     * @param int $productID
     * @return void
     */
    public function ajaxImportFromLib($projectID, $applicationID, $productID)
    {
        $this->app->loadLang('testcase');
        $this->project->setMenu($projectID);

        $libraries = $this->loadModel('caselib')->getLibraries();
        if(empty($libraries))
        {
            echo js::alert($this->lang->testcase->noLibrary);
            return false;
        }
        $libraries = array('' => '') + $libraries;

        $products = $this->project->getMultiLinkedProducts($projectID);
        unset($products['na']);
        if(empty($products))
        {
            echo js::alert($this->lang->testcase->noProduct);
            return false;
        }
        $products = ['' => ''] + $products;
        if($productID == 'na' || $productID == 'all')
        {
            $productID = '';
        }

        $this->view->products      = $products;
        $this->view->projectID     = $projectID;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->libraries     = $libraries;
        $this->display();
    }
}
