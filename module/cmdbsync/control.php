<?php
class cmdbsync extends control
{
    /**
     * cmdb同步列表
     *
     */
    public function browse($browseType = 'all', $param = 0,  $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->view->title = $this->lang->cmdbsync->browse;
        $browseType = strtolower($browseType);
        $users = array('' => '') + $this->loadModel('user')->getPairs('noletter');
        $appList =  $this->loadModel('application')->getPairsAll();
        $this->config->cmdbsync->search['params']['app']['values'] = array('' => '') +$appList;
        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('cmdbsync', 'browse', "browseType=bySearch&param=myQueryID");
        $this->cmdbsync->buildSearchForm($queryID, $actionURL);
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $data = $this->cmdbsync->getList($browseType, $queryID, $orderBy, $pager);
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->users = $users;
        $this->view->appList = $appList;
        $this->view->data = $data;
        $this->display();
    }

    /**
     * 导出
     *
     * @param string $orderBy
     */
    public function export($orderBy = 'id_desc')
    {
        if($_POST)
        {
            $cmdbsyncLang   = $this->lang->cmdbsync;
            $cmdbsyncConfig = $this->config->cmdbsync;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $cmdbsyncConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($cmdbsyncLang->$fieldName) ? $cmdbsyncLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            /* Get changes. */
            $cmdbsyncs = array();
            if($this->session->cmdbsyncOnlyCondition)
            {
                $cmdbsyncs = $this->dao->select('*')->from(TABLE_CMDBSYNC)->where($this->session->cmdbsyncQueryCondition)
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)->fetchAll('id');

            }
            else
            {
                $stmt = $this->dbh->query($this->session->cmdbsyncQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $cmdbsyncs[$row->id] = $row;
            }

            if($cmdbsyncs){
                $users = $this->loadModel('user')->getPairs('noletter');

                //投产系统
                $appList =  $this->loadModel('application')->getPairsAll();

                //投产
                foreach($cmdbsyncs as $val) {
                    $val->app           = zmget($appList, $val->app);
                    $val->status        = zget($cmdbsyncLang->statusList, $val->status);
                    $val->type        = zget($cmdbsyncLang->typeList, $val->type);
                    $val->dealUser      = zmget($users, $val->dealUser);
                }

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $cmdbsyncs);
            $this->post->set('kind', 'cmdbsync');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->view->fileName        = $this->lang->cmdbsync->exportName;
        $this->view->allExportFields = $this->config->cmdbsync->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * 详情页
     * @param $id
     * @return void
     */
    public function view($id){
        $this->loadModel('application');
        $this->view->title = $this->lang->cmdbsync->view;
        $cmdbsyncInfo = $this->cmdbsync->getByID($id);
        $info = json_decode($cmdbsyncInfo->info);
        $cmdbsyncInfo->addInfo = $info->addInfo;
        $cmdbsyncInfo->updateInfo = $info->updateInfo;
        $cmdbsyncInfo->deleteInfo = $info->deleteInfo;
        $this->view->cmdbsyncInfo = $cmdbsyncInfo;
        $users =  $this->loadModel('user')->getPairs('noletter');
        $this->view->users = $users;
        $this->view->actions  = $this->loadModel('action')->getList('cmdbsync', $id);
        $putproductionInfo = $this->dao->select("*")
            ->from(TABLE_PUTPRODUCTION)
            ->where('id')->eq($cmdbsyncInfo->putproductionId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        $this->view->putproductionInfo = $putproductionInfo;
        $this->lang->application->baseapplicationList = $this->loadModel('baseapplication')->getPairs();
        $this->display();
    }

    /**
     * 处理问题单
     * @param $id
     * @return void
     */
    public function deal($id)
    {
        if($_POST) {
            $this->cmdbsync->deal($id);

            if(dao::isError()) {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //$actionID = $this->loadModel('action')->create('cmdbsync', $id, 'deal', '');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $cmdbsyncInfo = $this->cmdbsync->getByID($id);
        $this->view->title       = $this->lang->cmdbsync->deal;
        $this->view->cmdbsyncInfo = $cmdbsyncInfo;
        $this->display();
    }

    public function repush($id){
        $updateInfo = array();
        $updateInfo['dealUser'] = '';
        $updateInfo['sendStatus'] = 'tosend';
        $updateInfo['failNum'] = '0';
        $this->dao->update(TABLE_CMDBSYNC)->data($updateInfo)->where('id')->eq($id)->exec();
        $actionID = $this->loadModel('action')->create('cmdbsync', $id, 'repush', '');
        die(js::reload());
    }

    public function getUnPushedAndPush(){
        $res = $this->cmdbsync->getUnPushedAndPush();
        echo json_encode($res);
    }
}