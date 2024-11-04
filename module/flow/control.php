<?php
/**
 * The control file of flow of ZDOO.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     商业软件，非开源软件
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     flow
 * @version     $Id$
 * @link        http://www.zdoo.com
 */
class flow extends control
{
    /**
     * Browse record list of a flow.
     *
     * @param  string $module
     * @param  string $mode         browse | bysearch
     * @param  int    $label
     * @param  string $category
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($module, $mode = 'browse', $label = 0, $category = '', $orderBy = '', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $action = 'browse';
        $label  = (int)$label;
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);
        $this->flow->setSearchParams($flow);

        $labels = $this->loadModel('workflowlabel', 'flow')->getList($module);
        if(!$label && $mode == 'browse')
        {
            reset($labels);
            $label = current($labels)->id;
        }

        if($mode == 'browse') $this->flow->checkLabel($flow, array_keys($labels), $label);

        $categories      = $this->flow->getCategories($module, $mode, $label, $orderBy, $recTotal, $recPerPage, $pageID);
        $currentCategory = reset($categories);
        $categoryQuery   = '';
        $categoryValue   = 0;
        if($category)
        {
            list($categoryType, $categoryQuery, $categoryValue) = $this->flow->checkCategory($module, $category);

            if(isset($categories[$categoryType])) $currentCategory = $categories[$categoryType];
        }

        $this->app->loadClass('pager', $static = true);
        $pager    = new pager($recTotal, $recPerPage, $pageID);
        $dataList = $this->flow->getDataList($flow, $mode, $label, $categoryQuery, 0, $orderBy, $pager);
        $fields   = $this->setFields($flow, $action, $dataList);

        $browseLink = $this->createLink($module, 'browse', "label=$label&category=$category&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID");
        $this->session->set('flowList', $browseLink);

        $this->view->dataList        = $dataList;
        $this->view->moduleMenu      = $this->flow->getModuleMenu($flow, $labels, $categories);
        $this->view->menuActions     = $this->flow->buildMenuActions($flow);
        $this->view->batchActions    = $this->flow->buildBatchActions($module);
        $this->view->summary         = $this->flow->getSummary($dataList, $fields);
        $this->view->categories      = $categories;
        $this->view->currentCategory = $currentCategory;
        $this->view->categoryValue   = $categoryValue;
        $this->view->mode            = $mode;
        $this->view->label           = $label;
        $this->view->categoryQuery   = $category;
        $this->view->orderBy         = $orderBy;
        $this->view->pager           = $pager;
        $this->display();
    }

    /**
     * Create a record of a flow.
     *
     * @param  string $module
     * @param  string $step         form | save
     * @param  string $prevModule
     * @param  int    $prevDataID
     * @access public
     * @return void
     */
    public function create($module, $step = 'form', $prevModule = '', $prevDataID = 0)
    {
        $action = 'create';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        if($step == 'save')
        {
            $result = $this->flow->post($flow, $action, 0, $prevModule);

            $this->flow->sendNotice($flow, $action, $result);

            return $this->send($result);
        }

        $fields = $this->setFields($flow, $action);

        $this->setFlowData($flow, $action, 0, $prevModule, $prevDataID);
        list($this->view->childFields, $this->view->childDatas) = $this->flow->setFlowChild($flow->module, $action->action, $fields);
        $this->flow->setFlowEditor($flow->module, 'create', $fields);

        $this->view->prevField     = $this->loadModel('workflowrelation', 'flow')->getField($prevModule, $flow->module);
        $this->view->relations     = $this->workflowrelation->getPrevList($flow->module);
        $this->view->users         = $this->loadModel('workflowaction', 'flow')->getUsers2Notice($flow->module);
        $this->view->actionURL     = $this->createLink($flow->module, 'create', "step=save&prevModule=$prevModule&prevDataID=$prevDataID");
        $this->view->formulaScript = $this->flow->getFormulaScript($flow->module, $action, $fields, $this->view->childFields);
        $this->view->linkageScript = $this->flow->getLinkageScript($action, $fields);
        $this->display();
    }

