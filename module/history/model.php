<?php
class historyModel extends model
{
    /**
     * [readExcel 往Excel表插入数据]
     * @param  [array] $data      [数据]
     * @param  [string] $filePath [文件路径]
     * @param  [array] $column    [表格列]
     * @return [type]             [description]
     */
    public function readExcel($data=array(),$filePath,$column)
    {
        $phpExcel = $this->app->loadClass('phpexcel');
        $this->app->loadClass('pclzip', true);

        //获取后缀名
        $extension = strtolower( pathinfo($filePath, PATHINFO_EXTENSION) );

        //判断
        if ($extension =='xlsx') {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
        } else if ($extension =='xls') {
            //$objReader = new PHPExcel_Reader_Excel5();//use excel2007 for 2007 format
            $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
        } else if ($extension=='csv') {
            //还没有测试过
            $objReader = new PHPExcel_Reader_CSV();
            //默认输入字符集
            $objReader->setInputEncoding('GBK');
            //默认的分隔符
            $objReader->setDelimiter(',');
        }

        //加载新文件
        $objPHPExcel = $objReader->load($filePath);
        $sheet       = $objPHPExcel->getSheet(0);
        $highestRow  = $sheet->getHighestRow();

        //从第几行追加
        $h = $highestRow + 1;
        //循环数据
        for($j = 0; $j < count($data); $j++){
            //循环数据内有多少个
            for ($i=0; $i < count($data[$j]); $i++) {
                //按照表格第几列追加内容
                $objPHPExcel->getActiveSheet()->setCellValue("$column[$i]"."$h",$data[$j][$i]); //填充文字
            //表格行+1
            }
            $h++;
        }

        $obj_writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007'); //生成文件
        $obj_writer->save($filePath); //保存文件

        //返回文件路径
        return $filePath;
    }

    /**
     * [exportexcel 下载Excel]
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
