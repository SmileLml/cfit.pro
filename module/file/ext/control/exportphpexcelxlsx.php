<?php

helper::import(dirname(dirname(dirname(__FILE__))) . "/control.php");
class myfile extends file
{
    public function exportphpexcelXlsx()
    {
        $exportArr = $_POST;

        $phpexcel = $this->app->loadClass('phpexcel', false);
//        ->setCreator("NIC卡tsai")
        $phpexcel->getProperties()->setTitle("office 2007 xlsx");

        $rowmergestartletternum = $exportArr['rowmerge']['startletter'].$exportArr['rowmerge']['startnum'];
        $rowmergeendletternum = $exportArr['rowmerge']['endletter'].$exportArr['rowmerge']['endnum'];
        $rowmergestr = $rowmergestartletternum.':'.$rowmergeendletternum;

        $colmergestartletternum = $exportArr['colmerge']['startletter'].$exportArr['colmerge']['startnum'];
        $colmergeendletternum = $exportArr['colmerge']['endletter'].$exportArr['colmerge']['endnum'];
        $colmergestr = $colmergestartletternum.':'.$colmergeendletternum;

        $phpexcel->createSheet();
        $phpexcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objsheet = $phpexcel->setActiveSheetIndex(0);
        $objsheet->setTitle($exportArr['sheettitle']);
        // 列合并
        $objsheet->mergeCells($rowmergestr);
        //合并列中上设置标题
        $objsheet->setCellValue($rowmergestartletternum,$exportArr['rowmergetitle']);

        // 行合并
        $objsheet->mergeCells($colmergestr);
        //数据填充起始列
        $firstLetter = 'A';
        $rowmergestart = ord($exportArr['rowmerge']['startletter']);
        $rowmergeend = ord($exportArr['rowmerge']['endletter']);

        $excelKey = [];
//            $objsheet->setCellValue($colmergeendletternum,$exportArr['rowmergetitle']);


        foreach ($exportArr['fields'] as $key=>$field){

            $excelKey[$key] = $firstLetter;
            $tempord = ord($firstLetter);

            if($tempord >= $rowmergestart and $tempord<=$rowmergeend){
                $objsheet->setCellValue($firstLetter.'2',html_entity_decode($field));
            }else{
                $objsheet->setCellValue($firstLetter.'1',html_entity_decode($field));
            }
            $firstLetter++;
        }

        $objsheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objsheet->getStyle($rowmergestartletternum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $i=$exportArr['stardatarow'];


        foreach ($exportArr['rows'] as $key2=>$data){

            foreach ($excelKey as $key=>$letter){
                if (isset($data->$key)){

                    $objsheet->setCellValue($letter.$i,$data->$key);

                }
            }
            $i++;
        }
        setcookie('downloading', 1, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        ob_end_clean();
        ob_start();
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"{$exportArr['filename']}.xlsx\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        $objwrite = PHPExcel_IOFactory::createWriter($phpexcel,'Excel2007');
        $objwrite->save('php://output');
        exit();
    }
}