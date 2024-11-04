<?php
/**
 * The control file of report module of zentaopms.
 *
 * @copyright   copyright 2009-2020 青岛易软天创网络科技有限公司(qingdao nature easy soft network technology co,ltd, www.cnezsoft.com)
 * @license     zpl (http://zpl.pub/page/zplv12.html)
 * @author      chunsheng wang <chunsheng@cnezsoft.com>
 * @package     report
 * @link        https://www.zentao.net
 */
include '../../control.php';
class myReport extends report
{
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
}
