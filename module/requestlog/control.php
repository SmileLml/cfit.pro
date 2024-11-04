<?php
class requestlog extends control
{
    /**
     * Browse the request logs with third-party interfaces.
     * 浏览与第三方接口的请求日志。
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
        $actionURL = $this->createLink('requestlog', 'browse', 'browseType=bysearch&queryID=myQueryID');
        $this->requestlog->buildSearchForm($actionURL, $queryID);

        /* Pass the fetched data to the page for display. */
        /* 将获取的数据传递到页面展示。*/
        $this->view->title      = $this->lang->requestlog->log;
        $this->view->position[] = $this->lang->requestlog->log;

        $this->view->logList    = $this->requestlog->getLogList($browseType, $queryID, $orderBy, $pager);
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;

        $this->display();
    }

    /**
     * Get request parameter information.
     *
     * @param int $id
     * @access public
     * @return void
     */
    public function ajaxGetParams($id)
    {
        $this->view->params = $this->requestlog->getByID($id);
        $this->display();
    }

    /**
     * Get the response result.
     *
     * @param int $id
     * @access public
     * @return void
     */
    public function ajaxGetResponse($id)
    {
        $this->view->response = $this->requestlog->getByID($id);
        $this->display();
    }

    /**
     * 消息重推
     */
    public function ajaxRepushMsg()
    {
        $id = $_POST['id'];
        $url = $this->config->global->pushMessageUrl;
        $headers[] = 'X-AppId: ' . $this->config->global->pushMsgAppid;
        $headers[] = 'X-AppSecret: ' . $this->config->global->pushMsgAppSecret;
        $info = $this->requestlog->getByID($id);
        $object = 'messagecenter';
        $objectType = 'pushMobileMsg';
        $method = 'POST';
        $pushData = json_decode($info->params,true);
        $this->loadModel('mobileapi')->saveLog(json_encode($pushData,JSON_UNESCAPED_UNICODE),'mobileapi','pushMsg');
        $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
        $code = 500;
        if (!empty($result)) {
            $res = json_decode($result,true);
            if ($res['msgCode'] != 200){
                $this->requestlog->saveRequestLog($url, $object, $objectType, $method, $pushData, $result, 'fail', '', '');
            }else{
                $code = 200;
                $this->dao->update(TABLE_REQUESTLOG)->set('`status`')->eq('success')->where('id')->eq($id)->exec();
            }
        }else{
            // 网络不通
            $this->requestlog->saveRequestLog($url, $object, $objectType, $method, $pushData, $result, 'fail', '', '');
        }
        $this->loadModel('mobileapi')->saveLog($result,'mobileapi','pushMsg');
        echo $code;
    }
}
