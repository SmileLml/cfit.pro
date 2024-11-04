<?php
class epgprocessModel extends model
{
    /**
     * Get EPG list.
     * @param  string  $browseType
     * @param  string  $orderBy
     * @param  object  $pager
     * @access public
     * @return void
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $epgprocessQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('epgprocessQuery', $query->sql);
                $this->session->set('epgprocessForm',  $query->form);
            }

            if($this->session->epgprocessQuery == false) $this->session->set('epgprocessQuery', ' 1 = 1');

            $epgprocessQuery = $this->session->epgprocessQuery;
        }
        return $this->dao->select('*')->from(TABLE_EPGPROCESS)
            ->beginIF($browseType == 'bysearch')->where($epgprocessQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get EPG.
     *
     * @param  int    $processID
     * @access public
     * @return void
     */
    public function getByID($processID)
    {
        $processImprove = $this->dao->findByID($processID)->from(TABLE_EPGPROCESS)->fetch();
        $processImprove->files = $this->loadModel('file')->getByObject('epgprocess', $processID);

        return $processImprove;
    }

    /**
     * Create a EPG.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        $processImprove = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->remove('uid,files,labels,comment')
            ->stripTags($this->config->epgprocess->editor->create['id'], $this->config->allowedTags)
            ->get();

        $this->dao->insert(TABLE_EPGPROCESS)->data($processImprove)->autoCheck()->batchCheck($this->config->epgprocess->create->requiredFields, 'notempty')->exec();

        return $this->dao->lastInsertID();
    }

    /**
     * Update EPG.
     *
     * @access int $processID
     * @access public
     * @return void
     */
    public function update($processID)
    {
        $oldProcess = $this->getByID($processID);
        $processImprove = fixer::input('post')
            ->remove('uid,files,labels,comment')
            ->stripTags($this->config->epgprocess->editor->edit['id'], $this->config->allowedTags)
            ->get();

        $this->dao->update(TABLE_EPGPROCESS)->data($processImprove)->autoCheck()->batchCheck($this->config->epgprocess->edit->requiredFields, 'notempty')->where('id')->eq($processID)->exec();

        return common::createChanges($oldProcess, $processImprove);
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
        $this->config->epgprocess->search['actionURL'] = $actionURL;
        $this->config->epgprocess->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->epgprocess->search);
    }
}
