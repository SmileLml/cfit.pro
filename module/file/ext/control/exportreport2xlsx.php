<?php
helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class myfile extends file
{
    /**
     * File count
     *
     * @var int
     * @access public
     */
    public $fileCount       = 1;

    /**
     * Data record in sharedStrings.
     *
     * @var int
     * @access public
     */
    public $record          = 0;

    /**
     * Style setting
     *
     * @var array
     * @access public
     */
    public $styleSetting    = array();

    /**
     * rels about link
     *
     * @var string
     * @access public
     */
    public $rels            = '';

    /**
     * sheet1 sheetData
     *
     * @var string
     * @access public
     */
    public $sheet1SheetData = '';

    /**
     * sheet1 params like cols mergeCells ...
     *
     * @var array
     * @access public
     */
    public $sheet1Params = array();

    /**
     * field cols.
     *
     * @var array
     * @access public
     */
    public $fieldCols = array();

    /**
     * every counts in need count.
     *
     * @var array
     * @access public
     */
    public $counts = array();

    /**
     * init for excel data.
     *
     * @access public
     * @return void
     */
    public function init()
    {
        $this->app->loadClass('pclzip', true);
        $this->zfile  = $this->app->loadClass('zfile');
        $this->fields = $this->post->fields;
        $this->rows   = $this->post->rows;

        /* Move files field to end. */
        if(isset($this->fields['files']))
        {
            $filesName = $this->fields['files'];
            unset($this->fields['files']);
            $this->fields['files'] = $filesName;
        }

        /* Init excel file. */
        $this->exportPath = $this->app->getCacheRoot() . $this->app->user->account . uniqid() . '/';
        if(is_dir($this->exportPath))$this->zfile->removeDir($this->exportPath);
        $this->zfile->mkdir($this->exportPath);
        $this->zfile->copyDir($this->app->getCoreLibRoot() . 'phpexcel/xlsx', $this->exportPath);

        $this->sharedStrings = file_get_contents($this->exportPath . 'xl/sharedStrings.xml');

        $this->fieldsKey = array_keys($this->fields);
        $this->sheet1Params['dataValidations'] = '';
        $this->sheet1Params['cols']            = array();
        $this->sheet1Params['mergeCells']      = '';
        $this->sheet1Params['hyperlinks']      = '';

        $this->counts['dataValidations'] = 0;
        $this->counts['mergeCells']      = 0;
        $this->counts['hyperlinks']      = 0;
    }

    /**
     * Export data to Excel. This is main function.
     *
     * @access public
     * @return void
     */
    public function exportreport2Xlsx()
    {
        $this->init();
        $this->setDocProps();
        $this->excelKey  = array();
        $this->maxWidths = array();
        for($i = 0; $i < count($this->fieldsKey); $i++)
        {
            $field = $this->fieldsKey[$i];
            $this->excelKey[$field] = $this->setExcelFiled($i);
            if(strpos($field, 'Date') !== false or in_array($field, $this->config->excel->dateField))$this->styleSetting['date'][$this->excelKey[$field]] = 1;
        }

            /* Show header data. */
            $this->sheet1SheetData = '<row r="1" spans="1:%colspan%">';
            foreach($this->fields as $key => $field) $this->sheet1SheetData .= $this->setCellValue($this->excelKey[$key], '1', $field);
            $this->sheet1SheetData .= '</row>';

        $i = 1;
        $excelData = array();
        foreach($this->rows as $num => $row)
        {
            $i ++;
            $columnData = array();
            $this->sheet1SheetData .= '<row r="' . $i . '" spans="1:%colspan%">';
            foreach($this->excelKey as $key => $letter)
            {
                $value = isset($row->$key) ? $row->$key : '';

                /* Merge Cells.*/
                if(isset($this->post->rowspan[$num]) and is_string($this->post->rowspan[$num]['rows']) and strpos($this->post->rowspan[$num]['rows'], ",$key,") !== false)
                {
                    $this->mergeCells($letter . $i, $letter . ($i + $this->post->rowspan[$num]['num'] - 1));
                }
                if(isset($this->post->rowspan[$num]['rows'][$key]))
                {
                    $this->mergeCells($letter . $i, $letter . ($i + $this->post->rowspan[$num]['rows'][$key] - 1));
                }
                if(isset($this->post->colspan[$num]) and strpos($this->post->colspan[$num]['cols'], ",$key,") !== false)
                {
                    $this->mergeCells($letter . $i , chr(ord($letter) + $this->post->colspan[$num]['num'] - 1) . $i);
                }

                /* Wipe off html tags.*/
                if(isset($this->config->excel->editor[$this->post->kind]) and in_array($key, $this->config->excel->editor[$this->post->kind])) $value = $this->file->excludeHtml($value);

                $this->sheet1SheetData .= $this->setCellValue($letter, $i, $value);


                /* Build excel list.*/
                if(!empty($_POST['listStyle']) and in_array($key, $this->post->listStyle)) $this->buildList($key, $i);
            }
            $this->sheet1SheetData .= '</row>';
        }

        $initField = isset($this->config->excel->{$this->post->kind}->initField) ? $this->config->excel->{$this->post->kind}->initField : array();

        $this->sheet1Params['colspan'] = count($this->excelKey) + $this->fileCount - 1;
        $endColumn = $this->setExcelFiled(count($this->excelKey) + $this->fileCount - 2);

        /*Add help lang in end.*/
        if($this->fileCount > 1) $this->mergeCells(end($this->excelKey) . 1, $endColumn . 1);
        $this->setStyle($i);

        if(!empty($this->sheet1Params['cols'])) $this->sheet1Params['cols'] = '<cols>' . join($this->sheet1Params['cols']) . '</cols>';
        if(!empty($this->sheet1Params['mergeCells'])) $this->sheet1Params['mergeCells'] = '<mergeCells count="' . $this->counts['mergeCells'] . '">' . $this->sheet1Params['mergeCells'] . '</mergeCells>';
        if(!empty($this->sheet1Params['dataValidations'])) $this->sheet1Params['dataValidations'] = '<dataValidations count="' . $this->counts['dataValidations'] . '">' . $this->sheet1Params['dataValidations'] . '</dataValidations>';
        if(!empty($this->sheet1Params['hyperlinks'])) $this->sheet1Params['hyperlinks'] = '<hyperlinks>' . $this->sheet1Params['hyperlinks'] . '</hyperlinks>';

        /* Save sheet1*/
        $sheet1 = file_get_contents($this->exportPath . 'xl/worksheets/sheet1.xml');


        $sheet1 = str_replace(array('%area%', '%xSplit%', '%cols%', '%sheetData%', '%mergeCells%', '%autoFilter%', '%dataValidations%', '%hyperlinks%', '%colspan%'),
                array($this->sheet1Params['area'], $this->sheet1Params['xSplit'], $this->sheet1Params['cols'], $this->sheet1SheetData, $this->sheet1Params['mergeCells'],
                empty($this->rows) ? '' : $endColumn,
                $this->sheet1Params['dataValidations'], $this->sheet1Params['hyperlinks'], $this->sheet1Params['colspan']), $sheet1);

        file_put_contents($this->exportPath . 'xl/worksheets/sheet1.xml', $sheet1);
        $workbookFile = file_get_contents($this->exportPath . 'xl/workbook.xml');
        $workbookFile = str_replace('%autoFilter%', empty($this->rows) ? '' : '!$A$1:$' . $endColumn . '$1', $workbookFile);
        $workbookFile = str_replace('%cascadeNames%', $this->cascadeNames, $workbookFile);
        file_put_contents($this->exportPath . 'xl/workbook.xml', $workbookFile);

        /* Save sharedStrings file. */
        $this->sharedStrings .= '</sst>';
        $this->sharedStrings  = str_replace('%count%', $this->record, $this->sharedStrings);
        $this->sharedStrings  = preg_replace('/[\x00-\x09\x0B-\x1F\x7F-\x9F]/u', '', $this->sharedStrings);
        file_put_contents($this->exportPath . 'xl/sharedStrings.xml', $this->sharedStrings);

        /* urlencode the filename for ie. */
        $fileName = uniqid() . '.xlsx';

        /* Zip to xlsx. */
        helper::cd($this->exportPath);
        $files = array('[Content_Types].xml', '_rels', 'docProps', 'xl');
        $zip   = new pclzip($fileName);
        $zip->create($files);

        $fileData = file_get_contents($this->exportPath . $fileName);
        $this->zfile->removeDir($this->exportPath);
        $this->sendDownHeader($this->post->fileName . '.xlsx', 'xlsx', $fileData);
    }

    /**
     * Set excel style
     *
     * @param  int    $excelSheet
     * @access public
     * @return void
     */
    public function setStyle($i)
    {
        $endColumn = $this->setExcelFiled(count($this->excelKey) + $this->fileCount - 2);
        $this->sheet1Params['area'] = "A1:$endColumn$i";
        $i         = isset($this->lang->excel->help->{$this->post->kind}) ? $i - 1 : $i;
        $letters   = array_values($this->excelKey);

        /* Freeze column.*/
        $this->sheet1Params['xSplit']      = '';
        if(isset($this->config->excel->freeze->{$this->post->kind}))
        {
            $xSplit = '<pane xSplit="%xSplit%" ySplit="1" topLeftCell="%topLeftCell%" activePane="bottomRight" state="frozenSplit"/><selection pane="topRight"/><selection pane="bottomLeft"/>';
            $column = $this->excelKey[$this->config->excel->freeze->{$this->post->kind}];
            $column ++;
            $this->sheet1Params['xSplit'] = str_replace(array("%xSplit%", '%topLeftCell%'), array(array_search($column, $letters), $column . '2'), $xSplit);
        }

        /* Set column width excel列宽度*/
        foreach($this->excelKey as $key => $letter)
        {
            $postion    = array_search($letter, $letters) + 1;
            $this->setWidth($postion, 22);
            if($key == 'id') $this->setWidth($postion, 5);
            if($key == 'reportDesc') $this->setWidth($postion, 40);
            if($key == 'progressStatus') $this->setWidth($postion, 45);
            if($key == 'progressDemand') $this->setWidth($postion, 45);
            if($key == 'progressRequirement') $this->setWidth($postion, 45);
            if($key == 'riskName') $this->setWidth($postion, 45);
            if($key == 'riskResolution') $this->setWidth($postion, 45);
            if($key == 'issueName') $this->setWidth($postion, 45);
            if($key == 'issueResolution') $this->setWidth($postion, 45);
            if($key == 'productPublishDesc') $this->setWidth($postion, 45);
            if($key == 'productPlan') $this->setWidth($postion, 30);
            if($key == 'productDemand') $this->setWidth($postion, 50);
            if($key == 'productRequirement') $this->setWidth($postion, 80);
        }
    }

    /**
     * Set excel filed name.
     *
     * @param  int    $count
     * @access public
     * @return void
     */
    public function setExcelFiled($count)
    {
        $letter = 'A';
        for($i = 1; $i <= $count; $i++)$letter++;
        return $letter;
    }

    /**
     * Format files field.
     *
     * @param  int    $excelSheet
     * @param  int    $i
     * @param  int    $content
     * @access public
     * @return void
     */
    public function formatFiles($letter, $i, $content)
    {
        if(empty($content))
        {
            $this->sheet1SheetData .= $this->setCellValue($letter, $i, $content);
            return;
        }

        $content    = str_replace('<br />', '<br/>', $content);
        $content    = explode('<br/>', $content);
        $fileCount  = 0;
        $fieldCount = count($this->excelKey);
        foreach($content as $key => $linkHtml)
        {
            if(empty($linkHtml)) continue;
            $fileCount ++;
            preg_match("/<a href='([^']+)'[^>]*>(.+)<\/a>/", $linkHtml, $out);
            $linkHref = $out[1];
            $linkName = $out[2];
            $fieldName = $this->setExcelFiled($fieldCount + $key - 1);
            $this->sheet1SheetData .= $this->setCellValue($fieldName, $i, $linkName);
            $this->counts['hyperlinks']++;
            $this->rels .= '<Relationship Id="rId' . $this->counts['hyperlinks'] . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="' . htmlspecialchars($linkHref) . '" TargetMode="External"/>';
            $this->sheet1Params['hyperlinks'] .= '<hyperlink ref="' . $fieldName . $i . '" r:id="rId' . $this->counts['hyperlinks'] . '" />';
        }

        if($fileCount > $this->fileCount) $this->fileCount = $fileCount;
    }

    /**
     * Write system data to sheet2
     *
     * @access public
     * @return void
     */
    public function writeSysData()
    {
        $count   = 0;
        $maxList = '';

        foreach($this->config->excel->sysDataField as $sysDataField)
        {
            $sysDataList = $sysDataField . 'List';
            $$sysDataList = isset($_POST[$sysDataList]) ? array_values($_POST[$sysDataList]) : '';

            if(!isset($_POST['cascade'][$sysDataField]))
            {
                if($$sysDataList and count($$sysDataList) > $count)
                {
                    $count   = count($$sysDataList);
                    $maxList = $sysDataList;
                }
            }
            else
            {
                $cascadeField = $_POST['cascade'][$sysDataField];
                foreach($_POST[$sysDataList] as $cascadeFieldID => $sysDatas)
                {
                    $sysCascadeKey  = $sysDataField . $cascadeField . $cascadeFieldID;
                    $$sysCascadeKey = array_values($sysDatas);
                    if(count($sysDatas) > $count)
                    {
                        $count   = count($sysDatas);
                        $maxList = $sysCascadeKey;
                    }
                }
            }
        }

        $sheetData = '';
        if($maxList)
        {
            foreach($$maxList as $key => $value)
            {
                $col = 'A';
                $row = $key + 1;
                $sheetData .= '<row r="' . $row . '" spans="1:5">';
                foreach($this->config->excel->sysDataField as $sysDataField)
                {
                    $sysDataList = $sysDataField . 'List';
                    if(!isset($_POST['cascade'][$sysDataField]))
                    {
                        $sysDatas   = isset($$sysDataList) ? $$sysDataList : '';
                        $sheetData .= $this->setCellValue($col, $row, isset($sysDatas[$key]) ? $sysDatas[$key] : '', false);
                        $this->fieldCols[$sysDataList] = $col;
                    }
                    else
                    {
                        $cascadeField = $_POST['cascade'][$sysDataField];
                        foreach($_POST[$sysDataList] as $cascadeFieldID => $sysDatas)
                        {
                            $sysCascadeKey = $sysDataField . $cascadeField . $cascadeFieldID;
                            $sysDatas      = $$sysCascadeKey;
                            $sheetData    .= $this->setCellValue($col, $row, isset($sysDatas[$key]) ? $sysDatas[$key] : '', false);
                            $this->fieldCols[$sysCascadeKey] = $col;
                            $col ++;
                        }

                        $cascadeList  = $cascadeField . 'List';
                        $cascadeDatas = isset($$cascadeList) ? $$cascadeList : '';
                        if(isset($cascadeDatas[$key]))
                        {
                            $cascadeValue   = $cascadeDatas[$key];
                            $cascadeFieldID = substr($cascadeValue, strrpos($cascadeValue, '#') + 1);
                            $cascadeFieldID = substr($cascadeFieldID, 0, strrpos($cascadeFieldID, ')'));
                            $sheetData .= $this->setCellValue($col, $row, $cascadeValue, false);
                            $this->fieldCols[$cascadeField . $sysDataField] = $col;
                            $col ++;
                            $sheetData .= $this->setCellValue($col, $row, "_{$cascadeFieldID}_", false);
                            $this->fieldCols[$cascadeField . $sysDataField . 'ID'] = $col;
                            $col ++;
                        }
                    }
                    $col ++;
                }

                $sheetData .= '</row>';
            }
        }

        $sheet2 = file_get_contents($this->exportPath . 'xl/worksheets/sheet2.xml');
        $sheet2 = sprintf($sheet2, "A1" . ($count ? ":{$col}" . $count : ''), $sheetData);
        file_put_contents($this->exportPath . 'xl/worksheets/sheet2.xml', $sheet2);

        $this->cascadeNames = '';

        foreach($this->config->excel->sysDataField as $sysDataField)
        {
            if(isset($_POST['cascade'][$sysDataField]))
            {
                $sysDataList  = $sysDataField . 'List';
                $cascadeField = $_POST['cascade'][$sysDataField];

                $cascadeList = $cascadeField . 'List';
                if(isset($_POST[$cascadeList]))
                {
                    foreach($_POST[$sysDataList] as $cascadeFieldID => $sysDatas)
                    {
                        $sysCascadeKey = $sysDataField . $cascadeField . $cascadeFieldID;
                        $lastField     = $this->fieldCols[$sysCascadeKey];

                        $cascadeName = "_{$cascadeFieldID}_";
                        if(empty($cascadeName)) continue;

                        $this->cascadeNames .= '<definedName name="' . $cascadeName . '">' . $this->lang->excel->title->sysValue . "!\${$lastField}\$1:\${$lastField}\$" . count($sysDatas) . '</definedName>';
                    }
                }
                if(isset($lastField)) $this->cascadeArea = "{$this->lang->excel->title->sysValue}!\$" . ++$lastField . "\$1:\$" . ++$lastField . "\$" . count($$cascadeList);
            }
        }
    }

    /**
     * Build drop list.
     *
     * @param  string    $field
     * @param  int       $row
     * @access public
     * @return void
     */
    public function buildList($field, $row)
    {
        $listName = $field . 'List';
        $range = is_array($this->post->$listName) ? '' : '"' . $this->post->$listName . '"';

        $sysDataField = $field == 'openedBuild' ? 'build' : $field;
        if(in_array($sysDataField, $this->config->excel->sysDataField))
        {
            if(!isset($_POST['cascade'][$sysDataField]))
            {
                $sysDataList = $sysDataField . 'List';
                $col         = $this->fieldCols[$sysDataList];
                $range       = (isset($_POST[$sysDataList])) ?  "{$this->lang->excel->title->sysValue}!\${$col}\$1:\${$col}\$" . (count($_POST[$sysDataList]) == 0 ? 1 : count($_POST[$sysDataList])) : $range;
            }
            elseif(!isset($this->cascadeArea))
            {
                return false;
            }
            else
            {
                $cascadeKey = 'A';
                $cascadeField = $_POST['cascade'][$sysDataField];
                foreach($this->fieldsKey as $fieldKey)
                {
                    if($fieldKey == $cascadeField) break;
                    $cascadeKey ++;
                }
                $range = "INDIRECT(VLOOKUP({$cascadeKey}{$row},{$this->cascadeArea},2,0))";
            }
        }

        $this->sheet1Params['dataValidations'] .= '<dataValidation type="list" showErrorMessage="1" errorTitle="' . $this->lang->excel->error->title . '" error="' . $this->lang->excel->error->info . '" sqref="' . $this->excelKey[$field] . $row . '">';
        $this->sheet1Params['dataValidations'] .= '<formula1>' . $range . '</formula1></dataValidation>';
        $this->counts['dataValidations']++;
    }

    /**
     * Merge cells
     *
     * @param  string    $start   like A1
     * @param  string    $end     like B1
     * @access public
     * @return void
     */
    public function mergeCells($start, $end)
    {
        $this->sheet1Params['mergeCells'] .= '<mergeCell ref="' . $start . ':' . $end . '"/>';
        $this->counts['mergeCells']++;
    }

    /**
     * Set column width
     *
     * @param  int    $column
     * @param  int    $width
     * @access public
     * @return void
     */
    public function setWidth($column, $width)
    {
        $this->sheet1Params['cols'][$column] = '<col min="' . $column . '" max="' . $column . '" width="' . $width . '" customWidth="1"/>';
    }

    /**
     * Set cell value
     *
     * @param  string    $key
     * @param  int       $i
     * @param  int       $value
     * @param  bool      $style
     * @access public
     * @return string
     */
    public function setCellValue($key, $i, $value, $style = true)
    {
        /* Set style. The id number in styles.xml. 每一个col 都有一个s节点，通过这个s索引的值 取cellXfs*/
        $s = '';
        if($style)
        {
            $s = $i % 2 == 0 ? '2' : '5';
            $s = $i == 1 ? 11 : $s;

            if($s != 11) //不是第一行（不是表头）
            {
                if(isset($this->styleSetting['center'][$key])) $s = $s == 2 ? 3 : 6;
                if(isset($this->styleSetting['date'][$key])) $s = $s <= 3 ? 4 : 7;
            } else {
                if(count($this->fields)>=37) { //字段必须全倒出才能渲染 不然乱了
                    if (in_array(strtoupper($key), ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'])) {
                        $s = 10; //红字 灰底
                    }
                    if (in_array(strtoupper($key), ['AJ', 'AH', 'AI'])) {
                        $s = 12; //黑字 红底
                    }
                    if (in_array(strtoupper($key), ['AE', 'AF','AG'])) {
                        $s = 13; //黑字 黄底
                    }
                    if (in_array(strtoupper($key), ['AB', 'AC', 'AD'])) {
                        $s = 14; //黑字 绿底
                    }
                }
            }
            $s = 's="' . $s . '"';
        }

        $cellValue = '';
        if(is_numeric($value))
        {
            $cellValue .= '<c r="' . $key . $i . '" ' . $s . '><v>' . $value . '</v></c>';
        }
        elseif(!empty($value))
        {
            $cellValue .= '<c r="' . $key . $i . '" ' . $s . ' t="s"><v>' . $this->record . '</v></c>';
            $this->appendSharedStrings($value);
        }
        else
        {
            $cellValue .= '<c r="' . $key . $i . '" ' . $s . '/>';
        }

        return $cellValue;
    }

    /**
     * Set doc props
     *
     * @access public
     * @return void
     */
    public function setDocProps()
    {
        $sheetTitle   = !empty($this->lang->excel->title->{$this->post->kind}) ? $this->lang->excel->title->{$this->post->kind} : $this->post->kind;
        $headingSize  = 2;
        $headingPairs = '';
        $titlesSize   = 2;
        $titlesVector = '';
        if(isset($_POST['cascade']))
        {
            $headingSize  = 4;

            foreach($_POST['cascade'] as $key => $value)
            {
                $listKey = $value . 'List';
                if(isset($_POST[$listKey]))
                {
                    $titlesSize += count($_POST[$listKey]);
                    foreach($_POST[$listKey] as $titleID => $titleName) $titlesVector .= "<vt:lpstr>_{$titleID}_</vt:lpstr>";
                }
            }
            $headingPairs = '<vt:variant><vt:lpstr>命名范围</vt:lpstr></vt:variant><vt:variant><vt:i4>' . ($titlesSize - 2) . '</vt:i4></vt:variant>';
        }

        $appFile = file_get_contents($this->exportPath . 'docProps/app.xml');
        $appFile = sprintf($appFile, $headingSize, $headingPairs, $titlesSize, $sheetTitle, $this->lang->excel->title->sysValue, $titlesVector);
        file_put_contents($this->exportPath . 'docProps/app.xml', $appFile);

        $coreFile   = file_get_contents($this->exportPath . 'docProps/core.xml');
        $createDate = date('Y-m-d') . 'T' . date('H:i:s') . 'Z';
        $coreFile   = sprintf($coreFile, $createDate, $createDate);
        file_put_contents($this->exportPath . 'docProps/core.xml', $coreFile);

        $workbookFile = file_get_contents($this->exportPath . 'xl/workbook.xml');
        $definedNames = '';
        if($this->rows or isset($_POST['cascade']))
        {
        $definedNames = '<definedNames>';
        if(isset($_POST['cascade'])) $definedNames .= '%cascadeNames%';
        if($this->rows) $definedNames .= '<definedName name="_xlnm._FilterDatabase" localSheetId="0" hidden="1">' . $sheetTitle . '%autoFilter%</definedName>';
        $definedNames .= '</definedNames>';
        }
        if(!in_array($this->post->kind, $this->config->excel->isShowSystemTab))
        {
            $workbookFile = str_replace(array('%sheetTitle%', '%sysValue%', '%definedNames%'), array($sheetTitle, $this->lang->excel->title->sysValue, $definedNames), $workbookFile);
        }
        else
        {
            $workbookFile = str_replace(array('%sheetTitle%', '%sysValue%', '%definedNames%'), array($sheetTitle, ' ', $definedNames), $workbookFile);
        }
        file_put_contents($this->exportPath . 'xl/workbook.xml', $workbookFile);
    }

    /**
     * Append shared strings
     *
     * @param  string    $value
     * @access public
     * @return void
     */
    public function appendSharedStrings($value)
    {
        $preserve = strpos($value, "\n") === false ? '' : ' xml:space="preserve"';
        $preserve = strpos($value, "#") === false ? '' : ' xml:space="preserve"';
        $value    = htmlspecialchars_decode($value);
        $value    = htmlspecialchars($value, ENT_QUOTES);
        $this->sharedStrings .= '<si><t' . $preserve . '>' . $value . '</t></si>';
        $this->record++;
    }

    /**
     * Set tips.
     *
     * @param  string    $y
     * @param  int       $x
     * @param  string    $tips
     * @access public
     * @return void
     */
    public function setTips($y, $x, $tips)
    {
        $this->mergeCells($y . $x, $this->setExcelFiled($this->sheet1Params['colspan'] - 1) . $x);

        if($tips == 'testcase')
        {
            /* 导出后的底部文字提示信息，加入自动化分类这个字段的描述。*/
            $this->lang->testcase->categoryList = array_filter($this->lang->testcase->categoryList);
            $this->lang->excel->help->testcase .= $this->lang->file->testcaseCategory;
            $this->lang->excel->help->testcase .= implode('，', $this->lang->testcase->categoryList);
        }
        elseif($tips == 'caselib')
        {
            /* 导出后的底部文字提示信息，加入自动化分类这个字段的描述。*/
            $this->lang->excel->help->caselib  = $this->lang->file->testcaseCategory;
            $this->lang->excel->help->caselib .= implode('，', $this->lang->testcase->categoryList);
        }

        $this->sheet1SheetData .= '<row r="' . $x . '" spans="1:%colspan%">';
        $this->sheet1SheetData .= $this->setCellValue($y, $x, $this->lang->excel->help->$tips);
        $this->sheet1SheetData .= '</row>';
    }
}
