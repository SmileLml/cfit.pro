<?php
class componentpublic extends control
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
    public function browse($browseType = 'all', $param = 0, $orderBy = 'createTime_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadLang('component');
        $browseType = strtolower($browseType);
        //搜索框的值
        $depts = $this->loadModel('dept')->getOptionMenu();
        $depts[0] = '';
        $this->config->componentpublic->search['params']['maintainerDept']['values'] = $depts;

        $this->config->componentpublic->search['params']['category']['values'] = array('' => '') + $this->lang->component->categoryList;
        $this->config->componentpublic->search['params']['developLanguage']['values'] = array('' => '') + $this->lang->component->developLanguageList;
        $this->config->componentpublic->search['params']['status']['values'] = array('' => '') + $this->lang->component->publishStatusList;

        $queryID = ($browseType == 'bysearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('componentpublic', 'browse', "browseType=bySearch&param=myQueryID");
        $this->componentpublic->buildSearchForm($queryID, $actionURL);

        /* 设置详情页面返回的url连接。*/
        $this->session->set('componentpublicList', $this->app->getURI(true));
        $this->session->set('common_back_url', $this->app->getURI(true),'backlog');
        $this->session->set('componentpublicHistory', $this->app->getURI(true));

        /* 构建pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $componentpublicList = $this->componentpublic->getList($browseType, $queryID, $orderBy, $pager);
        $code = $recPerPage*($pageID-1)+1;
        foreach($componentpublicList as $item){
            $item->code = $code;
            $code = $code + 1;
        }
        $this->view->title      = $this->lang->componentpublic->common;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->view->param      = $param;
        $this->view->browseType = $browseType;
        $this->view->datas   = $componentpublicList;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->display();
    }

    /**
     * 新建公共组件
     * @return void
     */
    public function create(){
        $this->app->loadLang('component');
        if($_POST){
            $id = $this->componentpublic->create();
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //操作记录
            $actionID = $this->loadModel('action')->create('componentpublic', $id, 'created');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }
        $this->view->title       = $this->lang->componentpublic->create;
        //维护人
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->display();
    }

    /**
     * 编辑功能
     * shixuyang
     * @return void
     */
    public function edit($componentpublicID = 0){
        if($_POST){
            $changes = $this->componentpublic->update($componentpublicID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('componentpublic', $componentpublicID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = inlink('browse');
            $this->send($response);

        }

        $componentpublic = $this->componentpublic->getByID($componentpublicID);
        $this->view->title       = $this->lang->componentpublic->edit;
        $this->view->componentpublic = $componentpublic;
        //维护人
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->display();
    }

    /**
     * 详情页面.
     * shixuyang
     * @param  int    $componentID
     * @access public
     * @return void
     */
    public function view($componentpublicID = 0)
    {
        $componentpublic = $this->componentpublic->getByID($componentpublicID);
        $componentpublic = $this->loadModel('file')->replaceImgURL($componentpublic, '');

        $this->view->title       = $this->lang->componentpublic->view;
        $this->view->users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $this->view->actions     = $this->loadmodel('action')->getList('componentpublic', $componentpublicID);
        $this->view->componentpublic = $componentpublic;
        $this->view->depts       = $this->loadModel('dept')->getOptionMenu();
        $this->view->detailDatas       = $this->componentpublic->getVersions($componentpublicID);

        if(!empty($componentpublic->componentId)){
            $component = $this->loadModel('component')->getByID($componentpublic->componentId);
            $this->view->component = $component;
        }
        $this->view->relationComponentList = $this->loadModel("component")->getRelationComponent($componentpublicID);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * 编辑基础信息功能
     * shixuyang
     * @return void
     */
    public function editinfo($componentpublicID = 0){
        if($_POST){
            $changes = $this->componentpublic->updateinfo($componentpublicID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('componentpublic', $componentpublicID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $componentpublic = $this->componentpublic->getByID($componentpublicID);
        $this->view->title       = $this->lang->componentpublic->editinfo;
        $this->view->componentpublic = $componentpublic;
        //维护人
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->versionList       = $this->componentpublic->getVersionPairs($componentpublicID);
        $this->display();
    }

    /**
     * 新建版本
     * @return void
     */
    public function createversion($componentpublicID = 0){
        $this->app->loadLang('component');
        if($_POST){
            $id = $this->componentpublic->createversion($componentpublicID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            //操作记录
            $actionID = $this->loadModel('action')->create('componentpublic', $componentpublicID, 'createversion',$this->lang->componentpublic->version.'：'.$this->post->version);
            $this->loadModel('action')->create('componentversion', $componentpublicID, 'createversion',$this->lang->componentpublic->version.'：'.$this->post->version);

            $response['result']  = 'success';
            $response['message'] = $this->lang->componentpublic->submitSuccess;
            $response['locate']  = 'parent';
            $this->send($response);

        }
        $this->view->title       = $this->lang->componentpublic->createversion;
        $this->display();
    }

    /**
     * 版本详情页面.
     * shixuyang
     * @param  int
     * @access public
     * @return void
     */
    public function viewversion($versionID = 0)
    {
        $version = $this->componentpublic->getVersionByID($versionID);
        $this->view->title       = $this->lang->componentpublic->viewversion;
        $this->view->version = $version;
        $this->display();
    }

    /**
     * 编辑版本
     * shixuyang
     * @return void
     */
    public function editversion($versionID = 0){
        if($_POST){
            $changes = $this->componentpublic->editversion($versionID);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($changes)
            {
                $version = $this->componentpublic->getVersionByID($versionID);
                $actionID = $this->loadModel('action')->create('componentpublic', $version->componentReleaseId, 'editversion',$this->lang->componentpublic->version.'：'.$version->version);
                $this->loadModel('action')->create('componentversion', $version->componentReleaseId, 'editversion',$this->lang->componentpublic->version.'：'.$version->version);
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        $version = $this->componentpublic->getVersionByID($versionID);
        $this->view->title       = $this->lang->componentpublic->editversion;
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
        $version = $this->componentpublic->getVersionByID($versionID);

        $componentpublicList = $this->dao->select('*')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('deleted')->eq('0')
            ->andWhere('componentId')->eq($version->componentReleaseId)
            ->andWhere('componentVersion')->eq($version->id)
            ->fetchAll();
        $version->usedNum = count($componentpublicList);
        if(!empty($_POST))
        {
            if($version->usedNum == 0){
                $this->dao->update(TABLE_COMPONENT_VERSION)->set('deleted')->eq('1')->where('id')->eq($versionID)->exec();
                $actionID = $this->loadModel('action')->create('componentpublic', $version->componentReleaseId, 'deleteversion', $this->lang->componentpublic->version.'：'.$version->version.'<br>'.$this->lang->componentpublic->comment.'：'.$this->post->comment);
                $this->loadModel('action')->create('componentversion', $version->componentReleaseId, 'deleteversion', $this->lang->componentpublic->version.'：'.$version->version.'<br>'.$this->lang->componentpublic->comment.'：'.$this->post->comment);

                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';
                $this->send($response);

            }else{
                $response['result']  = 'fail';
                $response['message'] = $this->lang->componentpublic->deleteVersionTip;
                $response['locate']  = 'parent';
                $this->send($response);
            }

        }
        $this->view->version = $version;
        $this->display();
    }

    /**
     * 删除组件
     * @param $modifyID
     * @return void
     */
    public function delete($componentpublicID = 0)
    {
        $componentpublic = $this->componentpublic->getByID($componentpublicID);
        $componentpublicList = $this->dao->select('*')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentId')->eq($componentpublic->id)->fetchAll();
        $componentpublic->usedNum = count($componentpublicList);
        if(!empty($_POST))
        {
            if($componentpublic->usedNum == 0){
                $this->dao->update(TABLE_COMPONENT_RELEASE)->set('deleted')->eq('1')->where('id')->eq($componentpublicID)->exec();
                $actionID = $this->loadModel('action')->create('componentpublic', $componentpublicID, 'deleted', $this->post->comment);

                $response['result']  = 'success';
                $response['message'] = $this->lang->saveSuccess;
                $response['locate']  = 'parent';
                $this->send($response);
            }else{
                $response['result']  = 'fail';
                $response['message'] = $this->lang->componentpublic->deleteTip;
                $response['locate']  = 'parent';
                $this->send($response);
            }

        }
        $this->view->componentpublic = $componentpublic;
        $this->display();
    }

    /**
     * 通过用户得到部门
     * @param $id
     * @return void
     */
    public function ajaxGetDeptByUser($id){
        $user = $this->loadModel('user')->getById($id);
        echo $user->dept;
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
            $componentpublicLang   = $this->lang->componentpublic;
            $componentpublicConfig = $this->config->componentpublic;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $componentpublicConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($componentpublicLang->$fieldName) ? $componentpublicLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            /* Get datas. */
            $datas = array();

            if($this->session->componentpublicOnlyCondition)
            {

                $datas = $this->dao->select('*')->from(TABLE_COMPONENT_RELEASE)->where($this->session->componentpublicOnlyCondition)
                    ->andWhere('deleted')->eq('0')
                    ->andWhere('type')->eq('public')
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy('level_asc,id_desc')->fetchAll('id');
            }
            else
            {

                $stmt = $this->dbh->query($this->session->componentpublicQueryCondition . ($this->post->exportType == 'selected' ? " AND id IN({$this->cookie->checkedItem})" : '') . " ORDER BY " . strtr($orderBy, '_', ' '));
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
                $data->level = zget($this->lang->component->levelList, $data->level);
                $data->category = zget($this->lang->component->categoryList, $data->category);
                $data->status = zget($this->lang->component->publishStatusList,$data->status);
                $data->maintainerDept = zget($depts,$data->maintainerDept);
                $data->maintainer = zget($users,$data->maintainer);
                $data->developLanguage = zget($this->lang->component->developLanguageList,$data->developLanguage);
                $componentpublicList = $this->dao->select('*')->from(TABLE_COMPONENT_ACCOUNT)->where('deleted')->eq('0')->andWhere('componentReleaseId')->eq($data->id)->fetchAll();
                $data->usedNum = count($componentpublicList);
                $data->functionDesc = htmlspecialchars_decode($data->functionDesc);    //处理【功能说明】中的图片和表格等
                $data->functionDesc = str_replace("&nbsp;","",$data->functionDesc);//将空格替换成空
                $data->functionDesc = strip_tags($data->functionDesc);

            }


            $this->post->set('fields', $fields);
            $this->post->set('rows', $datas);
            $this->post->set('kind', 'componentpublic');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName        = $this->lang->componentpublic->exportExcel.'-'.time();
        $this->view->allExportFields = $this->config->componentpublic->list->exportFields;
        $this->view->customExport    = false;

        $this->display();
    }
    /**
     * 需求建议
     * @param int $componentpublicID
     */
    public function demandAdvice($componentpublicID = 0){
        $componentpublic = $this->componentpublic->getByID($componentpublicID);
        $this->locate($this->createLink('cjdpf','create'));
       /* $this->view->publicComponentName = $componentpublic->name;
        $this->view->assignedTo = $componentpublic->maintainer;
        $this->locate($this->createLink('cjdpf','create','publicComponentName='.urldecode($componentpublic->name).'&assignedTo='.$componentpublic->maintainer));*/

    }
}
