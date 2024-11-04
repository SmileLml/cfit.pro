<?php
class maillog extends control
{
    /**
     * Browse the request logs with third-party interfaces.
     * 浏览邮件发送日志
     *
     * @param  string $browseType
     * @param  int    $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Load the paging tool class and initialize the paging object. */
        /* 加载分页工具类，初始化分页对象。*/
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Build the search form. */
        /* 构建搜索表单所需数据。*/
        $queryID   = ($browseType == 'bysearch') ? (int)$param : 0;
        $actionURL = $this->createLink('maillog', 'browse', 'browseType=bysearch&queryID=myQueryID');
        $this->maillog->buildSearchForm($actionURL, $queryID);

        /* Pass the fetched data to the page for display. */
        /* 将获取的数据传递到页面展示。*/
        $this->view->title      = $this->lang->maillog->log;
        $this->view->position[] = $this->lang->maillog->log;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');

        $this->view->logList    = $this->maillog->getLogList($browseType, $queryID, $orderBy, $pager);
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->actionURL  = $actionURL;
        $this->view->orderBy      = $orderBy;

        $this->display();
    }

    /**
     * 导出
     *
     * @param string $orderBy
     */
    public function export($orderBy = 'id_desc'){
        if($_POST) {
            $maillogLang = $this->lang->maillog;
            $maillogConfig = $this->config->maillog;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $maillogConfig->list->exportFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($maillogLang->$fieldName) ? $maillogLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get changes. */
            $maillogs = array();
            if ($this->session->maillogOnlyCondition) {
                $maillogs = $this->dao->select('*')->from(TABLE_MAIL_LOG)->where($this->session->maillogQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');

            } else {
                $stmt = $this->dbh->query($this->session->maillogQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while ($row = $stmt->fetch()) $maillogs[$row->id] = $row;
            }
            if ($maillogs) {

                $users = $this->loadModel('user')->getPairs('noletter');

                foreach ($maillogs as $val) {
                    $val->status     = zget($maillogLang->statusList, $val->status);
                    $val->objectType = zget($maillogLang->objectTypeList, $val->objectType,  $val->objectType);
                    $val->toList     = zmget($users, $val->toList, ',', $val->toList);
                    $val->ccList     = zmget($users, $val->ccList, ',', $val->ccList);
                    $val->createdBy = zget($users, $val->createdBy);
                    $val->error = strip_tags($val->error);
                }
                $this->post->set('fields', $fields);
                $this->post->set('rows', $maillogs);
                $this->post->set('kind', 'maillog');
                $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            }
        }
        $this->view->fileName = $this->lang->maillog->exportName;
        $this->view->allExportFields = $this->config->maillog->list->exportFields;
        $this->view->customExport = true;
        $this->display();
    }

    /**
     * Get request parameter information.
     *
     * @param int $id
     * @access public
     * @return void
     */
    public function ajaxGetContent($id)
    {
        $this->view->log = $this->maillog->getByID($id);
        $this->display();
    }

    /**
     * Get the response result.
     *
     * @param int $id
     * @access public
     * @return void
     */
    public function ajaxGetUserInfo($id)
    {
        $this->view->log = $this->maillog->getByID($id);
        $this->display();
    }
    public function ajaxGetError($id){
        $this->view->log = $this->maillog->getByID($id);
        $this->display();
    }
}
