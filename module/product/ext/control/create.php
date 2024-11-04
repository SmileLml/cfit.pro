<?php
include '../../control.php';
class myProduct extends product 
{
    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:55
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $programID
     */
    public function create($programID = 0,$app = 0)
    {
        if(!empty($_POST))
        {
            $productID = $this->product->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('product', $productID, 'opened');

            $this->executeHooks($productID);

            if(isonlybody()) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'callback' => "parent.loadProducts($productID)"));
            $openApp    = $this->app->openApp;
            $locate     = $this->createLink('product', 'all');
            if($openApp == 'doc') $locate = $this->createLink('doc', 'objectLibs', 'type=product');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
        }
        if($this->app->openApp == 'program' && $app == 0) $this->loadModel('program')->setMenu($programID);

        $this->loadModel('user');
        $poUsers = $this->user->getPairs('nodeleted|pofirst|noclosed',  '', $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["PO"] = $this->config->user->moreLink;

        $qdUsers = $this->user->getPairs('nodeleted|qdfirst|noclosed',  '', $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["QD"] = $this->config->user->moreLink;

        $rdUsers = $this->user->getPairs('nodeleted|devfirst|noclosed', '', $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["RD"] = $this->config->user->moreLink;

        $this->loadModel('productline');
        $lines = array('') + $this->productline->getPairsLineAndName($programID);

        $this->view->title      = $this->lang->product->create;
        $this->view->position[] = $this->view->title;
        $this->view->groups     = $this->loadModel('group')->getPairs();
        $this->view->programID  = $programID;
        $this->view->poUsers    = $poUsers;
        $this->view->qdUsers    = $qdUsers;
        $this->view->rdUsers    = $rdUsers;
        $this->view->users      = $this->user->getPairs('nodeleted|noclosed');
        $this->view->lines      = $lines;
        $this->view->URSRPairs  = $this->loadModel('custom')->getURSRPairs();
        $this->view->apps       = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        $this->view->oneapp        = $app;
        $this->view->selects              = $this->product->getSelects();
        $this->view->depts = $this->loadModel('dept')->getTopPairs();
        unset($this->lang->product->typeList['']);
        $this->display();
    }
}
