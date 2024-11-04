<?php
class requirementchange extends control
{
    /**
     * Method: browse
     * @param string $browseType
     * @param int $param
     * @param string $orderBy
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $browseType = strtolower($browseType);
        $this->app->loadLang('requirementchange');
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('requirementchange', 'browse', "browseType=bySearch&param=myQueryID");

        $this->requirementchange->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('requirementchangetList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->view->title      = $this->lang->requirementchange->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->projects      = $this->loadModel('project')->getProjects();
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }
    public function changeview($id){
        $info = $this->requirementchange->getById($id);
        $requirements = $this->loadModel('requirement')->getByCodes($info->changeEntry);
        $this->view->info = $info;
        $this->view->requirements = $requirements;
        $this->display();
    }
    //需求任务主题详情
    public function assigndetail($id){
        $requirement = $this->loadModel('requirement')->getByID($id);
        $lines       = $this->loadModel('product')->getLinePairs();
        $products    = $this->product->getPairs();
        $line = "";
        if ($requirement->line != ''){
            $lineArr = explode(',',$requirement->line);
            foreach ($lineArr as $lk) {
                $line .= $lines[$lk].'，';
            }
        }

        $requirement->lineStr = rtrim($line,'，');
        $product = "";
        if ($requirement->product != ''){
            $productArr = explode(',',$requirement->product);
            foreach ($productArr as $lk) {
                $product .= $products[$lk].'，';
            }
        }

        $requirement->productStr = rtrim($product,'，');
        $this->view->requirement = $requirement;
        $this->display();
    }
    public function demo(){
        $res = $this->requirementchange->sendmail(32,1);
        a($res);
    }
}
