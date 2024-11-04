<?php
include '../../control.php';
class myProduct extends product
{
    /**
     * Project: chengfangjinke
     * Method: all
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:55
     * Desc: This is the code comment. This method is called all.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     */
    public function all($browseType = 'noclosed', $param = 0, $orderBy = 'name_asc')
    {
        /* Load module and set session. */
        $this->loadModel('program');
        $this->session->set('productList', $this->app->getURI(true), 'product');
        $browseType = strtolower($browseType);
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;

        $apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();

        $actionURL = $this->createLink('product', 'all', "browseType=bySearch&param=myQueryID");
        $this->loadModel('secondline')->buildSearchForm($queryID, $actionURL,$apps);
        /* Process product structure. */
        $productStructure = array();
        $productStats     = $this->product->getProductStats($queryID, $orderBy, '', $browseType, '', 'story');

        foreach($productStats as $product)
        {
            $productStructure[$product->program][$product->line]['products'][$product->id]      = $product;
            if($product->line) $productStructure[$product->program][$product->line]['lineName'] = $product->lineName;
            if($product->program) $productStructure[$product->program]['programName']           = $product->programName;
        }

        $this->view->title      = $this->lang->product->common;
        $this->view->position[] = $this->lang->product->common;
        $this->view->apps       = $apps;

        $this->view->recTotal         = count($productStats);
        $this->view->productStats     = $productStats;
        $this->view->productStructure = $productStructure;
        $this->view->orderBy          = $orderBy;
        $this->view->browseType       = $browseType;
        $this->view->param            = $param;

        $this->display();
    }
}
