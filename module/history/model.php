<?php
class historyModel extends model
{
    /**
     * [readExcel ��Excel���������]
     * @param  [array] $data      [����]
     * @param  [string] $filePath [�ļ�·��]
     * @param  [array] $column    [�����]
     * @return [type]             [description]
     */
    public function readExcel($data=array(),$filePath,$column)
    {
        $phpExcel = $this->app->loadClass('phpexcel');
        $this->app->loadClass('pclzip', true);

        //��ȡ��׺��
        $extension = strtolower( pathinfo($filePath, PATHINFO_EXTENSION) );

        //�ж�
        if ($extension =='xlsx') {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
        } else if ($extension =='xls') {
            //$objReader = new PHPExcel_Reader_Excel5();//use excel2007 for 2007 format
            $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
        } else if ($extension=='csv') {
            //��û�в��Թ�
            $objReader = new PHPExcel_Reader_CSV();
            //Ĭ�������ַ���
            $objReader->setInputEncoding('GBK');
            //Ĭ�ϵķָ���
            $objReader->setDelimiter(',');
        }

        //�������ļ�
        $objPHPExcel = $objReader->load($filePath);
        $sheet       = $objPHPExcel->getSheet(0);
        $highestRow  = $sheet->getHighestRow();

        //�ӵڼ���׷��
        $h = $highestRow + 1;
        //ѭ������
        for($j = 0; $j < count($data); $j++){
            //ѭ���������ж��ٸ�
            for ($i=0; $i < count($data[$j]); $i++) {
                //���ձ��ڼ���׷������
                $objPHPExcel->getActiveSheet()->setCellValue("$column[$i]"."$h",$data[$j][$i]); //�������
            //�����+1
            }
            $h++;
        }

        $obj_writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007'); //�����ļ�
        $obj_writer->save($filePath); //�����ļ�

        //�����ļ�·��
        return $filePath;
    }

    /**
     * [exportexcel ����Excel]
     * @param  array  $data     [description]
     * @param  array  $title    [description]
     * @param  string $filename [description]
     * @return [type]           [description]
     */
    public function exportexcel($data=array(),$title=array(),$filename="fixCase.xlsx")
    {
        $fp=fopen($filename,"r");
        $filesize=filesize($filename);

        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Accept-Length:".$filesize);
        header("Content-Disposition:attachment;filename=".basename($filename));
        header("Pragma:no-cache");
        header("Expires:0");

        $buffer=1024;
        $buffer_count=0;
        while(!feof($fp)&&$filesize-$buffer_count>0)
        {
            $data=fread($fp,$buffer);
            $buffer_count+=$buffer;
            echo $data;
        }
        fclose($fp);
    }
}