    /**
     * Batch create records for a flow.
     *
     * @param  string $module
     * @param  string $step         form | save
     * @param  string $prevModule
     * @param  int    $prevDataID
     * @access public
     * @return void
     */
    public function batchCreate($module, $step = 'form', $prevModule = '', $prevDataID = 0)
    {
        $action = 'batchcreate';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        if($step == 'save')
        {
            $result = $this->flow->batchPost($flow, $action);

            $this->flow->sendNotice($flow, $action, $result);

            return $this->send($result);
        }

        $fields = $this->setFields($flow, $action);

        $this->setFlowData($flow, $action, 0, $prevModule, $prevDataID);

        $this->view->prevField     = $this->loadModel('workflowrelation', 'flow')->getField($prevModule, $flow->module);
        $this->view->notEmptyRule  = $this->loadModel('workflowrule', 'flow')->getByTypeAndRule('system', 'notempty');
        $this->view->users         = $this->loadModel('workflowaction', 'flow')->getUsers2Notice($flow->module);
        $this->view->actionURL     = $this->createLink($flow->module, $action->action, "step=save&prevModule=$prevModule&prevDataID=$prevDataID");
        $this->view->formulaScript = $this->flow->getFormulaScript($flow->module, $action, $fields);
        $this->display();
    }

    /**
     * Edit a record of a flow.
     *
     * @param  string $module
     * @param  int    $dataID
     * @access public
     * @return void
     */
    public function edit($module, $dataID)
    {
        $action = 'edit';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        if($_POST)
        {
            $result = $this->flow->post($flow, $action, $dataID);

            $this->flow->sendNotice($flow, $action, $result);

            return $this->send($result);
        }

        $data   = $this->setFlowData($flow, $action, $dataID);
        $fields = $this->setFields($flow, $action, $data);

        list($this->view->childFields, $this->view->childDatas) = $this->flow->setFlowChild($flow->module, $action->action, $fields, $dataID);
        $this->flow->setFlowEditor($flow->module, 'edit', $fields);

        $this->view->users         = $this->loadModel('workflowaction', 'flow')->getUsers2Notice($flow->module);
        $this->view->relations     = $this->loadModel('workflowrelation', 'flow')->getPrevList($flow->module);
        $this->view->actionURL     = $this->createLink($flow->module, 'edit', "dataID={$dataID}");
        $this->view->formulaScript = $this->flow->getFormulaScript($flow->module, $action, $fields, $this->view->childFields);
        $this->view->linkageScript = $this->flow->getLinkageScript($action, $fields);
        $this->display('flow', 'operate');
    }

    /**
     * Operate a record of a flow.
     *
     * @param  string $module
     * @param  string $action
     * @param  int    $dataID
     * @access public
     * @return void
     */
    public function operate($module, $action, $dataID = 0)
    {
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        if($_POST or $action->open == 'none')
        {
            $result = $this->flow->post($flow, $action, $dataID);

            $this->flow->sendNotice($flow, $action, $result);

            return $this->send($result);
        }

        $data   = $this->setFlowData($flow, $action, $dataID);
        $fields = $this->setFields($flow, $action, $data);

        list($this->view->childFields, $this->view->childDatas) = $this->flow->setFlowChild($flow->module, $action->action, $fields, $dataID);
        $this->flow->setFlowEditor($flow->module, 'operate', $fields);

        $this->view->users         = $this->loadModel('workflowaction', 'flow')->getUsers2Notice($flow->module);
        $this->view->relations     = $this->loadModel('workflowrelation', 'flow')->getPrevList($flow->module);
        $this->view->actionURL     = $this->createLink($flow->module, $action->action, "dataID=$dataID");
        $this->view->formulaScript = $this->flow->getFormulaScript($flow->module, $action, $fields, $this->view->childFields);
        $this->view->linkageScript = $this->flow->getLinkageScript($action, $fields);
        $this->view->dataID        = $dataID;
        $this->display();
    }

    /**
     * Batch operate records of a flow.
     *
     * @param  string $module
     * @param  string $action
     * @param  string $step
     * @access public
     * @return void
     */
    public function batchOperate($module, $action, $step = 'form')
    {
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        if($step == 'save' or $action->open == 'none')
        {
            $result = $this->flow->batchPost($flow, $action);

            $this->flow->sendNotice($flow, $action, $result);

            return $this->send($result);
        }

        $data   = $this->setFlowData($flow, $action);
        $fields = $this->setFields($flow, $action, $data);

        $this->view->notEmptyRule  = $this->loadModel('workflowrule', 'flow')->getByTypeAndRule('system', 'notempty');
        $this->view->users         = $this->loadModel('workflowaction', 'flow')->getUsers2Notice($flow->module);
        $this->view->actionURL     = $this->createLink($flow->module, $action->action, 'step=save');
        $this->view->formulaScript = $this->flow->getFormulaScript($flow->module, $action, $fields);
        $this->display();
    }

