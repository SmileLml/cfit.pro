<?php
class productlineModel extends model
{
    /**
     * Get productline list.
     * @param  string  $browseType 
     * @param  string  $orderBy 
     * @param  object  $pager 
     * @access public
     * @return void
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $productlineQuery = '';
        if($browseType == 'bysearch')
        {    
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('productlineQuery', $query->sql);
                $this->session->set('productlineForm', $query->form);
            }

            if($this->session->productlineQuery == false) $this->session->set('productlineQuery', ' 1 = 1');

            $productlineQuery = $this->session->productlineQuery;
        }

        $productlines = $this->dao->select('*')->from(TABLE_PRODUCTLINE)
            ->where('deleted')->eq(0)
            // ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($productlineQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        return $productlines;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:57
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @return mixed
     */
    public function getPairs()
    {
        $lines = $this->dao->select('id,name')->from(TABLE_PRODUCTLINE)
            ->where('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchPairs();
        return $lines;
    }

    /**
     * Build search form.
     * 
     * @param  int    $queryID 
     * @param  string $actionURL 
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->productline->search['actionURL'] = $actionURL;
        $this->config->productline->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->productline->search);
    }

    /**
     * Create a productline.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        if(!$this->post->depts)
        {
            dao::$errors['depts'] = $this->lang->productline->deptEmpty;
            return;
        }

        $line = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->join('depts', ',')
            ->remove('uid,files,labels,comment')
            ->stripTags($this->config->productline->editor->create['id'], $this->config->allowedTags)
            ->get();

        $this->dao->insert(TABLE_PRODUCTLINE)->data($line)->autoCheck()->batchCheck($this->config->productline->create->requiredFields, 'notempty')->exec();

        $lineID = 0;
        if(!dao::isError())
        {
            $lineID = $this->dao->lastInsertID();
        }

        return $lineID;
    }

    /**
     * Get productline.
     * 
     * @param  int    $lineID
     * @access public
     * @return void
     */
    public function getByID($lineID)
    {
        $productline = $this->dao->findByID($lineID)->from(TABLE_PRODUCTLINE)->fetch();
        $productline->files = $this->loadModel('file')->getByObject('productline', $lineID);

        return $productline;
    }

    /**
     * Update productline.
     * 
     * @access int $lineID 
     * @access public
     * @return void
     */
    public function update($lineID)
    {
        if(!$this->post->depts)
        {
            dao::$errors['depts'] = $this->lang->productline->deptEmpty;
            return;
        }

        $oldApp      = $this->getByID($lineID);
        $productline = fixer::input('post')
            ->join('depts', ',')
            ->remove('uid,files,labels,comment')
            ->stripTags($this->config->productline->editor->edit['id'], $this->config->allowedTags)
            ->get();

        $productline = $this->loadModel('file')->processImgURL($productline, $this->config->productline->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_PRODUCTLINE)->data($productline)->autoCheck()
            ->batchCheck($this->config->productline->edit->requiredFields, 'notempty')
            ->where('id')->eq($lineID)
            ->exec();

        $this->file->updateObjectID($this->post->uid, $lineID, 'productline');

        return common::createChanges($oldApp, $productline);
    }
}
