<?php
include '../../control.php';
class myProduct extends product 
{
    /**
     * Project: chengfangjinke
     * Method: requirement
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:56
     * Desc: This is the code comment. This method is called requirement.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $productID
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function requirement($productID, $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->product->setMenu($productID);

        $browseType = strtolower($browseType);

        /* By search. */
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('requirement', 'browse', "browseType=bySearch&param=myQueryID");
        $this->loadModel('requirement')->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title        = $this->lang->requirement->common;
        $this->view->requirements = $this->requirement->getByProduct($productID, $browseType, $queryID, $orderBy, $pager);
        $this->view->productID    = $productID;
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->view->browseType   = $browseType;
        $this->view->depts        = $this->loadModel('dept')->getOptionMenu();
        $this->view->projects     = $this->loadModel('projectplan')->getPairs();
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }
}