    /**
     * View record detail of a flow.
     *
     * @param  string $module
     * @param  int    $dataID
     * @param  string $linkType
     * @param  string $mode
     * @access public
     * @return void
     */
    public function view($module, $dataID, $linkType = '', $mode = 'browse')
    {
        $action = 'view';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $data   = $this->setFlowData($flow, $action, $dataID);
        $fields = $this->setFields($flow, $action, $data);

        list($this->view->childFields, $this->view->childDatas) = $this->flow->setFlowChild($flow->module, $action->action, $fields, $dataID);
        $this->flow->checkPrivilege($flow, $action);

        $blocks = json_decode($action->blocks);
        $processBlocks = $this->flow->processBlocks($blocks, $fields);

        $this->view->processBlocks  = $processBlocks;
        $this->view->users          = $this->loadModel('user')->getDeptPairs();
        $this->view->relations      = $this->loadModel('workflowrelation', 'flow')->getPrevList($flow->module);
        $this->view->linkedDatas    = $this->flow->getLinkedDatas($flow->module, $dataID);
        $this->view->dataPairs      = $this->flow->getDataPairs($flow, array($dataID => $dataID));
        $this->view->linkPairs      = $this->flow->getLinkPairs($flow->module);
        $this->view->backLink       = $this->session->flowList;
        $this->view->currentType    = $linkType;
        $this->view->currentMode    = $mode;
        $this->display();
    }

    /**
     * Delete a record of a flow.
     *
     * @param  string $module
     * @param  int    $dataID
     * @access public
     * @return void
     */
    public function delete($module, $dataID)
    {
        $action = 'delete';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->setFlowData($flow, $action, $dataID);

        $this->dao->update($flow->table)->set('deleted')->eq(1)->where('id')->eq($dataID)->exec();
        $this->loadModel('action')->create($flow->module, $dataID, 'deleted', '', $extra = ACTIONMODEL::CAN_UNDELETED);

        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $url = $_SERVER['HTTP_REFERER'];
        $getPattern = $this->config->moduleVar . '=' . $module . '&' . $this->config->methodVar . '=view';
        $pathinfoPattern = $module . '-view-';

        if(strpos($url, $getPattern) !== false || strpos($url, $pathinfoPattern) !== false)
        {
            return $this->send(array('result' => 'success', 'locate' => $this->createLink($flow->module, 'browse')));
        }
        else
        {
            return $this->send(array('result' => 'success'));
        }
    }

    /**
     * Link datas to a flow.
     *
     * @param  string $module
     * @param  int    $dataID
     * @param  string $linkType
     * @param  string $mode
     * @access public
     * @return void
     */
    public function link($module, $dataID, $linkType, $mode = 'browse')
    {
        $action = 'link';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->setFlowData($flow, $action, $dataID);

        if($this->post->dataIDList)
        {
            $this->flow->link($flow, $dataID, $linkType, $this->post->dataIDList);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $locate = $this->createLink($flow->module, 'view', "dataID=$dataID&linkType=$linkType");
            return $this->send(array('result' => 'success', 'locate' => $locate));
        }

        $linkedFlow = $this->workflow->getByModule($linkType);
        if(!$linkedFlow && in_array($linkType, $this->config->flow->linkPairs))
        {
            die($this->fetch($linkType, 'link', "module=$module&dataID=$dataID&mode=$mode"));
        }

        $actionURL = $this->createLink($module, 'view', "dataID=$dataID&linkType=$linkType&mode=bysearch");
        $this->flow->setSearchParams($linkedFlow, $action, $actionURL);

        $unlinkedDatas = $this->flow->getUnlinkedDatas($module, $dataID, $linkedFlow, $mode);

        $this->view->linkedFields  = $this->workflowaction->getFields($linkedFlow->module, 'browse', true, $unlinkedDatas);
        $this->view->unlinkedDatas = $unlinkedDatas;
        $this->view->linkedFlow    = $linkedFlow;
        $this->display();
    }

