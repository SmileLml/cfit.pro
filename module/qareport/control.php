<?php
/**
 * The control file of qareport currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     qareport
 * @version     $Id: control.php 5107 2013-07-12 01:46:12Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class qareport extends control
{
    public function __construct($module = '', $method = '')
    {
        parent::__construct($module, $method);
        $this->qareport->setMenu();
        $this->qareport->buildReportList();
        $this->view->chartType  = 'default';
        $this->view->reportType = 'default';
    }

    /**
     * Display bug-related data reports.
     *
     * @param  string $reportType
     * @param  string $chartType
     * @param  string $switch
     * @access public
     * @return void
     */
    public function browse($reportType = 'bugsPerExecution', $chartType = 'default', $switch = '')
    {
        $application = '';
        $product     = '';
        $project     = '';
        $begin       = date('Y-m-d', strtotime('-3 month'));
        $end         = date('Y-m-d');

        if($switch)
        {
            $params = $this->session->qaReportQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $begin       = $params['begin'];
            $end         = $params['end'];
            $application = $params['application'];
            $product     = $params['product'];
            $project     = $params['project'];

            $this->post->set('begin', $begin);
            $this->post->set('end', $end);
            $this->post->set('application', $application);
            $this->post->set('product', $product);
            $this->post->set('project', $project);
        }

        $charts = array();
        $datas  = array();
        if(!empty($_POST))
        {
            $data = $this->qareport->getPostPairs();
            extract($data);

            $chartFunc   = 'getDataOf' . $reportType;
            $chartData   = $this->qareport->$chartFunc();
            $chartOption = $this->lang->qareport->report->$reportType;
            if(!empty($chartType) and $chartType != 'default') $chartOption->type = $chartType;

            $this->qareport->mergeChartOption($reportType);

            $charts[$reportType] = $chartOption;
            $datas[$reportType]  = $this->qareport->computePercent($chartData);
        }

        $param     = array('application' => $application, 'product' => $product, 'project' => $project, 'begin' => $begin, 'end' => $end);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('qaReportQueryData', $queryData);

        $this->view->title      = $this->lang->qareport->common;
        $this->view->position[] = $this->lang->qareport->common;

        $this->view->charts           = $charts;
        $this->view->datas            = $datas;
        $this->view->reportType       = $reportType;
        $this->view->chartType        = $chartType;
        $this->view->begin            = $begin;
        $this->view->end              = str_replace(' 23:59:59', '', $end);
        $this->view->application      = $application;
        $this->view->product          = $product;
        $this->view->project          = $project;
        $this->view->applicationPairs = array('' => '') + $this->loadModel('application')->getPairs();
        $this->view->productPairs     = array('' => '') + $this->loadModel('product')->getPairs();
        $this->view->projectPairs     = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $this->display();
    }

    /**
     * Export data to chart.
     *
     * @access public
     * @return void
     */
    public function export()
    {
        if($_POST)
        {
            $fileType = $this->post->fileType;
            $items    = $this->post->items;
            foreach($items as $item)
            {
                $chartFunc = 'getDataOf' . $item;
                $chartData = $this->qareport->$chartFunc();

                $datas[$item]  = $this->qareport->computePercent($chartData);
                $images[$item] = isset($_POST["chart-$item"]) ? $this->post->{"chart-$item"} : '';
            }
            unset($_POST["chart-$item"], $_POST['application'], $_POST['product'], $_POST['project']);

            if($fileType == 'xls')
            {
                $fields = array();
                $rows   = array();

                $fields['item']    = $this->lang->qareport->itemNames[$items[0]] ? $this->lang->qareport->itemNames[$items[0]] : $this->lang->qareport->item;
                $fields['value']   = $this->lang->qareport->num;
                $fields['percent'] = $this->lang->qareport->percent;

                foreach($datas[$items[0]] as $dataName => $data)
                {
                    $row = new stdClass();

                    $row->item    = $data->name;
                    $row->value   = $data->value;
                    $row->percent = ($data->percent * 100) . '%';

                    $rows[] = $row;
                }
                $this->post->set('fields', $fields);
                $this->post->set('rows', $rows);
            }

            $this->loadModel('report');
            $this->post->set('datas',  $datas);
            $this->post->set('items',  $items);
            $this->post->set('images', $images);
            $this->post->set('kind',   'qareport');
            $this->fetch('file', 'export2'.$fileType, $_POST);
        }

        $this->display();
    }

    public function customBrowse($orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1) 
    {
        $this->loadModel('report');
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->qareport->customBrowse;
        $this->view->position[] = $this->lang->qareport->customBrowse;

        $this->view->reports       = $this->report->getReportList('test', $orderBy, $pager);
        $this->view->pager         = $pager;
        $this->display();
    }

    public function custom($step = 0, $reportID = 0, $from = '') 
    {
        $this->loadModel('report');
        if($from) $this->lang->navGroup->report = 'system';
        $this->view->type = $from;

        if($_POST and $step == 1)
        {
            $sql = str_replace("\t", '', stripslashes(trim($this->post->sql)));
            $sql = str_replace("；", '', $sql);
            if($result = $this->report->checkBlackList($sql))
            {
                if($result == 'noselect') die(js::alert($this->lang->crystal->noticeSelect));
                die(js::alert(sprintf($this->lang->crystal->noticeBlack, $result)));
            }

            if($sql != $this->session->reportSQL) $this->session->set('reportSQL', $sql);
            $this->session->set('sqlVarValues', '');
            if($this->post->sqlVars)  $this->session->set('sqlVarValues', serialize($this->post->sqlVars));
            die(js::locate(inlink('custom', "step=1&reportID=$reportID&from=$from"), 'parent'));
        }
        if($_POST and $step == 2)
        {
            $condition = fixer::input('post')->get();
            foreach($condition->reportType as $i => $reportType)
            {
                if($reportType == 'sum' and empty($condition->sumAppend[$i])) die(js::alert(sprintf($this->lang->crystal->noSumAppend, $i + 1)));
            }

            $this->session->set('reportParams', json_encode($condition));
            die(js::locate(inlink('custom', "step=2&reportID=$reportID&from=$from"), 'parent'));
        }

        $tables = array();
        $this->view->hasSqlVar = false;
        if($step != 0 and $this->session->reportSQL)
        {
            $sql    = $this->session->reportSQL;
            $result = $this->report->checkSqlVar($sql);

            if($result)
            {
                $sqlVarValues = $this->session->sqlVarValues ? unserialize($this->session->sqlVarValues) : array();
                if($sqlVarValues)
                {
                    foreach($result as $sqlVar)
                    {
                        $sqlVarValues[$sqlVar] = $this->report->processSqlVar($sqlVarValues[$sqlVar]);
                        $sql = str_replace('$' . $sqlVar, $this->dbh->quote($sqlVarValues[$sqlVar]), $sql);
                    }
                }

                $this->view->hasSqlVar    = true;
                $this->view->sqlVars      = json_decode($this->session->sqlVars, true);
                $this->view->sqlLangs     = json_decode($this->session->sqlLangs, true);
                $this->view->sqlVarValues = $sqlVarValues;
            }

            $dataList = array();
            $fields   = array();
            if(!$result or $this->session->sqlVarValues)
            {
                /* replace define table name to real table name. */
                $sql           = $this->report->replaceTableNames($sql);
                $tableAndField = $this->report->getTables($sql);
                $tables        = $tableAndField['tables'];
                $fields        = $tableAndField['fields'];

                try
                {
                    $dataList = $this->dbh->query($sql)->fetchAll();
                }
                catch(PDOException $exception)
                {
                    /* set error tag. */
                    $this->session->set('reportSQLError', true);
                    echo js::alert($this->lang->crystal->errorSql . str_replace("'", "\'", $exception->getMessage()));
                    die(js::locate(inlink('custom', "step=0&reportID=$reportID&from=$from")));
                }

                $moduleNames = array();
                if($tables)
                {
                    foreach($tables as $table)
                    {
                        if(strpos($table, $this->config->db->prefix) === false) continue;
                        $module = str_replace($this->config->db->prefix, '', $table);
                        if($module == 'case')   $module = 'testcase';
                        if($module == 'module') $module = 'tree';

                        /* Code for workflow.*/
                        if(strpos($module, 'flow_') !== false)
                        {
                            $moduleName = substr($module, 5);
                            $flowFields = $this->loadModel('workflowfield')->getFieldPairs($moduleName);
                            $this->lang->$moduleName = new stdclass();
                            foreach($flowFields as $flowField => $fieldName)
                            {
                                if(!$flowField) continue;
                                $this->lang->$moduleName->$flowField = $fieldName;
                            }

                            $moduleNames[$table] = $module;
                        }
                        else
                        {
                            if($this->app->loadLang($module)) $moduleNames[$table] = $module;
                        }
                    }
                }

                $data          = (array)current($dataList);
                $this->session->set('reportDataCount', count($dataList));
                $moduleNames   = array_reverse($moduleNames, true);
                $reverseFields = empty($fields) ? array() : array_reverse($fields, true);
                $mergeFields   = $this->report->mergeFields(array_keys($data), $reverseFields, $moduleNames);
            }

            if(($step == 2 or $reportID) and $this->session->reportParams)
            {
                $condition = json_decode($this->session->reportParams, true);
                if(!empty($condition['isUser'])) $this->view->users = $this->loadModel('user')->getPairs('noletter');

                $groupLang['group1'] = $this->report->getGroupLang($condition['group1'], $reverseFields, $moduleNames);
                $groupLang['group2'] = $this->report->getGroupLang($condition['group2'], $reverseFields, $moduleNames);
                list($headers, $reportData) = $this->report->processData($dataList, $condition);

                $this->view->headerNames = $this->report->getHeaderNames($fields, $moduleNames, $condition);
                $this->view->headers     = $headers;
                $this->view->condition   = $condition;
                $this->view->reportData  = $reportData;
                $this->view->groupLang   = $groupLang;
            }

            $this->view->dataList = $dataList;
            $this->view->tables   = $tables;
            $this->view->fields   = empty($mergeFields) ? array() : $mergeFields;
        }

        if($step == 0 and $reportID == 0) $this->session->set('sqlLangs', '');

        $this->view->title      = $this->lang->crystal->common;
        $this->view->position[] = $this->lang->crystal->common;
        $this->view->step       = $step;
        $this->view->reportID   = $reportID;
        $this->display();
    }

    public function saveReport($reportID = 0, $step = 2, $type = '')
    {
        $this->loadModel('report');
        if($_POST or $reportID)
        {
            $data = fixer::input('post')
                ->join('module', ',')
                ->add('sql', $this->session->reportSQL)
                ->add('step', (int)$step)
                ->add('params', $step == 2 ? $this->session->reportParams : '')
                ->add('vars', $this->session->sqlVars)
                ->add('langs', $this->session->sqlLangs)
                ->add('addedBy', $this->app->user->account)
                ->add('addedDate', helper::now())
                ->skipSpecial('sql,params,vars,langs')
                ->remove('desc,name')
                ->get();

            if(isset($_POST['name']))
            {
                $names = $this->post->name;
                $checkName = false;
                foreach($names as $langKey => $name)
                {
                    $name = trim($name);
                    if(!empty($name)) $checkName = true;
                    $names[$langKey] = htmlspecialchars($name);
                }
                if(!$checkName) die(js::alert($this->lang->crystal->emptyName));
                $data->name = json_encode($names);
            }

            if(isset($_POST['desc']))
            {
                $descs = $this->post->desc;
                foreach($descs as $langKey => $desc) $descs[$langKey] = htmlspecialchars($desc);
                $data->desc = json_encode($descs);
            }

            if($step == 0) die(js::alert($this->lang->crystal->noStep));
            $result = $this->report->checkSqlVar($data->sql);
            if(!$result)
            {
                $data->vars = '';
            }
            elseif(empty($data->vars))
            {
                die(js::alert($this->lang->crystal->errorSave));
            }
            if($step == 2 and empty($data->params)) die(js::alert(sprintf($this->lang->error->notempty, $this->lang->crystal->params)));

            if($reportID)
            {
                unset($data->addedBy);
                unset($data->addedDate);
                $this->dao->update(TABLE_REPORT)->data($data)->autocheck()->batchCheck('sql', 'notempty')->where('id')->eq($reportID)->exec();

                if(dao::isError()) die(js::error(dao::getError()));
                die(js::reload('parent'));
            }
            else
            {
                $this->dao->insert(TABLE_REPORT)->data($data)->autocheck()
                    ->batchCheck('code,sql', 'notempty')
                    ->check('code', 'unique')
                    ->exec();

                if(dao::isError()) die(js::error(dao::getError()));
                $reportID = $this->dao->lastInsertId();
                die(js::reload('parent.parent'));
            }
        }

        $this->lang->crystal->moduleList = array(
            'test'=>$this->lang->crystal->moduleList['test'],
        );

        $this->view->type = $type;
        $this->display();
    }

    /**
     * Set the field name.
     *
     * @param  int    $reportID
     * @access public
     * @return void
     */
    public function ajaxSetLang($reportID = 0)
    {
        $this->loadModel('report');
        if($_POST)
        {
            $data  = fixer::input('post')->get();
            $langs = array();
            foreach($data->fieldName as $i => $fieldName)
            {
                $fieldName  = trim($fieldName);
                if(empty($fieldName)) continue;
                foreach($data->fieldValue as $fieldLang => $fieldValue)
                {
                    $fieldValue[$i] = trim($fieldValue[$i]);
                    if(empty($fieldValue[$i])) break;

                    $langs[$fieldName][$fieldLang] = $fieldValue[$i];
                }

            }

            $langs = json_encode($langs);
            $this->session->set('sqlLangs', $langs);
            if($reportID) $this->dao->update(TABLE_REPORT)->set('langs')->eq($langs)->where('id')->eq($reportID)->exec();
            die(js::closeModal('parent.parent'));
        }

        $langs    = $this->session->sqlLangs ? json_decode($this->session->sqlLangs) : array();
        $sqlLangs = new stdclass();
        $sqlLangs->fieldName  = array();
        $sqlLangs->fieldValue = array();

        $i = 0;
        foreach($langs as $fieldName => $fieldLangs)
        {
            foreach($fieldLangs as $fieldLang => $fieldValue)
            {
                $sqlLangs->fieldName[$i]  = $fieldName;
                $sqlLangs->fieldValue[$fieldLang][$i] = $fieldValue;
            }
            $i++;
        }

        $this->view->sqlLangs = $sqlLangs;
        $this->display();
    }

    /**
     * Check whether the variable is legal.
     *
     * @param  int    $reportID
     * @param  int    $type
     * @access public
     * @return void
     */
    public function ajaxCheckVar($reportID = 0, $type = '')
    {
        $this->loadModel('report');
        if($_POST and !isset($_POST['sql']))
        {
            $data = fixer::input('post')->remove('copySql,varType')->get();
            foreach($data->varName as $i => $varName)
            {
                if(empty($varName)) die(js::alert($this->lang->crystal->noticeVarName));
                if(empty($data->requestType[$i])) die(js::alert(sprintf($this->lang->crystal->noticeRequestType, $data->varName[$i])));
                if(empty($data->showName[$i])) die(js::alert(sprintf($this->lang->crystal->noticeShowName, $data->showName[$i])));
            }

            $sql = trim($this->post->copySql);
            if($this->post->varType == 'add')
            {
                $result  = $this->report->checkSqlVar($sql);
                $sql .= ' $' . $data->varName[0];
                if($result)
                {
                    $sqlVars = json_decode($this->session->sqlVars);
                    foreach($sqlVars->varName as $i => $varName)
                    {
                        if(!in_array($varName, $result))
                        {
                            unset($sqlVars->varName[$i]);
                            unset($sqlVars->showName[$i]);
                            unset($sqlVars->requestType[$i]);
                            unset($sqlVars->default[$i]);
                            unset($sqlVars->selectList[$i]);
                        }
                    }
                    if($sqlVars and !in_array($data->varName[0], $sqlVars->varName))
                    {
                        $sqlVars->varName[] = $data->varName[0];

                        end($sqlVars->varName);
                        $endKey = key($sqlVars->varName);

                        $sqlVars->requestType[$endKey] = $data->requestType[0];
                        $sqlVars->default[$endKey]     = $data->default[0];
                        $sqlVars->showName[$endKey]    = $data->showName[0];
                        if($data->requestType[0] == 'select') $sqlVars->selectList[$endKey] = $data->selectList[0];
                    }
                }
                else
                {
                    $sqlVars = $data;
                }
            }
            else
            {
                $sqlVars = $data;
            }

            $this->session->set('reportSQL', $sql);
            $this->session->set('sqlVars', json_encode($sqlVars));
            foreach($sqlVars->varName as $varName) $sqlVarValues[$varName] = '';
            $this->session->set('sqlVarValues', serialize($sqlVarValues));

            die(js::locate(inlink('custom', "step=1&reportID=$reportID&from=$type"), 'parent'));
        }
        $sql = trim(stripslashes(trim($this->post->sql)), ';');
        $result = $this->report->checkSqlVar($sql);
        if(empty($result)) die(0);

        if($this->session->sqlVars)
        {
            $sqlVars = json_decode($this->session->sqlVars);
            $varDiff = array_diff($result, $sqlVars->varName);
            if(empty($varDiff) and count($result) == count($sqlVars->varName)) die(0);
        }

        die(json_encode(array_unique($result)));
    }

    /**
     * Design report.
     *
     * @param  int    $reportID
     * @param  string $from
     * @access public
     * @return void
     */
    public function useReport($reportID = 0, $from = '')
    {
        $this->loadModel('report');
        if($from) $this->lang->navGroup->report = 'system';

        $report = $this->report->getReportByID($reportID);
        if(!$report) die(js::alert($this->lang->crystal->errorNoReport));

        $this->session->set('reportSQL', $report->sql);
        $this->session->set('reportParams', $report->params);
        $this->session->set('sqlVars', $report->vars);
        $this->session->set('sqlLangs', $report->langs);

        $sqlVarValues = array();
        $sqlVars      = json_decode($report->vars, true);
        if($sqlVars)
        {
            foreach($sqlVars['varName'] as $i => $varName)
            {
                $varType = ($sqlVars['requestType'][$i] == 'select') ? $sqlVars['selectList'][$i] : $sqlVars['requestType'][$i];
                $sqlVarValues[$varName] = isset($sqlVars['default'][$i]) ? $sqlVars['default'][$i] : '';
                if($varType == 'dept' and empty($sqlVarValues[$varName])) $sqlVarValues[$varName] = 0;
            }
        }
        $this->session->set('sqlVarValues', serialize($sqlVarValues));

        $module = (strpos($report->module, 'cmmi') !== false) ? 'cmmi' : '';
        die(js::locate(inlink('custom', "step={$report->step}&reportID=$reportID&from=$module"), 'parent'));
    }

    /**
     * Edit report.
     *
     * @param  int    $reportID
     * @param  string $from
     * @access public
     * @return void
     */
    public function editReport($reportID = 0, $from = '')
    {
        $this->loadModel('report');
        if($from) $this->lang->navGroup->report = 'system';

        $report = $this->report->getReportByID($reportID);
        if(!$report) die(js::alert($this->lang->crystal->errorNoReport));

        if($_POST)
        {
            $data = fixer::input('post')->join('module', ',')->remove('desc,name')->get();
            if(!isset($data->module)) $data->module = '';

            $names = $this->post->name;
            $checkName = false;
            foreach($names as $langKey => $name)
            {
                $name = trim($name);
                if(!empty($name)) $checkName = true;
                $names[$langKey] = htmlspecialchars($name);
            }
            if(!$checkName) die(js::alert($this->lang->crystal->emptyName));
            $data->name = json_encode($names);

            $descs = $this->post->desc;
            foreach($descs as $langKey => $desc) $descs[$langKey] = strip_tags($desc, $this->config->allowedTags);
            $data->desc = json_encode($descs);

            $this->dao->update(TABLE_REPORT)->data($data)->where('id')->eq($reportID)->autocheck()
                ->batchCheck('code', 'notempty')
                ->check('code', 'unique', "id != $reportID")
                ->exec();

            if(dao::isError()) die(js::error(dao::getError()));
            die(js::reload('parent.parent'));
        }

        $this->lang->crystal->moduleList = array(
            'test'=>$this->lang->crystal->moduleList['test'],
        );

        $this->view->report = $report;
        $this->display();
    }

    /**
     * Delete report.
     *
     * @param  int    $reportID
     * @param  string $confirm yes|no
     * @param  string $from
     * @access public
     * @return void
     */
    public function deleteReport($reportID = 0, $confirm = 'no', $from = '')
    {
        $this->loadModel('report');
        if($from) $this->lang->navGroup->report = 'system';

        if($confirm == 'no')
        {
            die(js::confirm($this->lang->crystal->confirmDelete, $this->createLink('report', 'deleteReport', "reportID=$reportID&confirm=yes")));
        }

        $this->dao->delete()->from(TABLE_REPORT)->where('id')->eq($reportID)->exec();
        die(js::reload('parent'));
    }

    /**
     * Report details.
     *
     * @param  int    $reportID
     * @param  string $reportModule
     * @access public
     * @return void
     */
    public function show($reportID, $reportModule = '')
    {
        $this->loadModel('report');
        if($reportModule == 'program')
        {
            $this->loadModel('project')->setMenu($this->session->project);
            $this->view->projectID = $this->session->project;
        }
        else
        {
            /* Compatible with PHP5.x. */
            $reportModuleMenu = $this->lang->report->menu->$reportModule;
            $reportModuleMenu['alias'] = 'show';
            $this->lang->report->menu->$reportModule = $reportModuleMenu;
        }

        $report = $this->report->getReportByID($reportID);
        if(!$report)
        {
            echo js::alert($this->lang->crystal->errorNoReport);
            echo js::locate('back');
        }

        if(isset($this->config->maxVersion) and $this->session->project) $this->report->buildReportList($this->session->project);

        $this->session->set('reportSQL', $report->sql);
        $this->session->set('reportParams', $report->params);
        $this->session->set('sqlVars', $report->vars);
        $this->session->set('sqlLangs', $report->langs);

        $this->view->submenu    = $reportModule;
        $this->view->setVars    = false;
        $this->view->title      = $this->report->replace4Workflow($report->name[$this->app->getClientLang()]);
        $this->view->position[] = $report->name[$this->app->getClientLang()];

        $sql = $report->sql;
        if($report->vars)
        {
            $sqlVars             = json_decode($report->vars, true);
            $this->view->setVars = true;
            $this->view->sqlVars = $sqlVars;
            if(isset($_POST['sqlVars']))
            {
                $sqlVarValues = $this->post->sqlVars;
            }
            else
            {
                $sqlVarValues = array();
                foreach($sqlVars['varName'] as $i => $varName)
                {
                    $varType = ($sqlVars['requestType'][$i] == 'select') ? $sqlVars['selectList'][$i] : $sqlVars['requestType'][$i];
                    $sqlVarValues[$varName] = isset($sqlVars['default'][$i]) ? $sqlVars['default'][$i] : '';
                    if($varType == 'dept' and empty($sqlVarValues[$varName])) $sqlVarValues[$varName] = 0;
                }
            }

            $this->session->set('sqlVarValues', serialize($sqlVarValues));
            foreach($sqlVars['varName'] as $sqlVar)
            {
                $sqlVarValues[$sqlVar] = $this->report->processSqlVar($sqlVarValues[$sqlVar]);
                $sql = str_replace('$' . $sqlVar, $this->dbh->quote($sqlVarValues[$sqlVar]), $sql);
            }

            $this->view->sqlVarValues = $sqlVarValues;
        }

        /* replace define table name to real table name. */
        $sql           = $this->report->replaceTableNames($sql);
        $tableAndField = $this->report->getTables($sql);
        $tables        = $tableAndField['tables'];
        $fields        = $tableAndField['fields'];

        /* Data will be displayed after clicking query. */
        $dataList = array();
        if(!empty($_POST)) $dataList = $this->dao->query($sql)->fetchAll();

        $this->loadModel('workflowfield');
        $moduleNames = array();
        foreach($tables as $table)
        {
            if(strpos($table, $this->config->db->prefix) === false) continue;
            $module = str_replace($this->config->db->prefix, '', $table);
            if($module == 'case')   $module = 'testcase';
            if($module == 'module') $module = 'tree';

            /* Code for workflow.*/
            if(strpos($module, 'flow_') !== false)
            {
                $moduleName = substr($module, 5);
                $flowFields = $this->workflowfield->getFieldPairs($moduleName);
                $this->lang->$moduleName = new stdclass();

                foreach($flowFields as $flowField => $fieldName) 
                {
                    if(!$flowField) continue;
                    $this->lang->$moduleName->$flowField = $fieldName;
                }
                $moduleNames[$table] = $module;
            }
            else
            {
                if($this->app->loadLang($module)) $moduleNames[$table] = $module;
            }
        }

        $data          = (array)current($dataList);
        $this->session->set('reportDataCount', count($dataList));
        $moduleNames   = array_reverse($moduleNames, true);
        $reverseFields = array_reverse($fields, true);
        $mergeFields   = $this->report->mergeFields(array_keys($data), $reverseFields, $moduleNames);

        if($report->step == 2)
        {
            $condition = json_decode($report->params, true);
            if(!empty($condition['isUser'])) $this->view->users = $this->loadModel('user')->getPairs('noletter');

            $groupLang['group1'] = $this->report->getGroupLang($condition['group1'], $reverseFields, $moduleNames);
            $groupLang['group2'] = $this->report->getGroupLang($condition['group2'], $reverseFields, $moduleNames);
            list($headers, $reportData) = $this->report->processData($dataList, $condition);

            $this->view->headerNames = $this->report->getHeaderNames($fields, $moduleNames, $condition);
            $this->view->headers     = $headers;
            $this->view->condition   = $condition;
            $this->view->reportData  = $reportData;
            $this->view->groupLang   = $groupLang;
        }

        $this->view->dataList = $dataList;
        $this->view->tables   = $tables;
        $this->view->step     = $report->step;
        $this->view->reportID = $reportID;
        $this->view->report   = $report;
        $this->view->fields   = empty($mergeFields) ? array() : $mergeFields;

        $this->display();
    }

    /**
     * Export report.
     *
     * @param  int    $step
     * @param  int    $reportID
     * @access public
     * @return void
     */
    public function crystalExport($step = 2, $reportID = 0)
    {
        $this->loadModel('report');
        if($_POST)
        {
            $tables = array();
            $hasSqlVar = false;
            if($step != 0 and $this->session->reportSQL)
            {
                $sql    = $this->session->reportSQL;
                $result = $this->report->checkSqlVar($sql);

                if($result)
                {
                    $sqlVarValues = $this->session->sqlVarValues ? unserialize($this->session->sqlVarValues) : array();
                    if($sqlVarValues)
                    {
                        foreach($result as $sqlVar)
                        {
                            $sqlVarValues[$sqlVar] = $this->report->processSqlVar($sqlVarValues[$sqlVar]);
                            $sql = str_replace('$' . $sqlVar, $this->dbh->quote($sqlVarValues[$sqlVar]), $sql);
                        }
                    }

                    $hasSqlVar    = true;
                    $sqlVars      = json_decode($this->session->sqlVars, true);
                    $sqlVarValues = $sqlVarValues;
                }
                $rowspan  = array();
                $dataList = array();
                $fields   = array();
                if(!$result or $this->session->sqlVarValues)
                {
                    /* replace define table name to real table name. */
                    $sql = $this->report->replaceTableNames($sql);
                    $tableAndField = $this->report->getTables($sql);
                    $tables   = $tableAndField['tables'];
                    $fields   = $tableAndField['fields'];

                    $dataList = $this->dbh->query($sql)->fetchAll();
                                    
                    /* Set rowspan. */
                    $colField      = array_keys($fields);
                    $firstColField = array_shift($colField);
                    $dataLists     = $dataList;
                    $prevData      = '';
                    $colNum        = 0;
                    foreach($dataLists as $i => $colData)
                    {
                        if($prevData == $colData->$firstColField)
                        {
                            if(!isset($rowspan[$colNum]['rows'][$firstColField])) $rowspan[$colNum]['rows'][$firstColField] = 1; 
                            $rowspan[$colNum]['rows'][$firstColField] ++;
                            continue;
                        }

                        $prevData = $colData->$firstColField;
                        $colNum   = $i;
                    }
                    
                    $moduleNames = array();
                    foreach($tables as $table)
                    {
                        if(strpos($table, $this->config->db->prefix) === false) continue;
                        $module = str_replace($this->config->db->prefix, '', $table);
                        if($module == 'case')   $module = 'testcase';
                        if($module == 'module') $module = 'tree';
                        /* Code for workflow. */
                        if(strpos($module, 'flow_') !== false)
                        {
                            $moduleName = substr($module, 5);
                            $flowFields = $this->loadModel('workflowfield')->getFieldPairs($moduleName);
                            $this->lang->$moduleName = new stdclass();

                            foreach($flowFields as $flowField => $fieldName) 
                            {
                                if(!$flowField) continue;
                                $this->lang->$moduleName->$flowField = $fieldName;
                            }
                            $moduleNames[$table] = $module;
                        }
                        else
                        {
                            if($this->app->loadLang($module)) $moduleNames[$table] = $module;
                        }
                    }

                    $data = (array)current($dataList);
                    $this->session->set('reportDataCount', count($dataList));
                    $moduleNames   = array_reverse($moduleNames, true);
                    $reverseFields = array_reverse($fields, true);
                    $mergeFields   = $this->report->mergeFields(array_keys($data), $reverseFields, $moduleNames);
                }

                if($step == 2 and $this->session->reportParams)
                {
                    $condition = json_decode($this->session->reportParams, true);
                    if(!empty($condition['isUser'])) $users = $this->loadModel('user')->getPairs('noletter');

                    $reportData = array();
                    $headers    = array();
                    $groupLang['group1']  = $this->report->getGroupLang($condition['group1'], $reverseFields, $moduleNames);
                    $groupLang['group2']  = $this->report->getGroupLang($condition['group2'], $reverseFields, $moduleNames);
                    list($headers, $reportData) = $this->report->processData($dataList, $condition);

                    $headerNames = $this->report->getHeaderNames($fields, $moduleNames, $condition);
                }

                $fields = empty($mergeFields) ? array() : $mergeFields;

                if($step == 2)
                {
                    $step2Fields = array();
                    $step2Fields['group1'] = $fields[$condition['group1']];
                    if($condition['group2']) $step2Fields['group2'] = $fields[$condition['group2']];

                    /* Set dataCols. */
                    $dataCols   = array();
                    $sqlLangs   = json_decode($this->session->sqlLangs, true);
                    $clientLang = $this->app->getClientLang();
                    foreach($headers as $i => $reportFields)
                    {
                        $showed[$i] = false;
                        foreach($reportFields as $field => $reportField)
                        {
                            if(isset($headerNames[$i]))
                            {
                                foreach($headerNames[$i] as $key => $headerName)
                                {
                                    $step2Fields[] = empty($headerName) ? $this->lang->report->null : $headerName;
                                    $percentKey = (empty($key) ? 'null' : $key) . 'Percent';
                                    if(isset($condition['percent'][$i]) and isset($condition['showAlone'][$i]) and $condition['contrast'][$i] != 'crystalTotal') $step2Fields[] = isset($sqlLangs[$percentKey][$clientLang]) ? $sqlLangs[$percentKey][$clientLang] : $this->lang->crystal->percentAB;
                                    $dataCols[$i][] = $key;
                                }
                                $showed[$i] = true;
                            }
                            elseif(isset($condition['isUser']['reportField'][$i]))
                            {
                                $user = zget($users, $reportField, $reportField);
                                $step2Fields[] = empty($user) ? $this->lang->report->null : $user;
                                $dataCols[$i][] = $reportField;
                            }
                            else
                            {
                                $step2Fields[] = zget($fields, $reportField, $reportField);
                                $dataCols[$i][] = $reportField;
                            }
                            if($showed[$i]) break;
                            $percentKey = $reportField . 'Percent';
                            if(isset($condition['percent'][$i]) and isset($condition['showAlone'][$i]) and $condition['contrast'][$i] != 'crystalTotal') $step2Fields[] = isset($sqlLangs[$percentKey][$clientLang]) ? $sqlLangs[$percentKey][$clientLang] : $this->lang->crystal->percentAB;
                        }
                        if(isset($condition['reportTotal'][$i])) $step2Fields[] = $this->lang->crystal->total;
                        $percentKey = $reportField . 'Percent';
                        if(isset($condition['percent'][$i]) and isset($condition['showAlone'][$i]) and $condition['contrast'][$i] == 'crystalTotal') $step2Fields[] = isset($sqlLangs[$percentKey][$clientLang]) ? $sqlLangs[$percentKey][$clientLang] : $this->lang->crystal->percentAB;
                    }

                    foreach($step2Fields as $i => $field)
                    {
                        if(is_numeric($i))
                        {
                            $step2Fields["col$i"] = $field;
                            unset($step2Fields[$i]);
                        }
                    }

                    $allTotal = array();
                    $rowspan  = array();
                    $colspan  = array();
                    $row      = 0;
                    foreach($reportData as $group1 => $group1Data)
                    {
                        if($condition['group2'])
                        {
                            $group2Num = 0;
                            foreach($group1Data as $group2 => $data)
                            {
                                $reportData = new stdclass();
                                $group2Num++;
                                if($group2Num == 1)
                                {
                                    if(count($group1Data) > 1)
                                    {
                                        $rowspan[$row]['rows'] = ',group1,';
                                        $rowspan[$row]['num'] = count($group1Data);
                                    }
                                    $reportData->group1 = $group1;
                                    if(!empty($condition['isUser']['group1']))
                                    {
                                        $reportData->group1 = zget($users, $group1, $group1);
                                    }
                                    elseif($groupLang['group1'])
                                    {
                                        $reportData->group1 = zget($groupLang['group1'], $group1, $group1);
                                    }
                                }

                                $reportData->group2 = $group2;
                                if(!empty($condition['isUser']['group2']))
                                {
                                    $reportData->group2 = zget($users, $group2, $group2);
                                }
                                elseif($groupLang['group2'])
                                {
                                    $reportData->group2 = zget($groupLang['group2'], $group2, $group2);
                                }

                                $data         = $this->report->getCellData($data, $dataCols, $condition);
                                $allTotal     = $data['allTotal'];
                                $cellDataList = $data['cellData'];

                                foreach($cellDataList as $i => $cellData) $reportData->{"col$i"} = $cellData;
                                $rows[$row] = $reportData;
                                $row++;
                            }
                        }
                        else
                        {
                            $reportData = new stdclass();
                            $reportData->group1 = $group1;
                            if(!empty($condition['isUser']['group1']))
                            {
                                $reportData->group1 = zget($users, $group1, $group1);
                            }
                            elseif($groupLang['group1'])
                            {
                                $reportData->group1 = zget($groupLang['group1'], $group1, $group1);
                            }

                            $data         = $this->report->getCellData($group1Data, $dataCols, $condition);
                            $allTotal     = $data['allTotal'];
                            $cellDataList = $data['cellData'];
                            foreach($cellDataList as $i => $cellData)
                            {
                                $reportData->{"col$i"} = $cellData;
                            }
                            $rows[$row] = $reportData;
                            $row++;
                        }
                    }
                    $rows[$row] = new stdclass();
                    $rows[$row]->group1 = $this->lang->crystal->total;
                    if($condition['group2'])
                    {
                        $colspan[$row]['cols'] = ',group1,';
                        $colspan[$row]['num'] = 2;
                    }
                    foreach($step2Fields as $i => $field)
                    {
                        if(strpos($i, 'group') === false)
                        {
                            $i = str_replace('col', '', $i);
                            $rows[$row]->{"col$i"} = $allTotal[$i];
                        }
                    }
                    $fields   = $step2Fields;
                    $dataList = $rows;
                }

                if(isset($rowspan))$this->post->set('rowspan', $rowspan);
                if(isset($colspan))$this->post->set('colspan', $colspan);
                $this->post->set('fields', $fields);
                $this->post->set('rows', $dataList);
                $this->post->set('kind', 'report');
                $this->post->set('fileField', 'false');

                $this->loadModel('file');
                $this->lang->excel->title->report = $this->post->fileName;
                $this->config->excel->titleFields[] = 'group1';
                $this->config->excel->titleFields[] = 'group2';
                $this->config->excel->cellHeight    = 25;
                $this->config->excel->width->title  = 25;
                die($this->fetch('file', 'export2' . $this->post->fileType, $_POST));
            }
        }

        $report = $reportID ? $this->report->getReportByID($reportID) : '';
        $this->view->name = $reportID ? $report->name[$this->app->getClientLang()] : '';
        $this->display();
    }

    /**
     * 每日将前一天的统计数据存储下来.
     *
     * @param string $queryType
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     * @access public
     * @return void
     */
    public function bugTrendTimer()
    {
        set_time_limit(0);
        $this->report = $this->loadModel('report');

        $timeKey = date('Y-m-d', strtotime('-1 day'));

        $taskBuilded = $this->dao->select('id')->from(TABLE_REPORT_TASK_BUG)
        ->where('timeKey')->eq($timeKey)
        ->fetch();

        if(!$taskBuilded)
        {
            // 今日首次运行
            $projects = $this->loadModel('project')->getPairs();

            $projectTasks = $this->dao->select('project')->from(TABLE_REPORT_TASK_BUG)
                ->where('timeKey')->eq($timeKey)
                ->fetchAll('project');

            foreach ($projects as $projectID => $projectName)
            {
                if(isset($projectTasks[$projectID])) continue;

                $this->dao->insert(TABLE_REPORT_TASK_BUG)
                    ->set('project')->eq($projectID)
                    ->set('timeKey')->eq($timeKey)
                    ->exec();
            }

            echo 'build task success,project num:' . count($projects) . "\n";
            return;
        }

        $taskNum    = 20;
        $countTime  = $timeKey . ' 23:59:59';
        $countTypes = [
            // 激活数，激活时间为当前时间的bug
            'activated',
            // 关闭数，关闭时间为当前时间的bug
            'closed',
            // 累计待关闭，当前处于待关闭的bug，已解决，但未关闭
            'totalToClose',
            // 累计激活，当前处于激活状态的bug，reopened, not closed
            'totalActivated',
            // 累计待解决，当前处于待解决状态的bug，not closed，not resolved
            'totalToResolve',
        ];

        $projects = $this->dao->select('project')->from(TABLE_REPORT_TASK_BUG)
            ->where('timeKey')->eq($timeKey)
            ->andWhere('status')->eq('wait')
            ->limit($taskNum)
            ->fetchAll('project');
        if(!$projects)
        {
            echo 'no more task' . "\n";
            return;
        }

        $testtasksGroup = $this->loadModel('testtask')->getProjectTestTasksGroup(array_keys($projects));
        $projectCountTimes = 0;
        $projectCountedTimes = 0;
        foreach($projects as $projectID => $project)
        {
            $testtaskList = isset($testtasksGroup[$projectID]) ? $testtasksGroup[$projectID] : [];

            $emptyTesttask     = new stdClass();
            $emptyTesttask->id = 0;

            $testtaskList[] = $emptyTesttask;

            foreach($testtaskList as $testtask)
            {
                $testtaskID = $testtask->id;
                foreach($countTypes as $countType)
                {
                    $existValue = $this->dao->select('countValue')->from(TABLE_REPORT_HISTORY_BUG)
                        ->where('countType')->eq($countType)
                        ->andWhere('project')->eq($projectID)
                        ->andWhere('testtask')->eq($testtaskID)
                        ->andWhere('countTime')->eq($countTime)
                        ->fetch();

                    if($existValue)
                    {
                        $projectCountedTimes++;
                        continue;
                    }

                    $value = $this->report->countBugByReportFilter($timeKey, [$projectID], [$testtaskID], $countType);

                    $this->dao->insert(TABLE_REPORT_HISTORY_BUG)
                        ->set('countType')->eq($countType)
                        ->set('countValue')->eq($value)
                        ->set('project')->eq($projectID)
                        ->set('testtask')->eq($testtaskID)
                        ->set('time')->eq(date('Y-m-d H:i:s'))
                        ->set('timeKey')->eq($timeKey)
                        ->set('countTime')->eq($countTime)
                        ->exec();

                    $projectCountTimes++;
                }
            }

            $this->dao->update(TABLE_REPORT_TASK_BUG)
                ->set('status')->eq('done')
                ->where('project')->eq($projectID)
                ->andWhere('timeKey')->eq($timeKey)
                ->exec();
        }

        echo 'count success,project num:' . count($projects) . ',count times:' . $projectCountTimes . ',skiped times:' . $projectCountedTimes . "\n";
    }

    /**
     * Browse the bug trend report.
     *
     * @param  string $queryType
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bugTrend($queryType = 'default')
    {
        $this->loadModel('report');

        /* Get query conditions. */
        $end             = '';
        $begin           = date('Y-m-d', strtotime('-1 month'));
        $projectList     = '';
        $chartMode       = '';
        $testtaskList    = array();
        $testtasks       = array();

        if($queryType == 'page')
        {
            $params = $this->session->bugTrendQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $begin        = $params['begin'];
            $end          = $params['end'];
            $projectList  = $params['project'];
            $testtaskList = $params['testtask'];
            $chartMode    = $params['chartMode'];

            $this->post->set('begin', $begin);
            $this->post->set('end', $end);
            $this->post->set('project', $projectList);
            $this->post->set('testtask', $testtaskList);
            $this->post->set('chartMode', $chartMode);
        }

        if(!empty($_POST))
        {
            /* Get query conditions. */
            $data            = fixer::input('post')->get();
            $data->begin     = !empty($data->begin)     ? $data->begin     : '';
            $data->end       = !empty($data->end)       ? $data->end       : '';
            $data->project   = !empty($data->project)   ? $data->project   : array();
            $data->testtask  = !empty($data->testtask)  ? $data->testtask  : array();
            $data->chartMode = !empty($data->chartMode) ? $data->chartMode : '';

            $data->project  = array_filter($data->project, function($value){return !empty($value);});
            $data->testtask = array_filter($data->testtask, function($value){return !empty($value);});

            $begin        = $data->begin;
            $end          = $data->end;
            $projectList  = !empty($data->project)  ? $data->project  : array();
            $testtaskList = !empty($data->testtask) ? $data->testtask : array();
            $chartMode    = $data->chartMode;
        }

        /* When there is no active query, the data will not be displayed. */
        if(empty($projectList) and empty($testtaskList))
        {
            $trendData = array();
        }
        else
        {
            $trendData = $this->report->getTrendData($begin, $end, $projectList, $testtaskList, $chartMode);
            $chartMode = $trendData['chartMode'];
        }

        $param     = array('begin' => $begin, 'end' => $end, 'project' => $projectList, 'testtask' => $testtaskList);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('bugTrendQueryData', $queryData);
        $this->app->rawParams['queryType'] = 'page';

        if(!empty($projectList)) $testtasks = $this->loadModel('testtask')->getProjectTestTasks($projectList);

        $this->view->title      = $this->lang->report->bugTrend;
        $this->view->position[] = $this->lang->report->bugTrend;

        $this->view->trendData    = $trendData;
        $this->view->projects     = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $this->view->testtasks    = $testtasks;
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->projectList  = $projectList;
        $this->view->testtaskList = $testtaskList;
        $this->view->chartMode    = $chartMode;

        $this->view->chartType  = 'default';
        $this->view->reportType = 'bugTrend';

        $this->display();
    }

    /**
     * Browse the bug discovery rate report.
     *
     * @param  string $queryType
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bugEscape($queryType = 'default', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('report');

        /* Get query conditions. */
        $projectList = '';
        $deptList    = '';

        if($queryType == 'page')
        {
            $params = $this->session->bugEscapeQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $deptList    = $params['dept'];
            $projectList = $params['project'];

            $this->post->set('dept', $deptList);
            $this->post->set('project', $projectList);
        }

        /* Load pager */
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        if(!empty($_POST))
        {
            /* Get query conditions. */
            $data = fixer::input('post')->get();
            $data->dept    = !empty($data->dept)    ? $data->dept    : array();
            $data->project = !empty($data->project) ? $data->project : array();

            $data->dept    = array_filter($data->dept,    function($value){return !empty($value);});
            $data->project = array_filter($data->project, function($value){return !empty($value);});

            $deptList    = !empty($data->dept)    ? $data->dept    : array();
            $projectList = !empty($data->project) ? $data->project : array();
        }

        $userAccountList = '';
        if(!empty($deptList))
        {
            $this->loadModel('dept');
            $queryAccount = array();
            $queryDept    = array();
            if($deptList)
            {
                foreach($deptList as $deptID)
                {
                    if(empty($deptID)) continue;
                    $childDepts = $this->dept->getAllChildID($deptID);
                    foreach($childDepts as $childDeptID) $queryDept[$childDeptID] = $childDeptID;
                }
                $deptUserList = $this->dept->getUserPairsByDeptID($queryDept);
                foreach($deptUserList as $account => $realname) $queryAccount[$account] = $account;
            }

            $userAccountList = array_keys($queryAccount);
        }

        if(!empty($deptList) and empty($userAccountList))
        {
            $projectDataList = array();
        }
        else
        {
            $projectDataList = $this->dao->select('id,name,PM')->from(TABLE_PROJECT)
                ->where('deleted')->eq('0')
                ->andWhere('`type`')->eq('project')
                ->beginIF(!empty($projectList))->andWhere('id')->in($projectList)->fi()
                ->beginIF(!empty($userAccountList))->andWhere('PM')->in($userAccountList)->fi()
                ->orderBy('id_asc')
                ->page($pager)
                ->fetchAll('id');

            /* When there is no active query, the data will not be displayed. */
            if(empty($deptList) and empty($projectList)) $projectDataList = array();

            $projectDataList = $this->report->getBugEscapeList($projectDataList);
        }

        $param     = array('dept' => $deptList, 'project' => $projectList);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('bugEscapeQueryData', $queryData);
        $this->app->rawParams['queryType'] = 'page';

        $this->view->title      = $this->lang->report->bugEscape;
        $this->view->position[] = $this->lang->report->bugEscape;

        $this->view->queryType       = $queryType;
        $this->view->pager           = $pager;
        $this->view->projectDataList = $projectDataList;
        $this->view->projects        = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $this->view->depts           = array('' => '') + $this->loadModel('dept')->getSpecifyLevelDeptList();
        $this->view->projectList     = $projectList;
        $this->view->deptList        = $deptList;

        $this->view->chartType  = 'default';
        $this->view->reportType = 'bugEscape';

        $this->display();
    }

    /**
     * Browse the bug tester report.
     *
     * @param  string $queryType
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function bugTester($queryType = 'default', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('report');

        /* Get query conditions. */
        $userAccountList = array();
        $end             = '';
        $begin           = '';
        $accountList     = '';
        $deptList        = '';
        $projectList     = '';

        $newBegin = '';
        $newEnd   = '';

        if($queryType == 'page')
        {
            $params = $this->session->bugTesterQueryData;
            $params = helper::safe64Decode($params);
            $params = json_decode($params, true);

            $begin       = $params['begin'];
            $end         = $params['end'];
            $accountList = $params['account'];
            $deptList    = $params['dept'];
            $projectList = $params['project'];

            $userAccountList = $accountList;

            $this->post->set('begin', $begin);
            $this->post->set('end', $end);
            $this->post->set('account', $accountList);
            $this->post->set('dept', $deptList);
            $this->post->set('project', $projectList);
        }

        if(!empty($_POST))
        {
            /* Get query conditions. */
            $data = fixer::input('post')->get();

            if(!empty($data->begin)) $begin = $data->begin;
            if(!empty($data->end)) $end = $data->end;

            $data->begin   = !empty($data->begin)   ? $data->begin   : '2020-01-01';
            $data->end     = !empty($data->end)     ? $data->end     : date('Y-m-d');
            $data->account = !empty($data->account) ? $data->account : array();
            $data->dept    = !empty($data->dept)    ? $data->dept    : array();
            $data->project = !empty($data->project) ? $data->project : array();

            $data->account = array_filter($data->account, function($value){return !empty($value);});
            $data->dept    = array_filter($data->dept,    function($value){return !empty($value);});
            $data->project = array_filter($data->project, function($value){return !empty($value);});

            $accountList = !empty($data->account) ? $data->account : array();
            $deptList    = !empty($data->dept)    ? $data->dept    : array();
            $projectList = !empty($data->project) ? $data->project : array();

            $this->loadModel('dept');
            $queryAccount = array();
            $queryDept    = array();
            if($accountList) $queryAccount = $accountList;
            $queryAccount = array_filter($queryAccount);
            $queryAccount = array_flip($queryAccount);

            $deptUserAccountList = [];
            if($deptList)
            {
                foreach($deptList as $deptID)
                {
                    if(empty($deptID)) continue;
                    $childDepts = $this->dept->getAllChildID($deptID);
                    foreach($childDepts as $childDeptID) $queryDept[$childDeptID] = $childDeptID;
                }
                $deptUserList = $this->dept->getUserPairsByDeptID($queryDept);
                foreach($deptUserList as $account => $realname) $deptUserAccountList[] = $account;
            }

            if(empty($accountList))
            {
                $userAccountList = $deptUserAccountList;
            }
            else
            {
                $userAccountList = $accountList;
            }

            if(!empty($deptList))
            {
                $userAccountList = array_intersect($userAccountList, $deptUserAccountList);
            }

            $newBegin = $data->begin . ' 00:00:00';
            $newEnd   = $data->end   . ' 23:59:59';
        }


        /* When there is no active query, the data will not be displayed. */
        if(empty($accountList) and empty($deptList) and empty($projectList))
        {
            $queryAccountList = array();
        }
        else
        {
            $skipUserAccountList = false;
            if(empty($accountList) && empty($projectList) && empty($deptList)) $skipUserAccountList = true;
            $createBugs = $this->report->getTesterBugList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);
            $effectBugs = $this->report->getTesterEffectiveBugList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);
            $cases      = $this->report->getTesterCaseList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);
            $runs       = $this->report->getTesterCaseRunList($userAccountList, $projectList, $newBegin, $newEnd, $skipUserAccountList);

            $userList = $this->report->mergeDataByOpenedBy(array(), $userAccountList, $createBugs, $effectBugs, $cases, $runs);
            $queryAccountList = array_keys($userList);
        }

        /* Load pager */
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);
        $userInfoList = $this->dao->select('account,realname,dept')->from(TABLE_USER)
            ->where('account')->in($queryAccountList)
            ->andWhere('deleted')->eq('0')
            ->page($pager)
            ->fetchAll();

        $queryAccountList = array();
        foreach($userInfoList as $user)
        {
            $validUser            = $userList[$user->account];
            $user->caseTotal      = $validUser->caseTotal;
            $user->runs           = $validUser->runTotal;
            $user->bugTotal       = $validUser->createBugTotal;
            $user->effectiveTotal = $validUser->effectBugTotal;
            $user->projects       = $validUser->projects;

            $queryAccountList[] = $user->account;
        }

        $param     = array('begin' => $begin, 'end' => $end, 'account' => $accountList, 'dept' => $deptList, 'project' => $projectList);
        $queryData = helper::safe64Encode(json_encode($param));
        $this->session->set('bugTesterQueryData', $queryData);
        $this->app->rawParams['queryType'] = 'page';

        $depts = $this->loadModel('dept')->getOptionMenu();
        unset($depts[0]);

        $this->view->title      = $this->lang->report->bugTester;
        $this->view->position[] = $this->lang->report->bugTester;

        $this->view->userInfoList = $userInfoList;
        $this->view->queryType    = $queryType;
        $this->view->pager        = $pager;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter|noclosed|nodeleted');
        $this->view->depts        = $depts;
        $this->view->projects     = array('' => '') + $this->loadModel('project')->getPairsCodeName();
        $this->view->begin        = $begin;
        $this->view->end          = $end;
        $this->view->deptList     = $deptList;
        $this->view->accountList  = $accountList;
        $this->view->projectList  = $projectList;

        $this->view->chartType  = 'default';
        $this->view->reportType = 'bugTester';

        $this->display();
    }

    /**
     * Use case execution statistics table.
     *
     * @param  int $applicationID
     * @param  int $productID
     * @param  int $projectID
     * @access public
     * @return void
     */
    public function casesrun($applicationID = 0, $productID = 0, $projectID = 0)
    {
        $this->loadModel('report');

        $products     = ['' => ''] + $this->loadModel('product')->getPairs();
        $applications = ['' => ''] + $this->loadModel('application')->getPairs();
        $projects     = array('' => '') + $this->loadModel('project')->getPairsCodeName();

        $modules = [];
        if($applicationID or $productID or $projectID) $modules = $this->report->getCasesRun($applicationID, $productID, $projectID);

        $this->app->loadLang('testcase');
        $this->view->title         = $this->lang->report->casesrun;
        $this->view->products      = $products;
        $this->view->productID     = $productID;
        $this->view->applications  = $applications;
        $this->view->applicationID = $applicationID;
        $this->view->projects      = $projects;
        $this->view->projectID     = $projectID;
        $this->view->modules       = $modules;

        $this->view->chartType  = 'default';
        $this->view->reportType = 'casesrun';

        $this->display();
    }

    /**
     * Test case statistics table.
     *
     * @param int $productID
     * @access public
     * @return void
     */
    public function testcase($applicationID = 0, $productID = 0, $projectID = 0)
    {
        $this->loadModel('report');

        $products     = ['' => ''] + $this->loadModel('product')->getPairs();
        $applications = ['' => ''] + $this->loadModel('application')->getPairs();
        $projects     = array('' => '') + $this->loadModel('project')->getPairsCodeName();

        $modules = [];
        if($applicationID or $productID or $projectID) $modules = $this->report->getTestcases($applicationID, $productID, $projectID);

        $this->app->loadLang('testcase');
        $this->view->title         = $this->lang->report->testcase;
        $this->view->products      = $products;
        $this->view->productID     = $productID;
        $this->view->applications  = $applications;
        $this->view->applicationID = $applicationID;
        $this->view->projects      = $projects;
        $this->view->projectID     = $projectID;
        $this->view->modules       = $modules;

        $this->view->chartType  = 'default';
        $this->view->reportType = 'testcase';

        $this->display();
    }
}
