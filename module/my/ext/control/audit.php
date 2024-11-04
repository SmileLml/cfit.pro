<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mymy extends my
{
    /**
     * Project: chengfangjinke
     * Method: audit
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:46
     * Desc: This is the code comment. This method is called audit.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function audit($browseType = 'wait', $orderBy = 't1.id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {
        $this->loadModel('datatable');
        $this->session->set('reviewList', $this->app->getURI(true));
        $this->app->loadLang('review');
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $reviewList = $this->loadModel('review')->getUserReviews($browseType, $orderBy, $pager);
        $this->session->set('projectplanList', $this->app->getURI(true), 'my');
        $this->session->set('reviewList', $this->app->getURI(true), 'review');

        $this->view->title      = $this->lang->my->myReview;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|noletter');
        $this->view->depts      = $this->loadModel('dept')->getDeptPairs();
        $this->view->reviewList = $reviewList;
        $this->view->products   = $this->my->getProductPairs();
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->browseType = $browseType;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->mode       = 'audit';
        $this->display();
    }
}