    /**
     * Unlink datas from a flow.
     *
     * @param  string $module
     * @param  int    $dataID
     * @param  string $linkType
     * @param  int    $linkedID
     * @access public
     * @return void
     */
    public function unlink($module, $dataID, $linkType, $linkedID = 0)
    {
        $action = 'unlink';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->setFlowData($flow, $action, $dataID);

        $linkedIDList = array();
        if($this->post->dataIDList) $linkedIDList = $this->post->dataIDList;
        if($linkedID) $linkedIDList[] = $linkedID;

        if($linkedIDList)
        {
            $this->flow->unlink($flow, $dataID, $linkType, $linkedIDList);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        }

        $locate = $this->createLink($flow->module, 'view', "dataID=$dataID&linkType=$linkType");
        return $this->send(array('result' => 'success', 'locate' => $locate));
    }

    /**
     * Set flow, action.
     *
     * @param  string $module
     * @param  string $action
     * @access public
     * @return array
     */
    public function setFlowAction($module, $action)
    {
        $flow   = $this->loadModel('workflow', 'flow')->getByModule($module);
        $action = $this->loadModel('workflowaction', 'flow')->getByModuleAndAction($flow->module, $action);

        $this->view->title  = $action->name;
        $this->view->flow   = $flow;
        if($action->action == 'view') $this->view->flowAction = $action;
        if($action->action != 'view') $this->view->action     = $action;

        if(!isset($this->lang->apps->{$flow->app}))
        {
            /* If the flow's app isn't a built-in entry, refactor the main menu. */
            $entry = $this->loadModel('entry')->getByCode($flow->app);

            if($entry)
            {
                $this->lang->admin->common = $entry->name;
                $this->lang->apps->sys = $entry->name;
                $this->lang->menu->sys = $this->lang->menu->{$flow->app};
            }
        }

        return array($flow, $action);
    }

    /**
     * Set fields.
     *
     * @param  object $flow
     * @param  object $action
     * @param  array  $datas
     * @access public
     * @return array
     */
    public function setFields($flow, $action, $datas = array())
    {
        if($action->type == 'batch' && $action->batchMode == 'same') $datas = array();

        $fields = $this->loadModel('workflowaction', 'flow')->getFields($flow->module, $action->action, true, $datas);

        if($action->type == 'batch' && $action->batchMode == 'different') $fields = $this->flow->addDitto($fields);

        $this->view->fields = $fields;

        return $fields;
    }

    /**
     * Set data or data list of a flow.
     *
     * @param  object $flow
     * @param  object $action
     * @param  int    $dataID
     * @param  string $prevModule
     * @param  int    $prevDataID
     * @access public
     * @return void
     */
    public function setFlowData($flow, $action, $dataID = 0, $prevModule = '', $prevDataID = 0)
    {
        if($action->type == 'single')
        {
            if($action->action == 'create')
            {
                if(!$prevModule)
                {
                    /* Create one record of the current flow. */
                    $this->view->prevDataID = 0;
                }
                elseif($prevDataID)
                {
                    /* Create one record from one record of the prev flow (one 2 one). */
                    $this->view->prevDataID = $prevDataID;
                }
                else
                {
                    /* Create one record from many records of the prev flow (many 2 one). */
                    if($this->post->dataIDList) $this->filterPrevData($flow->module, $action->action, $prevModule);

                    $this->view->prevDataID = $this->post->dataIDList;
                }
            }
            else
            {
                /* Edit, assign, view, delete, etc. */
                $data = $this->flow->getDataByID($flow, $dataID);
                if(!$data) die(js::error($this->lang->flow->error->notFound) . js::locate('back'));

                $this->view->data = $data;

                return $data;
            }
        }

        if($action->type == 'batch')
        {
            if($action->action == 'batchcreate')
            {
                if(!$prevModule)
                {
                    /* Batch create records of the current flow. */
                    $dataIDList = array();
                    for($i = 1; $i <= $this->config->flow->batchCreateRow; $i++) $dataIDList[$i] = '';

                    $this->view->dataList = $dataIDList;
                }
                elseif($prevDataID)
                {
                    /* Batch create records from one record of the prev flow (one 2 many). */
                    $dataIDList = array();
                    for($i = 1; $i <= $this->config->flow->batchCreateRow; $i++) $dataIDList[$i] = $prevDataID;

                    $this->view->dataList = $dataIDList;
                }
                else
                {
                    /* Batch create records from many records of the prev flow (many 2 many). */
                    if(!$this->post->dataIDList) die(js::error($this->lang->flow->error->notFound) . js::locate('back'));

                    $this->filterPrevData($flow->module, $action->action, $prevModule);

                    $this->view->dataList = $this->post->dataIDList;
                }
            }
            else
            {
                /* Batch edit, batch assign, etc. */
                if(!$this->post->dataIDList) die(js::error($this->lang->flow->error->notFound) . js::locate('back'));

                $dataList = $this->flow->getDataByIDList($flow, $this->post->dataIDList);
                foreach($dataList as $key => $data)
                {
                    $enabled = $this->flow->checkConditions($action->conditions, $data);
                    if(!$enabled) unset($dataList[$key]);
                }

                if($action->batchMode == 'same')
                {
                    foreach($dataList as $key => $data) $dataList[$key] = $data->id;
                }

                $this->view->dataList = $dataList;

                return $dataList;
            }
        }
    }

