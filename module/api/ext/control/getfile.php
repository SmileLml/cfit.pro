<?php
include '../../control.php';
class myApi extends api
{
    /**
     * 无限制下载文件
     */
    public function getfile()
    {
        parse_str($this->server->query_String, $queryString); //获取get参数
        $filename = urldecode($queryString['filename']);
        $level = $queryString['level'] ?? 1;
        $localRealFile =  dirname(__FILE__, 5).'/www/data/upload/'. $level .'/'. $filename; //实际存的附件
        $fileInfo       = pathinfo($localRealFile);
        if(is_file($localRealFile) < 1) { die ("no such file:$filename"); }
        header('Content-type: application/x-'.$fileInfo['extension']);
        header('Content-Disposition: attachment; filename='.$fileInfo['basename']);
        header('Content-Length: '.filesize($localRealFile));
        ob_clean();
        ob_end_flush();
        $remote = fopen($localRealFile, 'rb');
        while(!feof($remote))
        {
            echo fread($remote, 4096);
        }
        exit();
    }
}