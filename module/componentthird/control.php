<?php
class componentthird extends control
{
    /**
     * 列表展示
     * shixuyang
     * @param $browseType
     * @param $param
     * @param $orderBy
     * @param $recTotal
     * @param $recPerPage
     * @param $pageID
     * @return void
     */
    public function browse($browseType = 'all', $param = 0, $orderBy = 'category_asc,id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadLang('component');
        $browseType = strtolower($browseType);
        //搜索页签的值
        $labelList = array('all' => '所有') + $this->lang->component->thirdcategoryList;
        //搜索框的值
        $this->config->componentthird->search['params']['category']['values'] = array('' => '') + $this->lang->component->thirdcategoryList;
        $this->config->componentthird->search['params']['developLanguage']['values'] = array('' => '') + $this->lang->component->developLanguageList;
        $this->config->componentthird->search['params']['chineseClassify']['values'] = array('' => '') + $this->lang->component->chineseClassifyList;
        $this->config->componentthird->search['params']['englishClassify']['values'] = array('' => '') + $this->lang->component->englishClassifyList;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('componentthird', 'browse', "browseType=bySearch&param=myQueryID");
        $this->componentthird->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('componentthirdList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('componentthirdHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $componentthirdList = $this->componentthird->getList($browseType, $queryID, $orderBy, $pager);
        $code = $recPerPage*($pageID-1)+1;
        foreach($componentthirdList as $item){
            $item->code = $code;
            $code = $code + 1;
        }
        $this->view->title      = $this->lang->componentthird->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->datas   = $componentthirdList;
        $this->view->labelList   = $labelList;
        $this->display();
    }

    /**
     * 新建第三方组件
     * @return void
     */
    public function create(){
        $this->app->loadLang('component');
        if($_POST){
            $id = $this->componentthird->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //操作记录
            $actionID = $this->loadModel('action')->create('componentthird', $id, 'created');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }
        $this->view->title       = $this->lang->componentthird->create;
        $this->display();
    }

    /**
     * 编辑功能
     * shixuyang
     * @return void
     */
    public function edit($componentthirdID = 0){
        if($_POST){
            $changes = $this->componentthird->update($componentthirdID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('componentthird', $componentthirdID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }

        $componentthird = $this->componentthird->getByID($componentthirdID);
        $this->view->title       = $this->lang->componentthird->edit;
        $this->view->componentthird = $componentthird;
        $this->display();
    }

    /**
     * 详情页面.
     * shixuyang
     * @param  int    $componentID
     * @access public
     * @return void
     */
    public function view($componentthirdID = 0)
    {
        $componentthird = $this->componentthird->getByID($componentthirdID);

        $this->view->title       = $this->lang->componentthird->view;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $this->view->actions     = $this->loadmodel('action')->getList('componentthird', $componentthirdID);
        $this->view->componentthird = $componentthird;
        $this->view->detailDatas       = $this->componentthird->getVersions($componentthirdID);
        if(!empty($componentthird->componentId)){
            $component = $this->loadModel('component')->getByID($componentthird->componentId);
            $this->view->component = $component;
        }
        $this->display();
    }

    /**
     * 编辑基础信息功能
     * shixuyang
     * @return void
     */
    public function editinfo($componentthirdID = 0){
        if($_POST){
            $changes = $this->componentthird->updateinfo($componentthirdID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('componentthird', $componentthirdID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $componentthird = $this->componentthird->getByID($componentthirdID);
        $this->view->title       = $this->lang->componentthird->editinfo;
        $this->view->componentthird = $componentthird;
        $this->view->versionList       = $this->componentthird->getVersionPairs($componentthirdID);
        $this->display();
    }

    /**
     * 新建版本
     * @return void
     */
    public function createversion($componentthirdID = 0){
        $this->app->loadLang('component');
        if($_POST){
            $id = $this->componentthird->createversion($componentthirdID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //操作记录
            $actionID = $this->loadModel('action')->create('componentthird', $componentthirdID, 'createversion',$this->lang->componentthird->version.'：'.$this->post->version);
            $this->loadModel('action')->create('componentversion', $componentthirdID, 'createversion',$this->lang->componentthird->version.'：'.$this->post->version);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);

        }
        $this->view->title       = $this->lang->componentthird->createversion;
        $this->display();
    }

    /**
     * 编辑版本
     * shixuyang
     * @return void
     */
    public function editversion($versionID = 0){
        if($_POST){
            $changes = $this->componentthird->editversion($versionID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $version = $this->componentthird->getVersionByID($versionID);
                $actionID = $this->loadModel('action')->create('componentthird', $version->componentReleaseId, 'editversion',$this->lang->componentthird->version.'：'.$version->version);
                $this->loadModel('action')->create('componentversion', $version->componentReleaseId, 'editversion',$this->lang->componentthird->version.'：'.$version->version);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $version = $this->componentthird->getVersionByID($versionID);
        $this->view->title       = $this->lang->componentthird->editversion;
        $this->view->version = $version;
        $this->display();
    }

    /**
     * 删除版本
     * @param $modifyID
     * @return void
     */
    public function deleteversion($versionID = 0)
    {
        $version = $this->componentthird->getVersionByID($versionID);
        if(!empty($_POST))
        {
            $this->dao->update(TABLE_COMPONENT_VERSION)->set('deleted')->eq('1')->where('id')->eq($versionID)->exec();
            $this->dao->update(TABLE_COMPONENT_RELEASE)->set('recommendVersion')->eq('')->where('recommendVersion')->eq($versionID)->exec();
            $actionID = $this->loadModel('action')->create('componentthird', $version->componentReleaseId, 'deleteversion', $this->lang->componentthird->version.'：'.$version->version.'<br>'.$this->lang->componentthird->comment.'：'.$this->post->comment);
            $this->loadModel('action')->create('componentversion', $version->componentReleaseId, 'deleteversion', $this->lang->componentthird->version.'：'.$version->version.'<br>'.$this->lang->componentthird->comment.'：'.$this->post->comment);

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->version = $version;
        $this->display();
    }

    /**
     * 删除组件
     * @param $modifyID
     * @return void
     */
    public function delete($componentthirdID = 0)
    {
        $componentthird = $this->componentthird->getByID($componentthirdID);
        $componentthirdList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentReleaseId')->eq($componentthird->id)->fetchAll();
        $componentthird->usedNum = count($componentthirdList);
        if(!empty($_POST))
        {
            if($componentthird->usedNum == 0){
                $this->dao->update(TABLE_COMPONENT_RELEASE)->set('deleted')->eq('1')->where('id')->eq($componentthirdID)->exec();
                $actionID = $this->loadModel('action')->create('componentthird', $componentthirdID, 'deleted', $this->post->comment);

                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';
                $this->send($response);
            }else{
                $response['result']  = 'fail';
                $response['message'] = $this->lang->componentthird->deleteTip;
                $response['locate']  = 'parent';
                $this->send($response);
            }

        }
        $this->view->componentthird = $componentthird;
        $this->display();
    }

    /**
     * 通过版本得到发布时间
     * @param $id
     * @return void
     */
    public function ajaxGetVersion($id,$componentthirdID){
        $version = $this->componentthird->getVersionByName($id,$componentthirdID);
        if(empty($version)){
            $version = new stdClass();
            $version->updatedDate = '';
        }
        echo $version->updatedDate;
    }

    /**
     * Project: chengfangjinke
     * Desc: 导出列表页数据 Excel
     * t_jinzhuliang
     */
    public function export($orderBy = 'id_desc', $browseType = 'all')
    {
        /* format the fields of every data in order to export data. */
        $this->app->loadLang('component');
        unset($this->lang->exportTypeList['selected']);
        $this->lang->exportTypeList['all'] = '全部查询结果';
        if($_POST)
        {
            $this->loadModel('file');
            $componentthirdLang   = $this->lang->componentthird;
            $componentthirdConfig = $this->config->componentthird;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $componentthirdConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($componentthirdLang->$fieldName) ? $componentthirdLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();

            if($this->session->componentthirdOnlyCondition)
            {

                $datas = $this->dao->select('*')->from(TABLE_COMPONENT_RELEASE)->where($this->session->componentthirdOnlyCondition)
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('type')->eq('third')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy('category_asc,id_desc')->fetchAll('id');
            }
            else
            {

                $stmt = $this->dbh->query($this->session->componentthirdQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
                while($row = $stmt->fetch()) $datas[$row->id] = $row;
            }

            $depts      = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadModel('user')->getPairs('noletter|noclosed');
            $i = 1;
            foreach ($datas as $k=>$data)
            {
                //组件

                $data->code = $i;
                $i++;
                $data->chineseClassify = zget($this->lang->component->chineseClassifyList,$data->chineseClassify);
                $data->englishClassify = zget($this->lang->component->englishClassifyList,$data->englishClassify);
                $data->category = zget($this->lang->component->thirdcategoryList, $data->category);
                $data->status = zget($this->lang->component->thirdStatusList,$data->status);
                $data->developLanguage = zget($this->lang->component->developLanguageList,$data->developLanguage);
                $data->maintainerDept = zget($depts,$data->maintainerDept);
                $data->maintainer = zget($users,$data->maintainer);

                $componentthirdList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentReleaseId')->eq($data->id)->fetchAll();
                $data->usedNum = count($componentthirdList);

            }


            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'componentthird');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->componentthird->exportExcel.'-'.time();
        $this->view->allExportFields = $this->config->componentthird->list->exportFields;
        $this->view->customExport    = false;

        $this->display();
    }
}