    /**
     * Filter prev data by action conditions.
     *
     * @param  string $module
     * @param  string $action
     * @param  string $prevModule
     * @access public
     * @return void
     */
    public function filterPrevData($module, $action, $prevModule)
    {
        $conditions = $this->dao->select('conditions')->from(TABLE_WORKFLOWACTION)->where('module')->eq($prevModule)->andWhere('`virtual`')->eq(1)->andWhere('action')->eq("{$module}_{$action}")->fetch('conditions');
        if(empty($conditions)) return true;

        $prevFlow     = $this->loadModel('workflow', 'flow')->getByModule($prevModule);
        $prevDataList = $this->flow->getDataByIDList($prevFlow, $this->post->dataIDList);
        foreach($this->post->dataIDList as $key => $dataID)
        {
            $enable = $this->flow->checkConditions($conditions, $prevDataList[$dataID]);
            if(!$enable) unset($_POST['dataIDList'][$key]);
        }
    }

    /**
     * Export datas of a flow.
     *
     * @param  string $module
     * @param  string $mode     all | thisPage
     * @access public
     * @return void
     */
    public function export($module, $mode = 'all')
    {
        $flow = $this->loadModel('workflow', 'flow')->getByModule($module);

        if($_POST)
        {
            $flowDatas      = array();
            $flowFields     = $this->loadModel('workflowfield', 'flow')->getList($flow->module);
            $queryCondition = $this->session->{$flow->module . 'QueryCondition'};

            if($queryCondition)
            {
                if($mode == 'all' && strpos($queryCondition, 'LIMIT') !== false) $queryCondition = substr($queryCondition, 0, strpos($queryCondition, 'LIMIT'));

                $stmt = $this->dbh->query($queryCondition);
                while($row = $stmt->fetch()) $flowDatas[$row->id] = $row;
            }

            $excelData = new stdclass();
            $excelData->dataList[] = $this->flow->getExportData($flow, $flowDatas, $flowFields);
            $excelData->fileName   = $this->post->fileName ? $this->post->fileName : $flow->name;

            $this->flow->setExcelFields($flow->module, $flowFields);

            $this->post->set('fields',$fields);
            $this->post->set('rows', $flowDatas);
            $this->post->set('kind', $flow->module);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName = $flow->name;
        $this->view->fields   = $this->loadModel('workflowfield', 'flow')->getExportFields($flow->module);
        $this->view->module   = $module;
        $this->display();
    }

    /**
     * Export data template of a flow.
     *
     * @param  string $module
     * @access public
     * @return void
     */
    public function exportTemplate($module)
    {
        $action = 'batchcreate';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        $fileName = $flow->name . $this->lang->flow->template;

        if($_POST)
        {
            $this->config->flowLimit = 0;

            $fields       = array();
            $rows         = array();
            $listFields   = array();
            $fieldList    = array();
            $action       = 'batchcreate';
            $actionFields = $this->loadModel('workflowaction', 'flow')->getFields($flow->module, $action);

            foreach($actionFields as $field)
            {
                if(!$field->show) continue;

                $fields[$field->field] = $field->name;

                for($i = 0; $i < $this->post->num; $i++) $rows[$i][$field->field] = '';

                if(!empty($field->options) && is_array($field->options))
                {
                    $listFields[] = $field->field;

                    $fieldList[$field->field . 'List'] = $field->options;
                }
            }

            $data = new stdclass();
            $data->kind        = $flow->module;
            $data->title       = $fileName;
            $data->fields      = $fields;
            $data->rows        = $rows;
            $data->sysDataList = $listFields;
            $data->listStyle   = $listFields;

            foreach($fieldList as $listName => $listArray) $data->$listName = $listArray;

            $excelData = new stdclass();
            $excelData->dataList[] = $data;
            $excelData->fileName   = $fileName;

            $this->flow->setExcelFields($module, $actionFields);

            $this->post->set('fields',$fields);
            $this->post->set('rows', $flowDatas);
            $this->post->set('kind', $flow->module);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->display();
    }

    /**
     * Import datas of a flow.
     *
     * @param  string $module
     * @access public
     * @return void
     */
    public function import($module)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $file = $this->loadModel('file')->getUpload('files');
            if(empty($file)) return $this->send(array('result' => 'fail', 'message' => $this->lang->excel->error->noFile));
            $file = $file[0];

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if(!$phpReader->canRead($fileName))
            {
                $phpReader = new PHPExcel_Reader_Excel5();
                if(!$phpReader->canRead($fileName))
                {
                    unlink($fileName);
                    die(js::alert($this->lang->excel->error->canNotRead));
                }
            }
            $this->session->set('importFile', $fileName);

            return $this->send(array('result' => 'success', 'locate' => ($this->createLink($module, 'showImport', "mode={$this->post->mode}"))));
        }

        $this->view->title  = $this->lang->import;
        $this->view->module = $module;
        $this->display();
    }

