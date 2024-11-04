<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mytestcase extends testcase
{
    public function showImport($applicationID, $productID, $branch = 0, $pagerID = 1, $maxImport = 0, $insert = '')
    {
        if(isset($this->config->bizVersion))
        {
            $appendFields = $this->dao->select('t2.*')->from(TABLE_WORKFLOWLAYOUT)->alias('t1')
                ->leftJoin(TABLE_WORKFLOWFIELD)->alias('t2')->on('t1.field=t2.field && t1.module=t2.module')
                ->where('t1.module')->eq('testcase')
                ->andWhere('t1.action')->eq('showimport')
                ->andWhere('t2.buildin')->eq(0)
                ->orderBy('order')
                ->fetchAll();

            $this->loadModel('workflowfield');
            if(!isset($this->config->testcase->appendFields)) $this->config->testcase->appendFields = '';
            foreach($appendFields as $appendField)
            {
                $this->lang->testcase->{$appendField->field} = $appendField->name;
                $this->config->testcase->exportFields .= ',' . $appendField->field;
                $this->config->testcase->appendFields .= ',' . $appendField->field;

                $appendField = $this->workflowfield->processFieldOptions($appendField);
                if($appendField->options)
                {
                    $this->config->testcase->export->listFields[] = $appendField->field;

                    $listKey = $appendField->field . 'List';
                    $this->lang->testcase->$listKey = $appendField->options;
                }
            }
            $this->view->appendFields = $appendFields;
        }

        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));
        $branch  = (int)$branch;

        /* Get product, then set menu. */
        if($this->app->openApp == 'project')
        {
            $projectID = $this->session->project;
            $this->loadModel('project')->setMenu($projectID);
            $this->view->products = $this->rebirth->getShowProductPairs($projectID, $applicationID, 'testcase');
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
            $this->testcase->createFromImport($applicationID, $productID, $branch);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate($this->session->caseList, 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "applicationID=$applicationID&productID=$productID&branch=$branch&pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        $this->config->testcase->export->listFields[] = 'applicationID';
        $this->config->testcase->export->listFields[] = 'product';
        $this->config->testcase->export->listFields[] = 'project';
        $this->config->testcase->export->listFields[] = 'execution';

        $caseLang   = $this->lang->testcase;
        $caseConfig = $this->config->testcase;
        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $data = unserialize(file_get_contents($tmpFile));
            $caseData = $data['caseData'];
            $stepData = $data['stepData'];
        }
        else
        {
            $pagerID   = 1;
            $fields    = $this->testcase->getImportFields();
            $rows      = $this->file->getRowsFromExcel($file);
            $optional  = array_flip($this->lang->testcase->categoryList);
            $columnKey = array();
            $caseData  = array();
            $stepData  = array();
            $stepVars  = 0;

            foreach($rows as $currentRow => $row)
            {
                $case = new stdclass();
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
                        $case->$field = '';
                        continue;
                    }

                    if(in_array($field, $caseConfig->import->ignoreFields)) continue;

                    if(in_array($field, $caseConfig->export->listFields))
                    {
                        if(strrpos($cellValue, '(#') === false)
                        {
                            $case->$field = $cellValue;
                            if(!isset($caseLang->{$field . 'List'}) or !is_array($caseLang->{$field . 'List'})) continue;

                            /* when the cell value is key of list then eq the key. */
                            $listKey = array_keys($caseLang->{$field . 'List'});
                            unset($listKey[0]);
                            unset($listKey['']);
                            if($field == 'stage')
                            {
                                $stages = explode("\n", $cellValue);

                                $case->stage = array();
                                foreach($stages as $stage) $case->stage[] = in_array($cellValue, $listKey) ? $cellValue : array_search($stage, $caseLang->{$field . 'List'});

                                $case->stage = join(',', $case->$field);
                                continue;
                            }

                            $fieldKey = array_search($cellValue, $caseLang->{$field . 'List'});
                            if($fieldKey) $case->$field = $fieldKey;
                        }
                        else
                        {
                            $id = trim(substr($cellValue, strrpos($cellValue,'(#') + 2), ')');
                            $case->$field = $id;
                        }
                    }
                    elseif($field == 'stepDesc' or $field == 'stepExpect')
                    {
                        $steps = $cellValue;
                        if(strpos($cellValue, "\n"))
                        {
                            $steps = explode("\n", $cellValue);
                        }
                        elseif(strpos($cellValue, "\r"))
                        {
                            $steps = explode("\r", $cellValue);
                        }
                        if(is_string($steps)) $steps = explode("\n", $steps);

                        $stepKey  = str_replace('step', '', strtolower($field));
                        $caseStep = array();
                        foreach($steps as $step)
                        {
                            $step = trim($step);
                            if(empty($step)) continue;
                            if(preg_match('/^(([0-9]+)\.[0-9]+)([.、]{1})/U', $step, $out))
                            {
                                $num     = $out[1];
                                $parent  = $out[2];
                                $sign    = $out[3];
                                $signbit = $sign == '.' ? 1 : 3;
                                $step    = trim(substr($step, strlen($num) + $signbit));
                                if(!empty($step)) $caseStep[$num]['content'] = $step;
                                $caseStep[$num]['type']    = 'item';
                                $caseStep[$parent]['type'] = 'group';
                            }
                            elseif(preg_match('/^([0-9]+)([.、]{1})/U', $step, $out))
                            {
                                $num     = $out[1];
                                $sign    = $out[2];
                                $signbit = $sign == '.' ? 1 : 3;
                                $step    = trim(substr($step, strpos($step, $sign) + $signbit));
                                if(!empty($step)) $caseStep[$num]['content'] = $step;
                                $caseStep[$num]['type'] = 'step';
                            }
                            elseif(isset($num))
                            {
                                $caseStep[$num]['content'] .= "\n" . $step;
                            }
                            else
                            {
                                if($field == 'stepDesc')
                                {
                                    $num = 1;
                                    $caseStep[$num]['content'] = $step;
                                    $caseStep[$num]['type']    = 'step';
                                }
                                if($field == 'stepExpect' and isset($stepData[$currentRow]['desc']))
                                {
                                    end($stepData[$currentRow]['desc']);
                                    $num = key($stepData[$currentRow]['desc']);
                                    $caseStep[$num]['content'] = $step;
                                }
                            }
                        }

                        unset($num);
                        unset($sign);
                        $stepVars += count($caseStep, COUNT_RECURSIVE) - count($caseStep);
                        $stepData[$currentRow][$stepKey] = $caseStep;
                    }
                    elseif($field == 'categories')
                    {
                        $case->$field = '';
                        if(trim($cellValue))
                        {
                            $categories = explode('|', $cellValue);
                            $categoriesKey = array();
                            foreach($categories as $categorie)
                            {
                                if(isset($optional[$categorie])) $categoriesKey[] = $optional[$categorie];
                            }
                            $case->$field = implode(',', $categoriesKey);
                        }
                    }
                    else
                    {
                        $case->$field = $cellValue;
                    }
                }

                if(empty($case->title)) continue;
                $caseData[$currentRow] = $case;
                unset($case);
            }

            $data['caseData'] = $caseData;
            $data['stepData'] = $stepData;
            file_put_contents($tmpFile, serialize($data));
        }

        if(empty($caseData))
        {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
            echo js::alert($this->lang->excel->noData);

            if($this->app->openApp == 'project') die(js::locate($this->createLink('project', 'testcase', "projectID={$this->session->project}")));
            die(js::locate(inlink('browse', "applicationID=$applicationID&productID=$productID&branch=$branch")));
        }

        $allCount = count($caseData);
        $allPager = 1;
        if($allCount > $this->config->file->maxImport)
        {
            if(empty($maxImport))
            {
                $this->view->applicationID = $applicationID;
                $this->view->productID     = $productID;
                $this->view->branch        = $branch;
                $this->view->allCount      = $allCount;
                $this->view->maxImport     = $maxImport;
                die($this->display());
            }

            $allPager = ceil($allCount / $maxImport);
            $caseData = array_slice($caseData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($caseData) and $this->app->openApp == 'project') die(js::locate($this->createLink('project', 'testcase', "projectID={$this->session->project}")));
        if(empty($caseData)) die(js::locate(inlink('browse', "applicationID=$applicationID&productID=$productID&branch=$branch")));

        /* Judge whether the editedCases is too large and set session. */
        $countInputVars  = count($caseData) * 12 + (isset($stepVars) ? $stepVars : 0);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $executions = array();
        if($this->app->openApp == 'project') $executions = $this->project->getExecutionByAvailable($projectID);

        $stories = array();
        $modules = array();
        if(is_numeric($productID))
        {
            $stories = $this->loadModel('story')->getProductStoryPairs($productID, $branch);
            $modules = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, $branch);
        }

        $this->view->title         = $this->lang->testcase->common . $this->lang->colon . $this->lang->testcase->showImport;
        $this->view->applicationID = $applicationID;
        $this->view->productID     = $productID;
        $this->view->stories       = $stories;
        $this->view->modules       = $modules;
        $this->view->executions    = $executions;
        $this->view->caseData      = $caseData;
        $this->view->stepData      = $stepData;
        $this->view->allCount      = $allCount;
        $this->view->allPager      = $allPager;
        $this->view->pagerID       = $pagerID;
        $this->view->isEndPage     = $pagerID >= $allPager;
        $this->view->maxImport     = $maxImport;
        $this->view->dataInsert    = $insert;
        $this->display();
    }
}
