<?php
/**
 * The model file of excel module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     excel
 * @link        https://www.zentao.net
 */
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mybug extends bug
{
    /**
     * Show import.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $pagerID
     * @param  int    $maxImport
     * @param  string $insert
     * @access public
     * @return void
     */
    public function showImport($applicationID, $productID, $branch = 0, $pagerID = 1, $maxImport = 0, $insert = '')
    {
        if(isset($this->config->bizVersion))
        {
            $appendFields = $this->dao->select('t2.*')->from(TABLE_WORKFLOWLAYOUT)->alias('t1')
                ->leftJoin(TABLE_WORKFLOWFIELD)->alias('t2')->on('t1.field=t2.field && t1.module=t2.module')
                ->where('t1.module')->eq('bug')
                ->andWhere('t1.action')->eq('showimport')
                ->andWhere('t2.buildin')->eq(0)
                ->orderBy('order')
                ->fetchAll();

            $this->loadModel('workflowfield');
            if(!isset($this->config->bug->appendFields)) $this->config->bug->appendFields = '';
            foreach($appendFields as $appendField)
            {
                $this->lang->bug->{$appendField->field} = $appendField->name;
                $this->config->bug->list->exportFields .= ',' . $appendField->field;
                $this->config->bug->appendFields       .= ',' . $appendField->field;

                $appendField = $this->workflowfield->processFieldOptions($appendField);
                if($appendField->options)
                {
                    $this->config->bug->export->listFields[] = $appendField->field;

                    $listKey = $appendField->field . 'List';
                    $this->lang->bug->$listKey = $appendField->options;
                }
            }
            $this->view->appendFields = $appendFields;
        }

        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));
        $branch  = (int)$branch;

        if($this->app->openApp == 'project')
        {
            $projectID = $this->session->project;
            $this->loadModel('project')->setMenu($projectID);
            $this->view->products = $this->rebirth->getShowProductPairs($projectID, $applicationID, 'bug');
        }
        else
        {
            $applicationID = $this->loadModel('rebirth')->saveState($this->applicationList, $applicationID, $productID);
            $this->rebirth->setMenu($applicationID, $productID);
            $productID = $this->rebirth->getProductIdByApplication($applicationID, $productID);
            $products  = $this->rebirth->getProductPairs($applicationID, true);
            $this->view->products = $products;
            $this->view->projects = array('0' => '') + $this->rebirth->getProductLinkProjectPairs($applicationID, $productID);
        }

        if($_POST)
        {
            $this->bug->createFromImport($applicationID, $productID, $branch);
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                if($this->app->openApp == 'project') die(js::locate($this->createLink('project', 'bug', "projectID=$projectID&applicationID=$applicationID&productID=$productID"), 'parent'));
                die(js::locate(inlink('browse', "applicationID=$applicationID&productID=$productID&branch=$branch"), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "applicationID=$applicationID&productID=$productID&branch=$branch&pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        $this->config->bug->export->listFields[] = 'applicationID';
        $this->config->bug->export->listFields[] = 'project';
        $this->config->bug->export->listFields[] = 'product';
        $this->config->bug->export->listFields[] = 'execution';

        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $bugData = unserialize(file_get_contents($tmpFile));
        }
        else
        {
            $pagerID   = 1;
            $bugLang   = $this->lang->bug;
            $bugConfig = $this->config->bug;
            $fields    = explode(',', $bugConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($bugLang->$fieldName) ? $bugLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $rows    = $this->file->getRowsFromExcel($file);
            $bugData = array();
            foreach($rows as $currentRow => $row)
            {
                $bug = new stdclass();
                foreach($row as $currentColumn => $cellValue)
                {
                    if($currentRow == 1)
                    {
                        $field = array_search($cellValue, $fields);
                        $columnKey[$currentColumn] = $field ? $field : '';
                        continue;
                    }

                    $cellValue  = preg_replace('/_x([0-9a-fA-F]{4})_/', '', $cellValue);
                    if(empty($columnKey[$currentColumn]))
                    {
                        $currentColumn++;
                        continue;
                    }
                    $field = $columnKey[$currentColumn];
                    $currentColumn++;

                    // check empty data.
                    if(empty($cellValue))
                    {
                        $bug->$field = '';
                        continue;
                    }

                    if(in_array($field, $bugConfig->import->ignoreFields)) continue;
                    if(in_array($field, $bugConfig->export->listFields))
                    {
                        if(strrpos($cellValue, '(#') === false)
                        {
                            $bug->$field = $cellValue;
                            if(!isset($bugLang->{$field . 'List'}) or !is_array($bugLang->{$field . 'List'})) continue;

                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($bugLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            $fieldKey = array_search($cellValue, $bugLang->{$field . 'List'});
                            if($fieldKey) $bug->$field = $fieldKey;
                        }
                        elseif($field == 'openedBuild')
                        {
                            $builds    = explode("\n", $cellValue);
                            $buildList = array();

                            foreach($builds as $build) $buildList[] = trim(substr($build, strrpos($build,'(#') + 2), ')');
                            $bug->$field = join(',', $buildList);
                        }
                        else
                        {
                            $id = trim(substr($cellValue, strrpos($cellValue,'(#') + 2), ')');
                            $bug->$field = $id;
                        }
                    }
                    elseif($field == 'steps')
                    {
                        $bug->$field = str_replace("\n", "\n", $cellValue);
                    }
                    else
                    {
                        $bug->$field = $cellValue;
                    }
                    $bug->deadline = isset($bug->deadline) ? $bug->deadline : '';
                }

                if(empty($bug->title)) continue;
                $bugData[$currentRow] = $bug;
                unset($bug);
            }
            file_put_contents($tmpFile, serialize($bugData));
        }

        if(empty($bugData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);

            if($this->app->openApp == 'project') die(js::locate($this->createLink('project', 'bug', "projectID={$this->session->project}")));
            die(js::locate(inlink('browse', "applicationID=$applicationID&productID=$productID&branch=$branch")));
        }

        $allCount = count($bugData);
        $allPager = 1;
        if($allCount > $this->config->file->maxImport)
        {
            if(empty($maxImport))
            {
                $this->view->allCount      = $allCount;
                $this->view->maxImport     = $maxImport;
                $this->view->applicationID = $applicationID;
                $this->view->productID     = $productID;
                $this->view->branch        = $branch;
                die($this->display());
            }

            $allPager = ceil($allCount / $maxImport);
            $bugData  = array_slice($bugData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }

        if(empty($bugData) and $this->app->openApp == 'project') die(js::locate($this->createLink('project', 'bug', "projectID={$this->session->project}")));
        if(empty($bugData)) die(js::locate(inlink('browse', "applicationID=$applicationID&productID=$productID&branch=$branch")));

        /* Judge whether the editedBugs is too large and set session. */
        $countInputVars  = count($bugData) * 14;
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $stories    = array();
        $modules    = array('0' => '/');
        $builds     = array('trunk' => $this->lang->trunk);
        $executions = array();
        if($this->app->openApp == 'project') $executions = $this->project->getExecutionByAvailable($projectID);

        if(is_numeric($productID))
        {
            $stories = $this->loadModel('story')->getProductStoryPairs($productID, $branch);
            $modules = $this->loadModel('tree')->getOptionMenu($productID, 'bug', 0, $branch);
            $builds  = $this->loadModel('build')->getProductBuildPairs($productID, $branch, 'noempty');
        }

        $this->view->title          = $this->lang->bug->common . $this->lang->colon . $this->lang->bug->showImport;
        $this->view->applicationID  = $applicationID;
        $this->view->stories        = $stories;
        $this->view->modules        = $modules;
        $this->view->builds         = $builds;
        $this->view->executions     = $executions;
        $this->view->bugData        = $bugData;
        $this->view->productID      = $productID;
        $this->view->branch         = $branch;
        $this->view->allCount       = $allCount;
        $this->view->allPager       = $allPager;
        $this->view->pagerID        = $pagerID;
        $this->view->isEndPage      = $pagerID >= $allPager;
        $this->view->maxImport      = $maxImport;
        $this->view->dataInsert     = $insert;
        $this->view->requiredFields = $this->config->bug->create->requiredFields;
        $this->view->flipBuilds     = array_flip($this->view->builds);
        $this->view->linkPlan       = $this->loadModel('productplan')->getPairs($productID, $branch, 'noempty,noterminate,nodone');
        $this->view->parentChildTypeList = $this->bug->getChildTypeParentList();

        $this->display();
    }
}