    /**
     * Show data list parsed from the imported file.
     *
     * @param  string $module
     * @param  string $mode     template : import by the import template | auto : import by the export file
     * @access public
     * @return void
     */
    public function showImport($module, $mode = 'template')
    {
        if(!$this->session->importFile) $this->locate($this->createLink($module, 'browse'));

        $action = 'batchcreate';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        if($_POST)
        {
            if($mode == 'template') $result = $this->flow->batchPost($flow, $action, 'import');
            if($mode == 'auto')     $result = $this->flow->import($flow);

            $this->flow->sendNotice($flow, $action, $result);

            return $this->send($result);
        }

        if($mode == 'auto')
        {
            list($fields, $titles, $dataList) = $this->flow->getImportData($flow);

            $this->view->titles = $titles;
        }
        else
        {
            $fields = $this->loadModel('workflowaction', 'flow')->getFields($flow->module, $action->action, false);

            $titles = array();
            foreach($fields as $field)
            {
                if($field->show) $titles[$field->field] = $field->name;
            }

            $this->flow->setExcelFields($module, $fields);

            $dataList = $this->loadModel('file')->parseExcel($titles);
            $fields   = $this->workflowaction->processFields($fields, true, $dataList);

            foreach($fields as $field)
            {
                if(!$field->show) continue;

                foreach($dataList as $data)
                {
                    foreach($data as $key => $value)
                    {
                        if($key != $field->field) continue;
                        if(!is_array($field->options) or !$field->options) continue;

                        /* 先把数据的导入值和字段选项数组的键匹配，如果匹配到了则取键作为数据的值，否则和字段选项数组的值匹配，如果匹配到了则取值对应的键作为数据的值，如还未匹配到则直接应用数据的导入值。 */
                        $data->$key = isset($field->options[$value]) ? $value : (in_array($value, $field->options) ? array_search($value, $field->options) : $value);
                    }
                }
            }
        }

        if(empty($dataList))
        {
            unlink($this->session->importFile);
            unset($_SESSION['importFile']);

            die(js::alert($this->lang->excel->error->noData) . js::locate($this->createLink($module, 'browse')));
        }

        $this->view->title        = $flow->name . $this->lang->colon . $this->lang->flow->showImport;
        $this->view->notEmptyRule = $this->loadModel('workflowrule', 'flow')->getByTypeAndRule('system', 'notempty');
        $this->view->mode         = $mode;
        $this->view->flow         = $flow;
        $this->view->fields       = $fields;
        $this->view->dataList     = $dataList;

        $this->display();
    }

