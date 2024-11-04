<?php
include '../../control.php';
class myApi extends api
{
    /**
     * filename文件MD5
     */
    public function filemd5()
    {
        parse_str($this->server->query_String, $queryString); //获取get参数
        $queryString['filename'] = urldecode($queryString['filename']);
        $file =  $this->app->getWwwRoot().'data/upload/1/'.urldecode($queryString['filename']);
        if(is_file($file))
        {
            $this->success(200, ['filename' => $queryString['filename'], 'md5' => md5_file($file)]);
        } else {
            $this->success(0, ['filename' => $queryString['filename'], 'md5' => 'no such file']);
        }


    }
}