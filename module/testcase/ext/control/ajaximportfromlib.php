<?php

helper::import(dirname(dirname(dirname(__FILE__))) . '/control.php');
class mytestcase extends testcase
{
    /**
     * Desc: ajax open dialog to select case.
     *
     * @param int $applicationID
     * @param int $productID
     * @param int $branch
     * @param int $moduleID
     * @return void
     */
    public function ajaxImportFromLib($applicationID, $productID, $branch, $moduleID = 0)
    {
        $libraries = $this->loadModel('caselib')->getLibraries();
        if(empty($libraries))
        {
            echo js::alert($this->lang->testcase->noLibrary);
            return false;
        }
        $libraries = ['' => ''] + $libraries;
        
        $products = $this->rebirth->getProductPairs($applicationID, true);
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

        $projects = array(0 => '') + $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);

        $this->view->products      = $products;
        $this->view->projects      = $projects;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->projectID     = '';
        $this->view->branch        = $branch;
        $this->view->moduleID      = $moduleID;
        $this->view->libraries     = $libraries;
        $this->display();
    }
}
