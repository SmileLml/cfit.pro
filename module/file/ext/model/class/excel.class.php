<?php
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
class excelFile extends fileModel
{
    public function excludeHtml($content, $extra = '')
    {
        $content = str_replace("\n", '',$content);
        $content = str_replace(array('<i>', '&nbsp;', '<br />', '</p>', '</li>'), array('', ' ', "\n", "\n", "\n"),$content);
        $content = preg_replace('/<[^ia\/]+(.*)>/U', '', $content);
        $content = preg_replace('/<\/[^a]{1}.*>/U', '', $content);
        $content = preg_replace('/<i .*>/U', '', $content);
        if($extra != 'noImg') $content = preg_replace('/<img src="data\/"(.*)\/>/U', "<img src=\"" . common::getSysURL() . "data/\"\$1/>", $content);
        return $content;
    }

    public function setAreaStyle($excelSheet, $style, $area)
    {
        $styleObj = new PHPExcel_Style(); 
        $styleObj->applyFromArray($style); 
        $excelSheet->setSharedStyle($styleObj, $area);
    }

    public function getRowsFromExcel($file)
    {
        /* Only parse files in zentao directory. */
        if(strpos($file, $this->app->getBasePath()) !== 0) return array();

        if(version_compare(substr(PHP_VERSION, 0, 3), '7.2') >= 0)
        {
            $this->app->loadClass('spout', true);

            $reader = ReaderFactory::create(Type::XLSX);

            $reader->open($file);
            $iterator = $reader->getSheetIterator();
            $iterator->rewind();

            $sheet   = $iterator->current();
            $rowIter = $sheet->getRowIterator();

            $rows = array();
            foreach($rowIter as $rowIndex => $row)
            {
                $cols = array();
                foreach($row as $col)
                {
                    $cols[] = ($col instanceof DateTime) ? $col->format('Y-m-d H:i:s') : $col;
                }
                $rows[$rowIndex] = $cols;
            }
        }
        else
        {
            $phpExcel  = $this->app->loadClass('phpexcel');
            $phpReader = new PHPExcel_Reader_Excel2007();
            if(!$phpReader->canRead($file)) $phpReader = new PHPExcel_Reader_Excel5();

            $phpExcel = $phpReader->load($file);
            $sheet    = $phpExcel->getSheet(0);
            $rows     = array();
            $rowIndex = 1;
            foreach($sheet->getRowIterator() as $row)
            {
                $cellIterator = $row->getCellIterator();

                $cols = array();
                foreach($cellIterator as $cell)
                {
                    if(is_null($cell)) continue;
                    $value = $cell->getValue();

                    if($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_NUMERIC)
                    {
                        $cellstyleformat = $cell->getStyle($cell->getCoordinate())->getNumberFormat();  

                        $formatcode = $cellstyleformat->getFormatCode();

                        if(preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode))
                        {
                            $value = gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP($value));
                        }
                        else
                        {
                            $value = PHPExcel_Style_NumberFormat::toFormattedString($value, $formatcode);
                        }
                    }

                    $cols[] = $value;
                }

                $rows[$rowIndex] = $cols;
                $rowIndex ++;
            }
        }

        return $rows;
    }
}
