<?php

class ImportException extends Exception {}

class cfjkFile extends fileModel
{
    /**
     * Project: chengfangjinke
     * Method: export2Word
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:42
     * Desc: This is the code comment. This method is called export2Word.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $name
     * @param $phpword
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function export2Word($name, $phpword)
    {
        header("Content-Disposition: attachment;filename=\"{$name}.docx\"");

        $wordWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007', $download = true);
        $wordWriter->save('php://output');

        exit;
    }

    /**
     * 获取Excel导入信息
     * @param $title
     * @param $sheet
     * @return Generator
     * @throws ImportException
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function import2Excel($title, $sheet = 0)
    {
        $file = $this->loadModel('file')->getUpload('file');
        $file = $file[0];

        if (empty($file)){
            throw new ImportException($this->lang->file->fileContentEmpty);
        }
        if('xlsx' != $file['extension']){
            throw new ImportException($this->lang->file->onlySupportXLSX);
        }

        $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
        move_uploaded_file($file['tmpname'], $fileName);
        $phpExcel  = $this->app->loadClass('phpexcel');
        $phpReader = new PHPExcel_Reader_Excel2007();
        if(!$phpReader->canRead($fileName)) {
            $phpReader = new PHPExcel_Reader_Excel5();
            if(!$phpReader->canRead($fileName)){
                throw new ImportException($this->lang->excel->canNotRead);
            }
        }

        $phpExcel     = $phpReader->load($fileName);
        $currentSheet = $phpExcel->getSheet($sheet);
        $allRows      = $currentSheet->getHighestRow();
        if($allRows <= 1){
            throw new ImportException($this->lang->file->fileContentEmpty);
        }

        $columns = $this->createColumn(count($title));
        foreach ($columns as $key => $column){
            $excelTitle = $this->getCalculatedValue($currentSheet, $column . 1);
            if($title[$key] != $excelTitle){
                throw new ImportException(sprintf($this->lang->file->titleError, $column));
            }
        }

        for ($currentRow = 2; $currentRow <= $allRows; $currentRow++){
            $row = [];
            foreach ($columns as $column){
                $row[$column] = $this->getCalculatedValue($currentSheet, $column . $currentRow);
            }
            yield $row;
        }

        $this->session->set('fileImport', $fileName);
    }

    /**
     * 根据列数获取列名数组
     * @param $num
     * @return array
     */
    private function createColumn($num)
    {
        $arr = [];
        for ($i = 1; $i <= $num; $i++){
            $arr[] = $this->getNameFromNum($i);
        }

        return $arr;
    }

    /**
     * 根据列数获取当前列名
     * @param $num
     * @return string
     */
    private function getNameFromNum($num)
    {
        $name = '';

        while ($num > 0){
            $mod  = ($num - 1) % 26;
            $name = chr($mod + 65) . $name;
            $num  = intdiv($num - 1, 26);
        }

        return $name;
    }

    /**
     * Project: chengfangjinke
     * Method: getCalculatedValue
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:53
     * Desc: This is the code comment. This method is called getCalculatedValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $sheet
     * @param $cell
     * @return mixed|string
     */
    private function getCalculatedValue($sheet, $cell)
    {
        $value = $sheet->getCell($cell)->getCalculatedValue();
        if(strpos($value, '_x000D') !== FALSE)
        {
            $vs = explode('_x000D', $value);
            $value = $vs[0];
        }

        return $value;
    }
}