    /**
     * Show reports of a flow.
     *
     * @param  string    $module
     * @access public
     * @return void
     */
    public function report($module)
    {
        $action = 'report';
        list($flow, $action) = $this->setFlowAction($module, $action);

        $this->flow->checkPrivilege($flow, $action);

        $reportPairs    = array();
        $charts         = array();
        $chartData      = array();
        $checkedReports = $this->post->reports;

        /* Query reports of a flow.*/
        $reportList = $this->loadModel('workflowreport', 'flow')->getList($module);
        foreach($reportList as $report) $reportPairs[$report->id] = $report->name;

        if($_POST)
        {
            $charts    = $this->flow->processReportList($module, $reportList);
            $chartData = $this->flow->getChartData($module, $charts);
        }

        $this->app->loadLang('report');

        $this->view->title          = $this->lang->report->common;
        $this->view->moduleMenu     = $this->flow->getModuleMenu($flow);
        $this->view->module         = $module;
        $this->view->checkedReports = $checkedReports;
        $this->view->reportPairs    = $reportPairs;
        $this->view->charts         = $charts;
        $this->view->chartData      = $chartData;
        $this->display();
    }

    /**
     * Get prev data by ajax.
     *
     * @param  string $prev
     * @param  string $next
     * @param  string $action
     * @param  int    $dataID
     * @param  string $element
     * @access public
     * @return void
     */
    public function ajaxGetPrevData($prev, $next, $action, $dataID, $element = 'tr')
    {
        $flow = $this->loadModel('workflow', 'flow')->getByModule($prev);
        if(!$flow) die();

        $data = $this->flow->getDataByID($flow, $dataID);
        if(!$data) die();

        $fields = $this->loadModel('workflowrelation', 'flow')->getLayoutFields($prev, $next, $action, true, $data);

        $prevData = '';
        foreach($fields as $field)
        {
            if(!$field->show) continue;

            if($field->control == 'file')
            {
                $filesName = "{$field->field}files";
                if(!$data->{$fileName}) continue;

                $files = $this->fetch('file', 'printFiles', array('files' => $data->{$filesName}, 'fieldset' => 'false'));

                if($element == 'tr') $prevData .= "<tr class='prevData {$prev}'><th>{$flow->name}{$field->name}</th><td>{$files}</td></tr>";
                if($element == 'p')  $prevData .= "<p  class='prevData {$prev}'>{$flow->name}{$field->name} : {$files}<p>";

                continue;
            }

            $value = '';
            if(is_array($data->{$field->field}))
            {
                foreach($data->{$field->field} as $fieldValue) $value .= zget($field->options, $fieldValue) . ' ';
            }
            else
            {
                if(strpos(',date,datetime,', ",$field->control,") !== false)
                {
                    $value = formatTime($data->{$field->field});
                }
                else
                {
                    $value = zget($field->options, $data->{$field->field});
                }
            }

            if($element == 'tr') $prevData .= "<tr class='prevData {$prev}'><th>{$field->name}</th><td>{$value}</td></tr>";
            if($element == 'p')  $prevData .= "<p  class='prevData {$prev}'>{$field->name} : {$value}<p>";
        }

        die($prevData);
    }

    /**
     * Get data pairs by ajax.
     *
     * @param  string $module
     * @param  string $field
     * @param  string $options
     * @param  string $search
     * @param  int    $limit
     * @access public
     * @return void
     */
    public function ajaxGetPairs($module = '', $field = '', $options = '', $search = '', $limit = 0)
    {
        $this->loadModel('workflowfield', 'flow');
        if($module && $field) $field = $this->workflowfield->getByField($module, $field);
        if(!$module or !$field)
        {
            if(!$options) die(json_encode(array()));

            $field = new stdclass();
            $field->type    = $options == 'dept' ? 'mediumint' : 'varchar';
            $field->options = $options;
        }

        if(!$limit) $limit = $this->config->searchLimit;
        $search  = $this->post->search ? $this->post->search : urldecode($search);
        $options = $this->workflowfield->getFieldOptions($field, false, '', $search, $limit);

        $results = array();
        foreach($options as $key => $value)
        {
            $result = new stdclass();
            $result->text  = $value;
            $result->value = $key;

            $results[] = $result;
        }

        die(json_encode($results));
    }
}
