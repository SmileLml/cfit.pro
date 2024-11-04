<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class mycaselib extends caselib
{
    public function showImport($libID, $pagerID = 1, $maxImport = 0, $insert = '')
    {
        $this->loadModel('testcase');

        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        if($_POST)
        {
            $this->caselib->createFromImport($libID);
            if($this->post->isEndPage)
            {
                unlink($tmpFile);
                die(js::locate(inlink('browse', "libID=$libID"), 'parent'));
            }
            else
            {
                die(js::locate(inlink('showImport', "libID=$libID&pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        $libraries = $this->caselib->getLibraries();
        if(empty($libraries)) $this->locate(inlink('createLib'));

        $this->caselib->setLibMenu($libraries, $libID);

        $caseLang   = $this->lang->testcase;
        $caseConfig = $this->config->testcase;
        $modules    = $this->loadModel('tree')->getOptionMenu($libID, $viewType = 'caselib', $startModuleID = 0);

        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $data = unserialize(file_get_contents($tmpFile));
            $caseData = $data['caseData'];
            $stepData = $data['stepData'];
        }
        else
        {
            $pagerID = 1;
            $fields  = explode(',', $caseConfig->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($caseLang->$fieldName) ? $caseLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $fields   = array_flip($fields);
            $rows     = $this->file->getRowsFromExcel($file);
            $optional = array_flip($this->lang->testcase->categoryList);
            $caseData = array();
            $stepData = array();
            $stepVars = 0;
            foreach($rows as $currentRow => $row)
            {
                $case = new stdclass();
                foreach($row as $currentColumn => $cellValue)
                {
                    if($currentRow == 1)
                    {
                        $columnKey[$currentColumn] = zget($fields, $cellValue, '');
                        continue;
                    }

                    $cellValue = preg_replace('/_x([0-9a-fA-F]{4})_/', '', $cellValue);
                    if(empty($columnKey[$currentColumn])) continue;
                    $field = $columnKey[$currentColumn];

                    // check empty data.
                    if(empty($cellValue))
                    {
                        $case->$field = '';
                        continue;
                    }

                    if(in_array($field, $caseConfig->import->ignoreFields)) continue;
                    if($field == 'module')
                    {
                        $case->$field = 0;
                        if(strrpos($cellValue, '(#') !== false)
                        {
                            $id = trim(substr($cellValue, strrpos($cellValue,'(#') + 2), ')');
                            $case->$field = $id;
                        }
                    }
                    elseif(in_array($field, $caseConfig->export->listFields))
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
                                if(!is_array($case->stage)) $case->stage = array();
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
                        $steps    = explode("\n", $cellValue);
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
            echo js::alert($this->lang->error->noData);
            die(js::locate($this->createLink('caselib', 'browse', "libID=$libID")));
        }

        $allCount = count($caseData);
        $allPager = 1;
        if($allCount > $this->config->file->maxImport)
        {
            if(empty($maxImport))
            {
                $this->view->allCount  = $allCount;
                $this->view->maxImport = $maxImport;
                $this->view->libID     = $libID;
                die($this->display());
            }

            $allPager = ceil($allCount / $maxImport);
            $caseData = array_slice($caseData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($caseData)) die(js::locate($this->createLink('caselib', 'browse', "libID=$libID")));

        /* Judge whether the editedCases is too large and set session. */
        $countInputVars  = count($caseData) * 9 + (isset($stepVars) ? $stepVars : 0);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $this->view->title      = $this->lang->testcase->common . $this->lang->colon . $this->lang->testcase->showImport;
        $this->view->position[] = $this->lang->testcase->showImport;

        $this->view->modules    = $modules;
        $this->view->cases      = $this->dao->select('id,module,stage,status,pri,type')->from(TABLE_CASE)->where('lib')->eq($libID)->andWhere('deleted')->eq(0)->andWhere('product')->eq(0)->fetchAll('id');
        $this->view->caseData   = $caseData;
        $this->view->stepData   = $stepData;
        $this->view->libID      = $libID;
        $this->view->allCount   = $allCount;
        $this->view->allPager   = $allPager;
        $this->view->pagerID    = $pagerID;
        $this->view->isEndPage  = $pagerID >= $allPager;
        $this->view->maxImport  = $maxImport;
        $this->view->dataInsert = $insert;
        $this->display();
    }
}
