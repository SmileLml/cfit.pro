<?php
class maillogModel extends model
{
    /**
     * Obtain request log information by id.
     * 通过id获取邮件日志信息。
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function getByID($id)
    {
        return $this->dao->select('*')->from(TABLE_MAIL_LOG)->where('id')->eq($id)->fetch();
    }

    /**
     * Configuration parameters required to handle paging.
     * 处理分页所需的配置参数。
     *
     * @param  string $actionURL
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return void
     */
    public function getLogList($browseType = 'all', $queryID, $orderBy, $pager)
    {
        /* Get the query criteria from session. */
        /* 从session中获取查询条件。*/
        $maillogQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('maillogQuery', $query->sql);
                $this->session->set('maillogForm', $query->form);
            }
            if($this->session->maillogQuery == false) $this->session->set('maillogQuery', ' 1=1');
            $maillogQuery = $this->session->maillogQuery;
        }

        $result = $this->dao->select('*')->from(TABLE_MAIL_LOG)
            ->where('id')->gt(0)
            ->andWhere('deleted')->eq('0')
            ->beginIF($browseType == 'bysearch')->andWhere($maillogQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'maillog', $browseType != 'bysearch');
        return $result;
    }

    /**
     * Configuration parameters required to handle paging.
     * 处理分页所需的配置参数。
     *
     * @param  string $actionURL
     * @param  int    $queryID
     * @access public
     * @return void
     */
    public function buildSearchForm($actionURL, $queryID)
    {
        $this->config->maillog->search['actionURL'] = $actionURL;
        $this->config->maillog->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->maillog->search);
    }

    /* 判断请求方式和参数是否合规。*/
    public function judgeRequestMode($logID = 0)
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') $this->response('fail', $this->lang->requestlog->notPostRequest, array(), $logID);
        if(empty($_POST)) $this->response('fail', $this->lang->requestlog->parameterEmpty, array(), $logID);
    }


}
