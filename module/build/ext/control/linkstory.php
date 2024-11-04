<?php
include '../../control.php';
class myBuild extends build
{
    /**
     * Project: chengfangjinke
     * Method: linkStory
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:25
     * Desc: This is the code comment. This method is called linkStory.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $buildID
     * @param string $browseType
     * @param int $param
     * @param int $recTotal
     * @param int $recPerPage
     * @param int $pageID
     */
    public function linkStory($buildID = 0, $browseType = '', $param = 0, $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['stories']))
        {
            $this->build->linkStory($buildID);
            die(js::locate(inlink('view', "buildID=$buildID&type=story"), 'parent'));
        }

        $this->session->set('storyList', inlink('view', "buildID=$buildID&type=story&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), $this->app->openApp);

        $build   = $this->build->getById($buildID);
        $product = $this->loadModel('product')->getById($build->product);

        $this->loadModel('execution')->setMenu($build->execution);
        $this->loadModel('story');
        $this->loadModel('tree');
        $this->loadModel('product');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        unset($this->config->product->search['fields']['product']);
        unset($this->config->product->search['fields']['project']);
        $this->config->product->search['actionURL'] = $this->createLink('build', 'view', "buildID=$buildID&type=story&link=true&param=" . helper::safe64Encode("&browseType=bySearch&queryID=myQueryID"));
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['style']     = 'simple';
        $this->config->product->search['params']['plan']['values']   = $this->loadModel('productplan')->getForProducts(array($build->product => $build->product));
        $this->config->product->search['params']['module']['values'] = $this->tree->getOptionMenu($build->product, 'story', 0);
        $this->config->product->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $this->lang->story->statusList);

        if($product->type == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $this->config->product->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);
            $branches = array('' => '') + $this->loadModel('branch')->getPairs($build->product, 'noempty');
            if($build->branch) $branches = array('' => '', $build->branch => $branches[$build->branch]);
            $this->config->product->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->product->search);

        if($browseType == 'bySearch')
        {
            $allStories = $this->story->getBySearch($build->product, $build->branch, $queryID, 'id', $build->execution, 'story', $build->stories, $pager);
        }
        else
        {
            $allStories = $this->build->getProjectStories($build->project, 0, 0, 't1.`order`_desc', 'byProduct', $build->product, 'story', $build->stories, $pager);
        }

        $this->view->allStories   = $allStories;
        $this->view->build        = $build;
        $this->view->buildStories = empty($build->stories) ? array() : $this->story->getByList($build->stories);
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType   = $browseType;
        $this->view->param        = $param;
        $this->view->pager        = $pager;
        $this->display();
    }
}
