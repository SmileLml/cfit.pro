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
     * Custom report.
     *
     * @param  int    $step
     * @param  int    $reportID
     * @param  string $from
     * @access public
     * @return void
     */
    public function custom($step = 0, $reportID = 0, $from = '')
    {
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
}
