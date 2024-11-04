<?php
include '../../control.php';
class myApi extends api
{
    /**
     * 文件MD5
     */
    public function downloadmd5()
    {
        parse_str($this->server->query_String, $queryString); //获取get参数
        $queryString['filename'] = urldecode($queryString['filename']);
        $remoteFile =  $queryString['filename'] ; //'/files/项目测试版本发布区/TEST/测试.txt';
        if(substr($remoteFile, 0, 7) !=='/files/'){
            $this->response('filename error 下载文件名错误');
        }
//        $this->checkApiToken(); //校验token
        $this->loadModel('downloads');

        $download = $this->downloads->getDownload($remoteFile); //是否已经有该文件的下载任务
        $this->success(200, ['md5' => $download['md5']]);

    }
}